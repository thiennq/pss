initTinymce('#product-description');
/*initTinymce('#product-specification');

$(window).on('load', function() {
  if($('.specification') && !$('.specification').data('value')) {
    var init = $('.specification').data('init');
    tinyMCE.get('product-specification').setContent(init);
  }
});*/


$('.btn-add-variant').click(function() {
  var obj = {};
  obj.id = $('.variant-item').length + 1;
  obj.static = staticURI;
  var variant = tmpl("add-variant", obj);
  $('.list-append').append(variant);
});

function readURL(files, callback) {
  function loadOne(file) {
    var reader = new FileReader();
    reader.onload = function(e) {
      var imgData = reader.result;
      output.push(imgData);
      if (output.length == files.length) {
        callback(output);
      }
    };
    reader.readAsDataURL(file);
  }
  var output = [];
  for (var i = 0; i < files.length; i++) {
    loadOne(files[i]);
  }
}

$(document).on('change', '.upload-list-image', function(){
  if($(this).val()) {
    var variant = $(this).closest('.variant-item');
    var files = this.files;
    readURL(files, function(imgsData) {
      $.each(imgsData, function(i, imgData) {
        var obj = {};
        obj.index = i;
        obj.src = imgData;
        var item_image = tmpl("item-image", obj);
        variant.find('.add-image').before(item_image);
      });
    });
  }
});

function uploadImg(formData, callback) {
  $.ajax({
    type: 'POST',
    url: '/admin/api/uploadImage',
    data: formData,
    cache: false,
    contentType: false,
    processData: false,
    success: function(json) {
      callback(json);
    }
  });
}

function uploadImgs($form, callback) {
  var files = $form.prop('files');
  var formData = new FormData();
  for (var i = 0; i < files.length; i++) {
   var f = files[i];
   if (!f.deleted) {
    formData.append('upload[]', f, f.name);
   }
  }
  uploadImg(formData, function(json) {
    var list_image = [];
    if(!json.code) {
      obj = json.data;
      $.each(obj, function(i,e) {
        list_image.push(e);
      });
    }
    callback(list_image);
  });
}

$('.btn-create-product').click(function() {
  $('.btn-create-product').addClass('disabled');
  var product = {};
  product.title = $('input[name="title"]').val();
  product.collection = $('select[name="collection"]').val();
  product.description = tinyMCE.get('product-description').getContent();
  product.meta_description = $('textarea[name="meta-description"]').val();
  product.meta_robots = $('select[name="meta-robots"]').val();
  product.display = $('select[name="display"]').val();
  $.ajax({
    type: 'POST',
    url: '/admin/product',
    data: product,
    success: function(json) {
      if(!json.code) {
        var $list_variant = $('.variant-item');
        var count = 0;
        var variant = {};
        variant.product_id = json.data;
        createVariant();
        function createVariant() {
          if (count ==  $list_variant.length) {
            $('.btn-create-product').removeClass('disabled');
            reloadPage();
            return false;
          }
          var itemVariant = $list_variant.eq(count);
          var $formRI = itemVariant.find('.upload-list-image');
          count++;
          variant.title = itemVariant.find('input[name="variant-name"]').val();
          variant.price = itemVariant.find('input[name="variant-price"]').val();
          variant.price_compare = itemVariant.find('input[name="variant-price-compare"]').val();
          variant.inventory = itemVariant.find('input[name="variant-inventory"]').val();
          uploadImgs($formRI, function(list_image) {
            variant.list_image = list_image;
            $.ajax({
              type: 'POST',
              url: '/admin/variant',
              data: data,
              success: function(json) {
                if(!json.code) {
                  toastr.success('Tạo variant ' +variant.title+' thành công');
                  createProduct();
                } else toastr.error('Tạo variant ' +variant.title+ ' thất bại');
              }
            });
          });
        }
      } 
      else {
        $('.btn-create-product').removeClass('disabled');
        toastr.error('Tạo sản phẩm ' +product.title+ ' thất bại');
      } 
    }
  });
});

$(document).on('click', '.btn-rotate-image', function(){
  var img = $(this).parent().find('img');
  var src = $(img).attr('src');
  src = src.replace('_240', '');
  var filename = src.split('/').pop();
  filename = filename.split('?')[0];
  $.get('/admin/api/rotate?filename=' + filename, function(res) {
    var timestamp = new Date() - 0;
    $(img).attr('src', '/uploads/' + resizeImage(filename, 240) + '?v=' + timestamp);
  });
});

