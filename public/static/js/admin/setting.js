$(document).find('select').each(function() {
  var value = $(this).data('value');
  if (value) $(this).val(value);
});

$('.btn-update-setting-index').click(function() {
  data = {};
  data.index_collection_id_1 = $('select[name="index_collection_id_1"]').val();
  data.index_collection_id_2 = $('select[name="index_collection_id_2"]').val();
  data.index_collection_id_3 = $('select[name="index_collection_id_3"]').val();
  data.index_collection_id_4 = $('select[name="index_collection_id_4"]').val();
  data.index_collection_title_1 = $('input[name="index_collection_title_1"]').val();
  data.index_collection_title_2 = $('input[name="index_collection_title_2"]').val();
  data.index_collection_title_3 = $('input[name="index_collection_title_3"]').val();
  data.index_collection_title_4 = $('input[name="index_collection_title_4"]').val();

  $.ajax({
    type: 'PUT',
    url: '/admin/api/settings/index',
    data: data,
    success: function(json) {
      if(!json.code) toastr.success('Cập nhật thành công');
      else toastr.error('Có lỗi xảy ra, xin vui lòng thử lại');
    }
  });
});



//Desktop
$('.btn-update-setting-desktop').click(function() {
  var data = {};
  var btn = $(this);
  data.banner_shopping_footer = $('input[name="banner_shopping_footer"]').val();
  data.banner_complain_footer = $('input[name="banner_complain_footer"]').val();
  data.banner_saleoff = $('input[name="banner_saleoff"]').val();
  data.hotline1 = $('input[name="hotline1"]').val();
  data.hotline2 = $('input[name="hotline2"]').val();
  data.sale_policy = $('textarea[name="sale_policy"]').val();
  data.footer1 = $('textarea[name="footer1"]').val();
  data.footer2 = $('textarea[name="footer2"]').val();
  btn.addClass('disabled');
  $.ajax({
    type: 'PUT',
    url: '/admin/settings/desktop',
    data: data,
    success: function(json){
      if(!json.code) toastr.success('Cập nhật thông tin thành công');
      else toastr.error('Có lỗi xảy ra, xin vui lòng thử lại');
    }
  });
});

//Meta
$('.btn-update-setting-meta-title').click(function() {
  var data = {};
  var btn = $(this);
  data.meta_title_default = $('input[name="meta_title_default"]').val();
  data.meta_description_default = $('textarea[name="meta_description_default"]').val();
  data.meta_title_new_product = $('input[name="meta_title_new_product"]').val();
  data.meta_description_new_product = $('textarea[name="meta_description_new_product"]').val();
  data.meta_title_saleoff = $('input[name="meta_title_saleoff"]').val();
  data.meta_description_saleoff = $('textarea[name="meta_description_saleoff"]').val();
  data.meta_description_product = $('textarea[name="meta_description_product"]').val();
  btn.addClass('disabled');
  $.ajax({
    type: 'PUT',
    url: '/admin/settings/metaTitle',
    data: data,
    success: function(json){
      if(!json.code) toastr.success('Cập nhật thông tin thành công');
      else toastr.error('Có lỗi xảy ra, xin vui lòng thử lại');
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

$('.btn-update-setting-shipping').click(function() {
  data = {};
  data.free_shipping = $('input[name="free_shipping"]').val();
  data.price_urban = $('input[name="price_urban"]').val();
  data.price_suburban = $('input[name="price_suburban"]').val();
  $.ajax({
    type: 'PUT',
    url: '/admin/api/settings/shipping',
    data: data,
    success: function(json) {
      if(!json.code) toastr.success('Cập nhật thành công');
      else toastr.error('Có lỗi xảy ra, xin vui lòng thử lại');
    }
  });
});

$('.btn-update-setting-livechat').click(function() {
  data = {};
  data.key = 'livechat';
  data.value = $('textarea[name="livechat"]').val();
  $.ajax({
    type: 'POST',
    url: '/admin/meta/saveMeta',
    data: data,
    success: function(json) {
      if(!json.code) toastr.success('Cập nhật thành công');
      else toastr.error('Có lỗi xảy ra, xin vui lòng thử lại');
    }
  });
});
