<?php
use Slim\Container as ContainerInterface;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

class BlockIP extends Illuminate\Database\Eloquent\Model {
  public $timestamps = false;
  protected $table = 'block_ip';

  public function block($data) {
    $ip = BlockIP::where('ip', $data->ip)->first();
    if(!$ip) {
      $block = new BlockIP;
      $block->ip = $data->ip;
      $block->time = time();
      $block->login_failed = 1;
      $block->reason = "Lần 1: " . $data->reason;
      $block->block = $data->block;
      $block->created_at = date('Y-m-d H:i:s');
      $block->updated_at = date('Y-m-d H:i:s');
      $block->save();
    } else {
      if($ip->login_failed < 3) $ip->login_failed = $ip->login_failed + 1;
      $ip->reason = $ip->reason."<br/> Lần " . $ip->login_failed . ": ". $data->reason;
      $ip->time = time();
      $ip->block = $data->block;
      if($ip->login_failed == 3) $ip->block = 1;
      $ip->updated_at = date('Y-m-d H:i:s');
      $ip->save();
    }
  }

  public function removeIP($ip) {
    $ip = BlockIP::where('ip', $ip)->delete();
    return true;
  }

  public function remove($id) {
    $ip = BlockIP::find($id);
    error_log($ip);
    if($ip->delete()) return true;
    return false;
  }

  public function checkBlockIP($ip) {
    $ip = BlockIP::where('ip', $ip)->where('block', 1)->first();
    if($ip) return $ip;
    return false;
  }
}
