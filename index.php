<?php

require_once 'vendor/autoload.php';

use phpRouter\Router;
use phpRouter\Request;
use phpRouter\Response;

$router = new Router();

$router->get("/", function(Request $req, Response $res) {
    var_dump($req->get_queries());
    $res->render("<p>running</p>");
});

$router->not_found(function(Request $req, Response $res) {
    $res->send_error("Not found");
});

$router->run();