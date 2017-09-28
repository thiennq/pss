$('#add-blog-modal').on('change', 'input[name="name"]', function() {
    $('input').removeClass('error');
    var data = {};
    data.name = $('input[name="name"]').val();
    createHandle(data.name, function(handle) {
      if(!handle) {
        toastr.error('Tên mục này đã có');
        $('#add-blog-modal').find('input[name="name"]').val('');
        $('#add-blog-modal').find('input[name="handle"]').val('');
      }
      else $('#add-blog-modal').find('input[name="handle"]').val(handle);
      $('input[name="name"]').removeClass('error');
    });
});

// $('#update-blog-modal').on('onchange', 'input[name="name"]', function() {
//     $('input').removeClass('error');
//     var data = {};
//     data.name = $('input[name="name"]').val();
//     data.handle = $('input[name="hanle"]').val();
//     createHandle(data.name, function(handle) {
//       if(!handle) {
//         toastr.error('Tên mục này đã có');
//         $('#update-blog-modal').find('input[name="name"]').val('');
//         $('#update-blog-modal').find('input[name="handle"]').val('');
//       }
//       else $('#update-blog-modal').find('input[name="handle"]').val(handle);
//       $('input[name="name"]').removeClass('error');
//     });
// });

$('#add-blog-modal').on('click', '.btn-add-blog', function(){
  $('input').removeClass('error');
  var data = {};
  data.name = $('input[name="name"]').val();
  data.handle = $('input[name="handle"]').val();
  if(!data.name.trim().length || !data.handle) {
		toastr.error('Vui lòng nhập đủ thông tin');
    $('input').addClass('error');
		return;
	}
  $.ajax({
		type: 'post',
		url: '/admin/blog',
		data: data,
		success: function(json) {
			if(!json.code) {
        toastr.success('Thêm mục thành công');
				setTimeout(function() {
					window.location.href = '/admin/blog';
				}, 1000);
			} else {
        toastr.error('Có lỗi xảy ra, xin vui lòng thử lại');
        btn.removeClass('disabled');
      }
		}
	});
});

$(document).on('click', '.btn-edit-blog', function(){
  var id = $(this).data('id');
  var tr = $(this).closest('tr');
  var name = tr.find('td.name').data('value');
  var handle = tr.find('td.handle').data('value');
  $('#update-blog-modal').find('input[name="name"]').val(name);
  $('#update-blog-modal').find('input[name="name"]').attr('data-id', id);
  $('#update-blog-modal').find('input[name="handle"]').val(handle);
  $('#update-blog-modal').modal('show');
});

$(document).on('click', '.btn-update-blog', function() {
  var modal = $('#update-blog-modal');
  var id = modal.find('input[name="name"]').attr('data-id');
  var data = {};
  data.name = modal.find('input[name="name"]').val();
  data.handle = modal.find('input[name="handle"]').val();
  $.ajax({
    type: 'PUT',
    url: '/admin/blog/' + id,
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

$('.btn-remove-blog').on('click', function(){
  var id = $(this).data('id');
  if (confirm('Xóa mục?')) {
    $.ajax({
			type: 'DELETE',
			url: '/admin/blog/' + id,
			success: function(json) {
				if(!json.code) {
				toastr.success('Xóa mục thành công');
          setTimeout(function(){
						window.location.href = '/admin/blog';
					}, 1000);
				} else toastr.error('Có lỗi xảy ra, xin vui lòng thử lại');
			}
		});
  }
})
