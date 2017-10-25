initDataTable('table');

$('#modal-add').on('click', '.btn-create', function() {
  var self = $(this);
  var modal = $('#modal-add');
  var data = {};
  data.title = modal.find('input[name="title"]').val();
  if (!data.title) {
    toastr.error("Chưa nhập tiêu đề");
    modal.find('input[name="title"]').addClass('error');
    return;
  }
  self.addClass('disabled');
  $.ajax({
      type: 'POST',
      url: '/admin/filters',
      data: data,
      success: function(json) {
          self.removeClass('disabled');
          if(!json.code) {
              toastr.success('Thêm thành công');
              reloadPage();
          } else if (json.code == -1) toastr.error("Thuộc tính đã tồn tại");
          else toastr.error('Có lỗi xảy ra, xin vui lòng thử lại');
      }
  });
});

$('.add-new-option').click(function() {
  var group = $(this).closest('.group-add-new-option');
  var data = {};
  data.title = group.find('input[name="title"]').val();
  if (!data.title) {
    toastr.error("Chưa nhập tiêu đề");
    group.find('input[name="title"]').addClass('error');
    return;
  }
  data.parent_id = $(this).data('parent_id');
  data.value = group.find('input[name="value"]').val();
  $.ajax({
      type: 'POST',
      url: '/admin/filters',
      data: data,
      success: function(json) {
          if(!json.code) {
              toastr.success('Thêm thành công');
              reloadPage();
          } else if (json.code == -1) toastr.error("Option đã tồn tại");
          else toastr.error('Có lỗi xảy ra, xin vui lòng thử lại');
      }
  });
});

$('.edit-attribute').click(function() {
  var id = $(this).data('id');
  $.ajax({
    type : 'GET',
    url : '/admin/filters/' + id,
    success : function(json) {
      if (!json.code) {
        var modal = $('#modal-edit');
        modal.find('input[name="title"]').val(json.data.title);
        modal.find('.btn-update').attr('data-id', json.data.id);
        $('#modal-edit').modal('show');
      } else toastr.error('Không tìm thấy');
    }
  })
});

$('#modal-edit').on('click', '.btn-update', function() {
  var self = $(this);
  var modal = $('#modal-edit');
  var id = $(this).attr('data-id');
  var data = {};
  data.title = modal.find('input[name="title"]').val();
  if (!data.title) {
    toastr.error("Chưa nhập tiêu đề");
    modal.find('input[name="title"]').addClass('error');
    return;
  }
  self.addClass('disabled');
  $.ajax({
      type: 'PUT',
      url: '/admin/filters/' + id,
      data: data,
      success: function(json) {
        self.removeClass('disabled');
        if(!json.code) {
          toastr.success('Cập nhật thành công');
          reloadPage();
        } else toastr.error('Có lỗi xảy ra, xin vui lòng thử lại');
      }
  });
});

$('.edit-option').click(function() {
  var id = $(this).data('id');
  $.ajax({
    type : 'GET',
    url : '/admin/filters/' + id,
    success : function(json) {
      if (!json.code) {
        var modal = $('#modal-edit-option');
        modal.find('input[name="title"]').val(json.data.title);
        modal.find('input[name="value"]').val(json.data.value);
        modal.find('.btn-update').attr('data-id', json.data.id);
        $('#modal-edit-option').modal('show');
      } else toastr.error('Không tìm thấy');
    }
  })
});

$('#modal-edit-option').on('click', '.btn-update', function() {
  var self = $(this);
  var modal = $('#modal-edit-option');
  var id = $(this).attr('data-id');
  var data = {};
  data.title = modal.find('input[name="title"]').val();
  if (!data.title) {
    toastr.error("Chưa nhập tiêu đề");
    modal.find('input[name="title"]').addClass('error');
    return;
  }
  data.value = modal.find('input[name="value"]').val();
  self.addClass('disabled');
  $.ajax({
      type: 'PUT',
      url: '/admin/filters/' + id,
      data: data,
      success: function(json) {
          self.removeClass('disabled');
          if(!json.code) {
            toastr.success('Cập nhật thành công');
            reloadPage();
          } else toastr.error('Có lỗi xảy ra, xin vui lòng thử lại');
      }
  });
});


$('.delete-filter').click(function() {
  var card = $(this).closest('.filter-card');
  if ($(this).data('option')) card = $(this).closest('.clearfix');
  var id = $(this).data('id');
  if (confirm('Xóa thuộc tính')) {
    $.ajax({
      type: 'DELETE',
      url: '/admin/filters/' + id,
      success: function(json) {
        if (!json.code) {
          toastr.success('Xóa thành công');
          card.remove();
          reloadPage();
        } else toastr.error('Có lỗi xảy ra, xin vui lòng thử lại');
      }
    })
  }
})
