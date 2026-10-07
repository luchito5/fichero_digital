# AMEMT - Fichero Digital

Proyecto PHP + MySQL/MariaDB para AMEMT, con flujo Empleado y flujo Administrador integrado sobre la misma base de datos.

## Arquitectura del Sistema

### Frontend
- `public/index.php` - Página de login (público, DNI de 8 dígitos)
- `public/dashboard.php` - Panel de empleado (autenticado, no admin): fichaje, actividad del día y sistema de sonidos
- `public/resumen.php` - Resumen mensual para empleados (tablas paginadas)
- `public/logout.php` - Cierre de sesión
- `public/admin/panel.php` - Panel de administrador
- `public/admin/` - 13 módulos administrativos (empleados, validación, cirugías, informes con export, config, etc.)
- `public/partials/` - Plantillas header/footer del flujo Empleado (incluye el control de sonido)
- `public/admin/partials/` - Plantillas header/footer del flujo Administrador
- `public/assets/` - CSS, JS, audios e imágenes (Bootstrap 5 local, offline)
  - `js/`: `app.js` (fichaje), `pagination.js`, `dni-input.js`, `confirm-modal.js`, `sound.js` (sonidos)
  - `sounds/`: audios MP3 de entrada, salida y cierre de sesión

### Backend (PHP)
- `src/auth.php` - Login/logout, sesión y refresco del rol desde la base
- `src/functions.php` - Lógica core: fichajes, cooldown anti-spam, estadísticas, cirugías, novedades
- `src/admin_functions.php` - Módulo admin: empleados, validación, procedimientos, edición de registros
- `src/security.php` - CSRF, sesión segura, rate limiting, validación de DNI, iconos, helpers
- `src/export_report.php` - Armado de datos del informe mensual para exportar
- `src/docx_writer.php` / `src/pdf_writer.php` - Generación de descargas Word y PDF

### API
- `public/api/fichaje.php` - Endpoint REST de clock in/out (JSON)

---

## Estructura de Archivos

```
fichero_digital/
├── config/           → configuración y conexión a base de datos
├── src/              → autenticación, seguridad y lógica de negocio
│   ├── auth.php              (sesión, login/logout)
│   ├── security.php         (CSRF, sesión segura, rate limiting, DNI, helpers)
│   ├── functions.php        (fichaje empleado + cooldown)
│   ├── admin_functions.php  (módulo administrador)
│   ├── export_report.php    (datos del informe mensual)
│   ├── docx_writer.php      (export Word)
│   └── pdf_writer.php       (export PDF)
├── database/        → esquema, datos de prueba y migraciones
└── public/          → único directorio expuesto al navegador
    ├── index.php            (login)
    ├── dashboard.php        (fichaje empleado)
    ├── resumen.php          (mi resumen)
    ├── logout.php           (cierre de sesión)
    ├── api/                 (endpoints JSON)
    ├── admin/               (módulo Administrador)
    ├── partials/            (header/footer del flujo Empleado)
    └── assets/
        ├── css/             (style.css + bootstrap-compat.css)
        ├── js/              (app.js, pagination.js, dni-input.js,
        │                     confirm-modal.js, sound.js)
        ├── sounds/          (entrada.mp3, salida.mp3, logout.mp3)
        ├── vendor/bootstrap/ (Bootstrap 5 local, offline)
        └── img/             (amemt_logo.jpg)
```

---

## Flujo Actual

### Empleado
1. Login en `index.php` (DNI de exactamente 8 dígitos)
2. Fichaje de entrada y salida (dashboard o API) con feedback visual y **sonoro**
3. Control de sonidos en la barra superior (mute + volumen, persistente por navegador)
4. Resumen mensual (`resumen.php?mes=YYYY-MM`) con tablas paginadas
5. Actividades y cirugías del día (burbujas de entrada/salida, sección "Cirugías de hoy")

El bloque "Fichero Digital" del menú es un enlace: lleva al panel de administración si el usuario es admin y al fichaje del día si es empleado.

### Administrador
1. Panel de administración con indicadores
2. Alerta de salidas pendientes y personal presente
3. Listado de empleados (búsqueda y filtro por contrato)
4. Alta, edición y baja lógica de empleados (DNI único y validado a 8 dígitos)
5. Cirugías: registro con fecha, hora, procedimiento y personal participante (Instrumentador/a, Anestesista, Enfermero/a y otros roles agrupados; alta y edición)
6. Vacaciones/ART: registro de novedades con historial y badges por tipo (alta y edición)
7. Alquileres: estadísticas, registro de quirófano y confirmación de la hora exacta de salida
8. Resumen mensual: horas, días, empleados activos, cirugías por empleado + **export DOCX/PDF**
9. Configuración: datos del sistema, tipos de procedimientos (alta/baja vía AJAX sin recarga), mi cuenta, cierre de sesión masivo
10. Todas las acciones destructivas usan un **modal de confirmación** accesible (reemplaza al `confirm()` nativo del navegador)

