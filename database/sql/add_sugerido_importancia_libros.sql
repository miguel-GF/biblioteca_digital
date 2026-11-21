-- Agrega los campos de "libros sugeridos" a la tabla libros.
-- Ejecutar directamente en MySQL / MariaDB (phpMyAdmin, Laragon, etc.).
-- No requiere `php artisan migrate`.

ALTER TABLE `libros`
  ADD COLUMN `sugerido` TINYINT(1) NOT NULL DEFAULT 0 AFTER `status`,
  ADD COLUMN `importancia` TINYINT UNSIGNED NOT NULL DEFAULT 1 AFTER `sugerido`,
  ADD INDEX `libros_sugerido_importancia_idx` (`sugerido`, `importancia`);

-- IMPORTANTE:
--   sugerido    = 0 (no) / 1 (sí)
--   importancia = 1 a 100 (a mayor número, más arriba aparece en los sugeridos)


-- ---------------------------------------------------------------------------
-- ROLLBACK (solo si necesitas revertir los cambios)
-- ---------------------------------------------------------------------------
-- ALTER TABLE `libros`
--   DROP INDEX `libros_sugerido_importancia_idx`,
--   DROP COLUMN `importancia`,
--   DROP COLUMN `sugerido`;
