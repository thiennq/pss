var elementImage;
var chooseMultipleImage = false;

function showModalUpload() {
  $('#modal-upload').modal('show');
}

$('.item-choose-image img').click(function() {
  elementImage = $(this).parent();
  $('#modal-upload').modal('show');
});

$('#modal-upload').on('show.bs.modal', function() {
  getUpload(1);
});

$('#modal-upload').on('hidden.bs.modal', function() {
  $('#modal-upload').find('.add-image').nextAll().remove();
});

function getUpload(page = 1) {
  $.get('/admin/api/uploads?page=' + page, function(json) {
    if (!json.code) {
      var list = '';
      $.each(json.data, function(index, elem) {
        var src = location.origin + '/uploads/' + resizeImage(elem, 240);
        var obj = {
          src: src,
          image: elem
        };
        var item = tmpl("item-upload-image", obj);
        list += item;
      });
      $('#modal-upload').find('.add-image').after(list);
    }
  });
}

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

$('.file-manager').on('change', '#upload-image', function() {
  if($(this).val()) {
    var form = $(this).closest('form');
    form.addClass('disabled');
    var formData = new FormData(form[0]);
    $.ajax({
      type: 'POST',
      url: '/admin/api/uploadImage',
      data: formData,
      cache: false,
      contentType: false,
      processData: false,
      success: function(json) {
        var list = '';
        $.each(json.data, function(i, e) {
          var src = location.origin + '/uploads/' + e;
          list += '<div class="col-xs-2 image"><div class="item" style="background: url('+src+')" data-src="'+e+'"></div></div>';
        });
        $('.file-manager').find('.add-image').after(list);
        form.removeClass('disabled');
      }
    });
  }
});

$('.file-manager').on('click', '.item', function() {
  if (!chooseMultipleImage) $('.file-manager').find('.item.active').removeClass('active');
  if ($(this).hasClass('active')) {
    $(this).removeClass('active');
  } else {
    $(this).addClass('active');
  }
  var countActive = $('.file-manager').find('.item.active').length;
  if (countActive) $('.file-manager').find('.btn-choose-image').removeClass('disabled');
  else $('.file-manager').find('.btn-choose-image').addClass('disabled');
});

$('.btn-choose-image').click(function() {
  if (!chooseMultipleImage) {
    var name = $('.file-manager').find('.item.active').attr('data-src');
    var src = '/uploads/' + resizeImage(name, 240);
    if (elementImage.hasClass('banner')) var src = '/uploads/' + resizeImage(name, 480);
    elementImage.find('img').attr('src', src);
    elementImage.find('.value').val(name);
    $('#modal-upload').modal('hide');
  }
});
