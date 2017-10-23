$('.btn-update-filter').click(function() {
  var data = [];
  $('.form-filter').find('.form-group').each(function() {
    var obj = {};
    obj.key = $(this).data('name');
    obj.value = $(document).find('input[name="'+obj.key+'"]').val();
    data.push(obj);
  });

  $.ajax({
    type: 'PUT',
    url: '/admin/filter',
    data: {
      data: data
    },
    success: function(json) {
      if(!json.code) toastr.success('Cập nhật thành công');
      else toastr.error('Có lỗi xảy ra. Xin vui lòng thử lại');
    }
  });

});
