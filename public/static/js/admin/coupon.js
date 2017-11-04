initDataTable('table');

$('.btn-create-coupon').click(function() {
  $(this).addClass('disabled');
  $(document).find('.error').removeClass('error');
  var modal = $(this).closest('.modal');
  var data = {};
  data.title = modal.find('input[name="title"]').val();
  data.code = modal.find('input[name="code"]').val();
  data.type = modal.find('select[name="type"]').val();
  data.value = modal.find('input[name="value"]').val();
  data.min_value_order = modal.find('input[name="min_value_order"]').val();
  data.usage_left = modal.find('input[name="usage_left"]').val();
  data.description = modal.find('input[name="description"]').val();
  data.expired_date = modal.find('input[name="expired_date"]').val();
  if(!data.title) {
    toastr.error('Chưa nhập tiêu đề');
    modal.find('input[name="title"]').addClass('error');
    return false;
  }
  if(!data.code) {
    toastr.error('Chưa nhập mã');
    modal.find('input[name="code"]').addClass('error');
    return false;
  }
  if(!data.value) {
    toastr.error('Chưa nhập giá trị');
    modal.find('input[name="value"]').addClass('error');
    return false;
  }
  if(!data.min_value_order) {
    toastr.error('Chưa nhập giá trị đơn hàng tối thiểu');
    modal.find('input[name="min_value_order"]').addClass('error');
    return false;
  }
  if(!data.usage_left) {
    toastr.error('Chưa nhập số lượng mã');
    modal.find('input[name="usage_left"]').addClass('error');
    return false;
  }
  if(!data.expired_date) {
    toastr.error('Chưa nhập hạn dùng');
    modal.find('input[name="expired_date"]').addClass('error');
    return false;
  }
  $.ajax({
    type: 'POST',
    url: '/admin/coupon',
    data: data,
    success: function(json) {
      modal.find('.btn-create-coupon').removeClass('disabled');
      if(!json.code) {
        toastr.success('Tạo coupon thành công');
        reloadPage();
      } else if(json.code == -1) toastr.error('Coupon đã tồn tại');
      else toastr.error('Có lỗi xảy ra, xin vui lòng thử lại');
    }
  });
});
var id;
$('.btn-edit-coupon').click(function () {
    id = $(this).data('id');
    var modal = $('#modal-update');
    $.ajax({
        type: 'GET',
        url: '/admin/coupon/' + id,
        success: function (json) {
            if (!json.code){
                modal.find('input[name="title"]').val(json.id.title);
                modal.find('input[name="code"]').val(json.id.code);
                modal.find('select[name="type"]').val(json.id.type);
                modal.find('input[name="value"]').val(json.id.value);
                modal.find('input[name="min_value_order"]').val(json.id.min_value_order);
                modal.find('input[name="usage_left"]').val(json.id.usage_left);
                modal.find('input[name="description"]').val(json.id.description);
                modal.find('input[name="expired_date"]').val(json.id.expired_date);
            }
        }
    })
})

$('.btn-update-coupon').click(function() {
    $(this).addClass('disabled');
    $(document).find('.error').removeClass('error');
    var modal = $(this).closest('.modal');
    var data = {};
    data.title = modal.find('input[name="title"]').val();
    data.code = modal.find('input[name="code"]').val();
    data.type = modal.find('select[name="type"]').val();
    data.value = modal.find('input[name="value"]').val();
    data.min_value_order = modal.find('input[name="min_value_order"]').val();
    data.usage_left = modal.find('input[name="usage_left"]').val();
    data.description = modal.find('input[name="description"]').val();
    data.expired_date = modal.find('input[name="expired_date"]').val();
    if(!data.title) {
        toastr.error('Chưa nhập tiêu đề');
        modal.find('input[name="title"]').addClass('error');
        return false;
    }
    if(!data.code) {
        toastr.error('Chưa nhập mã');
        modal.find('input[name="code"]').addClass('error');
        return false;
    }
    if(!data.value) {
        toastr.error('Chưa nhập giá trị');
        modal.find('input[name="value"]').addClass('error');
        return false;
    }
    if(!data.min_value_order) {
        toastr.error('Chưa nhập giá trị đơn hàng tối thiểu');
        modal.find('input[name="min_value_order"]').addClass('error');
        return false;
    }
    if(!data.usage_left) {
        toastr.error('Chưa nhập số lượng mã');
        modal.find('input[name="usage_left"]').addClass('error');
        return false;
    }
    if(!data.expired_date) {
        toastr.error('Chưa nhập hạn dùng');
        modal.find('input[name="expired_date"]').addClass('error');
        return false;
    }
  $.ajax({
    type: 'PUT',
    url: '/admin/coupon/' + id,
    data: data,
    success: function(json) {
      if(!json.code) {
        toastr.success('Cập nhật thành công');
        setTimeout(reloadPage(), 1000);
      }
      else toastr.error('Có lỗi xảy ra, xin vui lòng thử lại');
    }
  });
});

$(document).on('click', '.btn-remove-coupon', function() {
    var id = $(this).data('id');
    var tr = $(this).closest('tr');
    if (confirm('Xóa mã giảm giá')) {
        $.ajax({
            type: 'DELETE',
            url: '/admin/coupon/' + id,
            success: function(json) {
                if(!json.code) {
                    toastr.success('Xóa mã giảm giá thành công');
                    tbl.row(tr).remove().draw();
                } else toastr.error('Có lỗi xảy ra, xin vui lòng thử lại');
            }
        });
    }
})