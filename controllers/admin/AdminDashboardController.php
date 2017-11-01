<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
require_once("../models/Order.php");
require_once("../models/Contact.php");
require_once("../models/Product.php");
require_once("../models/Article.php");
require_once(ROOT . '/controllers/helper.php');
use ControllerHelper as Helper;

class AdminDashboardController extends AdminController{

    public function fetch(Request $request, Response $response){
        $data = [];
        $data['orderCount'] = Order::all()->count();
        $data['contactCount'] = Contact::all()->count();
        $data['productCount'] = Product::all()->count();
        $data['articleCount'] = Article::all()->count();
        return $this->view->render($response, 'admin/dashboard', array(
            'data' => $data
        ));
    }
}

?>
