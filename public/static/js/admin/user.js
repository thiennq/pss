initDataTable('table');

$('.form-add-user').on('submit', function(e) {
  e.preventDefault();
  $(this).find('.btn-admin').addClass('disabled');
  var data = $(this).serialize();
  $.ajax({
    type: 'POST',
    url: '/admin/user',
    data: data,
    success: function(json) {
      if(!json.code) {
        toastr.success('Thêm người dùng thành công');
        setTimeout(function(){
          window.location.reload();
        }, 1000);
      } else if(json.code == -1) toastr.error('Email đã tồn tại');
      else {
        toastr.error('Có lỗi xảy ra, xin vui lòng thử lại');
        setTimeout(function(){
          window.location.reload();
        }, 1000);
      }
    }
  });
});

$('#modal-add').on('hidden.bs.modal', function() {
  $('#modal-add').find('input').val('');
});

$('.btn-remove').click(function(){
	var id = $(this).data('id');
  var tr = $(this).closest('tr');
  if(confirm("Xóa người dùng ?")) {
    $.ajax({
			type: 'DELETE',
			url: '/admin/user/' + id,
			success: function(json) {
				if(!json.code) {
          toastr.success('Xóa thành công');
          tbl.row(tr).remove().draw();
				} else swal('Lỗi', json.message, 'error');
			}
		});
  }
});

$(document).on('click', '.btn-edit', function() {
  var id = $(this).data('id');
  $.ajax({
    type: 'GET',
    url: '/admin/user/' + id,
    success: function(json) {
      if(!json.code) {
        var modal = $('#modal-edit');
        modal.find('.btn-admin').attr('data-id', json.data.id);
        modal.find('input[name="name"]').val(json.data.name);
        modal.find('input[name="email"]').val(json.data.email);
        modal.find('select[name="role"]').val(json.data.role);
        modal.modal('show');
      } else toastr.error('Có lỗi xảy ra, xin vui lòng thử lại');
    }
  });
});

$('.form-edit-user').on('submit', function(e) {
  e.preventDefault();
  $(this).find('.btn-admin').addClass('disabled');
  var id = $(this).find('.btn-admin').attr('data-id');
  var data = $(this).serialize();
  $.ajax({
    type: 'PUT',
    url: '/admin/user/' + id,
    data: data,
    success: function(json) {
      console.log(json);
      if(!json.code) {
        toastr.success('Cập nhật thành công');
        setTimeout(function(){
          window.location.reload();
        }, 1000);
      } else if(json.code == -1) toastr.error('Email đã tồn tại');
      else {
        toastr.error('Có lỗi xảy ra, xin vui lòng thử lại');
        setTimeout(function(){
          window.location.reload();
        }, 1000);
      }
    }
  });
});
