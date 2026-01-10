# CoatzaSeguro - Plataforma de Vinculación Ciudadana C5 🛡️

> **Sistema Integral de Gestión de Incidencias y Denuncia Anónima.**
> *Puente digital entre la ciudadanía de Coatzacoalcos y las fuerzas de seguridad pública.*

---

## 🎯 Propósito del Sistema

**CoatzaSeguro** soluciona la brecha de comunicación entre el ciudadano y la autoridad. Permite reportar delitos o incidentes en tiempo real desde dispositivos móviles, generando un **Folio Único de Seguimiento** que garantiza la atención sin exponer la identidad del denunciante, mientras dota al C5 de un tablero de comando para la gestión táctica.

---

## 🔄 Flujo Operativo (User Journey)

### 1. Reporte Ciudadano (Interfaz Móvil)
El ciudadano accede a una Web App optimizada para móviles. Puede categorizar el incidente (Robo, Violencia, Servicios), adjuntar evidencia fotográfica y geolocalización. El diseño prioriza la velocidad y la facilidad de uso bajo estrés.
![Reporte Móvil](http://imgfz.com/i/UaKBqfn.png)

### 2. Generación de Folio (Tokenización)
Al enviar el reporte, el sistema genera un **Folio Alfanumérico Único** (Ej. *CZT-2025-A1B2*). Este folio es la llave maestra para que el ciudadano consulte el estatus de su caso sin necesidad de crear cuentas ni dar correos electrónicos.
![Generación de Folio](http://imgfz.com/i/Y4hpe1N.png)

### 3. Gestión de Autoridad (Seguridad)
El acceso al panel de control está restringido a oficiales y monitoristas validados. Cuenta con seguridad de sesión y roles de usuario.
![Login Admin](http://imgfz.com/i/Vyzh4kf.png)

### 4. Dashboard de Mando C5 (Business Intelligence)
Los monitoristas visualizan los reportes en tiempo real. El tablero incluye:
* **KPIs:** Tickets Abiertos vs Cerrados.
* **Gráficas:** Tipología del delito y mapas de calor.
* **Gestión:** Cambio de estatus y asignación de patrullas.
![Dashboard C5](http://imgfz.com/i/5oJg2yC.png)

### 5. Seguimiento y Chat Bidireccional
Una característica crítica es el **Chat de Seguimiento**. La autoridad puede solicitar más detalles y el ciudadano puede responder o ver la resolución de su caso ingresando su folio.
![Chat de Seguimiento](http://imgfz.com/i/T7J6qDi.png)

---

## 🛠️ Arquitectura Técnica

Sistema desplegado bajo arquitectura LAMP (Linux, Apache, MySQL, PHP) para máxima compatibilidad y robustez.

| Componente | Tecnología | Función |
| :--- | :--- | :--- |
| **Backend** | **PHP 8.2 (Nativo)** | Procesamiento de tickets, manejo de sesiones y lógica de negocio. |
| **Base de Datos** | **MySQL (PDO)** | Almacenamiento relacional de incidentes y chat. |
| **Frontend** | **Tailwind CSS** | Interfaz responsiva y ligera para carga rápida en redes móviles 4G. |
| **Analytics** | **Chart.js** | Visualización de datos estadísticos en el dashboard. |
| **Seguridad** | **Hash/Salting** | Encriptación de contraseñas y sanitización de inputs contra SQL Injection. |

---

## 👨‍💻 Guía de Despliegue (Installation Guide)

Si eres desarrollador y deseas montar este sistema en tu servidor, sigue esta estructura y configuración.

### 1. Estructura de Directorios
Organiza los archivos descargados de la siguiente manera en tu servidor web:

```bash
/public_html
├── admin/                  # Panel Administrativo (Seguro)
│   ├── dashboard.php
│   └── ticket_details.php
│
├── api/                    # Endpoints AJAX
│   ├── chat.php
│   ├── save_ticket.php
│   └── update_status.php
│
├── uploads/                # ¡CREAR MANUALMENTE! (Permisos 755)
│   └── (Aquí se guardarán las evidencias)
│
├── db.php                  # Configuración de Base de Datos
├── index.php               # Home / Formulario Ciudadano
├── login.php               # Acceso Administrativo
├── mailer.php              # Clase de Envío de Correos
└── seguimiento.php         # Portal de consulta por folio

2. Base de Datos (SQL Schema)
Ejecuta este script SQL en tu gestor (phpMyAdmin) para crear las tablas necesarias:

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL, -- Usar password_hash()
  `rol` enum('admin','monitorista') DEFAULT 'monitorista',
  PRIMARY KEY (`id`)
);

CREATE TABLE `tickets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `folio` varchar(20) NOT NULL,
  `titulo` varchar(150) NOT NULL,
  `descripcion` text NOT NULL,
  `categoria` varchar(50) DEFAULT 'Otros',
  `estatus` enum('Abierto','En Proceso','Cerrado') DEFAULT 'Abierto',
  `patrulla_id` varchar(50) DEFAULT NULL,
  `imagen_path` varchar(255) DEFAULT NULL,
  `nombre_ciudadano` varchar(100) DEFAULT 'Anónimo',
  `telefono` varchar(20) DEFAULT NULL,
  `email_contacto` varchar(100) DEFAULT NULL,
  `fecha_creacion` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
);

CREATE TABLE `mensajes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ticket_id` int(11) NOT NULL,
  `remitente` enum('ciudadano','oficial') NOT NULL,
  `mensaje` text NOT NULL,
  `fecha` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
);

3. Configuración Final
Edita los siguientes archivos con tus credenciales reales:

db.php: Ingresa host, usuario, password y nombre de tu BD.

mailer.php: Configura tu servidor SMTP para las notificaciones por correo.

admin/ y api/: Asegúrate de que las rutas relativas (../db.php) sean correctas según tu estructura.

🔒 Nota de Seguridad
El código fuente público ha sido sanitizado. Las credenciales de producción, llaves de API y correos electrónicos reales han sido eliminados.

Desarrollado por: William Velázquez Valenzuela Director de Tecnologías | Pixmedia Agency
