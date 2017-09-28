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
    $comment->product_id = $data['product_id'];
    $comment->parent_id = $data['parent_id'];
    $comment->email = $data['email'];
    $comment->avatar = $data['avatar'];
    $comment->name = $data['name'];
    $comment->content = $data['content'];
    $comment->display = $data['display'];
    $comment->created_at = date('Y-m-d H:i:s');
    $comment->updated_at = date('Y-m-d H:i:s');
    if ($comment->save()) return $comment;
    return 'Error';
  }

  public function update($id, $data) {
    $comment = Comment::find($id);
    if (!$comment) return 'Not found';
    $comment->product_id = $data['product_id'];
    $comment->parent_id = $data['parent_id'];
    $comment->email = $data['email'];
    $comment->avarta = $data['avarta'];
    $comment->name = $data['name'];
    $comment->content = $data['content'];
    $comment->display = $data['display'];
    $comment->updated_at = date('Y-m-d H:i:s');
    if ($comment->save()) return 'Updated';
    return 'Error';
  }

  public function getAvarta($email) {
    return Gravatar::image($email);
  }
  public function remove($id) {
    $comment = Comment::find($id);
    if(!$comment) return 'NotFound';
    if($comment->delete()) return 'Deleted';
    return 'Error';
  }
}
