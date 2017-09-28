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


function createHandle(str, callback) {
  $.get('/admin/api/create-handle?q='+str, callback);
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

$(document).ready(function(){
	$('.filter .input-group ul li').click(function(){
		$('.filter .input-group .dropdown-toggle .text').text($(this).text());
	});
	function tag() {
		if(document.getElementById('in').value!="") {
			var l = (document.getElementById('in').value);
			$('.filter .tagsinput #tag').tagsinput('add', l);
		}
	}
	function inputKeyUp(e) {
		e.which = e.which || e.keyCode;
	  	if(e.which == 13) {
			tag();
			document.getElementById('in').value = '';
	  }
	}
	$(document).on('keyup', '#in', inputKeyUp);

})


$(document).ready(function() {
  $('#modal-change-password').on('show.bs.modal', function() {
    $('#modal-change-password').find('input').val('');
  });
  $('.form-change-password').on('submit', function(e){
    e.preventDefault();
    var password = $(this).find('input[name="password"]').val();
    var new_password = $(this).find('input[name="new_password"]').val();
    $.ajax({
      type: 'PUT',
      url: '/admin/user/doi-mat-khau',
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
        } else if(json.code == -1) toastr.error('Mật khẩu không đúng');
        else toastr.error('Có lỗi xảy ra, xin vui lòng thử lại');
      }
    });
  });
});


$('.btn-update-css').click(function() {
  var css = $(document).find('textarea[name="custom_css"]').val()
	$.ajax({
		type: 'PUT',
		url: '/admin/information/custom-css',
		data: {
      css: css
    },
		success: function(json) {
			toastr.success('Cập nhật thành công');
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
		str= str.toLowerCase();
	  str= str.replace(/à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ/g,"a");
	  str= str.replace(/è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ/g,"e");
	  str= str.replace(/ì|í|ị|ỉ|ĩ/g,"i");
	  str= str.replace(/ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ/g,"o");
	  str= str.replace(/ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ/g,"u");
	  str= str.replace(/ỳ|ý|ỵ|ỷ|ỹ/g,"y");
	  str= str.replace(/đ/g,"d");
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
