# Proyecto tesis
Desarrollo de plataforma web para venta de boletos electrónicos con generación de códigos QR y facturación electrónica, desarrollado con **Laravel** y con integración para **facturación electrónica** conforme a la normativa del SRI (Ecuador).

---

## Características principales

- Plataforma web desarrollada en **Laravel 12**
- Integración con sistemas de **facturación electrónica (SRI)**
- Enfoque en la gestión, atención y servicios
- Integraciones externas:
  - **Resend** (envío de correos electrónicos)
  - **PayPhone** (procesamiento de pagos)

---

## Requisitos del sistema

- PHP >= 8.2
- Composer
- Laravel 12
- Node.js >= 20
- MySQL
- Docker (opcional, recomendado para desarrollo)

---

## Instalación local (sin Docker)

1. **Clona el repositorio:**
    ```bash
    git clone https://github.com/jquezadac3-create/Proyecto_tesis.git
    cd Proyecto_tesis
    ```

2. **Instala las dependencias:**
    ```bash
    composer install
    npm install
    ```

3. **Copia y configura el archivo de entorno:**
    ```bash
    cp .env.example .env
    ```

4. **Configura la base de datos y ejecuta las migraciones (solo si es necesario):**
    ```bash
    php artisan migrate
    ```

5. **Genera la clave de la aplicación:**
    ```bash
    php artisan key:generate
    ```

6. **Levanta el servidor de desarrollo:**
    ```bash
    php artisan serve
    ```

---

## Instalación usando Docker (recomendado)

> Asegúrate de **no usar el usuario `root`**. Tu usuario debe tener permisos en el grupo `docker`.

1. Clonar el repositorio

    ```bash
    # Con HTTPS
    git clone https://github.com/jquezadac3-create/Proyecto_tesis.git

    # O con SSH
    git clone git@github.com:jquezadac3-create/Proyecto_tesis.git

    cd Proyecto_tesis
    ```

2. Configuramos el entorno:
    ```bash
    cp .env.example .env
    ```
    Obtenemos el usuario e ID (si no los conoces):
    ```bash
    whoami # nombre del usuario

    id -a # ID del usuario
    ```
    Editamos el archivo `.env` o usa VSC:
    ```bash
    nano .env           # Editor en linea de comandos

    code .              # Instalará y abrirá Visual Studio Code
    ```
    Completa los siguientes valores:
    - `USER` y `ID` (datos recientemente obtenidos)
    - Credenciales para la base de datos
    - API Keys para:
        - Resend
        - Payphone
    - `APP_PREVIOUS_KEYS` (descomenta la variable y agrega las claves anteriores si aplica)
    
    Guarda los cambios con `CTRL + X`, luego `Y` y `Enter` (En caso de que sea linea de comandos).

3. Levantar los contenedores:
    ```bash
    docker compose -f docker-compose.dev.yml up --build -d
    ```
    > `--build`: Fuerza la reconstrucción de imágenes.

    > `-d`: Ejecuta los contenedores en segundo plano.
    
    Puedes elegir entre:

    > `docker-compose.dev.yml` (entorno de desarrollo).

    > `docker-compose.test.yml` (preparado para producción con más contenedores).

4. Verificar estado de los contenedores:
    ```bash
    docker ps           # Ver contenedores activos
    docker ps -a        # Ver todos, incluidos detenidos
    ```
    #### Ver logs de los servicios:
    ```bash
    docker logs nginx           # Revisa si el servidor web está corriendo correctamente
    docker logs laravel         # Verifica instalación y arranque de Laravel
    ```
5. Acceder al contenedor de Laravel:

    ```bash
    docker exec -it laravel bash
    ```
    Dentro del contenedor, generamos la key de la app:
    ```bash
    php artisan key:generate
    ```
    - Si las dependencias están correctamente instaladas el servicio se ejecutará correctamente.
6. Instalar dependencias de node:
    ```bash
    npm i
    ```
7. Ejecutamos servidor Vite:
    ```bash
    npm run dev
    ```
8. Accede a la aplicación
Accede desde tu navegador en:
    ```bash
    http://localhost
    ```

## Integración con Facturación Electrónica

El sistema permitirá emitir y gestionar **comprobantes electrónicos** conforme a la normativa vigente del **SRI (Ecuador)** conectando los **Web Services** de esta entidad.
