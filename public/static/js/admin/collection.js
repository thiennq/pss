initTinymce('#collection-description');
initDataTable('table');

$(window).on('load', function() {
  if($('.description').data('description')) tinyMCE.get('collection-description').setContent($('.description').data('description'));
});

$('.btn-create-collection').click(function() {
  var btn = $(this);
  $('input').removeClass('error');
	var data = {};
	data.title = $('input[name="title"]').val();
	if(!data.title.trim().length) {
    toastr.error('Chưa nhập tiêu đề nhóm sản phẩm');
    $('input[name="title"]').addClass('error');
		return;
	}
  if($('input[name="title"]').hasClass('exist')) {
    toastr.error('Nhóm sản phẩm đã tồn tại');
		return;
  }
	data.handle = $('input[name="handle"]').val();
	data.breadcrumb = '';
  data.link = '';
	data.parent_id = $('select[name="parent_id"]').val();
	if(data.parent_id) {
		data.breadcrumb = $('select[name="parent_id"]').find('option[value="'+data.parent_id+'"]').attr('data-breadcrumb');
    data.link = $('select[name="parent_id"]').find('option[value="'+data.parent_id+'"]').attr('data-link');
	}

	if(data.breadcrumb) data.breadcrumb += '/' + data.title;
	else data.breadcrumb = data.title;

	if(data.link) data.link += '/' + data.handle;
	else data.link = data.handle;

	data.image = $('input[name="image"]').val();
  data.banner = $('input[name="banner"]').val();
	data.description = tinyMCE.get('collection-description').getContent();
  data.meta_title = $('textarea[name="meta_title"]').val();
  data.meta_description = $('textarea[name="meta_description"]').val();
  data.show_landing_page = $('select[name="show_landing_page"]').val();
  var arr_tag = [];
  $('.list-tag').find('.item').each(function() {
    var obj = {};
    obj.name = $(this).find('input[name="name_tag"]').val();
    obj.handle = $(this).find('input[name="handle_tag"]').attr('data-handle');
    if(obj.name && obj.handle) arr_tag.push(obj);
  });
  data.arr_tag = arr_tag;
  btn.addClass('disabled');
	$.ajax({
		type: 'POST',
		url: '/admin/collection',
		data: data,
		success: function(json) {
      btn.removeClass('disabled');
			if(!json.code) {
        toastr.success('Tạo nhóm sản phẩm thành công');
        reloadPage('/admin/collections/' + json.id);
			} else toastr.error('Có lỗi xảy ra, xin vui lòng thử lại');
		}
	});
});

$('.btn-update-collection').click(function() {
  var btn = $(this);
	var id = $(this).data('id');
	var data = {};
	data.title = $('input[name="title"]').val();
	data.title = data.title.trim();
  if(!data.title.trim().length) {
    toastr.error('Chưa nhập tiêu đề nhóm sản phẩm');
    $('input[name="title"]').addClass('error');
		return;
	}
  if($('input[name="title"]').hasClass('exist')) {
    toastr.error('Nhóm sản phẩm đã tồn tại');
		return;
  }

  data.handle = $('input[name="handle"]').val();
  data.breadcrumb = '';
  data.link = '';
	data.parent_id = $('select[name="parent_id"]').val();
	if(data.parent_id) {
		data.breadcrumb = $('select[name="parent_id"]').find('option[value="'+data.parent_id+'"]').attr('data-breadcrumb');
    data.link = $('select[name="parent_id"]').find('option[value="'+data.parent_id+'"]').attr('data-link');
	}

	if(data.breadcrumb) data.breadcrumb += '/' + data.title;
	else data.breadcrumb = data.title;

	if(data.link) data.link += '/' + data.handle;
	else data.link = data.handle;

	data.image = $('input[name="image"]').val();
  data.banner = $('input[name="banner"]').val();
	data.description = tinyMCE.get('collection-description').getContent();
  data.meta_title = $('textarea[name="meta_title"]').val();
  data.meta_description = $('textarea[name="meta_description"]').val();
  data.show_landing_page = $('select[name="show_landing_page"]').val();
  var arr_tag = [];
  $('.list-tag').find('.item').each(function() {
    var obj = {};
    obj.name = $(this).find('input[name="name_tag"]').val();
    obj.handle = $(this).find('input[name="handle_tag"]').attr('data-handle');
    if(obj.name && obj.handle) arr_tag.push(obj);
  });
  data.arr_tag = arr_tag;
  btn.addClass('disabled');
	$.ajax({
		type: 'PUT',
		url: '/admin/collections/' + id,
		data: data,
		success: function(json) {
      btn.removeClass('disabled');
			if(!json.code) toastr.success('Cập nhật thành công');
      else toastr.error('Có lỗi xảy ra, xin vui lòng thử lại');
    }
	});
});

