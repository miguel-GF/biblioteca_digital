# Guía de liberación — Libros sugeridos

**Commit de esta liberación:** `8cce759`
**Rama:** `main`

Esta guía sirve para subir a producción la sección de **libros sugeridos**, el panel administrativo y la carga diferida de portadas de PDF.

> Importante: este cambio **no usa migración de Laravel**. Los campos nuevos se agregan con **SQL directo** en la base de datos.

---

## 1. Antes de empezar

1. Haz un respaldo de la base de datos de producción.
2. Haz un respaldo o ten a la mano la versión anterior del proyecto.
3. Confirma que tienes acceso a:
   - El servidor (SSH o panel de archivos).
   - phpMyAdmin o consola MySQL.
   - El repositorio de Git.

---

## 2. Compilar los assets (frontend)

En tu máquina local, dentro del proyecto:

```bash
npm ci
npm run build
```

Verifica que se haya generado este archivo:

```text
public/build/manifest.json
```

> `public/build` **no se sube por Git** porque está en `.gitignore`. Hay que subirlo manualmente o compilarlo en el servidor.

---

## 3. Subir el código

### Opción A: con Git (recomendada)

En local:

```bash
git push origin main
```

En el servidor:

```bash
git pull origin main
```

Recuerda que Git **no incluye** `public/build`. Después de hacer `pull`, continúa con el paso 4.

### Opción B: por FTP / panel de archivos

Sube solo estos archivos.

**Archivos modificados:**

```text
app/Http/Controllers/HomeController.php
app/Http/Controllers/LibroController.php
app/Http/Requests/LibroGuardarRequest.php
app/Models/Libro.php
routes/web.php
vite.config.js
resources/js/Components/Header.vue
resources/js/Components/PdfPreview.vue
resources/js/Components/Welcome.vue
resources/js/Layouts/AppLayout.vue
resources/js/Pages/Home.vue
resources/js/Pages/LibroAlta.vue
```

**Archivos nuevos:**

```text
resources/js/Components/LibrosSugeridosLista.vue
resources/js/Pages/LibrosSugeridos.vue
resources/js/Pages/LibrosSugeridosAdmin.vue
database/sql/add_sugerido_importancia_libros.sql
```

**Assets compilados:**

```text
public/build/   (carpeta completa)
```

No subas `node_modules`, `vendor`, `storage/logs` ni archivos temporales.

---

## 4. Publicar los assets en el servidor

### Si el servidor tiene Node.js

```bash
npm ci
npm run build
```

### Si el servidor NO tiene Node.js

Sube desde tu máquina la carpeta completa:

```text
public/build/
```

Verifica que exista:

```text
public/build/manifest.json
```

Si en el servidor ya existía una carpeta `public/build` vieja, reemplázala completa para evitar mezclar archivos con hash antiguo.

---

## 5. Ejecutar el SQL en producción (obligatorio)

### 5.1 Verificar si los campos ya existen

En phpMyAdmin, selecciona la base de datos y ejecuta:

```sql
SHOW COLUMNS FROM `libros` LIKE 'sugerido';
SHOW COLUMNS FROM `libros` LIKE 'importancia';
```

- Si **no devuelven nada**, continúa con el paso 5.2.
- Si **ya existen**, no vuelvas a ejecutar el `ALTER TABLE` (daría error de columna duplicada).

### 5.2 Ejecutar el ALTER TABLE

**Opción phpMyAdmin:**
1. Selecciona la base de datos.
2. Entra a la pestaña **SQL**.
3. Pega y ejecuta:

```sql
ALTER TABLE `libros`
  ADD COLUMN `sugerido` TINYINT(1) NOT NULL DEFAULT 0 AFTER `status`,
  ADD COLUMN `importancia` TINYINT UNSIGNED NOT NULL DEFAULT 1 AFTER `sugerido`,
  ADD INDEX `libros_sugerido_importancia_idx` (`sugerido`, `importancia`);
```

**Opción consola (Linux):**

```bash
mysql -u USUARIO -p NOMBRE_BASE < database/sql/add_sugerido_importancia_libros.sql
```

**Opción consola (Windows PowerShell):**

```powershell
Get-Content database\sql\add_sugerido_importancia_libros.sql | mysql -u USUARIO -p NOMBRE_BASE
```

> Este SQL solo agrega columnas e índice. **No borra libros ni modifica datos existentes.**

### 5.3 Confirmar

