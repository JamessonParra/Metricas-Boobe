# Metricas-Boobe
Archivo de prueba con Laravel + Bootstrap + JS + API.

1. Crear una base de datos:
    - En tu gestor de bases de datos (por ejemplo, phpMyAdmin), crea una nueva base de datos con el nombre broobe.
2. Clonar el repositorio:
    - Utiliza Git para clonar el repositorio del proyecto en tu máquina local.
3. Instalar dependencias y realizar configuraciones:
    - cd broobe
    - composer install
    - php artisan key:generate
    - cp .env.example .env
4. Editar el archivo .env:
    - APP_URL=http://127.0.0.1:8000/
    - DB_HOST = {TU_IP_LOCAL}
    - DB_DATABASE=broobe << Creada anteriormente
    - DB_USERNAME= {TU_USER_MYSQL}
    - DB_PASSWORD= {TU_PASS_MYSQL}
5. Ejecutar migraciones y semillas:
    - php artisan migrate --seed
6. Iniciar el servidor laravel:
    - php artisan serve
