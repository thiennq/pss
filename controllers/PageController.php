<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

class PageController extends Controller {

  public function show(Request $request, Response $response) {
    $handle = $request->getAttribute('handle');
    $lastest_article = Article::where('display', 1)->orderBy('updated_at', 'desc')->first();
    $seccond_articles = Article::where('display', 1)->orderBy('updated_at', 'desc')->skip(1)->take(2)->get();
    $articles = Article::where('display', 1)->orderBy('updated_at', 'desc')->skip(3)->take(5)->get();
    foreach ($articles as $key => $value) {
      $value->blog_name = $blog_id->name;
      $value->blog_handle = $blog_id->handle;
      $value->strip_content = strip_tags($value->content);
    }
    $most_view = Article::where('display', 1)->orderBy('view', 'DESC')->orderBy('updated_at', 'desc')->skip(0)->take(5)->get();
    $this->view->render($response, 'blog.pug', array(
      'data' => $blog,
      'new_articles' => $articles,
      'most_view_articles' => $most_view,
      'lastest_article' => $lastest_article,
      'seccond_articles' => $seccond_articles,
      'breadcrumb_title' => $blog->name
    ));
    return $response;
	}

  public function video(Request $request, Response $response) {
    $videos = Video::all();
    return $this->view->render($response, 'video.pug', array(
      'breadcrumb_title' => 'Video',
      'videos' => $videos
    ));
  }

  public function saleOff(Request $request, Response $response) {
    $page_number = 1;
		$params = $request->getQueryParams();
		if($params['page']) $page_number = $params['page'];
		$perpage = 12;
		$skip = ($page_number - 1) * $perpage;
    $query = Product::where('display', 1)->where('discount', '>', 0);
		$count_products = $query->count();
    $products = $query->orderBy('updated_at', 'desc')->skip($skip)->take($perpage)->get();
		$total_pages = ceil($count_products / 12);
		$products = Product::getInfoProduct($products);
		$all_products = Product::where('display', 1)->get();
    $meta_title_saleoff = Meta::where('key', 'meta_title_saleoff')->first();
    $meta_title_saleoff = $meta_title_saleoff->value;
    $meta_description_saleoff = Meta::where('key', 'meta_description_saleoff')->first();
    $meta_description_saleoff = $meta_description_saleoff->value;

    $banner_saleoff = Meta::where('key', 'banner_saleoff')->first();
    $banner_saleoff = getMeta('banner_saleoff');
    $fb_image = $banner_saleoff ? HOST . '/uploads/' . $banner_saleoff : '';
    return $this->view->render($response, 'saleoff.pug', array(
			'title' => $meta_title_saleoff,
      'fb_image' => $fb_image,
      'meta_description' => $meta_description_saleoff,
			'products' => $products,
			'total_pages' => $total_pages,
			'page_number' => $page_number,
			'breadcrumb_title' => 'Sale Off'
		));
  }

  public function branch(Request $request, Response $response) {
    $regions = array();
    $branchs = Branch::where('display', 1)->get();
    foreach ($branchs as $key => $branch) {
      if(!in_array($branch->region_id, $regions)) array_push($regions, $branch->region_id);
    }
    $arr_branch = array();
    foreach ($regions as $key => $region) {
      $obj = new stdClass();
      $region_name = Region::find($region)->name;
      $obj->name = 'HỆ THỐNG CỬA HÀNG ' . $region_name;
      $obj->branchs = Branch::where('region_id', $region)->where('display', 1)->get();
      array_push($arr_branch, $obj);
    }
    error_log("arr_branch");
    error_log($arr_branch);

    return $this->view->render($response, 'branch.pug', array(
      'breadcrumb_title' => 'Hệ thống chi nhánh',
      'regions' => $arr_branch
    ));
  }


}

?>
