<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
require_once("../models/Slider.php");


class AdminSliderController extends AdminController {

	public function index(Request $request, Response $response) {
		$data = Slider::all();
		// setMemcached("slider", '');
		return $this->view->render($response, 'admin/slider.pug', array(
			'sliders' => $data
		));
	}

	public function store (Request $request, Response $response) {
		$body = $request->getParsedBody();
		$Slider = new Slider;
    $Slider->title = $body['title'];
    $Slider->image = $body['image'];
    $Slider->link = $body['link'];
    $Slider->display = $body['display'];
		$Slider->created_at = date('Y-m-d H:i:s');
		$Slider->updated_at = date('Y-m-d H:i:s');
		if($Slider->save()) {
			return $response->withJson(array(
				'code' => 0,
				'message' => 'Created'
			));
		}
		return $response->withJson(array(
			'code' => -1,
			'message' => 'Error'
		));
	}

	public function getSlider(Request $request, Response $response)	{
		$id = $request->getAttribute('id');
		$slider = Slider::find($id);
		if ($slider) {
			return $response->withJson(array(
				'code' => 0,
				'data' => $slider
			));
		}
	}

	public function update (Request $request, Response $response) {
		$id = $request->getAttribute('id');
		$body = $request->getParsedBody();
		$slider = Slider::find($id);
		if($slider) {
      $slider->title = $body['title'];
      $slider->image = $body['image'];
      $slider->link = $body['link'];
      $slider->display = $body['display'];
			$slider->updated_at = date('Y-m-d H:i:s');
			if($slider->save()) {
				return $response->withJson(array(
					'code' => 0,
					'message' => 'Updated'
				));
			}
			return $response->withJson(array(
				'code' => -1,
				'message' => 'Error'
			));
		}
		return $response->withJson(array(
			'code' => -1,
			'message' => 'Unknown slider'
		));
	}

	public function delete(Request $request, Response $response) {
    $id = $request->getAttribute('id');
		$Slider = Slider::find($id);
		if($Slider) {
			if($Slider->delete()) {
				return $response->withJson(array(
					'code' => 0,
					'message' => 'Deleted'
				));
			}
			return $response->withJson(array(
				'code' => -1,
				'message' => 'Error'
			));
		}
		return $response->withJson(array(
			'code' => -1,
			'message' => 'Unknown slider'
		));
	}
}

?>
