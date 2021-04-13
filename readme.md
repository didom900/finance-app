<p align="center">
<b><a href="#requerimientos">Requerimientos</a></b>
|
<b><a href="#documentación">Documentación</a></b>
|
<b><a href="#instalación">Instalación</a></b>


# Requerimientos

- **PHP - Supported Versions**: >= 5.6
- **Webserver**: Apache 2.2
- **Database**: PostgreSQL 9.1
- **SO**: Ubuntu 12.04
- **Composer:** Como Gestor de Librerias de Php
- **Node JS:** Necesario para instalacion de npm y bower
- **uuid-ossp:** Necesaria para la creacion de campos UUID, la extension esta disponible pero no instalada por eso solo debemos ejecutar la consulta:  
```
CREATE EXTENSION "uuid-ossp";
```
# Documentación

- Descargamos el siguiente archivo en la raiz de finanzas:
```
$ curl -O http://get.sensiolabs.org/sami.phar
```

- Verificamos que se descargo bien ejecutanto el siguiente comando sin ningun argumento:
```
$ php sami.phar
```

- Ahora generaremos la documentacion con el siguiente comando:
```
$ php sami.phar update sami.php -v
```

- La documentacion se generara en la carpeta:
```
|- finanzas
|    |- docs
|    |    |- finanzas
|    |    |    |- 1.0
|    |    |    |    |-index.html
````

# Instalación


- Ingresamos a nuestro repositorio y copiamos el archivo **.env.example** a **.env**
```
cp .env.example .env
```

- Ahora editaremos el archivo configuracion.php y lo editaremos con las variables necesarias de nuestro entorno:
```
nano .env
```

- El listado de las dependencias de php necesarias para nuestro proyecto se encuentran en el archivo ***composer.json***, para la instalacion solo debemos ejecutar el comando:
```
composer install
```

- El listado de las dependencias de Javascript necesarias para el Backend de nuestro proyecto se encuentran en el archivo ***package.json***, para la instalacion solo debemos ejecutar el comando:
```
npm install
```

- Agregar permisos a la carpeta Storage
```
chmod -R 777 storage/
```

# Clonar Subrepositorios

- Dentro del repositorio de Finanzas ejecutamos los siguientes comandos:

- Clonar Módulos

- Clonar Traits
