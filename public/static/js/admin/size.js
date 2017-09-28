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
	$.ajax({
		type: 'POST',
		url: '/admin/size',
		data: data,
		success: function(json) {
			if(!json.code) {
        toastr.success('Thêm thành công');
        reloadPage();
			} else if(json.code == -1) toastr.error('Kích thước đã tồn tại');
      else toastr.error('Có lỗi xảy ra, xin vui lòng thử lại');
		}
	});
});

$(document).on('click', '.btn-edit', function() {
  var id = $(this).data('id');
  var tr = $(this).closest('tr');
  $('.modal-edit').find('input[name="name"]').val(tr.find('.name').text());
  $('.modal-edit').find('.btn-update').attr('data-id', id);
  $('.modal-edit').modal('show');
});

$('.btn-update').click(function() {
	var id = $(this).data('id');
	var data = {};
	data.name = $('.modal-edit').find('input[name="name"]').val();
  if(!data.name.trim().length) {
    toastr.error('Chưa nhập tiêu đề');
		return;
	}
	$.ajax({
		type: 'PUT',
		url: '/admin/size/' + id,
		data: data,
		success: function(json) {
			if(!json.code) {
        toastr.success('Cập nhật thành công');
        reloadPage();
      } else if(json.code ==-1) toastr.error('Kích thước đã tồn tại');
      else toastr.error('Có lỗi xảy ra, xin vui lòng thử lại');
    }
	});
});

$(document).on('click', '.btn-remove', function() {
	var id = $(this).data('id');
  var tr = $(this).closest('tr');
  if(confirm("Xóa chất liệu ?")) {
    $.ajax({
			type: 'DELETE',
			url: '/admin/size/' + id,
			success: function(json) {
				if(!json.code) {
					toastr.success('Xóa thành công');
          tbl.row(tr).remove().draw();
				} else toastr.error('Có lỗi xảy ra, xin vui lòng thử lại');
			}
		});
  }
});
