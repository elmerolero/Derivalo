<?php

declare(strict_types=1);

use App\Application\Actions\Section\AddSectionAction;
use App\Application\Actions\User\ListUsersAction;
use App\Application\Actions\User\ViewUserAction;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;
use App\Application\Actions\Visitor\AddVisitorAction;
use App\Application\Actions\Section\ListSectionsAction;

return function (App $app) {
    $app->options('/{routes:.*}', function (Request $request, Response $response) {
        // CORS Pre-Flight OPTIONS Request Handler
        return $response;
    });

    $app->get('/', function (Request $request, Response $response) {
        $response->getBody()->write('Hello world!');
        return $response;
    });

    $app->group('/api', function (Group $api) {
        $api->group('/visitors', function(Group $group){
            $group->get('', AddVisitorAction::class);
        });

        $api->group('/sections', function(Group $group){
            $group->get('', ListSectionsAction::class);
            $group->post('/add', AddSectionAction::class, );
        });
    });

    $app->group('/users', function (Group $group) {
        $group->get('', ListUsersAction::class);
        $group->get('/{id}', ViewUserAction::class);
    });
};
