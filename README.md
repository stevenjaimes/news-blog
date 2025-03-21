# Blog de Noticias

Este proyecto es una aplicación web en PHP que muestra las últimas noticias utilizando la API de NewsAPI.

## Requisitos

- PHP 7.4 o superior
- Composer
- Cuenta en [NewsAPI](https://newsapi.org/) para obtener una clave de API

## Instalación

1. Clona este repositorio en tu entorno local:

   ```sh
   git clone https://github.com/stevenjaimes/news-blog.git
   cd news-blog
   ```

2. Instala las dependencias con Composer:

   ```sh
   composer install
   ```

3. Crea un archivo `.env` en la raíz del proyecto y agrega tu clave de API de NewsAPI:

   ```sh
   NEWS_API_KEY=tu_clave_aqui
   ```

4. Inicia un servidor local con PHP:

   ```sh
   php -S localhost:8000
   ```

5. Accede a la aplicación desde tu navegador:

   ```
   http://localhost:8000
   ```

## Características

- Obtiene noticias de la API de NewsAPI
- Genera autores aleatorios para las noticias
- Implementa paginación para navegar entre las noticias
- Usa Bootstrap para un diseño moderno y responsivo

## Tecnologías utilizadas

- PHP
- cURL
- Bootstrap 5
- Dotenv para la gestión de variables de entorno

## Licencia

Este proyecto está bajo la licencia MIT. Puedes ver el archivo `LICENSE` para más detalles.
