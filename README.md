# AMEMT - Fichero Digital

Proyecto PHP + MySQL/MariaDB para AMEMT, con flujo Empleado y flujo Administrador integrado sobre la misma base de datos.

## Arquitectura del Sistema

### Frontend
- `public/index.php` - Página de login (público)
- `public/dashboard.php` - Panel de empleado (autenticado, no admin)
- `public/resumen.php` - Resumen mensual para empleados
- `public/admin/panel.php` - Panel de administrador
- `public/admin/` - 13 módulos administrativos (empleados, validación, config, etc.)
- `public/partials/` - Plantillas header/footer
- `public/assets/` - CSS, JS, imágenes (Bootstrap-based)

### Backend (PHP)
- `src/auth.php` - Login/logout, sesión y refresco del rol desde la base
- `src/functions.php` - Lógica core: fichajes, estadísticas, cirugías, novedades
- `src/admin_functions.php` - Módulo admin: empleados, validación, procedimientos, edición de registros
- `src/security.php` - CSRF, sesión segura, rate limiting, iconos, helpers

### API
- `public/api/fichaje.php` - Endpoint REST de clock in/out

---

## Estructura de Archivos

```
fichero_digital/
├── config/           → configuración y conexión a base de datos
├── src/              → autenticación, seguridad y lógica de negocio
│   ├── auth.php              (sesión, login/logout)
│   ├── security.php         (CSRF, sesión segura, helpers)
│   ├── functions.php        (fichaje empleado)
│   └── admin_functions.php  (módulo administrador)
├── database/        → esquema, datos de prueba y migraciones
└── public/          → único directorio expuesto al navegador
    ├── index.php            (login)
    ├── dashboard.php        (fichaje empleado)
    ├── resumen.php          (mi resumen)
    ├── api/                 (endpoints JSON)
    ├── admin/               (módulo Administrador)
    ├── partials/            (header/footer del flujo Empleado)
    └── assets/
        ├── css/             (style.css + bootstrap-compat.css)
        ├── js/              (app.js, pagination.js)
        └── vendor/bootstrap/ (Bootstrap 5 local, offline)
        └── img/             (amemt_logo.jpg)
```

---

## Flujo Actual

### Empleado
1. Login en `index.php`
2. Fichaje de entrada y salida (dashboard o API)
3. Resumen mensual (`resumen.php?mes=YYYY-MM`)
4. Actividades y cirugías del día

El bloque "Fichero Digital" del menú es un enlace: lleva al panel de administración si el usuario es admin y al fichaje del día si es empleado.

### Administrador
1. Panel de administración con indicadores
2. Alerta de salidas pendientes y personal presente
3. Listado de empleados (búsqueda y filtro por contrato)
4. Alta, edición y baja lógica de empleados
5. Cirugías: registro con fecha, hora, procedimiento y personal participante (alta y edición)
6. Vacaciones/ART: registro de novedades con historial (alta y edición)
7. Alquileres: estadísticas, registro de quirófano y confirmación de la hora exacta de salida
8. Resumen mensual: horas, días, empleados activos, cirugías por empleado
9. Configuración: datos del sistema, tipos de procedimientos, mi cuenta, cierre de sesión masivo

---

## Base de Datos (MySQL - `amemt`)

### Tablas Principales
- `usuarios` - Users (DNI, password_hash, es_admin, tipo_contrato)
- `fichajes` - Clock in/out records with validation status
- `cirugias` - Surgical procedures with participants
- `cirugia_personal` - Surgery-personnel mapping
- `tipos_procedimiento` - Procedure type catalog
- `novedades` - Holidays/absences (vacaciones, ART, capacitaciones)
- `alquileres` - Equipment rentals (con responsable interno o externo)
- `rate_limits` - Rate limiting tracking
- `auditoria` - Audit trail of all sensitive actions

### Campos Clave
- `usuarios`: id_usuario, nombre, apellido, dni (UNIQUE), password_hash, es_admin, activo, tipo_contrato, id_tipo_personal, sesion_token
- `fichajes`: id_fichaje, id_usuario, fecha, hora_entrada, hora_salida, horas_trabajadas, validado_admin, validado_por, obs_validacion
- `cirugias`: id_cirugia, fecha, hora_inicio, id_tipo_procedimiento, observaciones, registrado_por
- `novedades`: id_novedad, id_usuario, tipo, fecha_desde, fecha_hasta, observaciones, registrado_por
- `alquileres`: id_alquiler, id_usuario (NULL si es responsable externo), nombre_responsable, institucion, fecha, hora_entrada, hora_salida, hora_salida_confirmada, horas_uso, dato_facturacion

---

## Flujos de Trabajo Principales

1. **Login**: `index.php` → credenciales → `login_user()` → redirect a dashboard (empleado) o `admin/panel.php` (admin)

2. **Clock In/Out (empleado)**:
   - Dashboard muestra estado actual
   - POST a `api/fichaje.php?action=entrada/salida`
   - Valida no entrada duplicada, registra hora, devuelve JSON

3. **Validación Admin**:
   - `admin/fichajes_pendientes.php` lista correcciones pendientes
   - Valida/corre via POST (acciones: validar/corregir_entrada/corregir)
   - Tabla completa en `admin/validacion.php` con filtrado por mes/empleo/estado

4. **Resumen Mensual (empleado)**:
   - `resumen.php?mes=YYYY-MM`
   - Muestra horas, días, cirugías, novedades del mes

5. **Gestión de Empleados (admin)**:
   - Listar, buscar, crear, editar, toggle estado, eliminar
   - Cada empleado tiene tipo de contrato (Fijo/Por Hora/Por Cirugía/Alquiler/Admin)
   - El módulo de empleados no toca `es_admin`: un administrador no se puede crear ni degradar desde la interfaz

