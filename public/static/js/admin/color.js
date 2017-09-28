initDataTable('table');

$('.btn-add').click(function() {
  var btn = $(this);
  $('input').removeClass('error');
	var data = {};
	data.name = $('input[name="name"]').val();
	if(!data.name.trim().length) {
    toastr.error('Chưa nhập tiêu đề');
    $('input[name="name"]').addClass('error');
		return;
	}
	data.hex = $('input[name="hex"]').val();
	$.ajax({
		type: 'POST',
		url: '/admin/color',
		data: data,
		success: function(json) {
			if(!json.code) {
        toastr.success('Thêm thành công');
        setTimeout(function() {
          location.reload();
        }, 1000);
			} else if(json.code == -1) toastr.error('Tiêu đề đã tồn tại');
      else toastr.error('Có lỗi xảy ra, xin vui lòng thử lại');
		}
	});
});

$(document).on('click', '.btn-edit', function() {
  var id = $(this).data('id');
  var btn = $(this);
  $('.modal-edit').find('input[name="name"]').val(btn.attr("data-name"));
  $('.modal-edit').find('input[name="hex"]').val(btn.attr("data-hex"));
  $('.modal-edit').find('.btn-update').attr('data-id', id);
  $('.modal-edit').modal('show');
});

$('.btn-update').click(function() {
	var id = $(this).data('id');
	var data = {};
	data.name = $('.modal-edit').find('input[name="name"]').val();
  data.hex = $('.modal-edit').find('input[name="hex"]').val();
  if(!data.name.trim().length) {
    toastr.error('Chưa nhập tiêu đề');
		return;
	}
	$.ajax({
		type: 'PUT',
		url: '/admin/color/' + id,
		data: data,
		success: function(json) {
			if(!json.code) {
        toastr.success('Cập nhật thành công');
        setTimeout(function() {
          location.reload();
        }, 1000);
      } else if(json.code ==-1) toastr.error('Tiêu đề đã tồn tại');
      else toastr.error('Có lỗi xảy ra, xin vui lòng thử lại');
    }
	});
});

$(document).on('click', '.btn-remove', function() {
	var id = $(this).data('id');
  var tr = $(this).closest('tr');
  if(confirm("Xóa màu sắc ?")) {
    $.ajax({
			type: 'DELETE',
			url: '/admin/color/' + id,
			success: function(json) {
				if(!json.code) {
					toastr.success('Xóa thành công');
          tbl.row(tr).remove().draw();
				} else toastr.error('Có lỗi xảy ra, xin vui lòng thử lại');
			}
		});
  }
});
