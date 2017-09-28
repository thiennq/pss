$('.btn-update-information').click(function() {
	var data = {};
	data.google_analytics = $(document).find('textarea[name="google-analytics"]').val();
	data.google_adwords = $(document).find('textarea[name="google-adwords"]').val();
	data.facebook_pixels = $(document).find('textarea[name="facebook-pixels').val();
	data.shipping_price = $(document).find('input[name="shipping_price').val();
	if(!data.shipping_price) data.shipping_price = 0;
	$.ajax({
		type: 'PUT',
		url: '/admin/information/meta',
		data: data,
		success: function(json) {
			toastr.success('Thêm thành công');
		}
	});
});
