<?php

$autoloadPath1 = __DIR__ . '/../../../autoload.php';
$autoloadPath2 = __DIR__ . '/../vendor/autoload.php';
if (file_exists($autoloadPath1)) {
        require_once $autoloadPath1;
} else {
        require_once $autoloadPath2;
}

use Slim\Factory\AppFactory;
use DI\Container;
use App\Connection;
use App\UrlDAO;

$container = new Container();
$container->set('renderer', function () {
        return new \Slim\Views\PhpRenderer(__DIR__ . '/../templates');
});

$app = AppFactory::createFromContainer($container);
$app->addErrorMiddleware(true, true, true);

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();
$databaseUrl = parse_url($_ENV['DATABASE_URL']);

/*try {
    Connection::get()->connect($databaseUrl);
    echo 'A connection to the PostgreSQL database sever has been established successfully.';
} catch (\PDOException $e) {
    echo $e->getMessage();
}*/

//$conn = Connection::get()->connect($databaseUrl);

$app->get('/', function ($request, $response, $args) {
    $params = [
        'value' = $args['value'],
        'errors' => []
    ];
    return $this->get('renderer')->render($response, 'index.phtml', $params)->setName('index');
});

$app->get('/url/{id}', function ($request, $response, array $args) {
    $id = $args['id'];
    $pdo = Connection::get()->connect($databaseUrl);
    $dao = new UrlDAO($pdo);
    $url = $dao-find($id);
    $params = [
        'url' => $url,
        'errors' => []
    ];
    return $this->get('renderer')->render($response, 'url.phtml', $params)->setName('url');
});

$app->get('/urls', function ($request, $response, $args) use ($databaseUrl) {
    $pdo = Connection::get()->connect($databaseUrl);
    $urls = new UrlDAO($pdo)->getAllUrl();
    $params = [
        'urls' => $urls,
        'errors' => []
    ];
    return $this->get('renderer')->render($response, 'urls.phtml', $params)->setName('urls');
});


$app->post('/urls/{id}', )

$app->run();
