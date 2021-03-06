<?php

require_once 'vendor/autoload.php';

use phpRouter\Router;
use phpRouter\Request;
use phpRouter\Response;
use phpRouter\View;

$router = new Router();

$router->serve("/public/(.*)", __DIR__ . "/dist/");

$router->get("/", function(Request $req, Response $res) {
    $res->send("Hello");
});

$router->not_found(function(Request $req, Response $res) {
    $res->send_error("Not found");
});

$router->on_error(function(Request $req, Response $res, string $error) {
    if($req->get_content_type() !== "application/json") {
        $res->render(new View(function() use ($error) {
            ?>
            <h1>Error</h1>
            <p><?= $error ?></p>
            <?php
        }));
    }
});

$router->run();