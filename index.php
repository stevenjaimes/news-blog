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
</head>
<body>
    <div class="container">
        <h1 class="my-4">Blog de Noticias</h1>
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

        <nav aria-label="Page navigation">
            <ul class="pagination">
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <li class="page-item <?php echo ($page == $i) ? 'active' : ''; ?>">
                        <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                    </li>
                <?php endfor; ?>
            </ul>
        </nav>
    </div>
</body>
</html>