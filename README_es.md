<!-- Language Navigation -->
<div align="right">
  <a href="./README.md">English</a> | <a href="./README_fr.md">Français</a> | <b><a href="./README_es.md">Español</a></b>
</div>

# Les Pages Parfumées - Sitio E-commerce en PHP

[![License: MIT](https://img.shields.io/badge/Licencia-MIT-blue.svg)](https://opensource.org/licenses/MIT)
![Language](https://img.shields.io/badge/Lenguaje-PHP-8892BF)
![Database](https://img.shields.io/badge/Base_de_Datos-MySQL-4479A1)
![Tech](https://img.shields.io/badge/Tecnología-Docker-2496ED)

"Les Pages Parfumées" es un sitio web de e-commerce completamente funcional, desarrollado desde cero como proyecto universitario. Simula una tienda en línea ficticia que vende libros de segunda mano, velas artesanales y cajas de regalo. La aplicación está desarrollada en **PHP nativo**, siguiendo una estructura modular, y ofrece dos opciones de configuración: un entorno de servidor local tradicional (WampServer, MAMP) o una configuración en contenedores con **Docker**.

![Captura de pantalla de la página de inicio](img/homepage.png)

## Tabla de Contenidos

- [Sobre el Proyecto](#sobre-el-proyecto)
- [Características Principales](#características-principales)
- [Stack Tecnológico](#stack-tecnológico)
- [Cómo Empezar](#cómo-empezar)
- [Estructura del Proyecto](#estructura-del-proyecto)
- [Licencia](#licencia)
- [Contacto](#contacto)

## Sobre el Proyecto

Este proyecto fue diseñado para demostrar la competencia en el desarrollo web backend con PHP puro, sin depender de un framework como Laravel o Symfony. El objetivo era construir una plataforma de e-commerce completa, segura y funcional, que abarcara funcionalidades tanto para el cliente como para el administrador.

## Características Principales

### 🛍️ Funcionalidades para el Cliente
*   **Catálogo de Productos:** Navegación por categorías con filtros avanzados (género, precio, etc.) y ordenación.
*   **Páginas de Producto Detalladas:** Vista de detalles, imágenes, descripciones y reseñas de clientes.
*   **Reseñas de Clientes:** Los usuarios registrados pueden calificar y comentar productos.
*   **Carrito de Compras:** Añadir, actualizar y eliminar artículos.
*   **Proceso de Pago Seguro:** Flujo de compra simulado pero completo.
*   **Autenticación Completa:** Registro, inicio de sesión, cierre de sesión y restablecimiento de contraseña seguros.
*   **Panel de Cuenta de Usuario:** Gestión de información, direcciones, historial de pedidos y devoluciones.

### ⚙️ Panel de Administración
*   **Inicio de Sesión Seguro para Administradores.**
*   **Gestión de Productos (CRUD):** Creación, Lectura, Actualización y Borrado para todos los productos.
*   **Registro de Auditoría:** Una tabla `audit_logs` registra los cambios importantes realizados en el backend.

## Stack Tecnológico

*   **Backend:** **PHP 8.2+** (enfoque procedural y funcional)
*   **Base de datos:** **MySQL**
*   **Frontend:** HTML5, CSS3, JavaScript nativo
*   **Entornos de desarrollo:** WampServer / MAMP / XAMPP, o **Docker**.

## Cómo Empezar

Puedes ejecutar este proyecto usando un servidor local tradicional o con Docker.

### Opción 1: Usar un Servidor Local (WampServer, MAMP, XAMPP)

Este es el método recomendado si estás familiarizado con entornos de desarrollo PHP locales.

1.  **Prerrequisitos:**
    *   Un entorno como [WampServer](https://www.wampserver.com/), MAMP o XAMPP instalado.
    *   Acceso a phpMyAdmin u otro cliente MySQL.

2.  **Clonar el Repositorio:**
    ```bash
    git clone https://github.com/Alespfer/alespfer-pages_parfumees_pise_2025.git
    ```

3.  **Colocar los Archivos:**
    Mueve la carpeta clonada al directorio `www` de tu instalación de WampServer (o `htdocs` para XAMPP/MAMP).

4.  **Configuración de la Base de Datos:**
    *   Inicia tu servidor local y abre **phpMyAdmin**.
    *   Crea una nueva base de datos llamada `ecommerce`.
    *   Selecciona la base de datos `ecommerce` recién creada.
    *   Ve a la pestaña "Importar".
    *   Haz clic en "Seleccionar archivo" y elige el archivo `docs/database.sql` de este proyecto.
    *   Haz clic en "Ejecutar" en la parte inferior. Esto creará todas las tablas y las llenará con datos de ejemplo.

5.  **Configuración:**
    *   Abre el archivo `parametrage/param.php`.
    *   Asegúrate de que las credenciales de la base de datos coincidan con tu configuración local (el valor por defecto para WampServer suele ser `DB_USER` = 'root', `DB_PASSWORD` = '').

6.  **Acceder a la Aplicación:**
    Abre tu navegador y navega a `http://localhost/alespfer-pages_parfumees_pise_2025/`.

### Opción 2: Usar Docker

Este método utiliza el `Dockerfile` para crear un entorno PHP. **Nota:** Esta configuración solo ejecuta el servidor PHP; necesitas tener un servidor MySQL funcionando en tu máquina local.

1.  **Prerrequisitos:**
    *   [Docker](https://www.docker.com/get-started) instalado y en ejecución.
    *   Un servidor MySQL funcionando en tu máquina (fuera de un contenedor).

2.  **Clonar y Configurar la BDD:**
    *   Clona el repositorio como en la Opción 1.
    *   Sigue el **Paso 4 (Configuración de la Base de Datos)** de la Opción 1 para crear y poblar tu base de datos `ecommerce` en tu servidor MySQL local.

3.  **Configuración para Docker:**
    *   Abre el archivo `parametrage/param.php`.
    *   Debes cambiar `DB_HOST` de `'localhost'` a `'host.docker.internal'`. Este nombre DNS especial permite al contenedor de Docker conectarse a los servicios de tu máquina anfitriona.
    ```php
    // En parametrage/param.php
    define('DB_HOST', 'host.docker.internal'); // Para la configuración con Docker
    ```

4.  **Construir y Ejecutar el Contenedor:**
    En tu terminal, en la raíz del proyecto, ejecuta:
    ```bash
    # Construir la imagen de Docker
    docker build -t pages-parfumees .

    # Ejecutar el contenedor
    docker run -p 8000:10000 pages-parfumees
    ```

5.  **Acceder a la Aplicación:**
    Abre tu navegador y navega a `http://localhost:8000`.

## Estructura del Proyecto

*   `*.php`: Archivos de Controlador/Vista.
*   `/parametrage/`: Archivo de configuración global.
*   `/fonction/`: Lógica de negocio y funciones de BDD.
*   `/partials/`: Componentes reutilizables (header, footer).
*   `/styles/`: Hojas de estilo CSS.
*   `/docs/`: Contiene el volcado SQL `database.sql`.
*   `Dockerfile`: Define el entorno de la aplicación.

## Licencia

Distribuido bajo la Licencia MIT. Ver el archivo `LICENSE`.

## Contacto

Alberto Esperon - [LinkedIn](https://www.linkedin.com/in/alberto-espfer) - [Perfil de GitHub](https://github.com/Alespfer)
