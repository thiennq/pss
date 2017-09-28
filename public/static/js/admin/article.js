initTinymce('#article_content');
initTinymce('#article_content_promotion');
initDataTable('table');

$(window).on('load', function() {
  if($('#article_content').data('value')) tinyMCE.get('article_content').setContent($('#article_content').data('value'));
  if($('#article_content_promotion').data('value')) tinyMCE.get('article_content_promotion').setContent($('#article_content_promotion').data('value'));
});

$(document).on('change', '.feature-image', function() {
  console.log($(this).val());
  if($(this).val()) {
    if(checkExtImage($(this).val())) {
      var form_group = $(this).closest('.form-group');
      form_group.find('.loading').removeClass('hidden');
      var form = $(this).closest('form');
      var formData = new FormData(form[0]);
      $.ajax({
        type: 'POST',
        url: '/admin/api/uploadImage',
        data: formData,
        cache: false,
        contentType: false,
        processData: false,
        success: function(json) {
          if(!json.code) {
            var image = json.data[0];
            var resize = resizeImage(image, '240');
            var timestamp = new Date() - 0;
            form.find('img').attr('src', '/uploads/' + resize + '?v=' + timestamp);
            form.find('input[name="image"]').val(image);
          } else toastr.error('Có lỗi xảy ra, xin vui lòng thử lại');
          form_group.find('.loading').addClass('hidden');
        }
      });
    }
  }
});

$('.btn-create-article').click(function(event) {
  var btn = $(this);
  $('input').removeClass('error');
  var data = {};
  data.title = $('input[name="title"]').val();
  if(!data.title.trim().length) {
    toastr.error('Chưa nhập tiêu đề');
    $('input[name="title"]').addClass('error');
    return;
  }
  data.handle = $('input[name="handle"]').val();
  if(!data.handle.trim().length) {
    toastr.error('Chưa nhập handle');
    $('input[name="handle"]').addClass('error');
    return;
  }
  data.type = $('select[name="type"]').val();
  data.image = $('input[name="image"]').val();
  if(data.type != 'khuyen-mai') {
    if(!data.image) {
      toastr.error('Chưa chọn hình đại diện');
      return;
    }
    data.description = $('textarea[name="description"]').val();
    if(!data.description) {
      toastr.error('Chưa nhập mô tả bài viết');
      return;
    }
  }
  data.description_seo = $('textarea[name="description_seo"]').val();
  data.content = tinyMCE.get('article_content').getContent();
  if(!data.content) {
    toastr.error('Chưa nhập nội dung bài viết');
    return;
  }
  data.content_promotion = tinyMCE.get('article_content_promotion').getContent();
  data.display = $('select[name="display"]').val();
  data.display = parseInt(data.display);
  data.meta_robots = $('select[name="meta_robots"]').val();
  data.collection_id = $(".chosen-select").chosen().val();

  var arr_related = [];
  $('.list-related').find('.item').each(function() {
    if($(this).attr('data-id')) arr_related.push($(this).attr('data-id'));
  });
  data.arr_related = arr_related;
  btn.addClass('disabled');

  $.ajax({
    type: 'POST',
    url: '/admin/article',
    data: data,
    success: function(json) {
      btn.removeClass('disabled');
      if(!json.code) {
        toastr.success('Thêm tin tức thành công');
        reloadPage('/admin/articles/' + json.id);
      } else toastr.error('Có lỗi xảy ra, xin vui lòng thử lại');
    }
  });
});

