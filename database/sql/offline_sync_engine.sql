-- ---------------------------------------------------------------------------
-- Motor de sincronización offline (módulo Offline)
--
-- Alternativa a `php artisan tenancy:migrate` para cuando la corrida completa
-- de migraciones falla. Ejecutar sobre CADA base de datos de tenant:
--
--   mysql -u root -p tenant_XXXX < database/sql/offline_sync_engine.sql
--
-- Es idempotente: se puede volver a correr sin romper nada.
-- ---------------------------------------------------------------------------

-- 1. Ampliación de offline_configurations -----------------------------------
--    (MySQL no soporta ADD COLUMN IF NOT EXISTS, se usa un procedimiento)

DROP PROCEDURE IF EXISTS offline_add_column;
DELIMITER //
CREATE PROCEDURE offline_add_column(IN t VARCHAR(64), IN c VARCHAR(64), IN d TEXT)
BEGIN
    IF NOT EXISTS (
        SELECT 1 FROM information_schema.COLUMNS
        WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = t AND COLUMN_NAME = c
    ) THEN
        SET @sql = CONCAT('ALTER TABLE `', t, '` ADD COLUMN `', c, '` ', d);
        PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;
    END IF;
END //
DELIMITER ;

CALL offline_add_column('offline_configurations', 'mode',          "VARCHAR(10) NOT NULL DEFAULT 'server'");
CALL offline_add_column('offline_configurations', 'terminal_code', 'VARCHAR(20) NULL');
CALL offline_add_column('offline_configurations', 'terminal_name', 'VARCHAR(100) NULL');
CALL offline_add_column('offline_configurations', 'sync_enabled',  'TINYINT(1) NOT NULL DEFAULT 0');
CALL offline_add_column('offline_configurations', 'sync_interval', 'INT UNSIGNED NOT NULL DEFAULT 60');
CALL offline_add_column('offline_configurations', 'is_online',     'TINYINT(1) NOT NULL DEFAULT 0');
CALL offline_add_column('offline_configurations', 'last_ping_at',  'TIMESTAMP NULL');
CALL offline_add_column('offline_configurations', 'last_push_at',  'TIMESTAMP NULL');
CALL offline_add_column('offline_configurations', 'last_pull_at',  'TIMESTAMP NULL');
CALL offline_add_column('offline_configurations', 'git_remote',    'VARCHAR(255) NULL');
CALL offline_add_column('offline_configurations', 'git_branch',    'VARCHAR(100) NULL');
CALL offline_add_column('offline_configurations', 'app_version',   'VARCHAR(40) NULL');

DROP PROCEDURE IF EXISTS offline_add_column;

UPDATE offline_configurations SET mode = 'client' WHERE is_client = 1;

-- 2. Bandeja de salida -------------------------------------------------------

