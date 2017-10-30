<?php
use Slim\Container as ContainerInterface;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

class Customer extends Illuminate\Database\Eloquent\Model {
    public $timestamps = false;
    protected $table = 'customer';

    public function store($data) {
        $customer = new Customer;
        $customer->name = $data['name'];
        $customer->phone = $data['phone'];
        $customer->email = $data['email'];
        $customer->address = $data['address'];
        $customer->region = $data['region'];
        $customer->subregion = $data['subregion'];
        $customer->created_at = date('Y-m-d H:i:s');
        $customer->updated_at = date('Y-m-d H:i:s');
        if($customer->save()) return $customer->id;
    }

    public function update($obj) {
        $customer = Customer::where('phone', $obj->phone)->first();
        if($customer) {
            $customer->name = $obj->name;
            $customer->email = $obj->email;
            $customer->address = $obj->address;
            $customer->region = $obj->region;
            $customer->subregion = $obj->subregion;
            $customer->updated_at = date('Y-m-d H:i:s');
            $customer->save();
            return $customer->id;
        }
    }

    public function delete($id){
        $customer = Customer::find($id);
        if(!$customer) return -2;
        if($customer->delete()) return $customer->id;
        return -3;
    }
}
