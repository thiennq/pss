initTinymce('#submenu');
initDataTable('table');

$(document).find('select').each(function() {
  if($(this).data('value')) $(this).val($(this).data('value'));
});

$('select[name="link_type"]').on('change', function() {
  var type = $(this).val();
  if(type == 'custom') {
    $(this).closest('.link').find('select[name="link"]').addClass('hidden');
    $(this).closest('.link').find('input[name="link"]').removeClass('hidden');
  } else {
    $(this).closest('.link').find('select[name="link"]').removeClass('hidden');
    $(this).closest('.link').find('input[name="link"]').addClass('hidden');
    $.get('/admin/menu/list-menu/' + type, function(json) {
      var options = '';
      $.each(json.data, function(i,e) {
        if(type == 'collection') options += '<option value="'+e.link+'">'+e.breadcrumb+'</option>';
        else if(type == "article") options += '<option value="'+e.handle+'">'+e.title+'</option>';
      });
      $('.link').find('select[name="link"]').html(options);
    });
  }
});


$('.btn-create-menu').click(function() {
  $(document).find('.error').removeClass('error');
  var data = {};
  data.title = $(document).find('input[name="title"]').val();
  if(!data.title) {
    toastr.error('Chưa nhập tiêu đề');
    $(document).find('input[name="title"]').addClass('error');
    return false;
  }
  data.link_type = $('select[name="link_type"]').val();
  if(data.link_type == "custom") {
    data.link = $(document).find('input[name="link"]').val();
    if(!data.link) {
      toastr.error('Chưa nhập địa chỉ web');
      return false;
    }
  } else data.link = $(document).find('select[name="link"]').val();
  data.submenu = tinyMCE.get('submenu').getContent();
  $(this).addClass('disabled');
  $.ajax({
    type: 'POST',
    url: '/admin/menu',
    data: data,
    success: function(json) {
      if(!json.code) {
        toastr.success('Tạo menu thành công');
        reloadPage('/admin/menus/' + json.id);
      } else toastr.error('Có lỗi xảy ra, xin vui lòng thử lại');
    }
  });
});

$('.btn-update-menu').click(function() {
  var id = $(this).data('id');
  $(document).find('.error').removeClass('error');
  var data = {};
  data.title = $(document).find('input[name="title"]').val();
  if(!data.title) {
    toastr.error('Chưa nhập tiêu đề');
    $(document).find('input[name="title"]').addClass('error');
    return false;
  }
  data.link_type = $('select[name="link_type"]').val();
  if(data.link_type == "custom") {
    data.link = $(document).find('input[name="link"]').val();
    if(!data.link) {
      toastr.error('Chưa nhập địa chỉ web');
      return false;
    }
  } else data.link = $(document).find('select[name="link"]').val();
  data.submenu = tinyMCE.get('submenu').getContent();
  $(this).addClass('disabled');
  $.ajax({
    type: 'PUT',
    url: '/admin/menu/' + id,
    data: data,
    success: function(json) {
      if(!json.code) toastr.success('Cập nhật thành công');
      else toastr.error('Có lỗi xảy ra, xin vui lòng thử lại');
    }
  });
});


$(document).on('click', '.btn-remove', function() {
  var id = $(this).data('id');
  if(confirm("Xóa menu?")) {
    var tr = $(this).closest('tr');
    $.ajax({
      type: 'DELETE',
      url: '/admin/menu/' + id,
      success: function(json) {
        if(!json.code) {
          toastr.success('Đã xóa');
          tbl.row(tr).remove().draw();
        } else toastr.error('Có lỗi xảy ra, xin vui lòng thử lại');
      }
    });
  }
});


$('.btn-save-menu-mobile').click(function() {
  var data = {};
  data.key = 'menu_mobile';
  data.value = $('textarea[name="menu_mobile"]').val();
  $.ajax({
    type: 'POST',
    url: '/admin/meta/saveMeta',
    data: data,
    success: function(json) {
      if(!json.code) toastr.success('Cập nhật thành công');
    }
  })
});
