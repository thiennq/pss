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
      $contact->phone = $data['phone'];
      $contact->email = $data['email'];
      $contact->content = $data['content'];
      $contact->status = 0;
      $contact->created_at = date('Y-m-d H:i:s');
      $contact->updated_at = date('Y-m-d H:i:s');
      if($contact->save()) return $contact->id;
    }

    public function update($id) {
      $contact = Contact::find($id);
      if(!$contact) return -2;
      $contact->status = 1;
      $contact->updated_at = date('Y-m-d H:i:s');
      if($contact->save()) return 0;
      return -3;
    }
    public function delete($id){
      $contact = Contact::find($id);
      if(!$contact) return -2;
      if($contact->delete()) return $contact->id;
      return -3;
    }
  }
?>
