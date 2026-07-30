# 📘 Documentación Técnica del Proyecto: Circulistas App (`circulistas-app3`)

Este documento ofrece una descripción detallada sobre cómo está desarrollado el proyecto **Circulistas App**, su arquitectura interna, las tecnologías utilizadas y los requisitos necesarios para ejecutarlo desde cero en cualquier computadora.

---

## 📄 1. Descripción General del Proyecto

**Circulistas App** es un sistema web integral diseñado para la gestión y administración de comunidades, retiros espirituales y eventos (tales como Emaús, Cursillos de Cristiandad, entre otros). Permite llevar el registro detallado de circulistas (participantes), controlar su asistencia y roles en cada evento, generar nóminas (circulares de retiro, cocina, etc.), realizar búsquedas avanzadas y detectar registros duplicados.

---

## 🛠️ 2. Arquitectura y Tecnologías Utilizadas

El proyecto está desarrollado bajo el patrón de arquitectura **MVC (Modelo-Vista-Controlador)** utilizando el framework moderno **Laravel 12** en el Backend y **Blade + Bootstrap 5 + Vite** en el Frontend.

### 🧰 Stack Tecnológico:

1. **Backend**:
   - **Lenguaje**: PHP `>= 8.2`
   - **Framework**: Laravel `12.x`
   - **ORMs y Utilidades**: Eloquent ORM, Laravel Tinker.
   - **Autenticación y Autorización**: Sistema nativo de Laravel combinado con un middleware de roles personalizado (`role:administrador,supervisor`).

2. **Base de Datos**:
   - **Motor principal**: **PostgreSQL** (optimizado para búsquedas sin acentos mediante la extensión `unaccent`).
   - **Compatibilidad**: Puede adaptarse a MySQL / MariaDB o SQLite configurando las variables correspondientes en el archivo `.env`.

3. **Frontend**:
   - **Motor de Plantillas**: Blade (Laravel Blade Templates).
   - **Framework de Estilos**: Bootstrap `5.3` complementado con Tailwind CSS `v4`.
   - **Bundler y Assets**: Vite `^7.0` con `laravel-vite-plugin`.
   - **Lógica del Cliente**: JavaScript modular (ES6+) y Axios para peticiones asíncronas AJAX (verificación rápida de duplicados, modales interactivos).

---

## 🗂️ 3. Estructura y Módulos Principales del Proyecto

La estructura del código sigue el estándar de Laravel:

```text
circulistas-app3/
├── app/
│   ├── Http/
│   │   ├── Controllers/        # Lógica de negocio (CirculistaController, EventoController, etc.)
│   │   └── Middleware/         # Control de acceso y roles de usuario
│   └── Models/                 # Modelos Eloquent (Circulista, Evento, Participacion, Rol, User)
├── database/
│   ├── migrations/             # Definición de estructura de tablas de la BD
│   └── seeders/                # Datos iniciales (Roles, Tipos de Eventos, Usuarios por defecto)
├── public/                     # Punto de entrada público (index.php, imágenes, assets compilados)
├── resources/
│   ├── views/                  # Vistas Blade (Layouts, Circulistas, Eventos, Usuarios)
│   ├── css/                    # Estilos CSS
│   └── js/                     # Scripts JS interactivos
├── routes/
│   └── web.php                 # Rutas HTTP de la aplicación web y middlewares aplicados
├── .env.example                # Plantilla de configuración de variables de entorno
├── composer.json               # Dependencias backend (PHP)
└── package.json                # Dependencias frontend (NodeJS/Vite/Bootstrap)
```

### 🧩 Entidades de Datos Principales:

* **`Circulista`** (`app/Models/Circulista.php`): Representa a la persona/participante. Contiene datos personales (nombre, apellido, DNI, fecha de nacimiento, teléfono, email, localidad, parroquia, observaciones) e incluye lógica para detectar duplicados por DNI o coincidencia de nombres.
* **`Evento`** (`app/Models/Evento.php`): Representa un retiro o actividad. Contiene tipo de evento, número de edición, fecha de inicio y fin, lema, lugar y estado (activo, cerrado, etc.).
* **`Participacion`** (`app/Models/Participacion.php`): Tabla pivote que vincula a un `Circulista` con un `Evento` y le asigna un **`Rol`** específico en dicho evento (ej. Caminante/Cursillista, Servidor, Cocina, etc.).
* **`Rol`** y **`TipoEvento`**: Tablas maestras para categorizar las participaciones y tipos de retiros.
* **`User`** (`app/Models/User.php`): Usuarios que inician sesión en el sistema con permisos estructurados (`administrador`, `supervisor`, `usuario`).

