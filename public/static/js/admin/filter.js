initDataTable('table');

$('#modal-add').on('click', '.btn-create', function() {
    var modal = $('#modal-add');
    var data = {};
    data.title = modal.find('input[name="title"]').val();
    modal.addClass('disabled');
    $.ajax({
        type: 'POST',
        url: '/admin/filters',
        data: data,
        success: function(json) {
            modal.removeClass('disabled');
            if(!json.code) {
                toastr.success('Thêm thành công');
                reloadPage();
            } else if (json.code == -1) toastr.error("Thuộc tính đã tồn tại");
            else toastr.error('Có lỗi xảy ra, xin vui lòng thử lại');
        }
    });
});

$('.form-edit-filter').on('submit', function(e) {
    e.preventDefault();
    $(this).find('.btn-admin').addClass('disabled');
    var id = $('input[name="id"]').val();
    var data = JSON.stringify( $(this).serializeArray() );
    data = JSON.parse(data);
    data = {
        'title' : data[0].value,
        'handle' : convertToHandle(data[0].value)
    }
    $.ajax({
        type: 'PUT',
        url: '/admin/filter/' + id,
        data: data,
        success: function(json) {
            $('.btn-admin').removeClass('disabled');
            if(!json.code) {
                toastr.success('Chỉnh sửa loại thuộc tính thành công');
                reloadPage();
            } else toastr.error('Có lỗi xảy ra, xin vui lòng thử lại');
        }
    });
});

$('.form-edit-option').on('submit', function(e) {
    e.preventDefault();
    $(this).find('.btn-admin').addClass('disabled');
    var id = $('input[name="id"]').val();
    var data = JSON.stringify( $(this).serializeArray() );
    data = JSON.parse(data);
    data = {
        'value' : data[0].value,
        'handle' : convertToHandle(data[0].value),
    }
    $.ajax({
        type: 'PUT',
        url: '/admin/filter-option/' + id,
        data: data,
        success: function(json) {
            $('.btn-admin').removeClass('disabled');
            if(!json.code) {
                toastr.success('Chỉnh sửa option thành công');
                reloadPage();
            } else toastr.error('Có lỗi xảy ra, xin vui lòng thử lại');
        }
    });
});

$('.add-new-option').on('click', function() {
    var filter = $(this).closest('.filter-card');
    var id = $(filter).attr('id');
    var idInput = "input_" + id;
    var valueOption = $("#" + idInput).val();
    if (!valueOption) alert('Vui lòng nhập giá trị thuộc tính mới!');
    else {
        var data = {
            value : valueOption,
            handle : convertToHandle(valueOption),
            filter_id : id
        }
        $.ajax({
            type : 'POST',
            url : '/admin/filter-option',
            data : data,
            success : function(res) {
                if (!res.code) {
                    toastr.success('Thêm thuộc tính thành công');
                    reloadPage();
                } else if (res.code == -2) {
                    toastr.error('Thuộc tính đã tồn tại. Vui lòng nhập thuộc tính khác');
                } else {
                    toastr.error('Có lỗi xảy ra, vui lòng thử lại.');
                }

            }
        });
    }
});

$('.delete-option').click(function() {
    var id = $(this).attr('id').split('_')[2];
    if (confirm('Xoá option?')) {
        $.ajax({
            type : 'DELETE',
            url : '/admin/filter-option/' + id,
            success : function(json) {
                reloadPage();
                    if (!json.code) {
                        toastr.success('Xoá thuộc tính thành công');
                    } else toastr.error('Xoá thuộc tính thất bại');
            }
        });
    }
});

$('.delete-filter').click(function() {
    var cf = confirm('Xoá bộ lọc?');
    var id = $(this).attr('id').split('_')[2];
    if (cf) {
        $.ajax({
            type : 'DELETE',
            url : '/admin/filter/' + id,
            success : function(res) {
                if (!res.code) {
                    toastr.success('Xoá loại thuộc tính thành công');
                    reloadPage();
                } else toastr.error('Xoá loại thuộc tính thất bại');

            }
        })
    }
})

$('.edit-filter').click(function() {
  var id = $(this).attr('id').split('_')[2];
  $.ajax({
    type : 'GET',
    url : '/admin/filter/' + id,
    success : function(res) {
      if (!res.code) {
        $('input[name="id"]').val(res.data.id);
        $('input[name="title"]').val(res.data.title);
        $('input[name="title_en"]').val(res.data.title_en);
        $('#modal-edit').modal('show');
      }
      else toastr.error('Có lỗi xảy ra.');
    }
  })
});
$('.edit-option').click(function() {
  var id = $(this).attr('id').split('_')[2];
  $.ajax({
    type : 'GET',
    url : '/admin/filter-option/' + id,
    success : function(res) {
      if (!res.code) {
        $('input[name="id"]').val(res.data.id);
        $('input[name="value"]').val(res.data.value);
        $('input[name="value_en"]').val(res.data.value_en);
        $('#modal-edit-option').modal('show');
      }
      else toastr.error('Có lỗi xảy ra.');
    }
  })
});
$('.add-new-option-price').click(function() {
  var valueLow = $("#price-option-low").val();
  var valueHigh = $('#price-option-high').val();
  if (!valueHigh) alert ('Vui lòng nhập giá mới');
  else {
    if ((parseInt(valueHigh) <= parseInt(valueLow)) || (isNaN(valueHigh)))  {
      alert('Giá ko hợp lệ');
    }
    else {
      var valueOption = valueLow + '-' + valueHigh;
      var data = {
        value : valueOption,
      }
      $.ajax({
        type : 'POST',
        url : '/admin/filter-price',
        data : data,
        success : function(res) {
          if (!res.code) {
            toastr.success('Thêm option thành công');
          }
          setTimeout(function(){
            window.location.reload();
          }, 1000);
        }
      })
    }
  }
})
