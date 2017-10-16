<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
require_once("../models/Customer.php");
require_once("../models/Subscribe.php");
require_once("../models/Region.php");
require_once("../models/SubRegion.php");


class AdminCustomerController extends AdminController {

	public function index(Request $request, Response $response) {
		$customers = Customer::join('region', 'customer.region', '=', 'region.id')->join('subregion', 'customer.subregion', '=', 'subregion.id')->select('customer.id', 'customer.name', 'customer.phone', 'customer.email','region.name as region')->get();
		return $this->view->render($response, 'admin/customer.pug', array(
			'customers' => $customers
		));
	}

	public function showOrder(Request $request, Response $response) {
    $id = $request->getAttribute('id');
    $customer_name = Customer::find($id)->name;
    $orders = Order::join('customer', 'customer.id', '=', 'order.customer_id')->where('customer_id', $id)
							->select('order.id', 'order.created_at', 'customer.name', 'order.total', 'order.order_status', 'order.id_odoo')
							->orderBy('id', 'desc')->get();
    return $this->view->render($response, 'admin/customer_order.pug', array(
			'orders' => $orders,
      'customer_name' => $customer_name
		));
	}

	public function subscribe(Request $request, Response $response) {
		$subscribe = Subscribe::all();
		return $this->view->render($response, 'admin/subscribe.pug', array(
			'data' => $subscribe
		));
	}

	public function export() {
		require_once("../controllers/ExportCustomer.php");
	}
}
?>