<?php
use Slim\Container as ContainerInterface;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

class User extends Illuminate\Database\Eloquent\Model {
    public $timestamps = false;
    protected $table = 'user';

    public function store($data) {
      error_log(json_encode($data));
      $check = User::where('email', $data['email'])->first();
      if($check) return -1;
      $random = randomString(50);
      $user = new User;
      $user->name = $data['name'];
      $user->email = $data['email'];
      $user->phone = $data['phone'] ? $data['phone'] : '';
      $user->role = $data['role'];
      $user->password = '';
      $user->random = $random;
      $user->created_at = date('Y-m-d H:i:s');
      $user->updated_at = date('Y-m-d H:i:s');
      $user->save();
      sendEmailUser($user->id);
      return 0;
    }

    public function update($id, $data) {
      $user = User::find($id);
      if (!$user) return -2;
      $check = User::where('email', $data['email'])->where('id', '!=', $id)->first();
      if($check) return -1;
      $user->name = $data['name'];
      $user->email = $data['email'];
      $user->phone = $data['phone'] ? $data['phone'] : '';
      $user->role = $data['role'];
      $user->updated_at = date('Y-m-d H:i:s');
      $user->save();
      return 0;
    }

    public function updatePassword($random, $password) {
      $user = User::where('random', $random)->first();
      if (!$user) return -2;
      $user->password = password_hash($password, PASSWORD_DEFAULT);
      $user->random = '';
      $user->updated_at = date('Y-m-d H:i:s');
      $user->save();
      return 0;
    }

    public function remove($id) {
      $user = User::find($id);
      if (!$user) return -2;
      $user->delete();
      return 0;
    }
}
