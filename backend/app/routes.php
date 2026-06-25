<?php

declare(strict_types=1);

use App\Application\Middleware\AuthMiddleware;
use Slim\App;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;
use App\Application\Actions\Section\AddSectionAction;
use App\Application\Actions\User\ListUsersAction;
use App\Application\Actions\User\ViewUserAction;
use App\Application\Actions\Visitor\AddVisitorAction;
use App\Application\Actions\User\UserLoginAction;
use App\Application\Actions\User\UserLogoutAction;
use App\Application\Actions\User\UserGetCurrentAction;
use App\Application\Actions\User\UserRefreshAction;
use App\Application\Actions\Section\ListSectionsAction;
use App\Application\Actions\Document\ViewDocumentAction;
use App\Application\Actions\Document\ListDocumentsBySectionAction;
use App\Application\Actions\Document\AddDocumentAction;

return function (App $app) {
    /*$app -> add(function($request, $handler){
        $response = $handler->handle($request);

        return $response -> withHeader('Access-Control-Allow-Origin', 'http://localhost:3000')
                         -> withHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-CSRF-Token')
                         -> withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
                         -> withHeader('Access-Control-Allow-Credentials', 'true');
    });*/

    $app->options('/{routes:.*}', function (Request $request, Response $response) {
        // CORS Pre-Flight OPTIONS Request Handler
        return $response;
    });

    /* Main page */
    $app -> get('/', function (Request $request, Response $response) {
        $html = file_get_contents('main.html');
        $response -> getBody() -> write($html);
        return $response;
    });

    /* Content section */
    $app -> group('/content', function(Group $content) {
        $content -> group('/documents', function(Group $documents){
            $documents -> get('/{id}', ViewDocumentAction::class);
        });
    });

    /* API Section */
    $app->group('/api', function (Group $api) {
        $api->group('/sections', function(Group $group){
            $group->get('', ListSectionsAction::class);
            $group->post('/add', AddSectionAction::class) -> add(new \App\Application\Middleware\CsrfMiddleware()) -> add(new AuthMiddleware());
            $group->post('/update', \App\Application\Actions\Section\UpdateSectionAction::class) -> add(new \App\Application\Middleware\CsrfMiddleware()) -> add(new AuthMiddleware());
        });

        $api->group('/content', function(Group $group){
            $group->get('/{section}', ListDocumentsBySectionAction::class);
            $group->post('/add', AddDocumentAction::class) -> add(new AuthMiddleware());
        });

        $api->group('/users', function (Group $group) {
            $group->get('', ListUsersAction::class);
            $group->get('/{id}', ViewUserAction::class);
        }) -> add(new AuthMiddleware());

        $api->group('/auth', function(Group $group){
            $group->post('/login', UserLoginAction::class);
            $group->post('/logout', UserLogoutAction::class);
            $group->get('/current', UserGetCurrentAction::class) -> add(new AuthMiddleware());
            $group->post('/refresh', UserRefreshAction::class);
            
        });
    });

    /* For react */
    $app->get('/{routes:.+}', function (Request $request, Response $response) {
        $html = file_get_contents(__DIR__ . '/../public/index.html');
        $response->getBody()->write($html);
        return $response;
    });
};
