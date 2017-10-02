$(document).on('change', '.feature-image', function(){
  if($(this).val()) {
    if(checkExtImage($(this).val())) {
      var form_group = $(this).closest('.form-group');
      form_group.find('.loading').removeClass('hidden');
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
            var image = json.data;
            var resize = resizeImage(image, '240');
            var timestamp = new Date() - 0;
            form.find('img').attr('src', '/uploads/' + resize + '?v=' + timestamp);
            form.find('input[name="featured_image"]').val(image);
          } else toastr.error('Có lỗi xảy ra, xin vui lòng thử lại');
          form_group.find('.loading').addClass('hidden');
        }
      });
    }
  }
});

$('.btn-update-branch').click(function(event) {
  var id = $(this).data('id');
  var btn = $(this);
  $('input').removeClass('error');
  var data = {};
  data.name = $('input[name="name"]').val();
  if(!data.name) {
    toastr.error('Chưa nhập tên chi nhánh');
    $('input[name="name"]').addClass('error');
    return;
  }
  data.address = $('input[name="address"]').val();
  data.region_id = $('select[name="region_id"]').val();
  data.hotline = $('input[name="hotline"]').val();
  data.open_hours = $('input[name="open_hours"]').val();
  data.close_hours = $('input[name="close_hours"]').val();
  data.link = $('input[name="link"]').val();
  data.featured_image = $('input[name="featured_image"]').val();
  if($('input[name="calc_inventory"]').is(':checked')) data.calc_inventory = 1;
  if($('input[name="branch_center"]').is(':checked')) data.branch_center = 1;
  data.display = $('select[name="display"]').val();
  btn.addClass('disabled');
  $.ajax({
    type: 'PUT',
    url: '/admin/branch/' + id,
    data: data,
    success: function(json){
      if(!json.code) toastr.success('Cập nhật chi nhánh thành công');
      else toastr.error('Có lỗi xảy ra, xin vui lòng thử lại');
    }
  });
});
