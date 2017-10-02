<?php
use Slim\Container as ContainerInterface;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

class Collection extends Illuminate\Database\Eloquent\Model {
    public $timestamps = false;
    protected $table = 'collection';

    public function store($data) {
        $collection = Collection::where('title', $data['title'])->first();
        if ($collection) return -1;
        $collection = new Collection;
		$collection->parent_id = $data['parent_id'] ? $data['parent_id'] : -1;
		$collection->title = $data['title'];
		$collection->handle = $data['handle'];
		$collection->breadcrumb = $data['breadcrumb'];
		$collection->link = $data['link'];
		$collection->description = $data['description'];
		$collection->content = $data['content'];
		$collection->image = $data['image'] ? renameOneImage($data['image'], 'collection_' . $collection->handle) : '';
		$collection->banner = $data['banner'] ? renameOneImage($data['banner'], 'collection_' . $collection->handle . '_banner') : '';
		$collection->meta_title = $data['meta_title'];
		$collection->meta_description = $data['meta_description'];
		$collection->created_at = date('Y-m-d H:i:s');
        $collection->updated_at = date('Y-m-d H:i:s');
        if ($collection->save()) return $collection->id;
        return -3;
    }

    public function update($id, $data) {
        $collection = Collection::find($id);
        if (!$collection) return -2;
        $check = Collection::where('id', '!=', $id)->where('title', $data['title'])->first();
        if ($check) return -1;
        
		$collection->parent_id = $data['parent_id'] ? $data['parent_id'] : -1;
		$collection->title = $data['title'];
		$collection->handle = $data['handle'];
		$collection->breadcrumb = $data['breadcrumb'];
		$collection->link = $data['link'];
		$collection->description = $data['description'];
		$collection->content = $data['content'];
		$collection->image = $data['image'] ? renameOneImage($data['image'], 'collection_' . $collection->handle) : '';
		$collection->banner = $data['banner'] ? renameOneImage($data['banner'], 'collection_' . $collection->handle . '_banner') : '';
		$collection->meta_title = $data['meta_title'];
		$collection->meta_description = $data['meta_description'];
        $collection->updated_at = date('Y-m-d H:i:s');
        if ($collection->save()) return 0;
        return -3;
    }

    public function remove($id) {
        $collection = Collection::find($id);
        $image = $collection->image;
        $banner = $collection->banner;
        if (!$collection) return -2;
        if ($collection->delete()) {
            Collection::where('parent_id', $id)->update(['parent_id' => -1]);
            removeImage($image);
            removeImage($banner);
            return 0;  
        }
        return -3;
    }
}
