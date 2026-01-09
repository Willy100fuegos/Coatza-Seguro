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
**William Velázquez Valenzuela**
*Director de Tecnologías | Pixmedia Agency*
