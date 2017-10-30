<?php
use Slim\Container as ContainerInterface;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use forxer\Gravatar\Gravatar;

class Comment extends Illuminate\Database\Eloquent\Model {
  public $timestamps = false;
  protected $table = 'comment';

  public function store($data) {
    $comment = new Comment;
    $comment->name = $data['name'];
    $comment->phone_number = $data['phone_number'];
    $comment->email = $data['email'];
    $comment->content = $data['content'];
    $comment->parent_id = $data['parent_id'];
    $comment->type = $data['type'];
    $comment->type_id = $data['type_id'];
    $comment->status = $data['status'];
    $comment->created_at = date('Y-m-d H:i:s');
    $comment->updated_at = date('Y-m-d H:i:s');
    if ($comment->save()) return $comment->id;
    return -3;
  }

  public function update($id) {
    $comment = Comment::find($id);
    if (!$comment) return -2;
    $comment->status = 1;
    $comment->updated_at = date('Y-m-d H:i:s');
    if ($comment->save()) return $comment->id;
    return -3;
  }

  public function getAvarta($email) {
    return Gravatar::image($email);
  }
  public function remove($id) {
    $comment = Comment::find($id);
    if(!$comment) return -2;
    if($comment->delete()) return $comment->id;
    return -3;
  }
}
