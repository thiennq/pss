initTinymce('#article_content');
initDataTable('table');

$(window).on('load', function() {
  if($('#article_content').data('value')) tinyMCE.get('article_content').setContent($('#article_content').data('value'));
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
  data.blog = $('select[name="blog"]').val();
  data.image = $('input[name="image"]').val();
  data.description = $('textarea[name="description"]').val();
  if(!data.description) {
    toastr.error('Chưa nhập mô tả bài viết');
    return;
  }
  data.description_seo = $('textarea[name="description_seo"]').val();
  data.content = tinyMCE.get('article_content').getContent();
  if(!data.content) {
    toastr.error('Chưa nhập nội dung bài viết');
    return;
  }
  data.display = $('select[name="display"]').val();
  data.display = parseInt(data.display);
  data.meta_robots = $('select[name="meta_robots"]').val();

  btn.addClass('disabled');

  $.ajax({
    type: 'POST',
    url: '/admin/article',
    data: data,
    success: function(json) {
      btn.removeClass('disabled');
      if(!json.code) {
        toastr.success('Thêm tin tức thành công');
        reloadPage('/admin/article/' + json.id);
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
  data.blog = $('select[name="blog"]').val();
  data.description = $('textarea[name="description"]').val();
  if(!data.description) {
    toastr.error('Chưa nhập mô tả bài viết');
    return;
  }
  data.description_seo = $('textarea[name="description_seo"]').val();
  data.content = tinyMCE.get('article_content').getContent();
  if(!data.content) {
    toastr.error('Chưa nhập nội dung bài viết');
    return;
  }
  data.display = $('select[name="display"]').val();
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

$(document).on('change', 'input[name="title"]', function() {
  var title = $(this).val();
  var handle = convertToHandle(title);
  $(document).find('input[name="handle"]').val(handle);
});
