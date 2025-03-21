<?php
require __DIR__ . '/vendor/autoload.php';

use Dotenv\Dotenv;

// Cargar variables de entorno solo en desarrollo
if (file_exists(__DIR__ . '/.env')) {
    $dotenv = Dotenv::createImmutable(__DIR__);
    $dotenv->load();
}

// Acceder a la variable de entorno NEWS_API_KEY
$newsApiKey = $_ENV['NEWS_API_KEY'] ?? $_SERVER['NEWS_API_KEY'] ?? null;

if (!$newsApiKey) {
    die("Error: La variable de entorno NEWS_API_KEY no está configurada.");
}

// Construir la URL de la API
$newsApiUrl = "https://newsapi.org/v2/top-headlines?country=us&apiKey=$newsApiKey";

// Iniciar una solicitud cURL
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $newsApiUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

// Configurar encabezados personalizados
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'User-Agent: MyNewsApp/1.0'
]);

// Ejecutar la solicitud y obtener la respuesta
$newsResponse = curl_exec($ch);

if (curl_errno($ch)) {
    die('Error al obtener noticias de la API: ' . curl_error($ch));
}

curl_close($ch);

// Decodificar la respuesta JSON
$newsData = json_decode($newsResponse, true);

if ($newsData['status'] !== 'ok') {
    die('Error en la respuesta de la API: ' . ($newsData['message'] ?? 'Respuesta desconocida'));
}

// Generar autores aleatorios
$authors = [];
for ($i = 0; $i < 10; $i++) {
    $randomUserResponse = file_get_contents('https://randomuser.me/api/');
    $randomUserData = json_decode($randomUserResponse, true);
    $authors[] = $randomUserData['results'][0]['name']['first'] . ' ' . $randomUserData['results'][0]['name']['last'];
}

// Paginación
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$perPage = 10;
$totalNews = count($newsData['articles']);
$totalPages = ceil($totalNews / $perPage);
$offset = ($page - 1) * $perPage;
$articles = array_slice($newsData['articles'], $offset, $perPage);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog de Noticias</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f8f9fa;
        }
        .navbar {
            background-color: #007bff !important;
        }
        .navbar-brand {
            color: #fff !important;
            font-weight: bold;
        }
        .card {
            border: none;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s;
        }
        .card:hover {
            transform: translateY(-5px);
        }
        .card-title {
            font-size: 1.5rem;
            font-weight: bold;
        }
        .card-text {
            color: #6c757d;
        }
        .btn-primary {
            background-color: #007bff;
            border: none;
        }
        .btn-primary:hover {
            background-color: #0056b3;
        }
        .footer {
            background-color: #007bff;
            color: #fff;
            padding: 20px 0;
            margin-top: 40px;
        }
    </style>
</head>
<body>
    <!-- Barra de navegación -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="#">Blog de Noticias</a>
        </div>
    </nav>

    <!-- Contenido principal -->
    <div class="container my-5">
        <h1 class="text-center mb-4">Últimas Noticias</h1>
        <?php foreach ($articles as $index => $article): ?>
            <div class="card mb-4">
                <div class="card-body">
                    <h2 class="card-title"><?php echo htmlspecialchars($article['title']); ?></h2>
                    <p class="card-text"><?php echo htmlspecialchars($article['description']); ?></p>
                    <p class="text-muted">Autor: <?php echo htmlspecialchars($authors[$index] ?? 'Desconocido'); ?></p>
                    <a href="<?php echo htmlspecialchars($article['url']); ?>" class="btn btn-primary">Leer más</a>
                </div>
            </div>
        <?php endforeach; ?>

        <!-- Paginación -->
        <nav aria-label="Page navigation">
            <ul class="pagination justify-content-center">
                <?php if ($page > 1): ?>
                    <li class="page-item">
                        <a class="page-link" href="?page=<?php echo $page - 1; ?>">Anterior</a>
                    </li>
                <?php endif; ?>
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <li class="page-item <?php echo ($page == $i) ? 'active' : ''; ?>">
                        <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                    </li>
                <?php endfor; ?>
                <?php if ($page < $totalPages): ?>
                    <li class="page-item">
                        <a class="page-link" href="?page=<?php echo $page + 1; ?>">Siguiente</a>
                    </li>
                <?php endif; ?>
            </ul>
        </nav>
    </div>

    <!-- Pie de página -->
    <footer class="footer text-center">
        <div class="container">
            <p>&copy; <?php echo date('Y'); ?> Blog de Noticias. Todos los derechos reservados.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>