$('.btn-update-article').click(function() {
  var id = $(this).data('id');
  var btn = $(this);
  $('input').removeClass('error');
  var data = {};
  data.title = $('input[name="title"]').val();
  if(!data.title.trim().length) {
    toastr.error('Chưa nhập tiêu đề');
    $('input[name="title"]').addClass('error');
    return;
  }
  data.handle = $('input[name="handle"]').val();
  if(!data.handle.trim().length) {
    toastr.error('Chưa nhập handle');
    $('input[name="handle"]').addClass('error');
    return;
  }
  data.image = $('input[name="image"]').val();
  data.type = $('select[name="type"]').val();
  data.description = $('textarea[name="description"]').val();
  if(data.type != 'khuyen-mai') {
    if(!data.image) {
      toastr.error('Chưa chọn hình đại diện');
      return;
    }
    if(!data.description) {
      toastr.error('Chưa nhập mô tả bài viết');
      return;
    }
  }
  data.description_seo = $('textarea[name="description_seo"]').val();
  data.content = tinyMCE.get('article_content').getContent();
  if(!data.content) {
    toastr.error('Chưa nhập nội dung bài viết');
    return;
  }
  data.content_promotion = tinyMCE.get('article_content_promotion').getContent();
  data.collection_id = $(".chosen-select").chosen().val();
  data.display = $('select[name="display"]').val();
  data.updated_at = $('input[name="updated_at"]').val();
  if(checkDate(data.updated_at) == 'Invalid Date') {
    toastr.error('Vui lòng nhập đúng định dạng ngày giờ (yyyy-mm-dd h:m:s)');
    $('input[name="updated_at"]').addClass('error');
    return;
  }

  var arr_related = [];
  $('.list-related').find('.item').each(function() {
    if($(this).attr('data-id')) arr_related.push($(this).attr('data-id'));
  });
  data.arr_related = arr_related;
  data.meta_robots = $('select[name="meta_robots"]').val();
  btn.addClass('disabled');

  $.ajax({
    type: 'PUT',
    url: '/admin/article/' + id,
    data: data,
    success: function(json) {
      btn.removeClass('disabled');
      if(!json.code) {
        toastr.success('Cập nhật tin tức thành công');
        reloadPage();
      } else toastr.error('Có lỗi xảy ra, xin vui lòng thử lại');
    }
  });
});


$(document).on('click', '.btn-remove-article', function() {
  var id = $(this).data('id');
  var tr = $(this).closest('tr');
  if (confirm('Xóa bài viết')) {
    $.ajax({
			type: 'DELETE',
			url: '/admin/article/' + id,
			success: function(json) {
				if(!json.code) {
          toastr.success('Xóa bài viết thành công');
          tbl.row(tr).remove().draw();
        }
        else toastr.error('Có lỗi xảy ra, xin vui lòng thử lại');
			}
		});
  }
})


$(window).click(function(e) {
  if ($(e.target).closest('.search-article').length > 0) return;
  $('.search-article').find('ul').hide();
});

var searchRequest = null;
$('input[name=related]').keyup(function() {
  var id = '';
  if($(this).data('id')) id = $(this).data('id');
  var title = $(this).val();
  if (title.length === 0) {
    $('ul.search-result').hide();
    return;
  }
  if (searchRequest != null) searchRequest.abort();
  searchRequest = $.get('/admin/article/search?q=' +title+ '&id='+id, function(json) {
    $('.search-article').find('ul').html('');
    if(!json.code) {
      $.each(json.data, function(index, elem) {
        var article = {};
        article.id = elem.id;
        article.title = elem.title;
        article.link = elem.link;
        var li = tmpl("search-article", article);
        $('.search-article').find('ul').append(li);
        $('.search-article').find('ul').show();
      });
    } else $('.search-article').find('ul').append('<li><a>Không tìm thấy kết quả phù hợp</a></li>');
    $('.search-article').find('ul').show();
  });
});

$(window).scroll(function() {
  $('.search-article').find('ul').hide();
});


$(document).on('click', '.add-article-related', function() {
  var article = {};
  article.id = $(this).closest('li').attr('data-id');
  article.title = $(this).closest('li').find('a').text();
  article.link = $(this).closest('li').find('a').attr('href');
  var item = tmpl("add-article", article);
  $('.list-related').append(item);
});

$('.list-related').on('click', '.btn-remove', function() {
  var item = $(this).closest('.item');
  if(item.attr('data-article_id')) {
    var article_id = item.attr('data-article_id');
    var article_related = item.attr('data-id');
    $.ajax('/admin/article/related/remove?article_id='+article_id + '&article_related='+article_related, function(json){
      if(!json.code) {
        console.log("Đã xóa");
      }
    });
  }
  toastr.success('Đã xóa');
  item.remove();
});

$(document).on('change', 'input[name="updated_at"]', function() {
  var dt = $(this).val();
  if(checkDate(dt) == 'Invalid Date') {
    toastr.error('Vui lòng nhập đúng định dạng ngày giờ (yyyy-mm-dd h:m:s)');
    $(this).addClass('error');
  }
});

$('select[name="type"]').change(function() {
  if($(this).val() == 'khuyen-mai') {
    $('.collection').removeClass('hidden');
    $(".chosen-select").chosen();
    $('.content-promotion').removeClass('hidden');
  }
  else {
    $('.collection').addClass('hidden');
    $('.content-promotion').addClass('hidden');
  }
});

$(document).on('change', 'input[name="title"]', function() {
  var title = $(this).val();
  var handle = convertToHandle(title);
  $(document).find('input[name="handle"]').val(handle);
});
