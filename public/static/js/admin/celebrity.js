initDataTable('table');

$('#modal-add').on('click', '.btn-add-celebrity', function(){
  var modal = $('#modal-add');
  var data = {};
  data.name = modal.find('input[name="name"]').val();
  data.image = modal.find('input[name="image"]').val();
  if(!data.image) {
    toastr.error('Chưa chọn hình ảnh');
    return;
  }
  data.link = modal.find('input[name="link"]').val();
  data.display = modal.find('select[name="display"]').val();

  $.ajax({
    type: 'POST',
    url: '/admin/celebrity',
    data: data,
    success: function(json) {
      if(!json.code) {
        toastr.success('Thêm thành công');
        location.reload();
      } else toastr.error('Có lỗi xảy ra, xin vui lòng thử lại');
    }
  });
});

$(document).on('click', '.btn-remove-celebrity', function(){
  var id = $(this).attr('data-id');
  var tr = $(this).closest('tr');
  if(confirm("Xóa celebrity?")) {
    $.ajax({
      type: 'DELETE',
      url: '/admin/celebrity/' + id,
      success: function(json) {
        if(!json.code) {
          toastr.success('Xóa thành công');
          tblSlider.row(tr).remove().draw();
        } else toastr.error('Có lỗi xảy ra, xin vui lòng thử lại');
      }
    });
  }
});

$(document).on('click', '.btn-edit-celebrity', function() {
  var id = $(this).data('id');
  $.get('/admin/celebrity/'+id, function(json) {
    var data = json.data;
    $('#modal-edit').find('input[name="name"]').val(data.name);
    $('#modal-edit').find('input[name="name"]').attr('data-id', id);
    $('#modal-edit').find('input[name="image"]').val(data.image);
    $('#modal-edit').find('input[name="link"]').val(data.link);
    $('#modal-edit').find('select[name="display"]').val(data.display);
    $('#modal-edit').find('img').attr('src', '/uploads/'+data.image);
    $('#modal-edit').modal('show');
  });
});

$(document).on('click', '.btn-update-celebrity', function() {
  var modal = $('#modal-edit');
  var id = modal.find('input[name="name"]').attr('data-id');
  var data = {};
  data.name = modal.find('input[name="name"]').val();
  data.image = modal.find('input[name="image"]').val();
  data.link = modal.find('input[name="link"]').val();
  data.display = modal.find('select[name="display"]').val();

  $.ajax({
    type: 'PUT',
    url: '/admin/celebrity/' + id,
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

$(document).on('change', '.upload', function(){
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
          var resize = resizeImage(image, 240);
          var timestamp = new Date() - 0;
          form.find('img').attr('src', '/uploads/' + resize + '?v=' + timestamp);
          form.find('input[name="image"]').val(image);
        } else toastr.error('Có lỗi xảy ra, xin vui lòng thử lại');
      }
    });
  }
});

$('.modal').on('hide.bs.modal', function(){
  $(this).find('input').val('');
  $(this).find('.form-upload-image').find('img').attr('src', '/images/no-image.png');
});
