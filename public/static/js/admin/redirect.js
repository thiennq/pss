initDataTable('table');

$('#modal-add').on('submit', 'form', function(e) {
  e.preventDefault();
  var data = {};
  data.old_url = $(this).find('input[name="old"]').val();
  data.new_url = $(this).find('input[name="new"]').val();
  if(data.old_url == data.new_url) {
    toastr.error("Url không được giống nhau");
    return;
  }
  $.ajax({
		type: 'POST',
		url: '/admin/redirect',
		data: data,
		success: function(json) {
			if(!json.code) {
        toastr.success('Thêm thành công');
        setTimeout(function() {
          location.reload();
        }, 1000);
			} else if (json.code == -1) toastr.error('Url đã tồn tại');
      else toastr.error('Có lỗi xảy ra, xin vui lòng thử lại');
		}
	});
});

$('#modal-edit').on('submit', 'form', function(e) {
  e.preventDefault();
  var id = $(this).attr('data-id');
  var data = {};
  data.old_url = $(this).find('input[name="old"]').val();
  data.new_url = $(this).find('input[name="new"]').val();
  if(data.old_url == data.new_url) {
    toastr.error("Url không được giống nhau");
    return;
  }
  $.ajax({
		type: 'PUT',
		url: '/admin/redirect/' + id,
		data: data,
		success: function(json) {
			if(!json.code) {
        toastr.success('Cập nhật thành công');
        setTimeout(function() {
          location.reload();
        }, 1000);
			} else if (json.code == -1) toastr.error('Url đã tồn tại');
      else toastr.error('Có lỗi xảy ra, xin vui lòng thử lại');
		}
	});
});

$(document).on('click', '.btn-edit', function() {
	var id = $(this).data('id');
  var tr = $(this).closest('tr');
  var modal = $("#modal-edit");
  modal.find('input[name="old"]').val(tr.find('.old').text());
  modal.find('input[name="new"]').val(tr.find('.new').text());
  modal.find('form').attr('data-id', id);
  modal.modal('show');
});


$(document).on('click', '.btn-remove', function() {
	var id = $(this).data('id');
  var tr = $(this).closest('tr');
  if(confirm("Xóa redirect ?")) {
    $.ajax({
			type: 'DELETE',
			url: '/admin/redirect/' + id,
			success: function(json) {
				if(!json.code) {
					toastr.success('Xóa thành công');
          tbl.row(tr).remove().draw();
				} else toastr.error('Có lỗi xảy ra, xin vui lòng thử lại');
			}
		});
  }
});

$('#import').on('submit', function(e) {
  e.preventDefault();
  var file_data = $('#file').prop('files')[0];
  if(!file_data) {
    toastr.error('Chưa chọn file');
    return;
  }
  var form_data = new FormData();
  form_data.append('file', file_data);
  $(this).find('button').addClass('disabled');
  $.ajax({
    type: 'POST',
    url: '/admin/redirect/import',
    data: form_data,
    cache: false,
    contentType: false,
    processData: false,
    success: function(json) {
      console.log(json);
      if(!json.code) toastr.success("Import thành công, số lượng: " + json.count + " dòng");
      else toastr.error("Có lỗi xảy ra, xin vui lòng thử lại");
      setTimeout(function() {
        location.reload();
      }, 1500);
    }
  });
});
