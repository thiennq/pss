initTinymce('#description');

var collection_id = $('.list-collection').data('value');
if (collection_id && collection_id.length) {
  for (var i = 0; i < collection_id.length; i++) {
    $('.list-collection').find('input[type="checkbox"][value="'+collection_id[i].collection_id+'"]').prop('checked', true);
  }
}

$('select[name="inventory-management"]').on('change', function() {
  if ($(this).val() == 0) {
    $('input[name="variant-inventory"]').each(function() {
      $(this).attr('disabled', true);
    })
  } else {
    $('input[name="variant-inventory"]').each(function() {
      $(this).attr('disabled', false);
    })
  }
});

$(document).find('select').each(function() {
  var data = $(this).data('value');
  if(data) $(this).val(data);
});

var featureImage = {};
var $fImg = $('#featured_img[data-name]');
featureImage.image = '';
featureImage.uploaded = false;
if ($fImg.length > 0) {
  featureImage.image = $fImg.attr('data-name')
  featureImage.uploaded = true;
}

var listFormData = [];
for (var i = 0; i < $('.variant-item').length; i++) {
  listFormData.push(new FormData());
}

$('.btn-add-variant').click(function() {
  var obj = {};
  obj.id = listFormData.length;
  obj.static = staticURI;
  var variant = tmpl("add-variant", obj);
  $('.list-append').append(variant);
  listFormData.push(new FormData());
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

$(document).on('click', '.add-image', function () {
  var $variant = $(this).closest('.variant-item');
  var $form = $variant.find('.upload-list-image');
  var files = $form.prop('files');
  var formData = listFormData[$form.attr('data-id')];
  for (var i = 0; i < files.length; i++) {
    var f = files[i];
    if (!f.deleted) {
      formData.append('upload[]', f, f.name);
    }
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

function uploadImgs(form, callback) {
  var index = form.attr('data-id');
  var files = form.prop('files');
  var formData = listFormData[parseInt(index)];
  if (!formData) {
    callback([]);
  }
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
        if (!featureImage.uploaded) {
          if (e.indexOf(featureImage.image) >= 0) {
            featureImage.image = e;
          }
        }
        list_image.push(e);
      });
    }
    callback(list_image);
  });
}

function updateFeaturedImage(product_id, featured_image) {
  data = {};
  data.featured_image = featured_image
  $.ajax({
    type: 'PUT',
    url: '/admin/products/featured-image/' + product_id,
    data: data,
    success: function(json) {
      if (json.code == -2) {
        toastr.error("Sản phẩm không tồn tại, không thể cập nhật Hình đại diện");
      } else if (json.code == -4) {
        toastr.error(json.message);
      } else if (json.code == -3) {
        toastr.error('Có lỗi xảy ra, xin vui lòng thử lại');
      }
    }
  });
}


