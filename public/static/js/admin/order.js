$('.update-order-status').click(function() {
  var id = $(this).data('id');
  var order_status = $('select[name="order_status"]').val();
  $(this).addClass('disabled');
  $.ajax({
    type: 'PUT',
    url: '/admin/orders/' + id,
    data: {
      order_status: order_status
    },
    success: function(json) {
      if(!json.code) toastr.success('Đã cập nhật thành công');
      else toastr.error('Có lỗi xảy ra, xin vui lòng thử lại');
      setTimeout(function() {
        window.location.reload();
      }, 1000);
    }
  });
});

$('.btn-update-order').click(function() {
  var id = $(this).data('id');
  var payment = $(document).find('select[name="payment_status"]').val();
  var shipping = $(document).find('select[name="shipping_status"]').val();

  if(payment == init_payment && shipping == init_shipping) {
    toastr.error('Đơn hàng không được cập nhật');
    return;
  }
  $(this).addClass('disabled');
  $.ajax({
    type: 'PUT',
    url: '/admin/orders/' + id,
    data: {
      payment_status: payment,
      shipping_status: shipping
    },
    success: function(json) {
      if(!json.code) toastr.success('Đã cập nhật thành công');
      else toastr.error('Có lỗi xảy ra, xin vui lòng thử lại');
      setTimeout(function() {
        window.location.reload();
      }, 1000);
    }
  });
});

$('.btn-cancel-order').click(function() {
  var id = $(this).data('id');

  var text = prompt("Nhập lý do hủy đơn hàng");
  if(text == '') {
    toastr.error('Chưa nhập lý do hủy đơn hàng');
    return;
  }
  var reason = 'Đơn hàng đã bị hủy. Lý do: ' + text;
  $.ajax({
    type: 'PUT',
    url: '/admin/orders/' + id,
    data: {
      order_status: 'cancel',
      reason: reason
    },
    success: function(json) {
      if(!json.code) {
        toastr.success('Đã hủy đơn hàng thành công');
        setTimeout(function() {
          window.location.reload();
        }, 1000);
      }
    }
  });
});

$('.btn-add-history').click(function() {
  var id = $(this).data('id');
  var content = $(document).find('input[name="notes-content"]').val();
  if(!content) {
    toastr.error('Chưa nhập nội dung');
    return;
  }
  $.ajax({
    type: 'POST',
    url: '/admin/history',
    data: {
      id: id,
      content: content
    },
    success: function(json) {
      if(!json.code) {
        toastr.success('Thêm ghi chú thành công');
        setTimeout(function(){
          window.location.reload();
        }, 1000);
      }
    }
  });
});

$('.btn-update-state').click(function() {
  var id = $(this).data('id');
  $.get('/api/crawler/updateOrderState/'+id, function(json) {
    if (!json.code) {
      toastr.success('Cập nhật đơn hàng thành công');
      setTimeout(function(){
        location.reload();
      }, 1000);
    }
  });
});
