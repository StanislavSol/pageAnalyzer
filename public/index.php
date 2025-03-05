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
use App\Validation;

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

$app->get('/', function ($request, $response, array $args) {
    $params = $args;
    return $this->get('renderer')->render($response, 'index.phtml', $params);
})->setName('index');

$app->get('/url/{id}', function ($request, $response, array $args) use ($databaseUrl) {
    $id = $args['id'];
    $pdo = Connection::get()->connect($databaseUrl);
    $dao = new UrlDAO($pdo);
    $url = $dao->find($id);
    $messages = $this->get('flash')->getMessages();
    $params = [
        'url' => $url,
        'messages' => $messages
    ];
    return $this->get('renderer')->render($response, 'url.phtml', $params);
})->setName('url');

$app->get('/urls', function ($request, $response, array $args) use ($databaseUrl) {
    $pdo = Connection::get()->connect($databaseUrl);
    var_dump(gettype($pdo));
    $dao = new UrlDAO($pdo);
    $urls = $dao->getAllUrl();
    $params = [
        'urls' => $urls,
        'errors' => []
    ];
    return $this->get('renderer')->render($response, 'urls.phtml', $params);
})->setName('urls');

$router = $app->getRouteCollector()->getRouteParser();

$app->post('/urls', function ($request, $response) use ($router, $databaseUrl) {

    $urlData = $request->getParsedBodyParam('url');
    $validator = new Validation();
    $errors = $validator->getErrors($urlData);

   // $parseUrl = parse_url($urlData);
  //  $sheme = isset($parsedUrl['scheme']) ? $parsedUrl['scheme'] . '://' : '';
   // $host = isset($parsedUrl['host']) ? $parsedUrl['host'] : '';
  //  $resultUrl = "{$sheme}{$host}";
  //  $validator = new Validator();
  //  $errors = $validator->getErrors($resultUrl);

    if (count($errors) === 0) {

        $parseUrl = parse_url($urlData);
        $sheme = isset($parsedUrl['scheme']) ? $parsedUrl['scheme'] . '://' : '';
        $host = isset($parsedUrl['host']) ? $parsedUrl['host'] : '';
        $resultUrl = "{$sheme}{$host}";

        $newUrl = new Url($resultData);
        $pdo = Connection::get()->connect($databaseUrl);
        $dao = new UrlDAO($pdo);
        $dao->save($newUrl);
        if ($dao->isSaveUrl()) {
            $this->get('flash')->addMessage('success', 'Страница успешно добавлена');
        } else {
            $this->get('flash')->addMessage('success', 'Страница уже существует');
        }
        $id = $url->getId();
        $url = $router->urlFor('url', ['id' => $id]);
        return $response->withRedirect($url);
    }
    $params = [
        'value' => $resultUrl,
        'errors' => $errors
    ];
    var_dump($errors);

    $response = $response->withStatus(422);
    return $this->get('renderer')->render($response, 'index.phtml', $params);
});

$app->run();