$(document).on('click', '.btn-remove-collection', function() {
	var id = $(this).data('id');
  var tr = $(this).closest('tr');
  var template = $(this).data('template');
  if(confirm("Xóa nhóm sản phẩm?")) {
    $.ajax({
			type: 'DELETE',
			url: '/admin/collections/' + id,
			success: function(json) {
				if(!json.code) {
					toastr.success('Xóa nhóm sản phẩm thành công');
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
          if(type == "banner") {
            var resize = resizeImage(image, 640);
            form.find('input[name="banner"]').val(image);
          } else {
            var resize = resizeImage(image, 240);
            form.find('input[name="image"]').val(image);
          }
          form.find('img').attr('src', '/uploads/' + resize + '?v=' + timestamp);
  			} else toastr.error('Có lỗi xảy ra, xin vui lòng thử lại');
      }
    });
  }
});

$(document).on('change', 'input[name="title"]', function() {
  $(document).find('.exist').removeClass('exist');
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
  var parent_id = $('select[name="parent_id"]').val();
  $.ajax ({
		type : 'POST',
    url : '/admin/api/create-handle-collection',
		data : {
			title: title,
			parent_id: parent_id
		},
		success : function(handle) {
      if(!handle) {
        toastr.error('Nhóm sản phẩm đã tồn tại');
        $(document).find('input[name="title"]').addClass('exist');
      }
      else $(document).find('input[name="handle"]').val(handle);
		}
	});
});

$(document).on('change', 'select[name="parent_id"]', function() {
  $(document).find('.exist').removeClass('exist');
	var title = $(document).find('input[name="title"]').val();
  var data_title = $(document).find('input[name="title"]').data('title');
  var data_handle = $(document).find('input[name="title"]').data('handle');
  var parent_id = $(this).val();
  if(title && title != data_title) {
    $.ajax ({
  		type : 'POST',
      url : '/admin/api/create-handle-collection',
  		data : {
  			title: title,
  			parent_id: parent_id
  		},
  		success : function(handle) {
        if(!handle) {
          toastr.error('Nhóm sản phẩm đã tồn tại');
          $(document).find('input[name="title"]').addClass('exist');
        }
        else $(document).find('input[name="handle"]').val(handle);
  		}
  	});
  }
});


$('.add-tag').click(function() {
  var item = tmpl("add-new-tag");
  $('.list-tag').append(item);
});

$('.list-tag').on('click', '.btn-remove', function() {
  var id = $(this).data('id');
  var item = $(this).closest('.item');
  item.remove();
  if(id) {
    $.get('/admin/collection/tag/delete?id='+id, function(json) {
      if(!json.code) {
        toastr.success('Đã xóa');
      }
    });
  }
});

$(document).on('change', 'input[name="name_tag"]', function() {
  var title = $(this).val();
  var handle = convertToHandle(title);
  $(this).closest('.item').find('input[name="handle_tag"]').attr('data-handle', handle);
  $(this).closest('.item').find('input[name="handle_tag"]').val('http://mia.vn/tag/' + handle);
});
