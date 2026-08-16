# 📜 Documento de Reglas de Negocio: Circulistas App (`circulistas-app3.0`)

Este documento compila de forma exhaustiva todas las **Reglas de Negocio (Business Rules)**, restricciones del dominio y validaciones implementadas en el sistema **Circulistas App** del Movimiento de Círculos de Juventud (MCJ).

---

## 🔐 1. Autenticación, Seguridad y Roles de Usuario (RBAC)

El sistema utiliza un esquema de control de acceso basado en roles (**Role-Based Access Control - RBAC**):

### 1.1 Niveles de Permisos y Roles
1. **Administrador (`administrador`)**:
   - Acceso y control total del sistema.
   - **Exclusivo:** Gestión completa (CRUD) de usuarios del sistema (`User`).
   - Gestión de Circulistas (Crear, Editar, Eliminar, Ver, Verificación de Duplicados).
   - Gestión de Eventos (Crear, Editar, Eliminar, Ver, Importación Masiva).
   - Gestión de Participaciones (Agregar, Editar rol/grupo, Quitar participante).
   - Acceso a Búsqueda Avanzada.
2. **Supervisor (`supervisor`)**:
   - Gestión operativa de Circulistas, Eventos e Importación Masiva.
   - Gestión de Participaciones en eventos y Búsqueda Avanzada.
   - **Restricción:** No puede acceder al módulo de gestión de usuarios ni crear/editar administradores.
3. **Invitado / Usuario General (`invitado` / sin rol superior)**:
   - Solo lectura en el sistema.
   - Puede consultar listados de circulistas, fichas de perfil, listados de eventos y detalles.
   - Puede consultar y generar las circulares imprimibles (Circular de Retiro y Circular de Cocina).
   - **Restricción:** No puede crear, modificar ni eliminar ningún registro.

### 1.2 Reglas de Seguridad en Usuarios
- **Unicidad de Email:** El correo electrónico del usuario debe ser único en la tabla `users`.
- **Contraseñas:** Deben tener una longitud mínima de 6 caracteres y confirmación coincidente.
- **Protección de Auto-Eliminación:** Un usuario autenticado **no puede eliminarse a sí mismo** del sistema para evitar bloqueos de acceso administrativo.

---

## 👤 2. Reglas de Negocio de Circulistas (Participantes)

### 2.1 Datos Personales y Validaciones
- **Campos Obligatorios:** `Nombre` (máx. 100 caracteres) y `Apellido` (máx. 100 caracteres).
- **Email:** Opcional, pero si se ingresa debe ser un email válido y **único** entre todos los circulistas registrados.
- **Teléfonos:** Opcionales (`telefono` fijo y `celular`).

### 2.2 Tratamiento Flexible de la Fecha de Nacimiento
Para respetar el registro histórico donde a veces se desconoce el año exacto de nacimiento de un participante, se contemplan 3 modalidades:
1. **Fecha Completa:** Se ingresa día, mes y año. Se guarda como fecha estándar (`Y-m-d`) y la casilla `sin_anio_nacimiento` se establece en `false`.
2. **Solo Día y Mes:** Se ingresa día y mes sin año. El sistema valida que la combinación de día y mes sea válida (mediante la función `checkdate` usando el año bisiesto 1904 como pivote), almacena la fecha internamente como `1904-MM-DD` y marca el indicador `sin_anio_nacimiento = true`.
3. **Sin Fecha (Ninguna):** Se guarda como `null`.

### 2.3 Búsqueda Flexible e Insensible a Acentos (PostgreSQL `unaccent`)
- La búsqueda de circulistas soporta múltiples términos (separados por espacio o coma).
- La búsqueda es completamente **insensible a mayúsculas, minúsculas y tildes/acentos** (ejemplo: buscar `"Alvarez, Maria"` coincidirá correctamente con `"Álvarez, María"`).
- Aplica sobre los campos: `nombre`, `apellido`, `email`, `localidad`, `provincia`, `celular` y `telefono`.

### 2.4 Detección y Prevención de Duplicados
Para evitar duplicar fichas de una misma persona en la base de datos:
- **Criterio Combinado de Duplicidad:** Se considera que un circulista ya existe en el sistema si coincide el `Nombre` y `Apellido` (insensible a acentos/mayúsculas) **Y ADEMÁS** se cumple al menos una de las siguientes condiciones:
  1. Mismo número de `celular` (normalizado solo a dígitos).
  2. Misma `fecha_nacimiento`.
- **Verificación AJAX en Vivo:** Durante la carga del formulario, el sistema consulta asíncronamente si existe un circulista coincidente para prevenir el guardado antes de enviar.
- **Módulo de Duplicados:** Permite agrupar y visualizar todos los registros repetidos según diferentes criterios seleccionables:
  1. Mismo `Apellido` y `Nombre` normalizado (insensible a acentos/mayúsculas).
  2. Mismo `Celular` (normalizado solo a dígitos).
  3. Mismo `Teléfono` fijo (normalizado solo a dígitos).
  4. Mismo `Email` (insensible a mayúsculas).
  5. Misma `Fecha de Nacimiento`.

---

## 🎪 3. Reglas de Negocio de Eventos (Retiros y Jornadas)