$('.btn-create').click(function() {
  $(document).find('.error').removeClass('error');
  var self = $(this);
  var data = {};
  data.title = $('input[name="title"]').val();
  if (!data.title) {
    toastr.error("Chưa nhập tiêu đề");
    $('input[name="title"]').addClass('error');
    return;
  }

  var firstVariant = $('.list-variant').find('.variant-item').eq(0);
  var variant_title = firstVariant.find('input[name="variant-title"]').val();
  var variant_price = firstVariant.find('input[name="variant-price"]').val();
  if (!variant_title || !variant_price) {
    toastr.error("Sản phẩm phải có ít nhất 1 phiên bản");
    $('.list-variant').addClass('error');
    return;
  }
  var variant_inventory = firstVariant.find('input[name="variant-inventory"]').val();
  if (parseInt($('select[name="inventory-management"]').val()) && !variant_inventory) {
    toastr.error("Sản phẩm phải có ít nhất 1 phiên bản");
    $('.list-variant').addClass('error');
    return;
  }

  data.collections = [];
  $('.list-collection').find('input[type="checkbox"]:checked').each(function() {
    data.collections.push($(this).val());
  });
  data.description = tinyMCE.get('description').getContent();
  data.meta_title = $('input[name="meta_title"]').val();
  data.meta_description = $('textarea[name="meta_description"]').val();
  data.meta_robots = $('select[name="meta_robots"]').val();
  data.inventory_management = $('select[name="inventory-management"]').val();
  data.display = $('select[name="display"]').val();
  self.addClass('disabled');
  $.ajax({
    type: 'POST',
    url: '/admin/products',
    data: data,
    success: function(json) {
      if (json.code == -1) {
        toastr.error("Sản phẩm đã tồn tại");
        self.removeClass('disabled');
      } else if (json.code == -4) {
        toastr.error(json.message);
        self.removeClass('disabled');
      } else if (json.code == -3) {
        toastr.error('Có lỗi xảy ra, xin vui lòng thử lại');
        self.removeClass('disabled');
      } else {
        var product_id = json.id;
        var list_variant = $('.variant-item');
        var count = 0;
        var variant = {};
        variant.product_id = product_id;
        createVariant();
        function createVariant() {
          if (count ==  list_variant.length) {
            self.removeClass('disabled');
            updateFeaturedImage(product_id, featureImage.image);
            reloadPage('/admin/products/' + product_id);
            return false;
          }
          var itemVariant = list_variant.eq(count);
          var formRI = itemVariant.find('.upload-list-image');
          variant.title = itemVariant.find('input[name="variant-title"]').val();
          variant.price = itemVariant.find('input[name="variant-price"]').val();
          variant.price_compare = itemVariant.find('input[name="variant-price-compare"]').val();
          variant.inventory = itemVariant.find('input[name="variant-inventory"]').val();
          parseInt($('select[name="inventory-management"]').val()) ? '' : (variant.inventory ? '': variant.inventory = 1);
          uploadImgs(formRI, function(list_image) {
            variant.list_image = list_image;
            $.ajax({
              type: 'POST',
              url: '/admin/variants',
              data: variant,
              success: function(json) {
                if(!json.code) {
                  count++;
                  createVariant();
                } else toastr.error('Tạo phiên bản ' +variant.title+ ' thất bại');
              }
            });
          });
        }
      }
    }
  });
});