$('.btn-update-product').click(function(event) {
  var id = $(this).data('id');
  var btn = $(this);
  $('input').removeClass('error');
  var data = {};
  data.group_id = $('input[name="group_id"]').val();
  data.barcode = $('input[name="barcode"]').val();
  if(!data.barcode.trim().length) {
    toastr.error('Chưa nhập Barcode');
    $('input[name="barcode"]').addClass('error');
    return;
  }
  data.title = $('input[name="title"]').val();
  if(!data.title.trim().length) {
    toastr.error('Chưa nhập tên sản phẩm');
    $('input[name="title"]').addClass('error');
    return;
  }
  data.handle = $('input[name="handle"]').val();
  // data.price = $('input[name="price"]').val();
  // data.price = data.price.replace(/[^0-9]+/g, "");
  // data.price_compare = $('input[name="price_compare"]').val();
  // data.price_compare = data.price_compare.replace(/[^0-9]+/g, "");
  // data.meta_description = $('textarea[name="meta_description"]').val();
  data.description = tinyMCE.get('product-description').getContent();
  // data.specification = tinyMCE.get('product-specification').getContent();
  var list_image = [];
  if($('.list-image').find('.image').length) {
    $('.list-image').find('.image').each(function(i,e) {
      list_image.push($(this).attr('data-name'));
    });
  }
  data.list_image = list_image;
  var color_id = [];
  var collection_id = [];
  var special_id = [];
  $('.list-color').find('input[type="checkbox"]:checked').each(function() {
    color_id.push($(this).val());
  });
  $('.list-collection').find('input[type="checkbox"]:checked').each(function() {
    collection_id.push($(this).val());
  });
  $('.list-special').find('input[type="checkbox"]:checked').each(function() {
    special_id.push($(this).val());
  });
  data.collection_id = collection_id;
  data.color_id = color_id;
  data.special_id = special_id;
  data.content = $('textarea[name="content"]').val();
  data.brand = $('select[name="brand"]').val();
  data.material = $('select[name="material"]').val();
  data.size = $('select[name="size"]').val();
  data.bag = $('select[name="bag"]').val();
  data.display = $('select[name="display"]').val();
  data.dropship = $('select[name="dropship"]').val();
  data.meta_robots = $('select[name="meta-robots"]').val();
  data.updated_at = $('input[name="updated_at"]').val();
  if(checkDate(data.updated_at) == 'Invalid Date') {
    toastr.error('Vui lòng nhập đúng định dạng ngày giờ (yyyy-mm-dd h:m:s)');
    $('input[name="updated_at"]').addClass('error');
    return;
  }
  btn.addClass('disabled');
  $.ajax({
    type: 'PUT',
    url: '/admin/san-pham/' + id,
    data: data,
    success: function(json) {
      btn.removeClass('disabled');
      if(!json.code) {
        toastr.success('Cập nhật sản phẩm thành công');
        reloadPage();
      }
      else toastr.error('Có lỗi xảy ra, xin vui lòng thử lại');
    }
  });
});

$(document).on('click', '.btn-remove-product', function() {
  var id = $(this).attr('data-id');
  var tr = $(this).closest('tr');
  var template = $(this).data('template');
  if(confirm("Xóa sản phẩm?")) {
    $.ajax({
      type: 'DELETE',
      url: '/admin/san-pham/' + id,
      success: function(json) {
        if(!json.code) {
          toastr.success('Xóa sản phẩm thành công');
          if(template != 'edit') tblProduct.row(tr).remove().draw();
          else {
            setTimeout(function(){
              window.location.href = '/admin/product';
            }, 1000);
          }
        } else toastr.error('Có lỗi xảy ra, xin vui lòng thử lại');
      }
    });
  }
});


$(document).on('click', '.btn-remove-variant', function() {
  var item = $(this).closest('.variant-item');
  var variant_id = item.data('id');
  item.remove();
  if(variant_id) {
    $.ajax({
      type: 'DELETE',
      url: '/admin/variants/' + variant_id,
      success: function(json) {
        if(!json.code) {
          console.log('Remove variant');
        }
      }
    });
  }
});

$(document).on('click', '.move-next', function() {
  $(document).find('.moving').removeClass('moving');
  var item = $(this).closest('.image').addClass('moving');
  var index = $(document).find('.list-image').find(item).index();
  if($(document).find('.list-image').find('.image').eq(index+1).html()) {
    var html = $(document).find('.list-image').find(item).get(0).outerHTML;
    $(document).find('.list-image').find('.image').eq(index+1).after(html);
    item.remove();
  }
});

$(document).on('click', '.move-prev', function() {
  $(document).find('.moving').removeClass('moving');
  var item = $(this).closest('.image').addClass('moving');
  var index = $(document).find('.list-image').find(item).index();
  if(index) {
    if($(document).find('.list-image').find('.image').eq(index-1).html()) {
      var html = $(document).find('.list-image').find(item).get(0).outerHTML;
      $(document).find('.list-image').find('.image').eq(index-1).before(html);
      item.remove();
    }
  }
});

$('.btn-update-product-odoo').click(function() {
  $(this).addClass('disabled');
  $.get('/api/crawler/getAllProduct', function(json) {
    if(!json.code) toastr.success('Cập nhật thành công');
    else toastr.error('Có lỗi xảy ra, xin vui lòng thử lại');
    setTimeout(function() {
      location.reload();
    }, 1500);
  });
});

$('.list-image').on('click', '.remove', function() {
  var id = $(this).data('id');
  if(id) {
    $.ajax({
      type: 'DELETE',
      url: '/admin/api/product/image/' + id,
      success: function(json) {
        if(!json.code) toastr.success('Xóa hình thành công');
        else toastr.error('Có lỗi xảy ra, vui lòng thử lại');
      }
    });
  }
  $(this).parent().remove();
});

$(document).on('change', 'input[name="updated_at"]', function() {
  var dt = $(this).val();
  if(checkDate(dt) == 'Invalid Date') {
    toastr.error('Vui lòng nhập đúng định dạng ngày giờ (yyyy-mm-dd h:m:s)');
    $(this).addClass('error');
  }
});
