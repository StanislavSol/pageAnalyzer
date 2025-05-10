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
use App\HtmlCheck;

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

$app->get('/urls/{id}', function ($request, $response, array $args) use ($databaseUrl) {

    $id = (integer) $args['id'];
    $pdo = Connection::get()->connect($databaseUrl);
    $urlDao = new UrlDAO($pdo);
    $checkDao = new CheckDAO($pdo);
    $url = $urlDao->find($id);
    $checks = $checkDao->getChecks($id);

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
    $daoUrl = new UrlDAO($pdo);
    $urls = $daoUrl->getAllUrl();
    $daoCheck = new CheckDAO($pdo);
    $infoUrls = [];
    foreach ($urls as $url) {
        $info["url"] = $url;
        $info["check"] = $daoCheck->findLastCheck($url->getId());
        $infoUrls[] = $info;
    }
    $params = [
        'urls' => $infoUrls,
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
    $pdo = Connection::get()->connect($databaseUrl);

    $urlDao = new UrlDAO($pdo);
    $url = $urlDao->find($urlId);

    $htmlCheck = new HtmlCheck($url->getUrlName());
    [$statusCode, $message] = $htmlCheck->getStatusCode();

    if (!is_null($statusCode)) {
        $newCheck = new Check($urlId);
        $newCheck->setStatusCode($statusCode);

        $dao = new CheckDAO($pdo);
        $dao->save($newCheck);
    }

    $this->get('flash')->addMessage(...$message);

    $url = $router->urlFor('url', ['id' => $urlId]);
    return $response->withRedirect($url);

});

$app->run();
