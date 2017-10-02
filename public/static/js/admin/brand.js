initTinymce('#brand-description');
initDataTable('table');

$('.btn-create-brand').click(function() {
  var btn = $(this);
  $('input').removeClass('error');
	var data = {};
	data.name = $('input[name="name"]').val();
	if(!data.name.trim().length) {
    toastr.error('Chưa nhập tên thương hiệu');
    $('input[name="name"]').addClass('error');
		return;
	}
	data.image = $('input[name="image"]').val();
	data.description = tinyMCE.get('brand-description').getContent();
  data.meta_title = $('textarea[name="meta_title"]').val();
  data.meta_description = $('textarea[name="meta_description"]').val();
  data.highlight = $('select[name="highlight"]').val();
  data.display = $('select[name="display"]').val();
  btn.addClass('disabled');
	$.ajax({
		type: 'POST',
		url: '/admin/brand',
		data: data,
		success: function(json) {
			if(!json.code) {
        toastr.success('Tạo thương hiệu thành công');
				setTimeout(function() {
					window.location.href = '/admin/brands/' + json.id;
				}, 1000);
			} else {
        toastr.error('Thương hiệu đã tồn tại');
      }
		}
	});
});

$('.btn-update-brand').click(function() {
  var btn = $(this);
	var id = $(this).data('id');
	var data = {};
	data.name = $('input[name="name"]').val();
	data.name = data.name.trim();
  if(!data.name.trim().length) {
    toastr.error('Chưa nhập tiêu đề nhóm sản phẩm');
    $('input[name="name"]').addClass('error');
		return;
	}
  data.image = $('input[name="image"]').val();
	data.description = tinyMCE.get('brand-description').getContent();
  data.meta_title = $('textarea[name="meta_title"]').val();
  data.meta_description = $('textarea[name="meta_description"]').val();
  data.highlight = $('select[name="highlight"]').val();
  data.display = $('select[name="display"]').val();

  btn.addClass('disabled');
	$.ajax({
		type: 'PUT',
		url: '/admin/brand/' + id,
		data: data,
		success: function(json) {
			if(!json.code) toastr.success('Cập nhật thành công');
      else toastr.error('Có lỗi xảy ra, xin vui lòng thử lại');
    }
	});
});

$(document).on('click', '.btn-remove-brand', function() {
	var id = $(this).data('id');
  var tr = $(this).closest('tr');
  var template = $(this).data('template');
  if(confirm("Xóa thương hiệu?")) {
    $.ajax({
			type: 'DELETE',
			url: '/admin/brand/' + id,
			success: function(json) {
				if(!json.code) {
					toastr.success('Xóa thương hiệu thành công');
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
          var resize = resizeImage(image, 240);
          form.find('input[name="image"]').val(image);
          form.find('img').attr('src', '/uploads/' + resize + '?v=' + timestamp);
  			} else toastr.error('Có lỗi xảy ra, xin vui lòng thử lại');
      }
    });
  }
});