$('.btn-update-product').click(function(event) {
  $(document).find('.error').removeClass('error');
  var id = $(this).attr('data-id');
  var self = $(this);
  var data = {};
  data.title = $('input[name="title"]').val();
  if (!data.title) {
    toastr.error("Chưa nhập tiêu đề");
    $('input[name="title"]').addClass('error');
    return;
  }
  var firstVariant = $('.list-variant').find('.variant-item').eq(0);
  var variant_title = firstVariant.find('input[name="variant-title"]').val();
  var variant_price = firstVariant.find('input[name="variant-price"]').val();
  if (!variant_title || !variant_price) {
    toastr.error("Sản phẩm phải có ít nhất 1 phiên bản");
    $('.list-variant').addClass('error');
    return;
  }
  var variant_inventory = firstVariant.find('input[name="variant-inventory"]').val();
  if (parseInt($('select[name="inventory-management"]').val()) && !variant_inventory) {
    toastr.error("Sản phẩm phải có ít nhất 1 phiên bản");
    $('.list-variant').addClass('error');
    return;
  }

  data.collections = [];
  $('.list-collection').find('input[type="checkbox"]:checked').each(function() {
    data.collections.push($(this).val());
  });
  data.description = tinyMCE.get('description').getContent();
  data.meta_title = $('input[name="meta_title"]').val();
  data.meta_description = $('textarea[name="meta_description"]').val();
  data.meta_robots = $('select[name="meta_robots"]').val();
  data.inventory_management = $('select[name="inventory-management"]').val();
  data.display = $('select[name="display"]').val();
  self.addClass('disabled');

  $.ajax({
    type: 'PUT',
    url: '/admin/products/' + id,
    data: data,
    success: function(json) {
      if (json.code == -2) {
        toastr.error("Sản phẩm không tồn tại");
        self.removeClass('disabled');
      } else if (json.code == -4) {
        toastr.error(json.message);
        self.removeClass('disabled');
      } else if (json.code == -3) {
        toastr.error('Có lỗi xảy ra, xin vui lòng thử lại');
        self.removeClass('disabled');
      } else {
        var list_variant_update = $('.variant-item[data-update="true"]');
        var list_variant_upload = $('.variant-item:not([data-update])');
        var count_update = 0;
        var count_upload = 0;
        var variant = {};
        variant.product_id = id;
        updateVariant();
        function createVariant() {
          if (count_upload ==  list_variant_upload.length) {
            self.removeClass('disabled');
            updateFeaturedImage(variant.product_id, featureImage.image);
            reloadPage('/admin/products/' + variant.product_id);
            return false;
          }
          var itemVariant = list_variant_upload.eq(count_upload);
          var formRI = itemVariant.find('.upload-list-image');
          uploadImgs(formRI, function(list_image) {
            var obj = {};
            obj.product_id = variant.product_id;
            obj.title = itemVariant.find('input[name="variant-title"]').val();
            obj.price = itemVariant.find('input[name="variant-price"]').val();
            obj.price_compare = itemVariant.find('input[name="variant-price-compare"]').val();
            obj.inventory = itemVariant.find('input[name="variant-inventory"]').val();
            parseInt($('select[name="inventory-management"]').val()) ? '' : (variant.inventory ? '': variant.inventory = 1);
            obj.list_image = list_image;
            $.ajax({
              type: 'POST',
              url: '/admin/variants',
              data: obj,
              success: function(json) {
                if(!json.code) {
                  count_upload++;
                  createVariant();
                } else {
                  toastr.error('Tạo phiên bản ' +variant.title+ ' thất bại');
                  self.removeClass('disabled');
                }
              }
            });
          });
        }
        function updateVariant() {
          if (count_update ==  list_variant_update.length) {
            createVariant();
            return false;
          }
          var itemVariant = list_variant_update.eq(count_update);
          var formRI = itemVariant.find('.upload-list-image');
          variant.id = itemVariant.find('.btn-remove-variant').attr('data-id');
          variant.title = itemVariant.find('input[name="variant-title"]').val();
          variant.price = itemVariant.find('input[name="variant-price"]').val();
          variant.price_compare = itemVariant.find('input[name="variant-price-compare"]').val();
          variant.inventory = itemVariant.find('input[name="variant-inventory"]').val();
          parseInt($('select[name="inventory-management"]').val()) ? '' : (variant.inventory ? '': variant.inventory = 1);
          image_deleted = [];
          itemVariant.find('.image[data-deleted="true"]').each(function (i,e) {
            image_deleted.push($(this).attr('data-id'));
          });
          variant.image_deleted = image_deleted;
          uploadImgs(formRI, function(list_image) {
            variant.list_image = list_image;
            $.ajax({
              type: 'PUT',
              url: '/admin/variants/' + variant.id,
              data: variant,
              success: function(json) {
                if(!json.code) {
                  count_update++;
                  updateVariant();
                } else toastr.error('Cập nhật phiên bản ' +variant.title+ ' thất bại');
              }
            });
          });
        }
        function createVariant() {
          if (count_upload ==  list_variant_upload.length) {
            self.removeClass('disabled');
            updateFeaturedImage(variant.product_id, featureImage.image);
            reloadPage('/admin/products/' + variant.product_id);
            return false;
          }
          var itemVariant = list_variant_upload.eq(count_upload);
          var formRI = itemVariant.find('.upload-list-image');
          variant.title = itemVariant.find('input[name="variant-title"]').val();
          variant.price = itemVariant.find('input[name="variant-price"]').val();
          variant.price_compare = itemVariant.find('input[name="variant-price-compare"]').val();
          variant.inventory = itemVariant.find('input[name="variant-inventory"]').val();
          uploadImgs(formRI, function(list_image) {
            variant.list_image = list_image;
            $.ajax({
              type: 'POST',
              url: '/admin/variants',
              data: variant,
              success: function(json) {
                if(!json.code) {
                  count_upload++;
                  createVariant();
                } else toastr.error('Tạo phiên bản ' +variant.title+ ' thất bại');
              }
            });
          });
        }
      }
    }
  });
});

