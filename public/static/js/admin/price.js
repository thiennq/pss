$('.btn-add').click(function() {
  var btn = $(this);
  $('input').removeClass('error');
	var data = {};
	data.price = $('input[name="price"]').val();
	if(!data.price.trim().length) {
    toastr.error('Chưa nhập giá');
    $('input[name="name"]').addClass('error');
		return;
	}
	$.ajax({
		type: 'POST',
		url: '/admin/price',
		data: data,
		success: function(json) {
			if(!json.code) {
        toastr.success('Thêm thành công');
        setTimeout(function() {
          location.reload();
        }, 1000);
			} else if(json.code == -1) toastr.error('Giá đã tồn tại');
      else toastr.error('Có lỗi xảy ra, xin vui lòng thử lại');
		}
	});
});

$(document).on('click', '.btn-edit', function() {
  var id = $(this).data('id');
  var tr = $(this).closest('tr');
  $('.modal-edit').find('input[name="price"]').val(tr.find('.price').text());
  $('.modal-edit').find('.btn-update').attr('data-id', id);
  $('.modal-edit').modal('show');
});

$('.btn-update').click(function() {
	var id = $(this).data('id');
	var data = {};
	data.price = $('.modal-edit').find('input[name="price"]').val();
  if(!data.price.trim().length) {
    toastr.error('Chưa nhập giá');
		return;
	}
	$.ajax({
		type: 'PUT',
		url: '/admin/price/' + id,
		data: data,
		success: function(json) {
			if(!json.code) {
        toastr.success('Cập nhật thành công');
        setTimeout(function() {
          location.reload();
        }, 1000);
      } else if(json.code ==-1) toastr.error('Giá đã tồn tại');
      else toastr.error('Có lỗi xảy ra, xin vui lòng thử lại');
    }
	});
});

$(document).on('click', '.btn-remove', function() {
	var id = $(this).data('id');
  var tr = $(this).closest('tr');
  if(confirm("Xóa giá ?")) {
    $.ajax({
			type: 'DELETE',
			url: '/admin/price/' + id,
			success: function(json) {
				if(!json.code) {
					toastr.success('Xóa thành công');
          tr.remove();
				} else toastr.error('Có lỗi xảy ra, xin vui lòng thử lại');
			}
		});
  }
});
