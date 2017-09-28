<?php
use Slim\Container as ContainerInterface;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

class Coupon extends Illuminate\Database\Eloquent\Model {
    public $timestamps = false;
    protected $table = 'coupon';

    public function store($data) {
      $check = Coupon::where('code', $data['code'])->first();
      if($check) return -2;
      $coupon = new Coupon;
      $coupon->title = $data['title'];
      $coupon->description = $data['description'];
      $coupon->type = $data['type'];
      $coupon->code = $data['code'];
      $coupon->value = $data['value'];
      $coupon->usage_count = 0;
      $coupon->usage_left = $data['usage_left'];
      $dateSecond = strtotime($data['expired_date']);
      $coupon->min_value_order = $data['min_value_order'] ? $data['min_value_order'] : 0;
      $coupon->expired_date = $dateSecond;
  		$coupon->created_at = date('Y-m-d H:i:s');
  		$coupon->updated_at = date('Y-m-d H:i:s');
      if($coupon->save()) return 0;
      return -1;
    }

    public function update($id , $data) {
      error_log('id : '.$id);
      $coupon = Coupon::find($id);
      if (!$coupon) return -2;
      $check = Coupon::where('code', $data['code'])->first();
      if ($check && ($id != $check->id)) return -3;
      $coupon->title = $data['title'];
      $coupon->description = $data['description'];
      $coupon->type = $data['type'];
      $coupon->code = $data['code'];
      $coupon->value = $data['value'];
      $coupon->usage_left = $data['usage_left'];
      $dateSecond = strtotime($data['expired_date']);
      $coupon->min_value_order = $data['min_value_order'];
      $coupon->expired_date = $dateSecond;
  		$coupon->created_at = date('Y-m-d H:i:s');
  		$coupon->updated_at = date('Y-m-d H:i:s');
      if($coupon->save()) return 0;
      return -1;
    }
    public function list() {
      $data = Coupon::orderBy('updated_at', 'desc')->get();
      return $data;
    }

    public function use($code) {
      $coupon = Coupon::where('code', $code)->first();
      if (!$coupon) return -2;
      $coupon->usage_left = $coupon->usage_left - 1;
      $coupon->usage_count = $coupon->usage_count + 1;
      if ($coupon->save()) return 0;
      return -1;
    }

    public function checkValidCoupon($total, $code) {
      $coupon = Coupon::where('code', $code)->first();
      if (!$coupon) {
        //Coupon not found
        return -1;
      }
      if ($coupon->usage_left == 0) return -2;
      if ($total < $coupon->min_value_order) return -3;
      $dateSecond = strtotime($coupon->expired_date);
      $currentDate = date("Y/m/d");
      $currentDateSecond = strtotime($currentDate);
      if (($dateSecond - $currentDateSecond) > 0) {
        //Coupon expired
        return -4;
      }
      return 0;
    }

    public function getCouponDiscount($code, $total) {
      $coupon = Coupon::where('code', $code)->first();
      if ($coupon->type == 'value') {
        if ($coupon->value > $total) {
          return (0 - $total);
        }
        return (0 - $coupon->value);
      }
      if ($coupon->type == 'percentage') {
        return (0 - $total * ($coupon->value / 100));
      }
    }
}
