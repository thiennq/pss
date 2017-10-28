<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
require_once("../models/Article.php");
require_once("../models/Blog.php");
require_once("../models/BlogArticle.php");
require_once(ROOT . '/controllers/helper.php');
use ControllerHelper as Helper;

class AdminDashboardController extends AdminController{

    public function fetch(Request $request, Response $response){
        $data = Article::orderBy('updated_at', 'desc')->get();
        return $this->view->render($response, 'admin/article.pug', array(
            'data' => $data
        ));
    }
}

?>