<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
require_once("../models/User.php");
require_once("../models/Role.php");


class AdminUserController extends AdminController {

	public function index(Request $request, Response $response) {
		$user = User::orderBy('updated_at', 'desc')->get();
		return $this->view->render($response, 'admin/staff.pug', array(
			'login_email' => $login_email,
			'user' => $user
		));
	}

  public function show(Request $request, Response $response) {
    $id = $request->getAttribute('id');
    $user = User::find($id);
    $role = Role::where('email', $user->email)->first();
    $user->role = $role;
    return $response->withJson([
      'code' => 0,
      'data' => $user
    ]);
	}

  public function store (Request $request, Response $response) {
		$body = $request->getParsedBody();
    $user = User::where('email', $body['email'])->first();
    if($user) {
			return $response->withJson([
				'code' => -1,
				'message' => 'Exists'
			]);
    }
		$user = new User;
    $user->fullname = $body['fullname'];
    $user->email = $body['email'];
    $user->password = password_hash(123456, PASSWORD_DEFAULT);
		$user->created_at = date('Y-m-d H:i:s');
		$user->updated_at = date('Y-m-d H:i:s');
		if($user->save()) {
      $obj = new stdClass();
      $obj->email = $body['email'];
      $obj->product = $body['role_product'];
      $obj->order = $body['role_order'];
      $obj->customer = $body['role_customer'];
      $obj->article = $body['role_article'];
      $obj->setting = $body['role_setting'];
      $obj->staff = $body['role_staff'];
      Role::store($obj);
      User::sendEmail($body['fullname'], $body['email'], 123456);
			return json_encode(array(
				'code' => 0,
				'message' => 'Tạo tài khoản thành công'
			));
		} else {
			return json_encode(array(
				'code' => -2,
				'message' => 'Có lỗi xảy ra, xin vui lòng thử lại'
			));
		}
	}

  public function getlogin(Request $request, Response $response) {
    if(in_array('login', $_SESSION) && $_SESSION['login']){
      $href = $_SESSION['href'];
      if(!$href) $href = '/admin/collections';
      return $response->withStatus(302)->withHeader('Location', $href);
    }
    return $this->view->render($response, 'admin/login.pug');
  }

  public function checkLogin(Request $request, Response $response) {
    $body = $request->getParsedBody();
		$email = $body['email'];
		$password = $body['password'];
		$data = User::where('email', $email)->first();
    if($data) {
      if(password_verify($password, $data->password)){
        $_SESSION['login'] = true;
        $_SESSION['user_id'] = $data->id;
        $_SESSION['email'] = $data->email;
        $_SESSION['fullname'] = $data->fullname;
        $role = Role::where('email', $data->email)->first();
        $arr_role = array();
        if($role->product) array_push($arr_role, 'product');
        if($role->order) array_push($arr_role, 'order');
        if($role->customer) array_push($arr_role, 'customer');
        if($role->article) array_push($arr_role, 'article');
        if($role->setting) array_push($arr_role, 'setting');
        if($role->staff) array_push($arr_role, 'staff');
        $_SESSION['role'] = $arr_role;
        $href = '/admin/login';
        if($role->product) $href = '/admin/collections';
        else if($role->order) $href = '/admin/order';
        else if($role->customer) $href = '/admin/customer';
        else if($role->article) $href = '/admin/article/news';
        else if($role->staff) $href = '/admin/user';
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
    $User = User::find($user_id);
    if($User) {
      if(password_verify($password, $User->password)){
        $User->password = password_hash($new_password, PASSWORD_DEFAULT);
    		$User->updated_at = date('Y-m-d H:i:s');
        $User->save();
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
		$user = User::find($id);
		if($user) {
			$check = User::where('id', '!=', $id)->where('email', $body['email'])->first();
			if($check) {
				return $response->withJson([
					'code' => -1,
	        'message' => 'Exist'
				]);
			}
      $user->fullname = $body['fullname'];
      $user->email = $body['email'];
      $user->save();
      $obj = new stdClass();
      $obj->email = $body['email'];
      $obj->product = $body['role_product'];
      $obj->order = $body['role_order'];
      $obj->customer = $body['role_customer'];
      $obj->article = $body['role_article'];
      $obj->setting = $body['role_setting'];
      $obj->staff = $body['role_staff'];
      Role::store($obj);
			return $response->withJson([
				'code' => 0,
        'message' => 'Updated'
			]);
    }
		return $response->withJson([
			'code' => -2,
			'message' => 'Not found'
		]);
	}


  public function delete(Request $request, Response $response) {
    $id = $request->getAttribute('id');
		$user = User::find($id);
		if($user) {
      Role::where('email', $user->email)->delete();
      $user->delete();
			return $response->withJson([
				'code' => 0,
        'message' => 'Deleted'
			]);
    }
		return $response->withJson([
			'code' => -1,
			'message' => 'Not found'
		]);
	}

}

?>
