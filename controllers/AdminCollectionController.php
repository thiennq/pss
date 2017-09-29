<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
require_once("../models/Collection.php");
require_once("../models/Product.php");
require_once("../models/CollectionTag.php");

class AdminCollectionController extends AdminController {

	public function index(Request $request, Response $response) {
		$data = Collection::orderBy('title')->get();
		foreach ($data as $key => $value) {
			$value->image = convertImage($value->image, 240);
		}
		return $this->view->render($response, 'admin/collection.pug', array(
			'collections' => $data
		));
	}

	public function create(Request $request, Response $response) {
		$list_collection = Collection::where('parent_id', -1)->orderBy('breadcrumb', 'asc')->get();
		return $this->view->render($response, 'admin/collection_new.pug', array(
			'collection' => $list_collection
		));
	}

	public function show(Request $request, Response $response) {
    $id = $request->getAttribute('id');
		$list_collection = Collection::where('show_landing_page', 0)->orderBy('breadcrumb', 'asc')->get();
		foreach ($list_collection as $key => $collection) {
			$collection['breadcrumb'] = str_replace(',', '/', $collection['breadcrumb']);
			$collection['breadcrumb'] = strtoupper($collection['breadcrumb']);
		}
    $data = Collection::find($id);
		$arr_tag = CollectionTag::where('collection_id', $id)->get();
		$count_arr_tag = count($arr_tag);
		return $this->view->render($response, 'admin/collection_edit.pug', array(
			'data' => $data,
			'collection' => $list_collection,
			'arr_tag' => $arr_tag,
			'count_arr_tag' => $count_arr_tag
		));
	}

	public function store (Request $request, Response $response) {
		$body = $request->getParsedBody();
		$collection = new Collection;
		$collection->parent_id = -1;
		if($body['parent_id']) $collection->parent_id = $body['parent_id'];
		$collection->title = $body['title'];;
		$collection->handle = $body['handle'];
		$collection->link = $body['link'];
		$collection->breadcrumb = $body['breadcrumb'];
		$collection->description = $body['description'];
		if($body['image']) $collection->image = renameOneImage($body['image'], $body['handle']);
		if($body['banner']) $collection->banner = renameOneImage($body['banner'], $body['handle'].'_banner');
		$collection->meta_title = $body['meta_title'];
		$collection->meta_description = $body['meta_description'];
		$collection->show_landing_page = $body['show_landing_page'];
		$collection->created_at = date('Y-m-d H:i:s');
		$collection->updated_at = date('Y-m-d H:i:s');
		if($collection->save()) {
			$collection_id = $collection->id;
			$arr_tag = $body['arr_tag'];
			foreach ($arr_tag as $key => $tag) {
				CollectionTag::store($collection_id, $tag['name'], $tag['handle']);
			}
			return $response->withJson(array(
				'code' => 0,
				'message' => 'Created',
				'id' => $collection_id
			));
		}
		return $response->withJson(array(
			'code' => -1,
			'message' => 'Error'
		));
	}

	public function update (Request $request, Response $response) {
		$collection_id = $request->getAttribute('id');
		$body = $request->getParsedBody();
		$collection = Collection::find($collection_id);

		if(count($collection)) {
			$collection->title = $body['title'];
			$collection->parent_id = $body['parent_id'];
			$collection->handle = $body['handle'];
			$collection->link = $body['link'];
			$collection->breadcrumb = $body['breadcrumb'];
			$collection->description = $body['description'];
      $collection->image = $body['image'];
			if($body['image']) $collection->image = renameOneImage($body['image'], $body['handle']);
      $collection->banner = $body['banner'];
			if($body['banner']) $collection->banner = renameOneImage($body['banner'], $body['handle'].'_banner');
			$collection->meta_title = $body['meta_title'];
			$collection->meta_description = $body['meta_description'];
			$collection->show_landing_page = $body['show_landing_page'];
			$collection->updated_at = date('Y-m-d H:i:s');
			if($collection->save()) {
        $arr_tag = $body['arr_tag'];
        CollectionTag::where('collection_id', $collection_id)->delete();
  			foreach ($arr_tag as $key => $tag) {
  				CollectionTag::store($collection_id, $tag['name'], $tag['handle']);
  			}
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
			'message' => 'Unknown collection'
		));
	}

	public function delete(Request $request, Response $response) {
		$id = $request->getAttribute('id');
		$Collection = Collection::find($id);
		if($Collection) {
			$image = $Collection->image;
			removeImage($image);
			if($Collection->delete()) {
				$child = Collection::where('parent_id', $id)->get();
        CollectionTag::where('collection_id', $id)->delete();
				if(count($child)) {
					foreach ($child as $key => $value) {
						$temp = Collection::find($value->id);
						$temp->parent_id = '';
						$temp->save();
					}
				}
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
			'message' => 'Unknown collection'
		));
	}

	public function deleteTag(Request $request, Response $response) {
		$params = $request->getQueryParams();
		$id = $params['id'];
		$tag = CollectionTag::find($id);
		if($tag) {
			if($tag->delete()) {
				return $response->withJson([
					'code' => 0,
					'message' => 'Deleted'
				]);
			}
			return $response->withJson([
				'code' => -1,
				'message' => 'Error'
			]);
		}
		return $response->withJson([
			'code' => -1,
			'message' => 'Not found'
		]);
	}
}

?>
