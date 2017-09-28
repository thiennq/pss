<?php
use Slim\Container as ContainerInterface;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

class User extends Illuminate\Database\Eloquent\Model {
    public $timestamps = false;
    protected $table = 'user';

    public function store($data) {
      $check = User::where('email', $data->email)->first();
      if($check) return -2;
      $user = new User;
      $user->fullname = $data->fullname;
      $user->email = $data->email;
      $user->phone = $data->phone;
      $user->role = $data->role;
      $user->password = password_hash($data->password, PASSWORD_DEFAULT);
  		$user->created_at = date('Y-m-d H:i:s');
  		$user->updated_at = date('Y-m-d H:i:s');
      if($user->save()) return 0;
      return -1;
    }

    public function randomString($length) {
      $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
      $charactersLength = strlen($characters);
      $random = '';
      for ($i = 0; $i < $length; $i++) {
        $random .= $characters[rand(0, $charactersLength - 1)];
      }
      return $random;
    }

    public function sendEmail($fullname, $email, $password) {
      $link = HOST . '/admin';
      $to = $email;
      $subject = '[mia.vn] - THÔNG TIN TÀI KHOẢN NGƯỜI DÙNG';
      $body = '<h3>Bạn vừa được tạo tài khoản tại mia.vn</h3>';
      $body = $body.'<p>Sau đây là thông tin tài khoản của bạn:</p>';
      $body = $body.'<ul>';
      $body = $body.'<li>Họ tên: '.$fullname.'</li>';
      $body = $body.'<li>Email: '.$email.'</li>';
      $body = $body.'<li>Mật khẩu: '.$password.'</li>';
      $body = $body.'</ul>';
      $body = $body.'<p>Hãy <a target="_blank" href='.$link.'>đăng nhập</a> vào trang quản trị và thay đổi mật khẩu của bạn.</p>';
      $text = '';
      User::PHPMailer($to, $subject,$body, $text);
    }

    public function PHPMailer($to, $subject, $body, $text) {
  		$mail = new PHPMailer;
      include ROOT . '/framework/phpmailer.php';
  		$mail->isSMTP();
  		$mail->Host = $STMP_HOST;
  		$mail->SMTPAuth = true;
  		$mail->Username = $STMP_USERNAME;
  		$mail->Password = $STMP_PASSWORD;
  		$mail->SMTPSecure = $STMP_SECURE;
  		$mail->Port = $STMP_PORT;

  		$mail->setFrom($STMP_USERNAME, 'Admin');
  		$mail->addAddress($to);

  		$mail->isHTML(true);

  		$mail->Subject = $subject;
  		$mail->Body    = $body;
  		$mail->AltBody = $text;
  		$mail->CharSet = "UTF-8";
  		$mail->FromName = "mia.vn";
  		if(!$mail->send()) return $STMP_USERNAME;
      return true;
  	}
}
