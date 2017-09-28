initTinymce('#page-content');

$('.btn-create-page').click(function() {
	var btn = $(this);
	$('input').removeClass('error');
	var data = {};
	data.title = $('input[name="title"]').val();
	if(!data.title.trim().length) {
		toastr.error('Chưa nhập tiêu đề bài viết');
    $('input[name="title"]').addClass('error');
		return;
	}
	data.handle = $('input[name="handle"]').val();
	data.content = tinyMCE.get('page-content').getContent();
	if(!data.content) {
		toastr.error('Chưa nhập nội dung bài viết');
		return;
	}
	btn.addClass('disabled');
	$.ajax({
		type: 'POST',
		url: '/admin/page',
		data: data,
		success: function(json) {
			if(!json.code) {
        toastr.success('Tạo bài viết thành công');
				setTimeout(function() {
					window.location.href = '/admin/pages/' + json.id;
				}, 1000);
			} else {
        toastr.error('Có lỗi xảy ra, xin vui lòng thử lại');
        btn.removeClass('disabled');
      }
		}
	});
});

$('.btn-update-page').click(function() {
	var id = $(this).data('id');
	var data = {};
	var btn = $(this);
	data.title = $('input[name="title"]').val();
	if(!data.title.trim().length) {
		toastr.error('Chưa nhập tiêu đề bài viết');
    $('input[name="title"]').addClass('error');
		return;
	}
	data.handle = $('input[name="handle"]').val();
	data.content = tinyMCE.get('page-content').getContent();
	if(!data.content) {
		toastr.error('Chưa nhập nội dung bài viết');
		return;
	}
	btn.addClass('disabled');
	$.ajax({
		type: 'PUT',
		url: '/admin/pages/' + id,
		data: data,
		success: function(json) {
			if(!json.code) toastr.success('Cập nhật bài viết thành công');
      else toastr.error('Có lỗi xảy ra, xin vui lòng thử lại');
      btn.removeClass('disabled');
		}
	});
});

$('.btn-remove-page').click(function(){
	var id = $(this).attr('data-id');
  var tr = $(this).closest('tr');
  var template = $(this).data('template');
  if(confirm("Xóa bài viết?")) {
    $.ajax({
			type: 'DELETE',
			url: '/admin/pages/' + id,
			success: function(json) {
				if(!json.code) {
					toastr.success('Xóa bài viết thành công');
          if(template != 'edit') tblPage.row(tr).remove().draw();
          else {
            setTimeout(function(){
  						window.location.href = '/admin/page';
  					}, 1000);
          }
				} else toastr.error('Có lỗi xảy ra, xin vui lòng thử lại');
			}
		});
  }
});


$(document).on('change', 'input[name="title"]', function() {
	var title = $(this).val();
  if(!title) {
    toastr.error('Chưa nhập tiêu đề');
    $(document).find('input[name="handle"]').val('');
    return;
  }
  var data_title = $(this).data('title');
  var data_handle = $(this).data('handle');
  if(title == data_title) {
    $(document).find('input[name="handle"]').val(data_handle);
    return;
  }
  createHandle(title, function(handle) {
    if(!handle) {
      toastr.error('Tên bài viết đã tồn tại');
      $(document).find('input[name="title"]').val('');
      $(document).find('input[name="handle"]').val('');
    }
    else $(document).find('input[name="handle"]').val(handle);
    $('input[name="title"]').removeClass('error');
  });
});
