<?php
use Slim\Container as ContainerInterface;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

class Checkout extends Illuminate\Database\Eloquent\Model {
    public $timestamps = false;
    protected $table = 'checkout';
}