$(document).on('click', '.btn-rotate-image', function(e){
  e.stopPropagation();
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

$(document).on('click', '.btn-remove-product', function() {
  var id = $(this).attr('data-id');
  var tr = $(this).closest('tr');
  if(confirm("Xóa sản phẩm " + tr.find('td:first-child a').html() + " ?")) {
    $.ajax({
      type: 'DELETE',
      url: '/admin/products/' + id,
      success: function(json) {
        if(!json.code) {
          toastr.success('Xóa sản phẩm '+tr.find('td:first-child a').html()+' thành công');
          tblProduct.row(tr).remove().draw();
        } else toastr.error('Có lỗi xảy ra, xin vui lòng thử lại');
      }
    });
  }
});

$(document).on('click', '.btn-remove-variant', function() {
  var itemVariant = $(this).closest('.variant-item');
  var variant_id = $(this).attr('data-id');
  if(variant_id) {
    $.ajax({
      type: 'DELETE',
      url: '/admin/variants/' + variant_id,
      success: function(json) {
        if(!json.code) {
          toastr.success('Xóa phiên bản '+itemVariant.find('input[name="variant-title"]').val()+' thành công');
          itemVariant.remove();
        }
        else toastr.error('Có lỗi xảy ra, xin vui lòng thử lại');
      }
    });
  }
  else {
    itemVariant.remove();
    toastr.success('Xóa phiên bản thành công');
  }
});

$(document).on('click', '.move-next', function(e) {
  e.stopPropagation();
  $(document).find('.moving').removeClass('moving');
  var item = $(this).closest('.image').addClass('moving');
  var index = $(document).find('.list-image').find(item).index();
  if($(document).find('.list-image').find('.image').eq(index+1).html()) {
    var html = $(document).find('.list-image').find(item).get(0).outerHTML;
    $(document).find('.list-image').find('.image').eq(index+1).after(html);
    item.remove();
  }
});

$(document).on('click', '.move-prev', function(e) {
  e.stopPropagation();
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

$('.list-variant').on('click', '.remove-base64-img', function(e) {
  e.stopPropagation();
  $variant_images = $(this).closest('.variant-images');
  var index = $(this).parent().attr('data-index');
  var $form = $variant_images.find('.upload-list-image');
  var files = $form.prop('files');
  files[index].deleted = true;
  $(this).closest('.image').remove();
});

$('.list-variant').on('click', '.remove-uploaded-img', function(e) {
  e.stopPropagation();
  var uploadedImg = $(this).closest('.image');
  uploadedImg.attr('data-deleted', 'true');
  uploadedImg.addClass('hidden');
});

$('.list-variant').on('click', '.image' ,function(e) {
  var self = $(this);
  if (self.hasClass('is-featured-img')) {
    self.removeClass('is-featured-img');
    $('#featured_img').attr('src', staticURI + '/img/default_image.png');
    $('#featured_img').removeAttr('data-name');
    featureImage.image = '';
    featureImage.uploaded = true;
  }
  else {
    $('.image').removeClass('is-featured-img');
    self.addClass('is-featured-img');
    var background = self.css('background-image').replace('url("','').replace('")','');
    $('#featured_img').attr('src', background);
    if (self.attr('data-name')) {
      $('#featured_img').attr('data-name', self.attr('data-name'));
      featureImage.image = self.attr('data-name');
      featureImage.uploaded = true;
    }
    else {
      $variant_images = self.closest('.variant-images');
      var index = self.attr('data-index');
      var $form = $variant_images.find('.upload-list-image');
      featureImage.image = $form.prop('files')[index].name;
      featureImage.uploaded = false;
    }
  }
});
