<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
require_once("../models/Order.php");
require_once("../models/Customer.php");
require_once("../models/Cart.php");
require_once("../models/Region.php");
require_once("../models/Product.php");
require_once(ROOT.'/framework/push-noti.php');
use GuzzleHttp\Client;


class OrderController extends Controller {

  public function addToCart(Request $request, Response $response) {
    $body = $request->getParsedBody();
    if(isset($_SESSION["cart"]) && !empty($_SESSION["cart"])) {
      $cart = $_SESSION["cart"];
      $check_exists = false;
      foreach ($cart as $key => $item) {
        if($item->variant_id == $body['variant_id']) {
          $body['quantity'] += $item->quantity;
          if (!checkInventoryManagent($body['variant_id'], $body['quantity'])) {
            $variant = Variant::find($body['variant_id']);
            return $response->withJson([
              "code" => -1,
              "in_stock" => $variant->inventory,
              "variant" => $variant->title
            ]);
          }
          $item->quantity = (int) $item->quantity + 1;
          $check_exists = true;
        }
      }
      if(!$check_exists) {
        if (!checkInventoryManagent($body['variant_id'], $body['quantity'])) {
          $variant = Variant::find($body['variant_id']);
          return $response->withJson([
            "code" => -1,
            "in_stock" => $variant->inventory,
            "variant" => $variant->title
          ]);
        }
        $item = new stdClass();
        $item->variant_id = $body['variant_id'];
        $item->quantity = $body['quantity'];
        array_push($cart, $item);
      }
    } else {
      if (!checkInventoryManagent($body['variant_id'], $body['quantity'])) {
        $variant = Variant::find($body['variant_id']);
        return $response->withJson([
          "code" => -1,
          "in_stock" => $variant->inventory,
          "variant" => $variant->title
        ]);
      }
      $cart = array();
      $item = new stdClass();
      $item->variant_id = $body['variant_id'];
      $item->quantity = $body['quantity'];
      array_push($cart, $item);
    }
    $_SESSION["cart"] = $cart;
    return $response->withJson([
      "code" => 0,
      "data" => $_SESSION["cart"]
    ]);
  }

  public function updateCart(Request $request, Response $response) {
    $body = $request->getParsedBody();
    if(isset($_SESSION["cart"]) && !empty($_SESSION["cart"])) {
      $cart = $_SESSION["cart"];
      $total = 0;
      foreach ($cart as $key => $item) {
        if($item->variant_id == $body['variant_id'] ) {
          if (!checkInventoryManagent($body['variant_id'], $body['quantity'])) {
            $variant = Variant::find($body['variant_id']);
            return $response->withJson([
              "code" => -1,
              "in_stock" => $variant->inventory,
              "variant" => $variant->title
            ]);
          }
          $item->quantity = $body['quantity'];
        }
        $variant = Variant::where('id', $item->variant_id)->first();
        $total += (int) $variant->price * (int) $item->quantity;
      }
      $_SESSION["cart"] = $cart;
      return $response->withJson([
        "code" => 0,
        "data" => $_SESSION["cart"],
        "total" => $total
      ]);
    }
    return $response->withJson([
      "code" => -1,
      "message" => 'Not found'
    ]);
  }

  public function deleteCart(Request $request, Response $response) {
    $body = $request->getParsedBody();
    if(isset($_SESSION["cart"]) && !empty($_SESSION["cart"])) {
      $cart = $_SESSION["cart"];
      foreach ($cart as $key => $item) {
        if($item->variant_id == $body['variant_id'] ) {
          unset($cart[$key]);
        }
      }
      $_SESSION["cart"] = $cart;
      return $response->withJson([
        "code" => 0,
        "data" => $_SESSION["cart"]
      ]);
    }
    return $response->withJson([
      "code" => -1,
      "message" => 'Not found'
    ]);
  }

  public function getInfoCart(Request $request, Response $response) {
    $cart = $_SESSION['cart'];
    $data = array();
    $total = 0;
    foreach ($cart as $key => $value) {
      $variant = Variant::find($value->variant_id);
      $product = Product::find($variant->product_id);
      $value->title = $product->title;
      $value->variant = $variant->title;
      $value->handle = $product->handle;
      $value->price = $variant->price;
      $value->product_id = $variant->id;
      $value->image = $product->featured_image;
      $value->subTotal = (int) $variant->price * (int) $value->quantity;
      $value->in_stock = $variant->inventory;
      $total += $value->subTotal;
      array_push($data, $value);
    }
    return $response->withJson(array(
      'data' => $data
    ));
  }

