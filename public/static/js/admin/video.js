initDataTable('table');
$('#addVideo-modal').on('click', '.btn-add-video', function(){
  $('input').removeClass('error');
  var data = {};
  data.title = $('input[name="title"]').val();
  data.embed_link = $('input[name="embed_link"]').val();
  if(!data.title.trim().length || !data.embed_link) {
		toastr.error('Vui lòng nhập đủ thông tin');
    $('input[name="title"]').addClass('error');
		return;
	}
  $.ajax({
		type: 'POST',
		url: '/admin/video',
		data: data,
		success: function(json) {
			if(!json.code) {
        toastr.success('Thêm video thành công');
				setTimeout(function() {
					window.location.href = '/admin/video';
				}, 1000);
			} else {
        toastr.error('Có lỗi xảy ra, xin vui lòng thử lại');
        btn.removeClass('disabled');
      }
		}
	});
});

$(document).on('click', '.btn-edit-video', function(){
  var id = $(this).data('id');
  var tr = $(this).closest('tr');
  var title = tr.find('td.title').data('value');
  var embed_link = tr.find('td.embed-link').data('value');
  $('#updateVideo-modal').find('input[name="title"]').val(title);
  $('#updateVideo-modal').find('input[name="title"]').attr('data-id', id);
  $('#updateVideo-modal').find('input[name="embed_link"]').val(embed_link);
  $('#updateVideo-modal').modal('show');
});

$(document).on('click', '.btn-update-video', function() {
  var modal = $('#updateVideo-modal');
  var id = modal.find('input[name="title"]').attr('data-id');
  var data = {};
  data.title = modal.find('input[name="title"]').val();
  data.embed_link = modal.find('input[name="embed_link"]').val();
  $.ajax({
    type: 'PUT',
    url: '/admin/video/' + id,
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

$('.btn-remove-video').on('click', function(){
  var id = $(this).data('id');
  if (confirm('Xóa video?')) {
    $.ajax({
			type: 'DELETE',
			url: '/admin/video/' + id,
			success: function(json) {
				if(!json.code) {
				toastr.success('Xóa video thành công');
          setTimeout(function(){
						window.location.href = '/admin/video';
					}, 1000);
				} else toastr.error('Có lỗi xảy ra, xin vui lòng thử lại');
			}
		});
  }

})