### 3.1 Identificación Única de Evento
- Cada evento pertenece a un **Tipo de Evento** maestro (ej. *Enganche*, *Eslabón*, *Jornada Eslabón*, *Senda*, *Jornada Senda*, *Retiro Mariano*).
- **Regla de Unicidad:** La combinación de **`Tipo de Evento` + `Número de Evento`** debe ser **única** en todo el sistema (ej. No pueden existir dos eventos registrados como *"Eslabón Nº 15"*).

### 3.2 Coherencia de Fechas y Estado
- La fecha de finalización (`fecha_fin`) debe ser **igual o posterior** a la fecha de inicio (`fecha_inicio`).
- Un evento posee un estado lógico `activo` (booleano).

### 3.3 Fotografías del Evento
- Permite subir hasta dos imágenes adjuntas: `foto_evento` (foto general/patrulla) y `foto_cocina` (foto del equipo de cocina), con un tamaño máximo de 2048 KB por imagen.
- **Limpieza de Archivos:** Al reemplazar una foto o eliminar un evento, los archivos físicos almacenados en el disco son eliminados automáticamente para optimizar el almacenamiento.

---

## 🤝 4. Reglas de Negocio de Participaciones (Circulista ↔ Evento)

### 4.1 Unicidad por Evento
- Un circulista **solo puede estar registrado una única vez en un mismo evento** (la dupla `circulista_id` + `evento_id` en la tabla `participaciones` es única).

### 4.2 Asignación de Rol
- Todo participante en un evento debe tener asignado obligatoriamente un **Rol** de la tabla maestra de roles (ej. *Circulista*, *Peregrino*, *Rector*, *Vice Rector*, *Asistente*, *Jefe Cocina*, *Cocinero*, *Integrante de Cocina*, *Asesor*, *Servidor*, *Ganchista*, *Mensajero*, etc.).
- Opcionalmente se puede especificar la patrulla o `grupo` al que pertenece dentro del evento.

### 4.3 🛑 Regla Fundamental del Movimiento: Vivencia Única de Eslabón
- **Restricción de Negocio:** Si el evento es de tipo **"Eslabón"** (o *"Eslabon"*) y el rol que se intenta asignar es **"Circulista"**:
  - El sistema **bloquea la inscripción** si la persona ya ha participado previamente como *"Circulista"* en algún otro evento de tipo *"Eslabón"*.
  - **Fundamento espiritual/metodológico:** El retiro de Eslabón como vivencia inicial/circulista se realiza una sola vez en la vida dentro del movimiento. En eventos posteriores, la persona podrá participar en otros roles (Servidor, Cocina, Rector, etc.), pero nunca repetir el rol de Circulista.

---

## 📥 5. Reglas de Negocio para la Importación Masiva

El sistema cuenta con un módulo especial de importación para cargar listados completos desde planillas de Excel o documentos PDF:

1. **Creación o Selección de Evento:** Permite vincular la lista a un evento existente o crear un nuevo evento en el mismo paso cumpliendo las reglas de unicidad (`Tipo + Número`).
2. **Coincidencia e Indexación en Memoria:**
   - Para evitar duplicar personas durante una importación masiva, el sistema carga en memoria a los circulistas activos e indexa por dos claves:
     a) Clave por Nombre: `normalizar(apellido)|normalizar(nombre)`.
     b) Clave por Teléfono: Últimos 7 dígitos del número celular o fijo.
   - Si la persona importada coincide en nombre/apellido o teléfono con un circulista existente, el sistema **lo reutiliza automáticamente y asocia su ID existente**.
   - Si la persona no existe, la crea automáticamente como un nuevo `Circulista` activo.
3. **Transaccionalidad (Todo o Nada):** Toda la importación masiva se ejecuta dentro de una transacción de base de datos (`DB::beginTransaction()`). Si ocurre algún error en cualquiera de los registros, se revierte toda la operación para garantizar la integridad de los datos.

---

## 🖨️ 6. Reglas de Negocio para la Generación de Circulares Imprimibles

El sistema genera automáticamente dos tipos de documentos oficiales/imprimibles ordenados jerárquicamente:

### 6.1 Circular de Retiro (Nómina General del Retiro)
Clasifica y distribuye automáticamente a los participantes del evento en el siguiente orden jerárquico:
1. **Asesores:** Participantes con rol *Asesor* o *Vice Asesor*.
2. **Rectores:** Participantes con rol *Rector*.
3. **Vice Rectores:** Participantes con rol *Vice Rector*.
4. **Grupos / Patrullas:** Resto de circulistas y servidores organizados y agrupados por su campo `grupo` (excluyendo automáticamente al equipo de cocina).

### 6.2 Circular de Cocina (Nómina de Cocina)
Filtra y organiza exclusivamente al personal de servicio de cocina:
1. **Jefe de Cocina:** Participantes con rol *Jefe Cocina* (o que contengan 'Jefe' en su rol).
2. **Cocinero:** Participantes con rol *Cocinero*.
3. **Integrantes de Cocina:** Participantes con rol *Integrante de Cocina*.

---

## 🔍 7. Reglas de Negocio para Búsqueda Avanzada

- Permite realizar búsquedas cruzadas en el historial global de participaciones.
- Los criterios de filtrado incluyen:
  - Filtrar por **Tipo de Evento** específico.
  - Filtrar por **Rol desempeñado** (ej. buscar todas las personas que alguna vez fueron "Rector" o "Jefe Cocina").
- Los resultados se presentan ordenados cronológicamente desde la participación más reciente.

---

*Documento actualizado al estado actual del desarrollo del proyecto `circulistas-app3.0`.*
