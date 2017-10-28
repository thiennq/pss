<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
require_once('../models/User.php');
require_once(ROOT . '/controllers/helper.php');
use ControllerHelper as Helper;

class UserController extends Controller {

  public function createPassword (Request $request, Response $response) {
		$random = $request->getAttribute('random');
		$check = User::where('random', $random)->first();
		if (!$check) {
			$this->view->render($response, '404');
      return $response->withStatus(404);
		}
		return $this->view->render($response, 'create_password', [
      'random' => $random
    ]);
	}

  public function forgotPassword (Request $request, Response $response) {
		return $this->view->render($response, 'forgot_password');
	}

  public function checkLogin(Request $request, Response $response) {
    $body = $request->getParsedBody();
		$email = $body['email'];
		$password = $body['password'];
    if (!$email || !$password) {
      return $response->withJson([
        'code' => -1,
        'message' => 'Email không được rỗng'
      ]);
    }
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

  public function updatePassword (Request $request, Response $response) {
		$body = $request->getParsedBody();
    $code = User::updatePassword($body['random'], $body['password']);
    $result = Helper::response($code);
    return $response->withJson($result, 200);
	}

  public function resetPassword (Request $request, Response $response) {
    $params = $request->getQueryParams();
    $email = $params['email'];
    if (isset($email) && $email) {
      $user = User::where('email', $email)->first();
      if (!$user) {
        return $response->withJson([
          'code' => -1,
          'message' => 'Email không tồn tại'
        ]);
      }
      $random = randomString(50);
      $user->random = $random;
      $user->password = '';
      $user->save();
      sendEmailForgotPassword($email);
      return $response->withJson([
        'code' => 0,
        'message' => 'success'
      ]);
    }
    return $response->withJson([
      'code' => -1,
      'message' => 'Email không được rỗng'
    ]);
  }

}

?>
