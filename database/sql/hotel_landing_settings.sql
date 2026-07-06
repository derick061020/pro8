-- Configuración personalizable de la web pública de reservas (landing).
-- Aplicar por tenant (tenancy:migrate está bloqueado; ver memoria
-- tenant-migrate-blocked-hotel-rent-changes). Idempotente: no falla si ya existe.
CREATE TABLE IF NOT EXISTS `hotel_landing_settings` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `establishment_id` int(10) unsigned DEFAULT NULL,
  `data` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `hotel_landing_settings_establishment_id_index` (`establishment_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
