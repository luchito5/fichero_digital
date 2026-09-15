# AMEMT - Fichero Digital

Proyecto PHP + MySQL/MariaDB para AMEMT, con flujo Empleado y flujo Administrador integrado sobre la misma base de datos.

## Estructura

```
fichero_digital/
├── config/          → configuración y conexión a base de datos
├── src/             → autenticación, seguridad y lógica de negocio
│   ├── auth.php             (sesión, login/logout)
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
        ├── js/              (app.js)
        └── vendor/bootstrap/ (Bootstrap 5 local, offline)
```

## Flujo actual

### Empleado
- Login.
- Fichaje de entrada y salida.
- Resumen mensual.
- Actividades y cirugías del día.

### Administrador
- Panel de administración.
- Indicadores de personal, presentes, cirugías y alquileres.
- Alerta de salidas pendientes.
- Personal presente ahora.
- Listado de empleados (búsqueda y filtro por contrato).
- Alta, edición y baja lógica de empleados.
- Cirugías: registro con fecha, hora, procedimiento y personal participante (cirujano, instrumentador, anestesia, otro), más listado por mes y baja.
- Vacaciones / ART: registro de novedades (vacaciones, licencias, ART, capacitaciones) con historial.
- Alquileres: estadísticas (hoy, mes, horas cedidas), registro de alquileres de quirófano con responsable externo (nombre/institución) y listado.
- Resumen mensual: horas registradas, días trabajados, empleados activos y total de cirugías por empleado; exportación a Word (.doc) y a PDF imprimible.
- Configuración: datos del sistema (institución, dirección, teléfono, email), tipos de procedimientos quirúrgicos (agregar/eliminar), Mi cuenta (nombre, usuario, contraseña) y cierre de sesión de todos los usuarios.

## Frontend: Bootstrap 5 local

Bootstrap se incluye de forma **no invasiva**:

1. `assets/vendor/bootstrap/css/bootstrap.min.css` — el framework.
2. `assets/css/bootstrap-compat.css` — neutraliza los resets del reboot de Bootstrap para preservar el diseño original.
3. `assets/css/style.css` — hoja de estilos del proyecto.

La carga siempre se hace en ese orden (`bootstrap → compat → style`), y el JS bundle se incluye al final de cada página. Esto mantiene la apariencia actual intacta y deja disponibles las utilidades/componentes de Bootstrap para usos futuros. Requiere internet solo para descargar el framework; una vez en `vendor/` funciona offline.

## Instalación con XAMPP

1. Copiar la carpeta a `C:\xampp\htdocs\fichero_digital` (si la cambiás de carpeta, ajustar `BASE_URL` en `config/config.php`).
2. Iniciar Apache y MySQL.
3. En phpMyAdmin ejecutar `database/01_schema.sql`.
4. Ejecutar `database/02_seed.sql` para cargar datos de prueba.
5. Si ya tenías la BDD anterior, ejecutar `database/03_migration_admin.sql`, `database/04_migration_seguridad.sql` y `database/05_migration_admin.sql`.
6. Abrir `http://localhost/fichero_digital/public/`.

## Usuarios de prueba

Contraseña de prueba (guardada como **hash bcrypt**): `12345678`

- Administrador: DNI `30111222`
- Empleado: DNI `31222333`
- Administrador: DNI `33444555`
- Empleado: DNI `32333444`

> Las contraseñas del seed son hashes bcrypt. En producción no deben usarse estas credenciales.

## Seguridad aplicada

- PDO y consultas preparadas.
- Claves con `password_hash()` / `password_verify()` (bcrypt).
- Sesiones con regeneración del ID.
- Cookies HttpOnly/SameSite.
- CSRF en operaciones POST.
- Autorización de Administrador en servidor, no solo en la interfaz.
- Validación de datos recibidos.
- Escape HTML con `htmlspecialchars()`.
- **Rate limiting** contra fuerza bruta y spam: máx. 5 intentos de login fallidos por IP/DNI en 15 min (bloqueo temporal), y máx. 10 solicitudes/hora al endpoint de fichaje. Tabla `rate_limits`.
- **Auditoría**: toda acción sensible (login, login fallido, fichaje entrada/salida, alta/edición/baja de empleado, cirugías, novedades, alquileres, configuración) queda registrada en la tabla `auditoria` con fecha, usuario, acción, detalle e IP.
- **Invalidación global de sesiones**: cada login genera un token de sesión en `usuarios.sesion_token`; el botón "Cerrar sesión de todos los usuarios" de Configuración invalida todas las sesiones activas al instante (la sesión del propio administrador se conserva).
- Validación de estado en servidor con transacciones + `SELECT ... FOR UPDATE` para evitar duplicados (condiciones de carrera).
- Baja lógica para conservar información histórica.
- Directorios internos separados del directorio público (`config/`, `src/`, `database/` bloqueados por `.htaccess`).
- Headers de seguridad básicos y listado de directorios deshabilitado.

## API

`public/api/fichaje.php` responde JSON para registrar entrada/salida. Solo acepta POST con sesión válida y token CSRF. Su respuesta siempre es JSON estructurado:
`{"ok":true,"time":"10:30"}` o `{"ok":false,"message":"..."}`.

## Nota sobre tipo de contrato

El flujo Administrador muestra `Fijo`, `Por Hora`, `Por Cirugía` y `Alquiler`. Como ese dato no existía en la BDD original, se agregó `usuarios.tipo_contrato`. El rol/especialidad continúa usando `tipos_personal`, evitando mezclar dos conceptos diferentes.