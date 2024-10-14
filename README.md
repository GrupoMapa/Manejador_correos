
Instrucciones de uso

Este proyecto es una base para futuras aplicaciones de Grupomapa. Contiene un proyecto de Laravel con gestión de usuarios y Docker incluido.

Requisitos

Docker Engine instalado y en ejecución
PostgreSQL instalado y en ejecución
Instalación

Clona el repositorio:
git clone https://github.com/grupomapa/proyecto-base.git

Entra en la carpeta del proyecto:
cd proyecto-base

Construye la imagen de Docker:
docker image build --tag laravel_web -f Dockerfile .

Inicia Docker Compose:
docker compose up

Configuración
Una vez que la aplicación esté en marcha, accede a ella en tu navegador web.

Abre una terminal y ejecuta el siguiente comando para ver los contenedores en ejecución:

docker ps
Identifica el ID del contenedor de la aplicación web:
CONTAINER ID | IMAGE | COMMAND | STATUS | PORTS
---------- | -------- | -------- | -------- | --------
cf6cb6664668 | laravel_web | "php artisan serve" | Up | 0.0.0.0:8080->8080/tcp
Conecta a un shell del contenedor de la aplicación web:
docker exec -ti cf6cb6664668 bash
Instala las dependencias de Laravel:
composer i
Modifica el archivo .env para configurar la base de datos:
DB_CONNECTION=pgsql
DB_HOST=192.168.11.233
DB_PORT=5432
DB_DATABASE=lara_test
DB_USERNAME=postgres
DB_PASSWORD=259644asdfghjkl
Guarda los cambios en el archivo .env.

Reinicia la aplicación web:

docker restart cf6cb6664668
Ejecución

Una vez que la aplicación esté configurada, puedes acceder a ella en tu navegador web en la dirección http://localhost:8000.

Ayuda

Para obtener más ayuda, visita la documentación de Laravel: https://laravel.com/docs/8.x.



#### comandos utiles para la instalacion:
##### INICIO
    Nota: este docker no funciona a la primera es necesario ejecutar los comandos de postgres del dockerfile para que funcione y luego reiniciarlo

    Nota: si se da el fallo de que los archivos estaticos no se pueden acceder sera necesario customizar el archivo 000-default.conf del docker:
    <VirtualHost *:80>
        # The ServerName directive sets the request scheme, hostname and port that
        # the server uses to identify itself. This is used when creating
        # redirection URLs. In the context of virtual hosts, the ServerName
        # specifies what hostname must appear in the request's Host: header to
        # match this virtual host. For the default virtual host (this file) this
        # value is not decisive as it is used as a last resort host regardless.
        # However, you must set it for any further virtual host explicitly.
        #ServerName www.example.com
        DirectoryIndex index.php index.html
        ServerName central.bomba.com
        ServerAdmin webmaster@localhost
        DocumentRoot /var/www/html/public

        # Available loglevels: trace8, ..., trace1, debug, info, notice, warn,
        # error, crit, alert, emerg.
        # It is also possible to configure the loglevel for particular
        # modules, e.g.
        #LogLevel info ssl:warn
        <Directory /var/www/html/public>
                Options Indexes FollowSymLinks
                AllowOverride All
                Require all granted
                AddHandler application/x-httpd-php .php
        </Directory>
        ErrorLog /var/log/apache2/error.log
        CustomLog /var/log/apache2/access.log combined

        # For most configuration files from conf-available/, which are
        # enabled or disabled at a global level, it is possible to
        # include a line for only one particular virtual host. For example the
        # following line enables the CGI configuration for this host only
        # after it has been globally disabled with "a2disconf".
        #Include conf-available/serve-cgi-bin.conf
    </VirtualHost>

    y el archivo .htaccess de la carpeta public:

    <IfModule mod_rewrite.c>
        <IfModule mod_negotiation.c>
            Options -MultiViews -Indexes
        </IfModule>

        RewriteEngine On

        # Handle Authorization Header
        RewriteCond %{HTTP:Authorization} .
        RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]

        # Redirect Trailing Slashes If Not A Folder...
        RewriteCond %{REQUEST_FILENAME} !-d
        RewriteCond %{REQUEST_URI} (.+)/$
        RewriteRule ^ %1 [L,R=301]

        # Send Requests To Front Controller...
        RewriteCond %{REQUEST_FILENAME} !-d
        RewriteCond %{REQUEST_FILENAME} !-f
        RewriteRule ^ index.php [L]
    </IfModule>


###### editar el dominio del proyecto
'''
a2dissite 000-default.conf ; a2ensite 000-default.conf ; service apache2 reload
'''
###### problema en el rewrite
ejecutar:
'''
a2enmod rewrite
'''

###### crear un usuario en laravel desde consola
$user = new User();
$user->name = 'jeni';
$user->email = 'jenifferpineda@almacenesbomba.com';
$user->password = bcrypt('092120');
$user->save();
 