initTinymce('#content_vi');
initTinymce('#content_en');
initDataTable('table');

$('.box-tree').jstree({
    "core" : {
        "themes" : {
            "variant" : "large"
        }
    }
});

$('.btn-create-update').click(function() {
  var id = $(this).data('id');
  var self = $(this);
  $('input').removeClass('error');
  var data = {};
  data.parent_id = $('select[name="parent_id"]').val();

  data.title = $('#tab-vi').find('input[name="title"]').val();
  if(!data.title.trim().length) {
    toastr.error('Chưa nhập tiêu đề');
    $('input[name="title"]').addClass('error');
    return;
  }
  data.description = $('#tab-vi').find('textarea[name="description"]').val();
  data.content = tinyMCE.get('content_vi').getContent();

  data.meta_title = $('#tab-seo-vi').find('input[name="meta_title"]').val();
  data.meta_description = $('#tab-seo-vi').find('textarea[name="meta_description"]').val();
  data.meta_keyword = $('#tab-seo-vi').find('textarea[name="meta_keyword"]').val();
  data.meta_robots = $('#tab-seo-vi').find('select[name="meta_robots"]').val();

  data.image = $('input[name="image"]').val();
  data.banner = $('input[name="banner"]').val();
  data.display = $('select[name="display"]').val();

  self.addClass('disabled');

  if (id) updateCollection(id, data);
  else createCollection(data);
});

function createCollection(data) {
  $.ajax({
    type: 'POST',
    url: '/admin/collection',
    data: data,
    success: function(json) {
      $(document).find('.disabled').removeClass('disabled');
      if(!json.code) {
        toastr.success('Tạo thành công');
        reloadPage('/admin/collection/' + json.id);
      } else if (json.code == -1) {
        toastr.error('Nhóm sản phẩm đã tồn tại');
      } else if (json.code == -4) {
        toastr.error(json.message);
      } else toastr.error('Có lỗi xảy ra, xin vui lòng thử lại');
    }
  });
}

function updateCollection(id, data) {
  $.ajax({
    type: 'PUT',
    url: '/admin/collection/' + id,
    data: data,
    success: function(json) {
      $(document).find('.disabled').removeClass('disabled');
      if(!json.code) {
        toastr.success('Cập nhật thành công');
        reloadPage();
      } else if (json.code == -1) {
        toastr.error('Nhóm sản phẩm đã tồn tại');
      } else if (json.code == -4) {
        toastr.error(json.message);
      } else toastr.error('Có lỗi xảy ra, xin vui lòng thử lại');
    }
  });
}

$(document).on('click', '.btn-remove', function() {
  var id = $(this).data('id');
  var tr = $(this).closest('tr');
  if(confirm("Xóa nhóm sản phẩm?")) {
    $.ajax({
      type: 'DELETE',
      url: '/admin/collection/' + id,
      success: function(json) {
        if(!json.code) {
          toastr.success('Xóa thành công');
          tbl.row(tr).remove().draw();
        } else toastr.error('Có lỗi xảy ra, xin vui lòng thử lại');
      }
    });
  }
});
