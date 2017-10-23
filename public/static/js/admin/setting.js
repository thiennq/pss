//Website
$('.btn-update-setting-website').click(function() {
  var data = {};
  var btn = $(this);
  data.hotline1 = $('input[name="hotline1"]').val();
  data.hotline2 = $('input[name="hotline2"]').val();
  data.shop_name = $('input[name="shop_name"]').val();
  data.shop_address = $('input[name="shop_address"]').val();

  data.free_shipping = $('input[name="free_shipping"]').val();
  data.price_urban = $('input[name="price_urban"]').val();
  data.price_suburban = $('input[name="price_suburban"]').val();

  data.meta_title_default = $('input[name="meta_title_default"]').val();
  data.meta_description_default = $('textarea[name="meta_description_default"]').val();
  data.facebook_pixel = $('textarea[name="facebook_pixel"]').val();
  data.facebook_image = $('input[name="facebook_image"]').val();

  data.index_collection_id_1 = $('select[name="index_collection_id_1"]').val();
  data.index_collection_id_2 = $('select[name="index_collection_id_2"]').val();
  data.index_collection_id_3 = $('select[name="index_collection_id_3"]').val();
  data.index_collection_title_1 = $('input[name="index_collection_title_1"]').val();
  data.index_collection_title_2 = $('input[name="index_collection_title_2"]').val();
  data.index_collection_title_3 = $('input[name="index_collection_title_3"]').val();

  data.livechat = $('textarea[name="livechat"]').val();
  btn.addClass('disabled');
  $.ajax({
    type: 'PUT',
    url: '/admin/setting',
    data: data,
    success: function(json){
      btn.removeClass('disabled');
      if(!json.code) {
        toastr.success('Cập nhật thành công');
        reloadPage();
      } else toastr.error('Có lỗi xảy ra, xin vui lòng thử lại');
    }
  });
});

$(document).on('change', '.upload', function() {
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
  				var image = json.data;
          var timestamp = new Date() - 0;
          form.find('.upload-value').val(image);
          form.find('img').attr('src', '/uploads/' + image + '?v=' + timestamp);
  			} else toastr.error('Có lỗi xảy ra, xin vui lòng thử lại');
      }
    });
  }
});
