initTinymce('#content');
initDataTable('table');

$(document).ready(function() {
  var parent_id = $('select[name="parent_id"]').data('value');
  if (parent_id) $('select[name="parent_id"]').val(parent_id);
});

$('.btn-create').click(function() {
  var self = $(this);
  $('input').removeClass('error');
	var data = {};
  data.title = $('input[name="title"]').val();
	if(!data.title.trim().length) {
    toastr.error('Chưa nhập tiêu đề');
    $('input[name="title"]').addClass('error');
		return;
  }
  data.handle = handle(data.title);
	data.breadcrumb = data.title;
  data.link = data.handle;
	data.parent_id = $('select[name="parent_id"]').val();
	if(data.parent_id != "-1") {
    data.breadcrumb = $('select[name="parent_id"]').find('option[value="'+data.parent_id+'"]').attr('data-breadcrumb');
    data.breadcrumb += '/' + data.title;
    data.link = $('select[name="parent_id"]').find('option[value="'+data.parent_id+'"]').attr('data-link');
    data.link += '/' + data.handle;
	}

	data.image = $('input[name="image"]').val();
  data.banner = $('input[name="banner"]').val();
  data.description = $('textarea[name="description"]').val();
	data.content = tinyMCE.get('content').getContent();
  data.meta_title = $('input[name="meta_title"]').val();
  data.meta_description = $('textarea[name="meta_description"]').val();
  self.addClass('disabled');
	$.ajax({
		type: 'POST',
		url: '/admin/collection',
		data: data,
		success: function(json) {
      self.removeClass('disabled');
			if(!json.code) {
        toastr.success('Tạo thành công');
        reloadPage('/admin/collections/' + json.id);
			} else toastr.error('Có lỗi xảy ra, xin vui lòng thử lại');
		}
	});
});

$('.btn-update').click(function() {
  var self = $(this);
  var id = $(this).data('id');
  $('input').removeClass('error');
	var data = {};
  data.title = $('input[name="title"]').val();
	if(!data.title.trim().length) {
    toastr.error('Chưa nhập tiêu đề');
    $('input[name="title"]').addClass('error');
		return;
  }
  data.handle = handle(data.title);
	data.breadcrumb = data.title;
  data.link = data.handle;
	data.parent_id = $('select[name="parent_id"]').val();
	if(data.parent_id != "-1") {
    data.breadcrumb = $('select[name="parent_id"]').find('option[value="'+data.parent_id+'"]').attr('data-breadcrumb');
    data.breadcrumb += '/' + data.title;
    data.link = $('select[name="parent_id"]').find('option[value="'+data.parent_id+'"]').attr('data-link');
    data.link += '/' + data.handle;
	}

	data.image = $('input[name="image"]').val();
  data.banner = $('input[name="banner"]').val();
  data.description = $('textarea[name="description"]').val();
	data.content = tinyMCE.get('content').getContent();
  data.meta_title = $('input[name="meta_title"]').val();
  data.meta_description = $('textarea[name="meta_description"]').val();
  self.addClass('disabled');
	$.ajax({
		type: 'PUT',
		url: '/admin/collections/' + id,
		data: data,
		success: function(json) {
      self.removeClass('disabled');
			if(!json.code) {
        toastr.success('Cập nhật thành công');
        reloadPage();
			} else if (json.code == -1) {
        toastr.error('Nhóm sản phẩm đã tồn tại');
      } else toastr.error('Có lỗi xảy ra, xin vui lòng thử lại');
		}
	});
});

$(document).on('click', '.btn-remove', function() {
	var id = $(this).data('id');
  var tr = $(this).closest('tr');
  if(confirm("Xóa nhóm sản phẩm?")) {
    $.ajax({
			type: 'DELETE',
			url: '/admin/collections/' + id,
			success: function(json) {
				if(!json.code) {
					toastr.success('Xóa thành công');
          tbl.row(tr).remove().draw();
				} else toastr.error('Có lỗi xảy ra, xin vui lòng thử lại');
			}
		});
  }
});


$(document).on('change', '.upload', function() {
  var type = $(this).data('type');
  if(checkExtImage($(this).val())) {
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
          var timestamp = new Date() - 0;
          if(type == "banner") {
            var resize = resizeImage(image, 640);
            form.find('input[name="banner"]').val(image);
          } else {
            var resize = resizeImage(image, 240);
            form.find('input[name="image"]').val(image);
          }
          form.find('img').attr('src', '/uploads/' + resize + '?v=' + timestamp);
  			} else toastr.error('Có lỗi xảy ra, xin vui lòng thử lại');
      }
    });
  }
});