<?php
/**
 * Created by PhpStorm.
 * User: Marco
 * Date: 07.04.2019
 * Time: 18:07
 */

namespace PHPRouting\routing\handler;

use PHPRouting\routing\Request;
use PHPRouting\routing\response\Response;

class APIHandler implements Handler
{

    private $req;

    public function load(Request $req): void
    {
        $this->req = $req;
    }

    public function run(Response $res): void
    {
        // TODO: Implement run() method.
    }

}