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
  }
?>
