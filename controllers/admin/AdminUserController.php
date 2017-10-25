<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
require_once("../models/User.php");
require_once(ROOT . '/controllers/helper.php');
use ControllerHelper as Helper;

class AdminUserController extends AdminController {

	public function index(Request $request, Response $response) {
		$user = User::orderBy('updated_at', 'desc')->get();
		return $this->view->render($response, 'admin/user.pug', array(
			'login_email' => $login_email,
			'user' => $user
		));
	}

  public function show(Request $request, Response $response) {
    $id = $request->getAttribute('id');
    $user = User::find($id);
		if (!$user) {
			return $response->withJson([
				'code' => -2,
				'message' => 'Not found'
			]);
		}
    return $response->withJson([
      'code' => 0,
      'data' => $user
    ]);
	}

  public function store (Request $request, Response $response) {
		$body = $request->getParsedBody();
		$code = User::store($body);
		$result = Helper::response($code);
		return $response->withJson($result, 200);
	}

  public function getlogin(Request $request, Response $response) {
    if(in_array('login', $_SESSION) && $_SESSION['login']) {
      $href = $_SESSION['href'];
      if(!$href) $href = '/admin/collection';
      return $response->withStatus(302)->withHeader('Location', $href);
    }
    return $this->view->render($response, 'admin/login.pug');
  }

  public function checkLogin(Request $request, Response $response) {
    $body = $request->getParsedBody();
		$email = $body['email'];
		$password = $body['password'];
		$user = User::where('email', $email)->first();
    if($user) {
      if(password_verify($password, $user->password)) {
        $_SESSION['login'] = true;
        $_SESSION['user_id'] = $user->id;
        $_SESSION['email'] = $user->email;
        $_SESSION['name'] = $user->name;
				$_SESSION['role'] = $user->role;
        $href = '/admin/login';
        if($_SESSION['href']) $href = $_SESSION['href'];
        return $response->withJson([
          'code' => 0,
  				'message' => 'Đăng nhập thành công',
          'href' => $href
        ]);
      }
      return $response->withJson([
        'code' => -1,
        'message' => 'Mật khẩu không chính xác'
      ]);
    }
    return $response->withJson([
      'code' => -1,
      'message' => 'Email không tồn tại'
    ]);
  }

  public function getLogout(Request $request, Response $response){
    session_start();
    session_unset();
    session_destroy();
    return $response->withStatus(302)->withHeader('Location', '/admin/login');
  }

  public function changePassword (Request $request, Response $response) {
		$body = $request->getParsedBody();
    $user_id = $_SESSION["user_id"];
    $password = $body['password'];
    $new_password = $body['new_password'];
    $user = User::find($user_id);
    if($user) {
      if(password_verify($password, $user->password)){
        $user->password = password_hash($new_password, PASSWORD_DEFAULT);
    		$user->updated_at = date('Y-m-d H:i:s');
        $user->save();
        return $response->withJson([
					'code' => 0,
          'message' => 'Changed'
				]);
      }
			return $response->withJson([
				'code' => -1,
				'message' => 'Incorect'
			]);
    }
		return $response->withJson([
			'code' => -2,
			'message' => 'Not found'
		]);
	}
  public function update(Request $request, Response $response) {
    $id = $request->getAttribute('id');
    $body = $request->getParsedBody();
		$code = User::update($id, $body);
		$result = Helper::response($code);
		return $response->withJson($result, 200);
	}

  public function delete(Request $request, Response $response) {
    $id = $request->getAttribute('id');
		$code = User::remove($id);
		$result = Helper::response($code);
		return $response->withJson($result, 200);
	}

}

?>
