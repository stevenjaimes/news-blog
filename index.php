<?php
require __DIR__ . '/vendor/autoload.php';

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Verifica si la variable está cargada
if (getenv('NEWS_API_KEY')) {
    echo "NEWS_API_KEY está cargada: " . getenv('NEWS_API_KEY');
} else {
    echo "NEWS_API_KEY NO está cargada.";
}

$newsApiKey = getenv('NEWS_API_KEY');
$newsApiUrl = "https://newsapi.org/v2/top-headlines?country=us&apiKey=$newsApiKey";


$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $newsApiUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);


curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'User-Agent: MyNewsApp/1.0'
]);

$newsResponse = curl_exec($ch);

if (curl_errno($ch)) {
    die('Error al obtener noticias de la API: ' . curl_error($ch));
}

curl_close($ch);

$newsData = json_decode($newsResponse, true);

if ($newsData['status'] !== 'ok') {
    die('Error en la respuesta de la API: ' . $newsData['message']);
}


$authors = [];
for ($i = 0; $i < 10; $i++) {
    $randomUserResponse = file_get_contents('https://randomuser.me/api/');
    $randomUserData = json_decode($randomUserResponse, true);
    $authors[] = $randomUserData['results'][0]['name']['first'] . ' ' . $randomUserData['results'][0]['name']['last'];
}


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
                    <h2 class="card-title"><?php echo $article['title']; ?></h2>
                    <p class="card-text"><?php echo $article['description']; ?></p>
                    <p class="text-muted">Autor: <?php echo $authors[$index]; ?></p>
                    <a href="<?php echo $article['url']; ?>" class="btn btn-primary">Leer más</a>
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