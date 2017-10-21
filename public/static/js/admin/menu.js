initDataTable('table');

$('select[name="menu-type"]').on('change', function() {
  var type = $(this).val();
  var check = false;
  if(type == 'custom') {
    $(this).closest('.menu-link').find('select[name="menu-collection"]').addClass('hidden');
    $(this).closest('.menu-link').find('select[name="menu-article"]').addClass('hidden');
    $(this).closest('.menu-link').find('select[name="menu-page"]').addClass('hidden');
    $(this).closest('.menu-link').find('input[name="menu-link"]').removeClass('hidden');
  } else if (type == 'collection') {
    $(this).closest('.menu-link').find('select[name="menu-collection"]').removeClass('hidden');
    $(this).closest('.menu-link').find('select[name="menu-article"]').addClass('hidden');
    $(this).closest('.menu-link').find('select[name="menu-page"]').addClass('hidden');
    $(this).closest('.menu-link').find('input[name="menu-link"]').addClass('hidden');
    check = true;
  } else if (type == 'thong-tin') {
    $(this).closest('.menu-link').find('select[name="menu-page"]').removeClass('hidden');
    $(this).closest('.menu-link').find('select[name="menu-article"]').addClass('hidden');
    $(this).closest('.menu-link').find('select[name="menu-collection"]').addClass('hidden');
    $(this).closest('.menu-link').find('input[name="menu-link"]').addClass('hidden');
    check = true;
  } else {
    $(this).closest('.menu-link').find('select[name="menu-article"]').removeClass('hidden');
    $(this).closest('.menu-link').find('select[name="menu-collection"]').addClass('hidden');
    $(this).closest('.menu-link').find('select[name="menu-page"]').addClass('hidden');
    $(this).closest('.menu-link').find('input[name="menu-link"]').addClass('hidden');
    check = true;
  }
  if (check) {
    $.get('/admin/menu/list-menu/' + type, function(json) {
      var options = '';
      $.each(json.data, function(i,e) {
        if(type == 'collection') {
          options += '<option value="'+e.link+'">'+e.breadcrumb+'</option>';
          $('.menu-link').find('select[name="menu-collection"]').html(options);
        } else if(type == "tin-tuc") {
          options += '<option value="'+e.link+'">'+e.title+'</option>';
          $('.menu-link').find('select[name="menu-article"]').html(options);
        } else if(type == "thong-tin") {
          options += '<option value="'+e.link+'">'+e.title+'</option>';
          $('.menu-link').find('select[name="menu-page"]').html(options);
        }
      });
    });
  }
});

/*$(document).find('select').each(function() {
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
});*/


$('.btn-create-menu').click(function() {
  $(this).addClass('disabled');

  $(document).find('.error').removeClass('error');
  var modal = $(this).closest('.modal');
  var data = {};
  data.title = modal.find('input[name="title"]').val();
  data.parent_id = modal.find('select[name="parent_id"]').val();
  if(!data.title) {
    toastr.error('Chưa nhập tiêu đề');
    modal.find('input[name="title"]').addClass('error');
    return false;
  }
  data.link_type = modal.find('select[name="menu-type"]').val();
  if(data.link_type == "custom") data.link = modal.find('input[name="menu-link"]').val();
  else if (data.link_type == 'collection') data.link = modal.find('select[name="menu-collection"]').val();
  else if (data.link_type == 'tin-tuc') data.link = modal.find('select[name="menu-article"]').val();
  else if (data.link_type == 'thong-tin') data.link = modal.find('select[name="menu-page"]').val();

  $.ajax({
    type: 'POST',
    url: '/admin/menu',
    data: data,
    success: function(json) {
      modal.find('.btn-create-menu').removeClass('disabled');
      if(!json.code) {
        toastr.success('Tạo menu thành công');
        reloadPage();
      } else if(json.code == -1) toastr.error('Tiêu đề đã tồn tại');
      else toastr.error('Có lỗi xảy ra, xin vui lòng thử lại');
    }
  });
});

