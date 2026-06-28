-- ============================================================================
-- Campos de la web pública de reservas para las habitaciones de hotel.
--
-- tenancy:migrate está bloqueado en este proyecto (ver memoria del equipo), así
-- que estas columnas se aplican con un ALTER directo sobre la base de datos de
-- CADA tenant. Ejecutar una vez por tenant:
--
--   mysql -u root -p <db_del_tenant> < database/sql/hotel_rooms_landing_fields.sql
--
-- Las sentencias son idempotentes a nivel de "ya existe" sólo si tu MySQL es
-- 8.0+ (IF NOT EXISTS en ADD COLUMN). Si tu versión no lo soporta, quita los
-- "IF NOT EXISTS" y aplica sólo las columnas que falten.
-- ============================================================================

ALTER TABLE `hotel_rooms`
    ADD COLUMN IF NOT EXISTS `images`            TEXT         NULL AFTER `description`,
    ADD COLUMN IF NOT EXISTS `amenities`         TEXT         NULL AFTER `images`,
    ADD COLUMN IF NOT EXISTS `short_description` VARCHAR(255) NULL AFTER `amenities`,
    ADD COLUMN IF NOT EXISTS `capacity`          INT UNSIGNED NULL AFTER `short_description`,
    ADD COLUMN IF NOT EXISTS `beds`              VARCHAR(100) NULL AFTER `capacity`,
    ADD COLUMN IF NOT EXISTS `size`              INT UNSIGNED NULL AFTER `beds`,
    ADD COLUMN IF NOT EXISTS `featured`          TINYINT(1)   NOT NULL DEFAULT 0 AFTER `size`;
