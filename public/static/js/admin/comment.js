initDataTable('table');

$('.btn-create-comment').click(function() {
  $(this).addClass('disabled');
  $(document).find('.error').removeClass('error');
  var modal = $(this).closest('.modal');
  var data = {};
  data.name = modal.find('input[name="name"]').val();
  data.phone_number = modal.find('input[name="phone"]').val();
  data.email = modal.find('input[name="email"]').val();
  data.content = modal.find('input[name="content"]').val();
  data.parent_id = -1;
  data.type = 1;
  data.type_id = 0;
  data.status = 0;
  if(!data.name) {
    toastr.error('Chưa nhập tên');
    modal.find('input[name="name"]').addClass('error');
    return false;
  }
  if(!data.content) {
    toastr.error('Chưa nhập nội dung');
    modal.find('input[name="content"]').addClass('error');
    return false;
  }
  $.ajax({
    type: 'POST',
    url: '/api/comment',
    data: data,
    success: function(json) {
      modal.find('.btn-create-comment').removeClass('disabled');
      if(!json.code) {
        toastr.success('Tạo comment thành công');
        reloadPage();
      } else if(json.code == -1) toastr.error('Tiêu đề đã tồn tại');
      else toastr.error('Có lỗi xảy ra, xin vui lòng thử lại');
    }
  });
});

$('.btn-update-comment').click(function() {
  $(this).addClass('disabled');
  var id = $(this).data('id');
  var modal = $(this).closest('.modal');
  modal.find('.error').removeClass('error');
  var data = {};
  data.name = modal.find('input[name="name"]').val();
  data.phone_number = modal.find('input[name="phone"]').val();
  data.email = modal.find('input[name="email"]').val();
  data.content = modal.find('input[name="content"]').val();
  data.parent_id = -1;
  data.type = 1;
  data.type_id = 0;
  data.status = 0;
  if(!data.name) {
    toastr.error('Chưa nhập tên');
    modal.find('input[name="name"]').addClass('error');
    return false;
  }
  if(!data.content) {
    toastr.error('Chưa nhập nội dung');
    modal.find('input[name="content"]').addClass('error');
    return false;
  }
  $.ajax({
    type: 'PUT',
    url: '/admin/comment/' + id,
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

$(document).on('click', '.btn-remove-comment', function() {
    var id = $(this).data('id');
    var tr = $(this).closest('tr');
    if (confirm('Xóa bài viết')) {
        $.ajax({
            type: 'DELETE',
            url: '/admin/comment/' + id,
            success: function(json) {
                if(!json.code) {
                    toastr.success('Xóa bài viết thành công');
                    tbl.row(tr).remove().draw();
                } else toastr.error('Có lỗi xảy ra, xin vui lòng thử lại');
            }
        });
    }
})