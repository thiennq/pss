<?php
use Slim\Container as ContainerInterface;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

class Customer extends Illuminate\Database\Eloquent\Model {
    public $timestamps = false;
    protected $table = 'customer';

    public function update($obj) {
      $customer = Customer::where('phone', $obj->phone)->first();
      if($customer) {
        $customer->name = $obj->name;
        $customer->email = $obj->email;
        $customer->gender = $obj->gender;
        $customer->address = $obj->address;
        $customer->region = $obj->region;
        $customer->subregion = $obj->subregion;
        $customer->updated_at = date('Y-m-d H:i:s');
        $customer->save();
        return $customer->id;
      }
      $customer = new Customer;
      $customer->name = $obj->name;
      $customer->phone = $obj->phone;
      $customer->gender = $obj->gender;
      $customer->email = $obj->email;
      $customer->address = $obj->address;
      $customer->region = $obj->region;
      $customer->subregion = $obj->subregion;
      $customer->created_at = date('Y-m-d H:i:s');
      $customer->updated_at = date('Y-m-d H:i:s');
      $customer->save();
      return $customer->id;
    }
}
