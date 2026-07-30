# 📘 Circulistas App (`circulistas-app3`)

Sistema de gestión de comunidades, retiros espirituales y eventos (Emaús, Cursillos de Cristiandad, etc.), desarrollo de circulistas, nóminas y control de participaciones.

## 📄 Documentación Técnica Completa

Para consultar la arquitectura detallada, tecnologías utilizadas, guía de instalación paso a paso y requisitos para cualquier computadora, revisa el archivo de documentación en la raíz del repositorio:

👉 **[DOCUMENTACION.md](file:///c:/xampp/htdocs/circulistas-app3/DOCUMENTACION.md)**

---

## 🚀 Inicio Rápido

1. **Instalar dependencias Backend y Frontend**:
   ```bash
   composer install
   npm install
   ```

2. **Configurar entorno y base de datos**:
   ```bash
   cp .env.example .env
   php artisan key:generate
   php artisan migrate --seed
   ```

3. **Compilar assets y ejecutar servidor**:
   ```bash
   npm run build
   php artisan serve
   ```
   Accede en `http://127.0.0.1:8000`.