---

## Sistema de Sonidos (lado Empleado)

Al fichar y al cerrar sesión se reproduce una voz (audio MP3). **Solo aplica del lado empleado**: el layout de administración no lo carga.

### Qué suena y cuándo
| Evento | Audio | Condición |
|---|---|---|
| Registrar entrada | `entrada.mp3` | Solo si el servidor confirmó el fichaje (overlay "REGISTRO CORRECTO") |
| Registrar salida | `salida.mp3` | Solo si el servidor confirmó el fichaje |
| Cerrar sesión | `logout.mp3` | Al hacer clic en "Cerrar sesión" |

Errores, fichajes duplicados o bloqueos por cooldown **no suenan**.

### Archivos de audio
- Ubicación: `public/assets/sounds/{entrada,logout,salida}.mp3`
- Los nombres se configuran en el mapa `SOUNDS` al inicio de `public/assets/js/sound.js`
- Si falta un archivo: no suena, pero **todo lo demás sigue funcionando** (sin errores)
- El script se carga desde `public/partials/footer.php` con cache-busting `?v=filemtime`

### Control de mute y volumen
- Icono de altavoz + slider (0–100%) en la barra superior, junto al badge de usuario; visible en "Fichaje" y "Mi resumen"
- **Mute y volumen persisten en `localStorage`** (`fd_sound_muted`, `fd_sound_volume`) por navegador
- Muteado o volumen 0: no suena nada, el cierre de sesión navega al instante y mutear **corta** el audio que esté sonando
- El volumen se aplica en vivo, incluso a un audio en reproducción

### Comportamiento del audio
- El audio de fichaje **no se corta** al apretar "Continuar" del cartel de éxito: la recarga espera a que termine (`FDSound.waitForIdle()`, tope 10 s)
- Al cerrar sesión se intercepta el clic, suena el audio y recién se navega (tope 2,5 s); Ctrl/Cmd+click o click central no se interceptan (sigue abriendo en otra pestaña)
- Para desactivar el sistema: eliminar la etiqueta `<script>` de `sound.js` en `public/partials/footer.php`

---

## Exportación de Informes (DOCX / PDF)

- **Página**: `public/admin/resumen_mensual.php` (sección "Resumen general")
- **Botones**: `Word (.DOCX)` y `PDF (.PDF)` — conservan los filtros activos (mes, empleado, tipo de contrato)
- **Formatos**: DOCX y PDF (no soporta Excel ni CSV)
- **Nombre de archivo**: `fichero_digital_amemt_YYYY-MM[_contrato]`
- **Contenido del reporte** (con fila de totales por sección):
  1. Resumen por empleado (fichajes, horas, cirugías, novedades)
  2. Fichajes del mes (horas y estado de validación)
  3. Cirugías del mes
  4. Novedades (vacaciones, ART, licencias, capacitaciones)
  5. Alquileres (salida confirmada/aproximada, horas de uso)
- **Archivos**: `src/export_report.php` (armado de datos), `src/docx_writer.php`, `src/pdf_writer.php` (generación)

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

> El sistema de sonidos y la exportación de informes **no agregan tablas**: los audios son archivos estáticos y los reportes se generan en memoria a partir de las tablas existentes.

### Campos Clave
- `usuarios`: id_usuario, nombre, apellido, dni (UNIQUE), password_hash, es_admin, activo, tipo_contrato, id_tipo_personal, sesion_token
- `fichajes`: id_fichaje, id_usuario, fecha, hora_entrada, hora_salida, horas_trabajadas, validado_admin, validado_por, obs_validacion
- `cirugias`: id_cirugia, fecha, hora_inicio, id_tipo_procedimiento, observaciones, registrado_por
- `novedades`: id_novedad, id_usuario, tipo, fecha_desde, fecha_hasta, observaciones, registrado_por
- `alquileres`: id_alquiler, id_usuario (NULL si es responsable externo), nombre_responsable, institucion, fecha, hora_entrada, hora_salida, hora_salida_confirmada, horas_uso, dato_facturacion

---

## Flujos de Trabajo Principales

1. **Login**: `index.php` → credenciales → `login_user()` → redirect a dashboard (empleado) o `admin/panel.php` (admin)
   - El DNI se valida en cliente (`dni-input.js`) y en servidor (`dni_solo_digitos`/`dni_es_valido`): exactamente 8 dígitos
   - Rate limit: 5 intentos por IP y por DNI (ventana 15 min) con bloqueo de 3 minutos