CREATE TABLE IF NOT EXISTS `offline_sync_queue` (
    `id`              BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `uuid`            CHAR(36) NOT NULL,
    `terminal_code`   VARCHAR(20) NULL,
    `entity`          VARCHAR(60) NOT NULL,
    `entity_id`       BIGINT UNSIGNED NOT NULL,
    `operation`       VARCHAR(10) NOT NULL DEFAULT 'create',
    `payload`         LONGTEXT NULL,
    `depends_on`      TEXT NULL,
    `priority`        SMALLINT UNSIGNED NOT NULL DEFAULT 100,
    `status`          VARCHAR(15) NOT NULL DEFAULT 'pending',
    `attempts`        SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    `last_error`      TEXT NULL,
    `next_attempt_at` TIMESTAMP NULL,
    `remote_id`       BIGINT UNSIGNED NULL,
    `synced_at`       TIMESTAMP NULL,
    `created_at`      TIMESTAMP NULL,
    `updated_at`      TIMESTAMP NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `offline_sync_queue_uuid_unique` (`uuid`),
    KEY `offline_sync_queue_dispatch_index` (`status`, `priority`, `id`),
    KEY `offline_sync_queue_entity_index` (`entity`, `entity_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3. Mapa de ids local <-> remoto -------------------------------------------

CREATE TABLE IF NOT EXISTS `offline_id_maps` (
    `id`            BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `terminal_code` VARCHAR(20) NOT NULL DEFAULT '',
    `entity`        VARCHAR(60) NOT NULL,
    `local_id`      BIGINT UNSIGNED NOT NULL,
    `remote_id`     BIGINT UNSIGNED NOT NULL,
    `uuid`          CHAR(36) NULL,
    `created_at`    TIMESTAMP NULL,
    `updated_at`    TIMESTAMP NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `offline_id_maps_local_unique` (`terminal_code`, `entity`, `local_id`),
    KEY `offline_id_maps_remote_index` (`entity`, `remote_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 4. Rangos de correlativos reservados --------------------------------------

CREATE TABLE IF NOT EXISTS `offline_number_ranges` (
    `id`               BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `uuid`             CHAR(36) NOT NULL,
    `terminal_code`    VARCHAR(20) NOT NULL,
    `model_alias`      VARCHAR(60) NOT NULL DEFAULT 'document',
    `document_type_id` VARCHAR(3) NULL,
    `series`           VARCHAR(10) NOT NULL,
    `from_number`      BIGINT UNSIGNED NOT NULL,
    `to_number`        BIGINT UNSIGNED NOT NULL,
    `current_number`   BIGINT UNSIGNED NULL,
    `status`           VARCHAR(15) NOT NULL DEFAULT 'active',
    `allocated_at`     TIMESTAMP NULL,
    `exhausted_at`     TIMESTAMP NULL,
    `reported_at`      TIMESTAMP NULL,
    `created_at`       TIMESTAMP NULL,
    `updated_at`       TIMESTAMP NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `offline_number_ranges_uuid_unique` (`uuid`),
    KEY `offline_number_ranges_terminal_index` (`terminal_code`, `status`),
    KEY `offline_ranges_series_index` (`model_alias`, `document_type_id`, `series`, `status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 5. Estado del pull incremental --------------------------------------------

CREATE TABLE IF NOT EXISTS `offline_pull_states` (
    `id`             INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `entity`         VARCHAR(60) NOT NULL,
    `last_synced_at` TIMESTAMP NULL,
    `last_remote_id` BIGINT UNSIGNED NULL,
    `records`        INT UNSIGNED NOT NULL DEFAULT 0,
    `created_at`     TIMESTAMP NULL,
    `updated_at`     TIMESTAMP NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `offline_pull_states_entity_unique` (`entity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 6. Bitácora ----------------------------------------------------------------

CREATE TABLE IF NOT EXISTS `offline_sync_logs` (
    `id`            BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `terminal_code` VARCHAR(20) NULL,
    `direction`     VARCHAR(12) NOT NULL,
    `entity`        VARCHAR(60) NULL,
    `success`       TINYINT(1) NOT NULL DEFAULT 1,
    `records`       INT UNSIGNED NOT NULL DEFAULT 0,
    `message`       TEXT NULL,
    `duration_ms`   INT UNSIGNED NULL,
    `created_at`    TIMESTAMP NULL,
    `updated_at`    TIMESTAMP NULL,
    PRIMARY KEY (`id`),
    KEY `offline_sync_logs_direction_index` (`direction`, `created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 7. Terminales registrados (lado servidor) ---------------------------------

CREATE TABLE IF NOT EXISTS `offline_terminals` (
    `id`               INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `code`             VARCHAR(20) NOT NULL,
    `name`             VARCHAR(100) NULL,
    `establishment_id` INT UNSIGNED NULL,
    `user_id`          INT UNSIGNED NULL,
    `active`           TINYINT(1) NOT NULL DEFAULT 1,
    `app_version`      VARCHAR(40) NULL,
    `last_ip`          VARCHAR(45) NULL,
    `last_seen_at`     TIMESTAMP NULL,
    `last_push_at`     TIMESTAMP NULL,
    `last_pull_at`     TIMESTAMP NULL,
    `pending_hint`     INT UNSIGNED NOT NULL DEFAULT 0,
    `created_at`       TIMESTAMP NULL,
    `updated_at`       TIMESTAMP NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `offline_terminals_code_unique` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 8. Marca las migraciones como aplicadas para que artisan no las repita -----

INSERT IGNORE INTO `migrations` (`migration`, `batch`)
SELECT v.m, COALESCE((SELECT MAX(batch) FROM migrations), 0) + 1
FROM (
    SELECT '2026_08_03_000001_tenant_offline_upgrade_configurations_table' AS m
    UNION ALL SELECT '2026_08_03_000002_tenant_create_offline_sync_queue_table'
    UNION ALL SELECT '2026_08_03_000003_tenant_create_offline_id_maps_table'
    UNION ALL SELECT '2026_08_03_000004_tenant_create_offline_number_ranges_table'
    UNION ALL SELECT '2026_08_03_000005_tenant_create_offline_pull_states_table'
    UNION ALL SELECT '2026_08_03_000006_tenant_create_offline_sync_logs_table'
    UNION ALL SELECT '2026_08_03_000007_tenant_create_offline_terminals_table'
) v;
