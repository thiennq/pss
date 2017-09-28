$(document).ready(function() {
  var userInfo = JSON.parse(localStorage.getItem('userInfo'));
  if (userInfo) {
    $('.form-comment').find('input[name=email]').val(userInfo.email);
    $('.form-comment').find('input[name=name]').val(userInfo.name);
  }
});

$(document).on('click', '.reply-comment', function() {
  var product_id = $(this).data('product_id');
  var parent_id = $(this).data('parent_id');
  // var reply_frame = $('.list-comment').find('#reply-comment');
  var reply_comment = tmpl("add-reply-comment");
  $(this).find('.reply-wrapper').append(reply_comment);
});

$(document).on('click', '.btn-add-comment', function() {
  var data = {};
  data.email = $('.form-comment').find('input[name=email]').val();
  data.name = $('.form-comment').find('input[name=name]').val();
  // Save to localStorage
  localStorage.setItem('userInfo', JSON.stringify(data));
  data.content = $('.form-comment').find('#comment').val();
  if (data.email == "" || data.name == "" || data.content == "") {
    toastr.error("Vui lòng điền đủ thông tin");
    return;
  }
  data.display = false;
  data.product_id = $(this).data('product_id');
  data.parent_id = $(this).data('parent_id') ? $(this).data('parent_id') : '0';
  $.ajax({
    type: 'POST',
    url: '/api/add-comment',
    data: data,
    success: function(json) {
      if(!json.code) {
          toastr.error('Gửi bình luận thành công, đang chờ Admin kiểm duyệt');
          // var comment = {};
          // comment.id = json.data['id'];
          // comment.name = json.data['name'];
          // comment.avarta = json.data['avarta'];
          // comment.content = json.data['content'];
          // console.log(comment);
          // var comment_item = tmpl("add-new-comment", comment);
          // $('.list-comment').prepend(comment_item);
          // $(comment_item).find('.reply-comment').attr('data-product_id', data.product_id);
          // $(comment_item).find('.reply-comment').attr('data-parent_id', data.parent_id);
          // $('.form-comment').find('#comment').val("");
      } else toastr.error("Có lỗi xảy ra, xin vui lòng thử lại");
    }
  })
});
