initTinymce('#page_content');
initDataTable('table');

$(document).on('change', '.feature-image', function() {
  if($(this).val()) {
    if(checkExtImage($(this).val())) {
      var form_group = $(this).closest('.form-group');
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
        }
      });
    }
  }
});

$('.btn-create').click(function() {
  var btn = $(this);
  $('input').removeClass('error');
  var data = {};
  data.title = $('input[name="title"]').val();
  if(!data.title.trim().length) {
    toastr.error('Chưa nhập tiêu đề');
    $('input[name="title"]').addClass('error');
    return;
  }
  data.image = $('input[name="image"]').val();
  data.description = $('textarea[name="description"]').val();
  if(!data.description) {
    toastr.error('Chưa nhập mô tả bài viết');
    $('textarea[name="description"]').addClass('error');
    return;
  }
  data.content = tinyMCE.get('page_content').getContent();
  if(!data.content) {
    toastr.error('Chưa nhập nội dung bài viết');
    return;
  }
  data.display = $('select[name="display"]').val();
  data.meta_title = $('input[name="meta_title"]').val();
  data.meta_description = $('textarea[name="meta_description"]').val();
  data.meta_robots = $('select[name="meta_robots"]').val();
  btn.addClass('disabled');
  $.ajax({
    type: 'POST',
    url: '/admin/page',
    data: data,
    success: function(json) {
      btn.removeClass('disabled');
      if(!json.code) {
        toastr.success('Thêm tin tức thành công');
        reloadPage('/admin/page/' + json.id);
      } else if (json.code == -1) {
        toastr.error('Tiêu đề đã tồn tại');
      } else toastr.error('Có lỗi xảy ra, xin vui lòng thử lại');
    }
  });
});

$('.btn-update').click(function() {
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
  data.image = $('input[name="image"]').val();
  data.description = $('textarea[name="description"]').val();
  if(!data.description) {
    toastr.error('Chưa nhập mô tả bài viết');
    $('textarea[name="description"]').addClass('error');
    return;
  }
  data.content = tinyMCE.get('page_content').getContent();
  if(!data.content) {
    toastr.error('Chưa nhập nội dung bài viết');
    return;
  }
  data.display = $('select[name="display"]').val();
  data.meta_title = $('input[name="meta_title"]').val();
  data.meta_description = $('textarea[name="meta_description"]').val();
  data.meta_robots = $('select[name="meta_robots"]').val();
  btn.addClass('disabled');
  $.ajax({
    type: 'PUT',
    url: '/admin/page/' + id,
    data: data,
    success: function(json) {
      btn.removeClass('disabled');
      if(!json.code) {
        toastr.success('Cập nhật tin tức thành công');
        reloadPage();
      } else if (json.code == -1) {
        toastr.error('Tiêu đề đã tồn tại');
      } else toastr.error('Có lỗi xảy ra, xin vui lòng thử lại');
    }
  });
});


$(document).on('click', '.btn-remove', function() {
  var id = $(this).data('id');
  var tr = $(this).closest('tr');
  if (confirm('Xóa trang')) {
    $.ajax({
			type: 'DELETE',
			url: '/admin/page/' + id,
			success: function(json) {
				if(!json.code) {
          toastr.success('Xóa bài viết thành công');
          tbl.row(tr).remove().draw();
        } else toastr.error('Có lỗi xảy ra, xin vui lòng thử lại');
			}
		});
  }
})