  public function viewCart(Request $request, Response $response) {
    $cart = $_SESSION['cart'];
    $total = 0;
    foreach ($cart as $key => $value) {
      $variant = Variant::where('id', $value->variant_id)->first();
      $product = Product::where('id', $variant->product_id)->first();
      $value->title = $product->title;
      $value->variant = $variant->title;
      $value->handle = $product->handle;
      $value->price = $variant->price;
      $value->product_id = $variant->id;
      $value->image = $product->featured_image;
      $value->subTotal = (int) $variant->price * (int) $value->quantity;
      $total += $value->subTotal;
    }
    return $this->view->render($response, 'cart', [
      'cart' => $cart,
      'total' => $total,
    ]);
  }

  public function checkOut(Request $request, Response $response) {
    $cart = $_SESSION['cart'];
    $total = 0;
    foreach ($cart as $key => $value) {
      $variant = Variant::where('id', $value->variant_id)->first();
      $product = Product::where('id', $variant->product_id)->first();
      $value->title = $product->title;
      $value->variant = $variant->title;
      $value->handle = $product->handle;
      $value->price = $variant->price;
      $value->product_id = $variant->id;
      $value->image = $product->featured_image;
      $value->subTotal = (int) $variant->price * (int) $value->quantity;
      $total += $value->subTotal;
    }
    $region = Region::orderBy('name', 'asc')->get();
    return $this->view->render($response, 'checkout', [
      'cart' => $cart,
      'total' => $total,
      'region' => $region
    ]);
  }

  public function orderSuccess(Request $request, Response $response) {
    if (isset($_SESSION['order_id']) || isset($_SESSION['order_id_dropship'])) {
      $arr_cart = array();
      $total = 0;
      if (isset($_SESSION['order_id'])) {
        $order = Order::find($_SESSION['order_id']);
        $customer = Customer::find($order->customer_id);
        $cart = Cart::where('order_id', $order->id)->get();
        foreach ($cart as $key => $value) {
          $variant = Variant::where('id', $value->variant_id)->first();
          $product = Product::where('id', $variant->product_id)->first();
          $value->title = $product->title;
          $value->variant = $variant->title;
          $value->handle = $product->handle;
          $value->price = $variant->price;
          $value->image = $product->featured_image;
          $value->subTotal = (int) $variant->price * (int) $value->quantity;
          $total += $value->subTotal;
          array_push($arr_cart, $value);
          unset($_SESSION['order_id']);
        }
      }
      return $this->view->render($response, 'successful', [
        'customer' => $customer,
        'total' => $total,
        'cart' => $arr_cart
      ]);
    }
    return $response->withStatus(302)->withHeader('Location', '/');
  }

  public function store(Request $request, Response $response) {
    $body = $request->getParsedBody();
    $name = $body['name'];
    $phone = $body['phone'];
    $region = $body['region'];
    $subregion = $body['subregion'];
    $address = $body['address'];
    $email = $phone . '@gmail.com';
    $shipping_price = $body['shipping_price'];
    $discount = $body['discount'];
    $payment_method = $body['payment_method'];

    $customer = new stdClass();
    $customer->name = $name;
    $customer->email = $email;
    $customer->phone = $phone;
    $customer->address = $address;
    $customer->region = $region;
    $customer->subregion = $subregion;
    $customer_id = Customer::update($customer);

    $details = array();
    $cart = $_SESSION['cart'];
    $subTotal = 0;
    foreach ($cart as $key => $value) {
      $item = new stdClass();
      $variant = Variant::find($value->variant_id);
      $product = Product::find($variant->product_id);
      $item->variant_id = $variant->id;
      $item->price = $variant->price;
      $item->quantity = $value->quantity;
      $subTotal += (int) $variant->price * (int) $value->quantity;
      array_push($details, $item);
    }

    $total = $subTotal + $shipping_price - $discount;
    $order_id = Order::store($customer_id, $body, $subTotal, $total);
    if ($order_id) {
      foreach ($details as $key => $value) {
        $value->order_id = $order_id;
        Cart::store($value);
        Product::updateSell($value->product_id, $value->quantity);
      }
      unset($_SESSION['cart']);
      $_SESSION['order_id'] = $order_id;
      return $response->withJson([
        'code' => 0,
        'message' => 'success'
      ]);
    }
    return $response->withJson([
      'code' => -1,
      'message' => 'error'
    ]);
  }
}

?>
