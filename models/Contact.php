<?php
  use Slim\Container as ContainerInterface;
  use \Psr\Http\Message\ServerRequestInterface as Request;
  use \Psr\Http\Message\ResponseInterface as Response;

  class Contact extends Illuminate\Database\Eloquent\Model {
    public $timestamps = false;
    protected $table = 'contact';

    public function store($data) {
      $contact = new Contact;
      $contact->name = $data['name'];
      $contact->email = $data['email'];
      $contact->order_id = $data['order_id'];
      $contact->message = $data['message'];
      $contact->status = 0;
      $contact->created_at = date('Y-m-d H:i:s');
      $contact->updated_at = date('Y-m-d H:i:s');
      $contact->save();
      return $contact->id;
    }

    public function update($id, $data) {
      $Contact = Contact::find($id);
      if(!$Contact) return -2;
      $Contact->status = $data['status'];
  		$Contact->updated_at = date('Y-m-d H:i:s');
      if($Contact->save()) return 0;
      return -3;
    }

    public function sendEmailContactUs($obj, $email) {
      $to = $email;
      $subject = '[COMBENTO] - Email Contact Us';
      $body = '<h3>Bạn vừa nhận được một email từ mục liên hệ</h3>';
      $body = $body.'<p> Nội dung : ' . $obj['message'];
      $text = '';
      Contact::PHPMailer($to, $subject,$body, $text, $obj['email']);
    }

    public function PHPMailer($to, $subject, $body, $text, $emailReplyTo) {
      $mail = new PHPMailer;
      include ROOT . '/framework/phpmailer.php';
      $mail->isSMTP();
      $mail->Host = $STMP_HOST;
      $mail->SMTPAuth = true;
      $mail->Username = $STMP_USERNAME;
      $mail->Password = $STMP_PASSWORD;
      $mail->SMTPSecure = $STMP_SECURE;
      $mail->Port = $STMP_PORT;
      error_log($emailReplyTo);
      $mail->setFrom($STMP_USERNAME, 'Admin');
      $mail->AddReplyTo($emailReplyTo, 'Reply to name');
      $mail->addAddress($to);

      $mail->isHTML(true);

      $mail->Subject = $subject;
      $mail->Body    = $body;
      $mail->AltBody = $text;
      $mail->CharSet = "UTF-8";
      $mail->FromName = "COMBENTO";

      if(!$mail->send()) return $STMP_USERNAME;
      return true;
  }
  }
?>
