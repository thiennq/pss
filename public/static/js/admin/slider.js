initDataTable('table');

$('#modal-add-slide').on('click', '.btn-add-slide', function(){
  var modal = $('#modal-add-slide');
  var data = {};
  data.image = modal.find('input[name="image"]').val();
  if(!data.image) {
    toastr.error('Chưa chọn hình ảnh');
    return;
  }
  data.title = modal.find('input[name="title"]').val();
  data.link = modal.find('input[name="link"]').val();
  data.display = modal.find('select[name="display"]').val();
  data.display = parseInt(data.display);

  $.ajax({
    type: 'POST',
    url: '/admin/slider',
    data: data,
    success: function(json) {
      if(!json.code) {
        toastr.success('Thêm thành công');
        location.reload();
      } else toastr.error('Có lỗi xảy ra, xin vui lòng thử lại');
    }
  });
});

$(document).on('click', '.btn-remove-slide', function(){
	var id = $(this).attr('data-id');
  var tr = $(this).closest('tr');
  if(confirm("Xóa slider?")) {
    $.ajax({
			type: 'DELETE',
			url: '/admin/slider/' + id,
			success: function(json) {
				if(!json.code) {
					toastr.success('Xóa thành công');
          tblSlider.row(tr).remove().draw();
				} else toastr.error('Có lỗi xảy ra, xin vui lòng thử lại');
			}
		});
  }
});

$(document).on('click', '.btn-edit-slide', function() {
  var id = $(this).data('id');
  $.get('/admin/slider/'+id, function(json) {
    var data = json.data;
    $('#modal-edit-slide').find('input[name="title"]').val(data.title);
    $('#modal-edit-slide').find('input[name="title"]').attr('data-id', id);
    $('#modal-edit-slide').find('input[name="image"]').val(data.image);
    $('#modal-edit-slide').find('input[name="link"]').val(data.link);
    $('#modal-edit-slide').find('select[name="display"]').val(data.display);
    $('#modal-edit-slide').find('img').attr('src', '/uploads/'+data.image);
    $('#modal-edit-slide').modal('show');
  });
});

$(document).on('click', '.btn-update-slide', function() {
  var modal = $('#modal-edit-slide');
  var id = modal.find('input[name="title"]').attr('data-id');
  var data = {};
  data.title = modal.find('input[name="title"]').val();
  data.image = modal.find('input[name="image"]').val();
  data.link = modal.find('input[name="link"]').val();
  data.display = modal.find('select[name="display"]').val();

  $.ajax({
    type: 'PUT',
    url: '/admin/slider/' + id,
    data: data,
    success: function(json) {
      if(!json.code) {
        toastr.success('Cập nhật thành công');
        setTimeout(function(){
          window.location.reload();
        }, 1000);
      } else toastr.error('Có lỗi xảy ra, xin vui lòng thử lại');
    }
  });
});

$(document).on('change', '.upload', function(){
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
  				var resize = resizeImage(image, 240);
          var timestamp = new Date() - 0;
  				form.find('img').attr('src', '/uploads/' + resize + '?v=' + timestamp);
  				form.find('input[name="image"]').val(image);
  			} else toastr.error('Có lỗi xảy ra, xin vui lòng thử lại');
      }
    });
  }
});

$('.modal').on('hide.bs.modal', function(){
  $(this).find('input').val('');
  $(this).find('.form-upload-image').find('img').attr('src', '/static/img/no-image.png');
});