```sql
SHOW COLUMNS FROM `libros` LIKE 'sugerido';
SHOW COLUMNS FROM `libros` LIKE 'importancia';
```

Deben aparecer:
- `sugerido` → `tinyint(1)`, default `0`
- `importancia` → `tinyint unsigned`, default `1`

---

## 6. Dependencias de PHP (Composer)

Si el servidor **no tiene** la carpeta `vendor` o cambió el `composer.lock`:

```bash
composer install --no-dev --optimize-autoloader
```

Si `vendor` ya está instalado y solo subiste código:

```bash
composer dump-autoload -o
```

> En esta liberación no agregamos clases PHP nuevas, así que `composer dump-autoload` no es obligatorio, pero es recomendable.

---

## 7. Limpiar y regenerar cachés

En el servidor:

```bash
php artisan optimize:clear
php artisan config:cache
php artisan view:cache
```

> **No ejecutes `php artisan route:cache` en este proyecto.**  
> Las rutas tienen closures (`preview-libro` y el fallback), y Laravel no puede serializarlas. Si accidentalmente se generó, `php artisan optimize:clear` lo limpia.

Si usas caché de configuración y necesitas cambiarla después, recuerda volver a ejecutar:

```bash
php artisan config:clear
php artisan config:cache
```

---

## 8. Permisos (solo servidores Linux)

```bash
chmod -R 775 storage bootstrap/cache
```

Si el usuario del servidor web es distinto:

```bash
chown -R www-data:www-data storage bootstrap/cache
```

En Windows/IIS no aplica normalmente.

---

## 9. Verificación después de publicar

### 9.1 Páginas públicas

Abre:

```text
https://TU-DOMINIO/home
https://TU-DOMINIO/libros
https://TU-DOMINIO/libros-sugeridos
```

Comprueba:
- El home carga sin error.
- En escritorio aparece el botón flotante **Sugeridos** arriba a la derecha.
- Al abrirlo, solo se desplaza la lista y el botón **Ver todos** queda fijo.
- En móvil, la sección aparece debajo de las áreas.
- Las portadas de PDF cargan solo al acercarse a la pantalla.

### 9.2 Panel administrativo

Entra a:

```text
https://TU-DOMINIO/login
```

> El login de este proyecto usa la tabla `usuarios`, no `users`.
> El usuario debe tener `status = ACTIVO` y `tipo = ADMINISTRADOR`.

Después entra a:

```text
https://TU-DOMINIO/dashboard/libros-sugeridos
```

Prueba:
1. Buscar un libro.
2. Agregarlo a sugeridos.
3. Agregar otro.
4. Reordenarlos.
5. Cambiar una importancia.
6. Presionar **Guardar cambios**.
7. Confirmar que solo se hace una petición al guardar.
8. Volver al home y ver el orden actualizado.
9. Verificar que el botón **Ver sugeridos en la biblioteca** abre la página pública.

### 9.3 Si algo falla

Revisa:

```text
storage/logs/laravel.log
```

Y confirma que `public/build/manifest.json` exista.

---

## 10. Rollback (si es necesario)

### Código

```bash
git revert 8cce759
```

O cambia al commit anterior y vuelve a compilar/subes los assets.

### Base de datos (opcional)

El archivo SQL incluye el rollback comentado:

```sql
ALTER TABLE `libros`
  DROP INDEX `libros_sugerido_importancia_idx`,
  DROP COLUMN `importancia`,
  DROP COLUMN `sugerido`;
```

> **Cuidado:** esto elimina la configuración de sugeridos e importancias. Úsalo solo si vas a revertir la funcionalidad por completo.

---

## 11. Checklist rápido

- [ ] Respaldo de la base de datos realizado.
- [ ] `npm run build` ejecutado.
- [ ] `public/build/manifest.json` existe.
- [ ] Código subido (Git o FTP).
- [ ] `public/build` subido o compilado en el servidor.
- [ ] SQL ejecutado en producción.
- [ ] Columnas `sugerido` e `importancia` verificadas.
- [ ] `composer install` o `composer dump-autoload` ejecutado.
- [ ] `php artisan optimize:clear` ejecutado.
- [ ] `php artisan config:cache` ejecutado.
- [ ] `php artisan view:cache` ejecutado.
- [ ] Permisos de `storage` y `bootstrap/cache` revisados.
- [ ] Home, libros y libros sugeridos probados.
- [ ] Login y panel de sugeridos probados.
- [ ] Guardado por lote probado.
- [ ] Orden reflejado en el home.
