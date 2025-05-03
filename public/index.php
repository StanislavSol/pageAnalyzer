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
use App\Url;
use App\CheckDAO;
use App\Check;
use App\NormalizationAndValidationURL;

const INDEX_FIRST_ERROR = 0;

session_start();

$container = new Container();
$container->set('renderer', function () {
        return new \Slim\Views\PhpRenderer(__DIR__ . '/../templates');
});
$container->set('flash', function () {
        return new \Slim\Flash\Messages();
});

$app = AppFactory::createFromContainer($container);
$app->addErrorMiddleware(true, true, true);

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();
$databaseUrl = parse_url($_ENV['DATABASE_URL']);

$app->get('/', function ($request, $response) {
    $params = [
        'value' => '',
        'error' => ''
    ];
    return $this->get('renderer')->render($response, 'index.phtml', $params);
})->setName('index');

$app->get('/url/{id}', function ($request, $response, array $args) use ($databaseUrl) {
    $id = (integer) $args['id'];
    $pdo = Connection::get()->connect($databaseUrl);
    $urlDao = new UrlDAO($pdo);
    $checkDao = new CheckDAO($pdo);
    $url = $urlDao->find($id);
    $checks = $checkDao->find($id);
    var_dump($checks);

    $messages = $this->get('flash')->getMessages();
    $params = [
        'url' => $url,
        'messages' => $messages,
        'checks' => $checks
    ];
    return $this->get('renderer')->render($response, 'url.phtml', $params);
})->setName('url');

$app->get('/urls', function ($request, $response, array $args) use ($databaseUrl) {
    $pdo = Connection::get()->connect($databaseUrl);
    $dao = new UrlDAO($pdo);
    $urls = $dao->getAllUrl();
    $params = [
        'urls' => $urls,
    ];
    return $this->get('renderer')->render($response, 'urls.phtml', $params);
})->setName('urls');

$router = $app->getRouteCollector()->getRouteParser();

$app->post('/urls', function ($request, $response) use ($router, $databaseUrl) {

    $gotUrl = $request->getParsedBodyParam('url')['name'];
    $parsedUrl = new NormalizationAndValidationURL($gotUrl);
    $errors = $parsedUrl->getErrors();

    if (count($errors) === 0) {
        $normalUrl = $parsedUrl->getUnparseUrl();
        $newUrl = new Url($normalUrl);
        $pdo = Connection::get()->connect($databaseUrl);
        $dao = new UrlDAO($pdo);
        $dao->save($newUrl);
        if ($dao->isSaveUrl) {
            $this->get('flash')->addMessage('success', 'Страница успешно добавлена');
        } else {
            $this->get('flash')->addMessage('success', 'Страница уже существует');
        }

        $id = $newUrl->getId();
        $url = $router->urlFor('url', ['id' => $id]);
        return $response->withRedirect($url);
    }
    $params = [
        'value' => $gotUrl,
        'error' => $errors['URL'][INDEX_FIRST_ERROR]
    ];

    $response = $response->withStatus(422);
    return $this->get('renderer')->render($response, 'index.phtml', $params);
});

$app->post('/urls/{id}/checks', function ($request, $response, array $args) use ($router, $databaseUrl) {

    $urlId = $args['id'];
    $newCheck = new Check($urlId);
    $pdo = Connection::get()->connect($databaseUrl);
    $dao = new CheckDAO($pdo);
    $dao->save($newCheck);
    $this->get('flash')->addMessage('success', 'Страница успешно проверена');
    var_dump();

    $id = $newCheck->getUrlId();
    $url = $router->urlFor('url', ['id' => $id]);
    return $response->withRedirect($url);

});

$app->run();
