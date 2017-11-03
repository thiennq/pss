var staticURI = $('body').data('uri');

function initTinymce(item) {
  if ($(item).length) {
    tinymce.init({
    	selector: item,
    	height: 300,
    	theme: 'modern',
      relative_urls : false,
      remove_script_host : false,
      convert_urls : true,
    	plugins: [
    		'code advlist autolink lists link image charmap print preview hr anchor pagebreak',
    		'searchreplace wordcount visualblocks visualchars code fullscreen',
    		'insertdatetime media nonbreaking save table contextmenu directionality',
    		'emoticons template paste textcolor colorpicker textpattern imagetools'
    	],
    	toolbar1: 'insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image ',
    	toolbar2: 'print preview media | forecolor backcolor emoticons | removeformat',
    	image_advtab: true,
      automatic_uploads: true,
      images_upload_base_path: '/uploads',
      imageupload_url: '/',
      file_browser_callback_types: 'file image media',
      file_browser_callback: function(field_name, url, type, win) {
        tinymce.activeEditor.windowManager.open({
            title: "Hình ảnh",
            filetype: 'all',
            file: '/admin/api/tinymce/images',
            width: 800,
            height: 450,
            inline: 1
          }, {
          window : win,
          input : field_name
        });
      },
      images_upload_handler: function (blobInfo, success, failure) {
        var xhr, formData;
        xhr = new XMLHttpRequest();
        xhr.withCredentials = false;
        xhr.open('POST', 'postAcceptor.php');
        xhr.onload = function() {
          var json;

          if (xhr.status != 200) {
            failure('HTTP Error: ' + xhr.status);
            return;
          }
          json = JSON.parse(xhr.responseText);

          if (!json || typeof json.location != 'string') {
            failure('Invalid JSON: ' + xhr.responseText);
            return;
          }
          success(json.location);
        };
        formData = new FormData();
        formData.append('file', blobInfo.blob(), blobInfo.filename());
        xhr.send(formData);
      }
    });
  }
}

var tbl;
function initDataTable(item) {
  if ($(item).length) {
    tbl = $(item).DataTable({
      aaSorting: [],
      bDestroy: true,
      rowReorder: {
        selector: 'td:nth-child(2)'
      },
      responsive: true,
      rowReorder: false,
    });
  }
}

function checkExtImage(image) {
  var ext = image.split('.').pop().toLowerCase();
  if($.inArray(ext, ['png','jpg','jpeg', 'gif']) == -1) {
	  toastr.error('Vui lÃ²ng chá»n Ä‘Ãºng Ä‘á»‹nh dáº¡nh áº£nh');
		return 0;
	}
  return 1;
}

function resizeImage(image, size) {
  if(image) {
    var ext = image.split('.').pop();
  	var a = '.' + ext;
  	var b = '_' + size + a;
  	var c = image.replace(a, b);
    return c;
  }
  return image;
}

$('#modal-change-password').on('show.bs.modal', function() {
  $('#modal-change-password').find('input').val('');
});

$('.form-change-password').on('submit', function(e){
  e.preventDefault();
  var password = $(this).find('input[name="password"]').val();
  var new_password = $(this).find('input[name="new_password"]').val();
  $.ajax({
    type: 'PUT',
    url: '/admin/api/user/changePassword',
    data: {
      password: password,
      new_password: new_password
    },
    success: function(json) {
      if(!json.code) {
        toastr.success('Đổi mật khẩu thành công');
        setTimeout(function(){
          window.location.reload();
        }, 1000);
      } else if(json.code == -1) toastr.error('Mật khẩu cũ không đúng');
      else toastr.error('Có lỗi xảy ra, xin vui lòng thử lại');
    }
  });
});

$('.btn-remove-image').click(function() {
  var image = $(this).data('image');
  $(this).closest('.form-group').find('input').val('');
  $(this).closest('.form-group').find('img').attr('src', '/static/img/' + image);
});

function convertToHandle(str) {
	if(str) {
		str = str.trim();
		str = str.toLowerCase();
	  str = str.replace(/à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ/g,"a");
	  str = str.replace(/è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ/g,"e");
	  str = str.replace(/ì|í|ị|ỉ|ĩ/g,"i");
	  str = str.replace(/ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ/g,"o");
	  str = str.replace(/ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ/g,"u");
	  str = str.replace(/ỳ|ý|ỵ|ỷ|ỹ/g,"y");
	  str = str.replace(/đ/g,"d");
    str = str.replace(/\,/g, '-');
    str = str.replace(/\./g, '-');
    str = str.replace(/\!/g, '-');
    str = str.replace(/\?/g, '-');
    str = str.replace(/\~/g, '-');
    str = str.replace(/\ /g, '-');
    str = str.replace(/\|/g, '-');
		str = str.replace(/\./g, '-');
    str = str.replace(/\"/g, '-');
    str = str.replace(/\'/g, '-');
    str = str.replace(/\--/g, '-');
		str = str.replace(/\--/g, '-');
		str = str.replace(/\--/g, '-');
    str = str.replace(/\--/g, '-');
    if(str.slice(-1) == '-') str = str.substring(0, str.length - 1);
	}
  return str;
}

function checkDate(datetime) {
  return new Date(datetime);
}

function reloadPage(url = null) {
  setTimeout(function() {
    if (url) location.href = url;
    else location.reload();
  }, 1000);
}

$(window).on('load', function() {
  $("select[data-value]").val(function(){
    return $(this).data('value');
  });
});

$('.main-item').click(function() {
  var treeview = $(this).closest('.treeview');
  treeview.siblings().removeClass('active');
  if (treeview.hasClass('active')) {
    treeview.removeClass('active');
  } else treeview.addClass('active');
});

$('.main-sidebar .treeview-menu a').click(function(e) {
  e.stopPropagation();
});

var windowUpload;
function uploadManager() {
  var w = $(window).width() * 0.7;
  var h = $(window).height() * 0.8;
  var l = $(window).width()  * 0.15;
  var t = $(window).height()  * 0.1;
  windowUpload = window.open('/admin/uploads', 'name', 'height='+h+', width='+w+', left='+l+', top='+t+', location=no, menubar=no');
  if (window.focus) {
    windowUpload.focus();
  }
  return false;
}