6. **Configuración (admin)**:
   - Gestionar tipos de procedimientos
   - Actualizar cuenta de usuario (contraseña)
   - Cerrar sesión de todos los usuarios (auto-cierra fichajes abiertos)

7. **Edición de registros (admin)**:
   - Cirugías, novedades y alquileres tienen botón "Editar" en cada fila
   - `?editar=<id>` recarga el mismo formulario de alta con los datos cargados (mismos campos y validaciones) y botón "Cancelar"
   - El POST manda `action=editar` + `id`; si la validación falla, el formulario sigue abierto con lo que se cargó
   - Cada actualización queda en `auditoria` (`cirugia_actualizada`, `novedad_actualizada`, `alquiler_actualizada`)

8. **Novedades / Vacaciones / ART (admin)**:
   - "Fecha hasta" solo ofrece días posteriores a "Fecha desde", y el servidor rechaza `fecha_hasta <= fecha_desde`
   - El historial se muestra en orden ascendente (más antigua primero)
   - No hay borrado automático de novedades vencidas: la baja es manual

9. **Alquileres: hora exacta de salida (admin)**:
   - Al dar de alta el alquiler la hora de salida es aproximada
   - Cada fila pendiente tiene un campo de hora + botón "Confirmar" (etiqueta gris `aprox.`); al confirmar se recalculan las `horas_uso`, se marca `hora_salida_confirmada = 1`, el botón desaparece y quedan solo "Editar" y "Eliminar"
   - El indicador "Salidas sin confirmar" muestra cuántas faltan
   - Si se edita la hora de salida desde "Editar", el alquiler vuelve a quedar como aproximado hasta que se confirme
   - Rechaza una hora de salida anterior o igual a la de entrada

---

## Seguridad Aplicada

- PDO con consultas preparadas en todas las consultas
- CSRF tokens en todos los formularios
- Rate limiting por IP y DNI (5 intentos/15min login, 10/hour fichajes)
- Sesiones con regeneración de ID, cookies seguras (HTTPS-only, Lax SameSite)
- El rol se relee de la base en cada request: un cambio de `es_admin` se aplica sin esperar un nuevo login
- Autorización de administrador en servidor (`require_admin()`), no solo ocultando links de la interfaz
- Contraseñas hasheadas via `password_verify`/`password_hash` (bcrypt)
- Escape de salida con `htmlspecialchars()`
- Auditoría de todas las acciones sensibles (login, fichaje, empleado, cirugías, novedades, alquileres, configuración)
- Invalidación global de sesiones: cada login genera token en `usuarios.sesion_token`; botón "Cerrar sesión de todos" invalida todas simultáneamente
- Validación en servidor con transacciones + `SELECT ... FOR UPDATE` para evitar condiciones de carrera
- Baja lógica para conservar información histórica
- Directorios internos bloqueados por `.htaccess`
- Headers de seguridad y listado de directorios deshabilitado

---

## API Endpoint

`POST public/api/fichaje.php`

- **Headers**: `Content-Type: application/json`, token CSRF en `X-CSRF-Token`
- **Rate limit**: 10 requests/hora por usuario, bloqueo 1 hora al exceder
- **Actions**:
  - `entrada` - Registrar entrada
  - `salida` - Registrar salida
- **Response**: JSON `{'ok': bool, 'message': string, 'time': string|''}`
  - Ejemplo éxito: `{"ok":true,"time":"10:30"}`
  - Ejemplo error: `{"ok":false,"message":"Ya existe una entrada abierta para hoy."}`

---

## Nota sobre Tipo de Contrato

El flujo administrador muestra `Fijo`, `Por Hora`, `Por Cirugía` y `Alquiler`. Este dato se agregó a `usuarios.tipo_contrato`. El rol/especialidad continúa usando `tipos_personal`, evitando mezclar dos conceptos diferentes.

---

## Acceso Administrador

- El acceso al panel depende únicamente de `usuarios.es_admin`.
- Hay un solo administrador (DNI `30111222`); el resto de los usuarios son empleados.
- Si se necesita un administrador adicional (por ejemplo Contabilidad de AMEMT), se crea el usuario con `es_admin = 1` directamente en la base: no hay pantalla para dar de alta admins.
- Al degradar o desactivar un admin conviene limpiar su `sesion_token` para que pierda el acceso en el momento.

---

## Instalación con XAMPP

1. Copiar la carpeta a `C:\xampp\htdocs\fichero_digital` (si se cambia de carpeta, ajustar `BASE_URL` en `config/config.php`)
2. Iniciar Apache y MySQL
3. En phpMyAdmin ejecutar `database/01_schema.sql`
4. Ejecutar `database/02_seed.sql` para cargar datos de prueba
5. Si ya tenías la BDD anterior, ejecutar `database/03_migration_admin.sql`, `database/04_migration_seguridad.sql`, `database/05_migration_admin.sql`, `database/06_migration_username_dni.sql` y `database/07_migration_hora_salida_confirmada.sql`
6. Abrir `http://localhost/fichero_digital/public/`

---

## Usuarios de Prueba

Contraseña de prueba (guardada como **hash bcrypt**): `12345678`

- Administrador: DNI `30111222` (en la base activa: Natalia Díaz; el seed lo crea como "Juan Pérez")
- Empleado: DNI `31222333`
- Empleado: DNI `33444555`
- Empleado: DNI `32333444`

> Solo el DNI `30111222` tiene `es_admin = 1`; el resto son empleados (ver "Acceso Administrador").

> Las contraseñas del seed son hashes bcrypt. En producción no deben usarse estas credenciales.
