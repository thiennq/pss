<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
require_once("../models/Customer.php");
require_once("../models/Region.php");
require_once("../models/SubRegion.php");


class AdminCustomerController extends AdminController {

	public function fetch(Request $request, Response $response) {
		$customers = Customer::join('region', 'customer.region', '=', 'region.id')->join('subregion', 'customer.subregion', '=', 'subregion.id')->select('customer.id', 'customer.name', 'customer.phone', 'customer.email','region.name as region')->get();
		return $this->view->render($response, 'admin/customer', array(
			'customers' => $customers
		));
	}

	public function create(Request $request, Response $response) {
		return $this->view->render($response, 'admin/customer_create');
	}

	public function showOrder(Request $request, Response $response) {
    $id = $request->getAttribute('id');
    $customer_name = Customer::find($id)->name;
    $orders = Order::join('customer', 'customer.id', '=', 'order.customer_id')->where('customer_id', $id)
							->select('order.id', 'order.created_at', 'customer.name', 'order.total', 'order.order_status')
							->orderBy('id', 'desc')->get();
    return $this->view->render($response, 'admin/customer_order', array(
			'orders' => $orders,
      'customer_name' => $customer_name
		));
	}

	public function export() {
		require_once("../controllers/ExportCustomer.php");
	}

    public function store(Request $request, Response $response) {
        $data = $request->getParsedBody();
        $code = Customer::store($data);
        $result = Helper::response($code);
        return $response->withJson($result, 200);
    }

    public function update(Request $request, Response $response) {
        $id = $request->getAttribute('id');
        $code = Customer::update($id);
        $result = Helper::response($code);
        return $response->withJson($result, 200);
    }

    public function delete(Request $request, Response $response) {
        $id = $request->getAttribute('id');
        $code = Customer::delete($id);
        $result = Helper::response($code);
        return $response->withJson($result, 200);
    }

    public function search(Request $request, Response $response){
        $query = $request->getQueryParams();
		$result = Customer::where('name','LIKE','%'.$query['key'].'%')->
			orWhere('phone','LIKE','%'.$query['key'].'%')->
			orWhere('email','LIKE','%'.$query['key'].'%')->
			orWhere('address','LIKE','%'.$query['key'].'%')->
			orWhere('region','LIKE','%'.$query['key'].'%')->
			orWhere('subregion','LIKE','%'.$query['key'].'%')->get();
        if(count($result)) {
            return $response->withJson(array(
                "code" => 0,
                "message" => "success",
                "data" => $result
            ));
        }
        return $response->withJson(array(
            "code" => -1,
            "message" => "Not found"
        ));
	}
}
?>