$('.btn-edit-menu').click(function() {
  var id = $(this).data('id');
  var tr = $(this).closest('tr');
  var modal = $('#modal-update');
  $.get("/admin/menu/"+id, function (json) {
    var data = json.data;
    var link_type = data.link_type;
    if (tr.attr('data-submenu') > 0) {
      modal.find('select[name="parent_id"]').prop('disabled', true);
    }
    if (tr.attr('data-parent_id') == -1) {
      modal.find('select[name="parent_id"] option[value='+id+']').prop('disabled', true);
    }
    modal.find('select[name="parent_id"]').val(data.parent_id);
    modal.find('input[name="title"]').val(data.title);
    modal.find('select[name="menu-type"]').val(data.link_type);
    if(link_type == 'custom') {
      modal.find('input[name="menu-link"]').val(data.link);
      modal.find('select[name="menu-article"]').addClass('hidden');
      modal.find('select[name="menu-collection"]').addClass('hidden');
      modal.find('select[name="menu-page"]').addClass('hidden');
      modal.find('input[name="menu-link"]').removeClass('hidden');
    } else if (link_type == 'collection') {
      modal.find('select[name="menu-collection"]').val(data.link);
      modal.find('select[name="menu-collection"]').removeClass('hidden');
      modal.find('select[name="menu-article"]').addClass('hidden');
      modal.find('input[name="menu-link"]').addClass('hidden');
    } else if (link_type == 'tin-tuc') {
      modal.find('select[name="menu-article"]').val(data.link);
      modal.find('select[name="menu-article"]').removeClass('hidden');
      modal.find('select[name="menu-collection"]').addClass('hidden');
      modal.find('input[name="menu-link"]').addClass('hidden');
      modal.find('select[name="menu-page"]').addClass('hidden');
    } else if (link_type == 'thong-tin') {
      modal.find('select[name="menu-page"]').val(data.link);
      modal.find('select[name="menu-page"]').removeClass('hidden');
      modal.find('select[name="menu-article"]').addClass('hidden');
      modal.find('select[name="menu-collection"]').addClass('hidden');
      modal.find('input[name="menu-link"]').addClass('hidden');
    }
    modal.find('.btn-update-menu').attr('data-id', id);
    modal.modal('show');
  });
});

$('.btn-update-menu').click(function() {
  $(this).addClass('disabled');
  var id = $(this).data('id');
  var modal = $(this).closest('.modal');
  modal.find('.error').removeClass('error');
  var data = {};
  data.title = modal.find('input[name="title"]').val();
  data.parent_id = modal.find('select[name="parent_id"]').val();
  if(!data.title) {
    toastr.error('Chưa nhập tiêu đề');
    modal.find('input[name="title"]').addClass('error');
    return false;
  }
  data.link_type = modal.find('select[name="menu-type"]').val();
  if(data.link_type == "custom") data.link = modal.find('input[name="menu-link"]').val();
  else if (data.link_type == 'collection') data.link = modal.find('select[name="menu-collection"]').val();
  else if (data.link_type == 'tin-tuc') data.link = modal.find('select[name="menu-article"]').val();
  else if (data.link_type == 'thong-tin') data.link = modal.find('select[name="menu-page"]').val();
  if(!data.link) {
    toastr.error('Chưa nhập địa chỉ web');
    return false;
  }
  $.ajax({
    type: 'PUT',
    url: '/admin/menu/' + id,
    data: data,
    success: function(json) {
      if(!json.code) toastr.success('Cập nhật thành công');
      else if(json.code == -1) toastr.error('Tiêu đề đã tồn tại');
      else toastr.error('Có lỗi xảy ra, xin vui lòng thử lại');
    }
  });
});

$(document).on('click', '.btn-remove-menu', function() {
  var id = $(this).data('id');
  var submenu = $(this).closest('tr').attr('data-submenu');
  var message = (submenu > 0) ? "Khi xóa menu này, các menu con cũng sẽ bị xóa theo!\nBạn có chắc chắn muốn xóa menu?" : "Xóa menu này?";
  if(confirm(message)) {
    var tr = $(this).closest('tr');
    var parent_id = tr.data('parent_id');
    var trs = $('tr[data-parent_id='+id+']');
    $.ajax({
      type: 'DELETE',
      url: '/admin/menu/' + id,
      success: function(json) {
        if(!json.code) {
          toastr.success('Đã xóa');
          tbl.row(tr).remove().draw();
          if (parent_id != -1 ) {
            tr = $('tr[data-id='+parent_id+']');
            var submenu = tr.attr('data-submenu');
            tr.attr('data-submenu', submenu - 1);
          }
          if (trs) {
            trs.each(function (i, e) {
              tbl.row(e).remove().draw();
            });
          }
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