2. **Clock In/Out (empleado)**:
   - Dashboard muestra estado actual, botones y countdown si hay bloqueo
   - POST a `api/fichaje.php` con `FormData` (`action=entrada|salida`) y header `X-CSRF-TOKEN`
   - Valida no entrada duplicada, registra hora, devuelve JSON y reproduce el sonido correspondiente
   - **Cooldown anti-spam**: al alcanzar un múltiplo de 10 fichajes del día (entrada+salida), el fichaje queda cortado 3 minutos desde el último registro (próximo corte en la fichada 20, 30, …). Responde HTTP 429 con `cooldown` y `retry_after`; el dashboard muestra "Fichaje bloqueado… Podés volver a fichar en M:SS" con cuenta regresiva en vivo

3. **Validación Admin**:
   - `admin/fichajes_pendientes.php` lista correcciones pendientes
   - Valida/corre via POST (acciones: validar/corregir_entrada/corregir)
   - Tabla completa en `admin/validacion.php` con filtrado por mes/empleo/estado

4. **Resumen Mensual (empleado)**:
   - `resumen.php?mes=YYYY-MM`
   - Muestra horas, días, cirugías, novedades del mes (tablas paginadas de 10 filas)

5. **Gestión de Empleados (admin)**:
   - Listar, buscar, crear, editar, toggle estado, eliminar
   - Cada empleado tiene tipo de contrato (Fijo/Por Hora/Por Cirugía/Alquiler/Admin)
   - El módulo de empleados no toca `es_admin`: un administrador no se puede crear ni degradar desde la interfaz
   - DNI validado a 8 dígitos y **único** también al editar

6. **Configuración (admin)**:
   - Gestionar tipos de procedimientos (alta y baja vía AJAX: la lista se actualiza sin recargar la página)
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

10. **Export de informes (admin)**:
    - `admin/resumen_mensual.php?mes=YYYY-MM[&empleado=][&tipo=]&export=docx|pdf`
    - Genera y sirve la descarga sin renderizar la página (ver sección "Exportación de Informes")

11. **Sistema de sonidos (empleado)**:
    - Ver sección "Sistema de Sonidos": audios de entrada/salida/logout, control de mute y volumen persistente

---

## Seguridad Aplicada

- PDO con consultas preparadas en todas las consultas
- CSRF tokens en todos los formularios; la API valida el token por header `X-CSRF-TOKEN`
- Rate limiting por IP y DNI (login: 5 intentos/15 min con bloqueo de 3 min; fichajes: 10/hora con bloqueo de 3 min)
- Cooldown anti-spam de fichaje: 10 eventos por día cortan el fichaje 3 minutos (auditoría `fichaje_bloqueado`; los rechazos generan `fichaje_rechazado`)
- Validación de DNI de 8 dígitos en cliente y servidor (`dni-input.js` + `dni_validar()`)
- Modal de confirmación para acciones destructivas (`confirm-modal.js`): evita confirmaciones accidentales y soporta flujos AJAX
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

- **Request**: body `FormData` con `action=entrada|salida` (no es JSON), header `X-CSRF-TOKEN`
- **Rate limit**: 10 requests/hora por usuario, bloqueo 3 minutos al exceder (HTTP 429)
- **Actions**:
  - `entrada` - Registrar entrada
  - `salida` - Registrar salida
- **Response**: JSON `{'ok': bool, 'message': string, 'time': string|'', 'cooldown'?: bool, 'retry_after'?: int}`
  - Éxito: `{"ok":true,"time":"10:30"}`
  - Error de negocio: `{"ok":false,"message":"Ya existe una entrada abierta para hoy."}`
  - Cooldown (HTTP 429): `{"ok":false,"cooldown":true,"retry_after":142,"message":"Alcanzaste el límite de 10 fichajes por día..."}`
  - Errores: 401 (sesión expirada), 403 (CSRF inválido), 400 (acción inválida), 429 (rate limit)

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
7. *(Opcional)* Copiar los audios MP3 a `public/assets/sounds/` con los nombres `entrada.mp3`, `salida.mp3` y `logout.mp3`. Si no existen, el sistema funciona igual pero sin sonido

---

## Usuarios de Prueba

Contraseña de prueba (guardada como **hash bcrypt**): `12345678`

- Administrador: DNI `30111222` (en la base activa: Natalia Díaz; el seed lo crea como "Juan Pérez")
- Empleado: DNI `31222333`
- Empleado: DNI `33444555`
- Empleado: DNI `32333444`

> Solo el DNI `30111222` tiene `es_admin = 1`; el resto son empleados (ver "Acceso Administrador").

> Las contraseñas del seed son hashes bcrypt. En producción no deben usarse estas credenciales.