---

## 🖥️ 4. Requisitos para Funcionar en Cualquier Computadora

Para poder ejecutar este proyecto en una máquina nueva (Windows, macOS o Linux), se debe disponer de las siguientes herramientas instaladas:

### 📋 Requisitos de Software (Prerequisitos):

1. **PHP `>= 8.2`**
   - Extensiones de PHP necesarias habilitadas en `php.ini`:
     - `pdo_pgsql` (o `pdo_mysql` / `pdo_sqlite` según la BD a usar)
     - `mbstring`, `openssl`, `tokenizer`, `xml`, `curl`, `fileinfo`
2. **Composer** (`v2.x`): Gestor de paquetes de PHP.
3. **Node.js** (`v18.x` o superior) y **npm**: Para compilar los archivos del frontend.
4. **Base de Datos**:
   - **PostgreSQL** (Recomendado, versión 12 o superior) o servicio cloud como Supabase.
   - Alternativamente: MySQL / MariaDB o SQLite.
5. **Servidor Web Local** (Opcional):
   - XAMPP, Laragon, Herd o simplemente el servidor embebido de PHP (`php artisan serve`).

---

## 🚀 5. Guía de Instalación Paso a Paso

Sigue estos pasos para poner a funcionar el proyecto en cualquier computadora:

### Paso 1: Obtener el Código Fuente
Copia o clona el directorio del proyecto en la computadora destino (por ejemplo, en `C:\xampp\htdocs\circulistas-app3` o en tu carpeta personal).

### Paso 2: Instalación de Dependencias Backend (PHP)
Abre la terminal en la carpeta raíz del proyecto y ejecuta:
```bash
composer install
```

### Paso 3: Instalación de Dependencias Frontend (Node.js)
En la misma terminal, ejecuta:
```bash
npm install
```

### Paso 4: Configurar las Variables de Entorno (`.env`)
1. Duplica el archivo `.env.example` y renómbralo a `.env`:
   * En Windows (PowerShell): `copy .env.example .env`
   * En Linux/macOS: `cp .env.example .env`
2. Edita el archivo `.env` configurando las credenciales de tu base de datos:
   ```env
   APP_NAME="Circulistas App"
   APP_ENV=local
   APP_KEY=
   APP_DEBUG=true
   APP_URL=http://localhost:8000

   # Configuración de Base de Datos (Ejemplo con PostgreSQL local)
   DB_CONNECTION=pgsql
   DB_HOST=127.0.0.1
   DB_PORT=5432
   DB_DATABASE=circulistas_db
   DB_USERNAME=postgres
   DB_PASSWORD=tu_contraseña

   # O si usas SQLite:
   # DB_CONNECTION=sqlite
   ```

### Paso 5: Generar la Clave Única de la Aplicación
```bash
php artisan key:generate
```

### Paso 6: Ejecutar las Migraciones y Cargar Datos Iniciales (Seeders)
Crea las tablas en la base de datos y genera los usuarios y roles por defecto:
```bash
php artisan migrate --seed
```

### Paso 7: Compilar los Archivos Frontend
* **Para entorno de desarrollo (con recarga rápida)**:
  ```bash
  npm run dev
  ```
* **Para entorno de producción**:
  ```bash
  npm run build
  ```

### Paso 8: Iniciar el Servidor de Aplicación
Para iniciar el servidor local de desarrollo:
```bash
php artisan serve
```
El sistema estará accesible en tu navegador web ingresando a:
👉 **`http://127.0.0.1:8000`** (o `http://localhost:8000`).

---

## ⚡ Comandos Rápidos de Utilidad (Cheat Sheet)

* **Script de Inicialización Automática** (incluido en `composer.json`):
  ```bash
  composer run setup
  ```
* **Ejecutar Servidor + Vite en simultáneo**:
  ```bash
  npm run dev
  # o también:
  composer run dev
  ```
* **Limpiar Caché si hay cambios en configuración**:
  ```bash
  php artisan config:clear
  php artisan cache:clear
  ```

---

## 🔐 6. Consideraciones de Seguridad y Producción

1. **Variables de Entorno**: Nunca subir ni compartir el archivo `.env` real a repositorios públicos, ya que contiene credenciales y contraseñas.
2. **Permisos de Archivos**: Asegurarse de que la carpeta `storage/` y `bootstrap/cache/` tengan permisos de escritura.
3. **Modo Producción**: En servidores de producción reales, cambiar `APP_ENV=production` y `APP_DEBUG=false`.
