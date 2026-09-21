# AMEMT - Fichero Digital

Proyecto PHP + MySQL/MariaDB para AMEMT, con flujo Empleado y flujo Administrador integrado sobre la misma base de datos.

## Arquitectura del Sistema

### Frontend
- `public/index.php` - Página de login (público)
- `public/dashboard.php` - Panel de empleado (autenticado, no admin)
- `public/resumen.php` - Resumen mensual para empleados
- `public/admin/panel.php` - Panel de administrador
- `public/admin/` - 14 módulos administrativos (empleados, validación, config, etc.)
- `public/partials/` - Plantillas header/footer
- `public/assets/` - CSS, JS, imágenes (Bootstrap-based)

### Backend (PHP)
- `src/auth.php` - Autenticación, sesiones, CSRF, rate limiting, auditoría
- `src/functions.php` - Lógica core: fichajes, estadísticas, cirugías, novedades
- `src/admin_functions.php` - Módulo admin: empleados, validación, procedimientos
- `src/security.php` - Utilidades de seguridad

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

### Administrador
1. Panel de administración con indicadores
2. Alerta de salidas pendientes y personal presente
3. Listado de empleados (búsqueda y filtro por contrato)
4. Alta, edición y baja lógica de empleados
5. Cirugías: registro con fecha, hora, procedimiento y personal participante
6. Vacaciones/ART: registro de novedades con historial
7. Alquileres: estadísticas y registro de quirófano
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
- `alquileres` - Equipment rentals
- `rate_limits` - Rate limiting tracking
- `auditoria` - Audit trail of all sensitive actions

### Campos Clave
- `usuarios`: id_usuario, nombre, apellido, dni (UNIQUE), password_hash, es_admin, activo, tipo_contrato, id_tipo_personal, sesion_token
- `fichajes`: id_fichaje, id_usuario, fecha, hora_entrada, hora_salida, horas_trabajadas, validado_admin, validado_por, obs_validacion
- `cirugias`: id_cirugia, fecha, hora_inicio, id_tipo_procedimiento, observaciones, registrado_por

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

6. **Configuración (admin)**:
   - Gestionar tipos de procedimientos
   - Actualizar cuenta de usuario (contraseña)
   - Cerrar sesión de todos los usuarios (auto-cierra fichajes abiertos)

---

## Seguridad Aplicada

- CSRF tokens en todos los formularios
- Rate limiting por IP y DNI (5 intentos/15min login, 10/hour fichajes)
- Sesiones con regeneración de ID, cookies seguras (HTTPS-only, Lax SameSite)
- Contraseñas hasheadas via `password_verify`/`password_hash` (bcrypt)
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

## Instalación con XAMPP

1. Copiar la carpeta a `C:\xampp\htdocs\fichero_digital` (si se cambia de carpeta, ajustar `BASE_URL` en `config/config.php`)
2. Iniciar Apache y MySQL
3. En phpMyAdmin ejecutar `database/01_schema.sql`
4. Ejecutar `database/02_seed.sql` para cargar datos de prueba
5. Si ya tenías la BDD anterior, ejecutar `database/03_migration_admin.sql`, `database/04_migration_seguridad.sql` y `database/05_migration_admin.sql`
6. Abrir `http://localhost/fichero_digital/public/`

---

## Usuarios de Prueba

Contraseña de prueba (guardada como **hash bcrypt**): `12345678`

- Administrador: DNI `30111222`
- Empleado: DNI `31222333`
- Administrador: DNI `33444555`
- Empleado: DNI `32333444`

> Las contraseñas del seed son hashes bcrypt. En producción no deben usarse estas credenciales.

---

## Seguridad Aplicada (resumen)

- PDO y consultas preparadas
- Claves con `password_hash()` / `password_verify()` (bcrypt)
- Sesiones con regeneración del ID
- Cookies HttpOnly/SameSite
- CSRF en operaciones POST
- Autorización de Administrador en servidor, no solo en la interfaz
- Validación de datos recibidos
- Escape HTML con `htmlspecialchars()`
- **Rate limiting** contra fuerza bruta y spam
- **Auditoría**: toda acción sensible queda registrada en la tabla `auditoria`
- **Invalidación global de sesiones**: cierra todas las sesiones activas al instante
- Validación de estado en servidor con transacciones + `SELECT ... FOR UPDATE`
- Baja lógica para conservar información histórica
- Directorios internos separados del directorio público
- Headers de seguridad básicos y listado de directorios deshabilitado