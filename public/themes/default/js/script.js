var sort_product = false;
var load_page = false;
var reload_page = false;
var send_ajax = false;

var themeURI = $('header').data('uri');

$(window).on('load', function() {
  $("img").unveil();
  // runVariantCarousel();
});

$('ul.list-variant').on('click', '.item-variant', function(){
  var variantId = $(this).data('id');
  $('.list-image .item').hide();
  $('.list-image .item[data-id="'+variantId+'"]').show();
  $(this).find('img').addClass('active');
  $(this).siblings().find('img').removeClass('active');
  $('.btn-order-product').attr('data-variant', $(this).data('id')).addClass('in-stock');
});

$(document).on('click', '.btn-order-product.in-stock', function(){
  var quantity = $('input[name="quantity"]').val();
  addToCart($(this).attr('data-id'), $(this).attr('data-variant'), quantity);
});

if($('.slider-homepage .index-slider-carousel').length) {
  $('.slider-homepage .index-slider-carousel').owlCarousel({
    nav: false,
    dots: true,
    loop: true,
    items: 1,
    animateOut: 'fadeOut'
  });
}
if ($('.celebs .index-slider-carousel').length) {
  $('.celebs .index-slider-carousel').owlCarousel({
    nav: false,
    dots: true,
    loop: true,
    items: 5,
    animateOut: 'fadeOut'
  });
}

// PRODUCT
if ($('.product .slide-small-image').length) {

  var zoomImage = $('.large-image-zoom');
  if ($('.large-image-zoom').length) var zoomImage = zoomImage.elevateZoom();

  $('.slide-small-image').slick({
    lazyLoad: 'ondemand',
    slidesToShow: 5,
    slidesToScroll: 1,
    vertical: true,
    prevArrow: '<span><i class="fa fa-angle-up"></i></span>',
    nextArrow: '<span><i class="fa fa-angle-down"></i></span>'
  });


  $(document).on('click', '.slick-slide', function() {
    $('.slide-small-image').find('.slick-slide.active').removeClass('active');
    $(this).addClass('active');
    var large = $(this).attr('data-large');
    var full = $(this).attr('data-full');
    $(document).find('.zoomContainer').remove();
    zoomImage.removeData('elevateZoom');
    zoomImage.attr('src', large);
    zoomImage.data('zoom-image', full);
    zoomImage.elevateZoom();
  });
}

// END PRODUCT

initPagination();

$(window).scroll(function() {
  $('ul.search-result').hide();
  if ($(this).scrollTop() > 10) $('#toTop').fadeIn();
  else $('#toTop').fadeOut();
});

$('#toTop').click(function(){
  $("html, body").animate({ scrollTop: 0 }, 600);
  return false;
});

$(window).click(function(e) {
  if ($(e.target).closest('form.search').length > 0) return;
  $('ul.search-result').hide();
});

var searchRequest = null;
$('input[name=q]').keyup(function() {
  var title = $(this).val();
  if (title.length === 0) {
    $('ul.search-result').hide();
    return;
  }
  if (searchRequest != null) searchRequest.abort();
  searchRequest = $.get('/api/san-pham/search?q=' + title, function(json) {
    $('ul.search-result').html('');
    var tpl = $("#search-product").html();
    if(!json.code) {
      $.each(json.data, function(index, elem) {
        var product = {};
        product.title = elem.title;
        product.handle = elem.handle;
        product.featured_image = '/static/img/default_image.png';
        if (elem.featured_image) {
          product.featured_image = elem.featured_image;
          product.featured_image = '/uploads/' + resizeImage(product.featured_image, 240);
        }
        product.price = formatMoney(elem.price);
        product.price_compare = '';
        if (elem.price_compare && parseInt(elem.price_compare) > parseInt(elem.price)) product.price_compare = formatMoney(elem.price_compare);
        var li = tmpl("search-product", product);
        $('ul.search-result').append(li);
        $('ul.search-result').show();
      });
    } else $('ul.search-result').append('<li><a>Không tìm thấy kết quả phù hợp</a></li>');
    $('ul.search-result').show();
  });
});

$(window).resize(function() {
  $('.blank-div').outerWidth($('.content-div').outerWidth());
  $('.blank-div').outerHeight($('.content-div').outerHeight());
});

$('.blank-div').outerWidth($('.content-div').outerWidth());
$('.blank-div').outerHeight($('.content-div').outerHeight());


// FILTER
var page = 1;

var page_type = $(document).find('input[name="page_type"]').val();
if (page_type && page_type == 'brand') {
  var brand = $(document).find('input[name="page_type"]').data('brand');
  $('.filter-sidebar').find('.item[data-type="brand"]').find('li[data-value="'+brand+'"]').addClass('active');
}

if(location.search.length) {
  var render = false;
  var search = location.search;
  search = search.substring(1, search.length);
  search = decodeURIComponent(search);
  var arr = search.split("&");
  arr.forEach(function(item) {
    if (checkFilter(item)) {
      render = true;
      var arr = item.split('=');
      if (arr[1].indexOf('+')) {
        var temp = arr[1].split('+');
        for (var i = 0; i < temp.length; i++) {
          $('.filter-sidebar').find('.item[data-type="'+arr[0]+'"]').find('li[data-value="'+temp[i]+'"]').addClass('active');
        }
      } else {
        $('.filter-sidebar').find('.item[data-type="'+arr[0]+'"]').find('li[data-value="'+arr[1]+'"]').addClass('active');
      }
    } else if (item.indexOf('page=') > -1) {
      page = item.replace('page=', '');
    } else if (item.indexOf('sort=') > -1) {
      var arr = item.split('=');
      $('.item-sort').find('li[data-value="'+arr[1]+'"]').addClass('active');
      render = true;
    } else if (item.indexOf('q=') > -1) {
      var arr = item.split('=');
      $(document).find('input[name="q"]').val(arr[1]);
      render = true;
    }
  });
  if (render) renderFilter();
}

$('.filter-sidebar .item').on('click', 'li', function() {
  var type = $(this).closest('.item').data('type');
  if($(this).hasClass('active')) {
    $(this).removeClass('active');
    //$(this).find('input').prop('checked', false);
  } else {
    if (type == 'price') {
      $(this).closest('ul').find('li.active').removeClass('active');
    }
    $(this).addClass('active');
    //$(this).find('input').prop('checked', true);
  }
  renderFilter();
  page = 1;
});

$('.item-sort').on('click', 'li', function() {
  $('.item-sort').find('li.active').removeClass('active');
  $(this).addClass('active');
  renderFilter();
});

function checkFilter(item) {
  if (item.indexOf('brand=') > -1 || item.indexOf('size=') > -1 || item.indexOf('bag=') > -1 || item.indexOf('material=') > -1 || item.indexOf('special=') > -1 || item.indexOf('price=') > -1 || item.indexOf('color=') > -1 ) {
    return true;
  }
  return false;
}

function renderFilter() {
  var url = '';
  var list_brand = '';
  var first = false;
  var arr_filter = [];
  var data = {};
  data.page = page;
  data.collection_id = $('.collection').data('id');

  var search = $(document).find('input[name="q"]').val();
  if (search) {
    url += 'q=' + search;
    var obj_filter = {};
    obj_filter.type = 'search';
    obj_filter.value = search
    arr_filter.push(obj_filter);
    first = true;
  }

  $('.filter-sidebar').find('.item').each(function() {
    var type = $(this).data('type');
    var item = $(this);
    if ($(this).find('li.active').length) {
      if (first) url += '&' + type + '=';
      else url += type + '=';
      item.find('li.active').each(function() {
        url += $(this).data('value') + '+';
        var obj_filter = {};
        obj_filter.type = type;
        obj_filter.value = $(this).data('value');
        arr_filter.push(obj_filter);
      });
      first = true;
      url = url.substr(0, url.length-1);
    }
  });

  if ($('.item-sort').find('li.active').length) {
    var sort = $('.item-sort').find('li.active').attr('data-value');
    if (first) url += '&sort=' + sort;
    else url += 'sort=' + sort;
    var obj_filter = {};
    obj_filter.type = 'sort';
    obj_filter.value = sort;
    arr_filter.push(obj_filter);
  }

  if (page > 1) {
    if (first) url += '&page=' + page;
    else url += 'page=' + page;
  }


  var newUrl = location.pathname + '?' + url;
  data.url = url;
  window.history.pushState("filter", "Title", newUrl);

  $('.list-filter').hide();
  if (arr_filter.length) {
    var arr_filter_text = '';
    $.each(arr_filter, function(index, elem) {
      if (elem.type == 'search') {
        arr_filter_text += '<span>Từ khóa: '+elem.value+'</span>';
      } else if (elem.type == 'sort') {
        var text = $('.item-sort').find('li.active').text();
        arr_filter_text += '<span data-type="'+elem.type+'">'+text+'<i class="fa fa-times btn-remove-filter"></i></span>';
      } else if (page_type == 'brand' && elem.type == 'brand') {
        arr_filter_text += '<span>'+elem.value+'</span>';
      } else {
        arr_filter_text += '<span data-type="'+elem.type+'">'+elem.value+'<i class="fa fa-times btn-remove-filter"></i></span>';
      }
    });
    arr_filter_text += '<span class="label btn-remove-all-filter">Xóa tất cả</span>';
    $('.list-filter').html(arr_filter_text);
    $('.list-filter').show();
  }

  $.post('/api/filter', data, function(products) {
    if(products != 'empty') {
      $('.list-product .row').html(products);
      $('.list-product').show();
      $('.no-product').addClass('hidden');
      $("img").unveil();
      initPagination();
    } else {
      $('.list-product').hide();
      $('.no-product').removeClass('hidden');
      $('.collection-pagination').hide();
    }
  });
}

$('.list-filter').on('click', '.btn-remove-filter', function() {
  var parent = $(this).parent();
  var type = parent.attr('data-type');
  var value = parent.text();
  if (type == 'sort') {
    $('.item-sort').find('li.active').removeClass('active');
  } else {
    $('.filter-sidebar').find('.item[data-type="'+type+'"]').find('li[data-value="'+value+'"]').removeClass('active');
  }
  $(this).remove();
  renderFilter();
});

$('.list-filter').on('click', '.btn-remove-all-filter', function() {
  $('.filter-sidebar').find('li.active').removeClass('active');
  $('.item-sort').find('li.active').removeClass('active');
  $('list-filter').hide();
  renderFilter();
});

$(document).on('click', '.dropdown-menu', function (e) {
  e.stopPropagation();
});

function formatMoney(num) {
  if(num) num = num.toString().replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,") +'đ' ;
  return num;
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

$(document).on('change', 'select[name="region"]', function() {
  var region_id = $(this).val();
  $.get('/api/region?region_id='+region_id, function(json) {
    if(!json.code) {
      $('select[name="subregion"]').html('');
      $.each(json.data, function(i,e) {
        $('select[name="subregion"]').append('<option data-price="'+e.shipping_price+'" value="'+e.id+'">'+e.name+'</option>');
      });
      calcShippingPrice();
    }
  });
});

// $(document).on('change', 'select[name="subregion"]', function() {
//   calcShippingPrice();
// });

function calcShippingPrice() {
  var shipping_init = $('.checkout').find('.shipping').data('price');
  if(shipping_init) {
    var value = $('select[name="subregion"]').val();
    var shipping_price = 40000;
    var subregion = $('select[name="subregion"]').find('option[value="'+value+'"]').attr('data-price');
    if(subregion && subregion != 'null') shipping_price = subregion;
    $('.checkout').find('.shipping').attr('data-price', shipping_price);
    $('.checkout').find('.shipping').find('span.orange').html(formatMoney(shipping_price));
    var main_price = $('.checkout').find('.main-price').data('price');
    var total_price = parseInt(main_price) + parseInt(shipping_price);
    $('.checkout').find('.total').attr('data-price', total_price);
    $('.checkout').find('.total').find('span.orange').html(formatMoney(total_price));
  }
}

$('.btn-checkout').click(function() {
  var data = {};
  $(document).find('.error').removeClass('error');
  data.name = $('input[name="name"]').val();
  if(!data.name) {
    $('input[name="name"]').addClass('error');
    toastr.error('Vui lòng nhập họ tên');
    return false;
  }
  data.phone = $('input[name="phone"]').val();
  if(!data.phone) {
    $('input[name="phone"]').addClass('error');
    toastr.error('Vui lòng nhập số điện thoại');
    return false;
  }
  if(!validatePhone(data.phone)) {
    toastr.error('Vui lòng nhập đúng số điện thoại');
    return false;
  }
  data.region = $('select[name="region"]').val();
  if(!data.region) {
    $('select[name="region"]').addClass('error');
    toastr.error('Vui lòng chọn tỉnh/thành phố');
    return false;
  }
  data.address = $('input[name="address"]').val();
  if(!data.address) {
    $('input[name="address"]').addClass('error');
    toastr.error('Vui lòng nhập địa chỉ');
    return false;
  }
  data.subregion = $('select[name="subregion"]').val();
  data.shipping_price = 0;
  data.discount = 0;
  data.payment_method = 'cod';
  $(this).addClass('disabled');
  $.ajax({
    type: 'POST',
    url: '/api/orders',
    data: data,
    success: function(json) {
      if(!json.code) {
        setTimeout(function() {
          location.href = '/dat-hang-thanh-cong';
        }, 1000);
      } else toastr.error("Có lỗi xảy ra, xin vui lòng thử lại");
    }
  });
});

function validatePhone(phone) {
  if (phone) {
    phone = phone.replace(/\s/g, "");
    var filter = /^[0-9-+]+$/;
    if(phone.length > 9 && phone.length < 12 && filter.test(phone)) return true;
  }
  return false;
}

$(document).on('click', 'span[data-role="remove"]', function (e) {
  e.stopPropagation();
});

$('.sale-off-title').on('click', function() {
  if($(this).hasClass('active')) {
    $(this).removeClass('active');
    $(this).find('input').prop('checked', false);
  }
  else {
    $(this).addClass('active');
    $(this).find('input').prop('checked', true);
  }
});


$('.sidebar').on('click', '.i-add','.i-subtract' , function(e) {
  e.preventDefault();
  var item = $(this).closest('.item');
  if(item.find('.item-child').length) {
    if(item.hasClass('open') || item.hasClass('active')) {
      item.find('.item-child').slideUp();
      item.removeClass('open');
      setTimeout(function() {
        item.removeClass('active');
      }, 500);
    } else {
      $('.sidebar').find('.item.open').find('.item-child').slideUp();
      $('.sidebar').find('.item.active').find('.item-child').slideUp();
      $('.sidebar').find('.item.open').removeClass('open');
      $('.sidebar').find('.item.active').removeClass('active');
      item.addClass('open');
      item.find('.item-child').slideDown();
    }
  }
});

$(document).on('click', '.btn-order', showModalOrder);

function showModalOrder() {
  var id = $(this).attr('data-id');
  if (!id) return;
  $.get('/api/san-pham/modal/'+id, function(data) {
    if (data == 'empty') {
      toastr.error('Sản phẩm không tồn tại');
    } else {
      $('#modal-order').find('.modal-content').html(data);
      $('#modal-order').modal('show');
      var slider = $('#modal-order .list-small-image').owlCarousel({
        items: 6,
        nav: true,
        loop: false,
        dots: false,
        animateOut: 'fadeOut',
        navText: ['<i class="fa fa-angle-left"></i>', '<i class="fa fa-angle-right"></i>'],
        navElement: 'span'
      });

      $('#modal-order').find('.item-variant').find('img').each(function() {
        var src = $(this).attr('data-src');
        $(this).attr('src', src);
      });
      setTimeout(function() {
        $('#modal-order .owl-carousel-variant').owlCarousel({
          margin: 0,
          nav:true,
          dots: false,
          navText: ['<img src="/images/left-arrow-angle.png">', '<img src="/images/right-arrow-angle.png">'],
          navElement: 'span',
          items: 5
        });
      }, 500);
    }
  });
}

$('#modal-order').on('click', '.owl-item', function() {
  var src = $(this).find('img').attr('data-large');
  $('#modal-order').find('.slider-large-image').find('img').attr('src', src);
});

$('.btn-plus').click(function() {
  var id = $(this).data('id');
  var price = $(this).data('price');
  var input = $(this).closest('.quantity-select').find('input[name="quantity"]');
  var quantity = parseInt(input.val()) + 1;
  input.val(quantity);
  var subTotal = parseInt(price) * parseInt(quantity);
  if (id && quantity) updateCart(id, quantity, subTotal);
});

$('.btn-minus').click(function() {
  var id = $(this).data('id');
  var price = $(this).data('price');
  var input = $(this).closest('.quantity-select').find('input[name="quantity"]');
  if (input.val() > 1) {
    var quantity = parseInt(input.val()) - 1;
    input.val(quantity);
    var subTotal = parseInt(price) * parseInt(quantity);
    if (id && quantity) updateCart(id, quantity, subTotal);
  }
});

function addToCart(product_id, variant_id, quantity) {
  $.ajax({
    type: 'POST',
    url: '/api/addToCart',
    data: {
      product_id: product_id,
      variant_id: variant_id,
      quantity: quantity
    },
    success: function(json) {
      if (!json.code) {
        toastr.success('Thêm vào giỏ hàng thành công');
      } else toastr.error('Có lỗi xảy ra, xin vui lòng thử lại');
    }
  });
}

function updateCart(product_id, quantity, subTotal) {
  $.ajax({
    type: 'PUT',
    url: '/api/updateCart',
    data: {
      product_id: product_id,
      quantity: quantity
    },
    success: function(json) {
      if (!json.code) {
        $('.total-amount').html(formatMoney(json.total));
        if (subTotal) $('.product-detail[data-id="'+product_id+'"]').find('.main-price').html(formatMoney(subTotal));
      } else alert('Có lỗi xảy ra, xin vui lòng thử lại');
    }
  });
}

function runVariantCarousel() {
  if ($('.variant-carousel').length) {
    $('.variant-carousel').owlCarousel({
      loop: false,
      nav: true,
      dots: false,
      navText: ['<i class="fa fa-angle-left"></i>', '<i class="fa fa-angle-right"></i>'],
      navElement: 'span',
      items: 4
    });
  }
}

$(document).on('click', '.item-thumb', function() {
  var large = $(this).attr('data-large');
  var item = $(this).closest('.item-product-loop');
  var main_image = item.find('.main-image');
  main_image.attr('src', large).fadeIn(5000);
  main_image.fadeOut(0, function() {
      main_image.attr('src', large);
  }).fadeIn(700);
  item.find('.title a').attr('href', '/san-pham/' + $(this).attr('data-handle'));
  item.find('.title a').html($(this).attr('data-title'));
  item.find('.main-price').html($(this).attr('data-price'));
  item.find('.price-compare').addClass('hidden');
  item.find('.percent').addClass('hidden');
  if($(this).attr('data-price_compare') && parseInt($(this).attr('data-display_discount'))) {
    item.find('.price-compare').removeClass('hidden');
    item.find('.percent').removeClass('hidden');
    item.find('.price-compare').html($(this).attr('data-price_compare'));
    item.find('.percent').html('-' + $(this).attr('data-percent'));
  }
});

if ($('.news-slider-carousel').length) {
  $('.news-slider-carousel').owlCarousel({
    nav: false,
    autoplay: 3000,
    dots: true,
    loop: true,
    items: 1
  });
}

if($('.news-slider-carousel-mobile').length) {
  $('.news-slider-carousel-mobile').owlCarousel({
    nav: false,
    autoplay: 3000,
    dots: true,
    loop: true,
    items: 1
  });
}

if ($('.owl-carousel-product-mobile').length) {
  var owl_large_image = $('.owl-carousel-product-mobile').owlCarousel({
    margin: 10,
    nav: false,
    dots: true,
    lazyLoad: true,
    items: 1
  });
}

if($('.owl-carousel-large-image').length) {

  $('.owl-carousel-variant').owlCarousel({
    margin: 0,
    nav:true,
    dots: false,
    navText: ['<img src="/images/left-arrow-angle.png">', '<img src="/images/right-arrow-angle.png">'],
    navElement: 'span',
    items: 5
  });
}

if($('.product-slider-carousel').length) {
  $('.product-slider-carousel').owlCarousel({
    nav: false,
    dots: true,
    loop: true,
    items: 1,
    animateOut: 'fadeOut'
  });
}

if($('.owl-carousel-variant').length) {
  $('.owl-carousel-variant').owlCarousel({
    margin: 5,
    nav:true,
    dots: false,
    // navText: ['<i class="fa fa-caret-left"></i>', '<i class="fa fa-caret-right"></i>'],
    navText: ['<img src="/images/left-arrow-angle.png">', '<img src="/images/right-arrow-angle.png">'],
    navElement: 'span',
    items: 5
  });
}

function initPagination() {
  $('.collection-pagination').hide();
  var page_number, total;
  var input = $(document).find('input[name="collection-filter"]');
  if (input.length && input.attr('data-total') && input.attr('data-page')) {
    total = input.attr('data-total');
    page_number = input.attr('data-page');
  } else {
    page_number = $(document).find('#pagination').data('page_number');
    total = $(document).find('#pagination').data('total');
  }
  if (parseInt(total) > 1) {
    $('.collection-pagination').show();
    $(document).find('#pagination').twbsPagination('destroy');
    $(document).find('#pagination').twbsPagination({
      totalPages: parseInt(total),
      visiblePages: 5,
      startPage: parseInt(page_number),
      initiateStartPageClick: false,
      first: '<i class="fa fa-angle-double-left" aria-hidden="true"></i>',
      prev: '<i class="fa fa-angle-left" aria-hidden="true"></i>',
      next: '<i class="fa fa-angle-right" aria-hidden="true"></i>',
      last: '<i class="fa fa-angle-double-right" aria-hidden="true"></i>',
      onPageClick: function (event, page) {
        var href = location.href;
        var s = location.search;
        if(!s || !s.indexOf('?page=')) {
          if(page == 1) href = location.pathname;
          else href = location.pathname + '?page=' + page;
        } else {
          if(href.indexOf('&page=') > -1) href = href.substring(0, href.indexOf('&page='));
          if(page > 1) href = href + '&page=' + page;
        }
        location.href = href;
      }
    });
  }
}

$('.btn-remove-item-cart').click(function() {
  var id = $(this).data('id');
  if (confirm('Xóa sản phẩm')) {
    $.ajax({
      type: 'DELETE',
      url: '/api/deleteCart',
      data: {
        product_id: id
      },
      success: function(json) {
        if (!json.code) {
          alert('Đã xóa');
          location.reload();
        }
        else alert('Có lỗi xảy ra, xin vui lòng thử lại');
      }
    });
  }
});

$('form.subscribe').submit(function(e) {
  e.preventDefault();
  var email = $(this).find('input[name="email_subscribe"]').val();
  $.post('/api/subscribe', {email: email}, function(json) {
    if (!json.code) {
      alert("Đăng ký thành công, mia sẽ gửi email cho bạn khi có thông tin khuyến mãi");
      $('input[name="email_subscribe"]').val('');
    } else if (json.code == -1) {
      alert("Email đã tồn tại");
    } else alert("Có lỗi xảy ra, xin vui lòng thử lại");
  });
});


$(document).on('click', '.item-variant-modal', function() {
  var id = $(this).data('id');
  $.get('/api/san-pham/variant/'+id, function(json) {
    if (!json.code) {
      product = json.data;
      $('#modal-order').find('.main-title-product').html(product.title);
      $('#modal-order').find('.main-price-product').html(formatMoney(product.price));
      $('#modal-order').find('.main-discount').addClass('hidden');
      $('#modal-order').find('.main-price-compare').addClass('hidden');
      if(product.display_discount) {
        $('#modal-order').find('.main-discount').removeClass('hidden');
        $('#modal-order').find('.main-price-compare').removeClass('hidden');
        $('#modal-order').find('.main-price-compare').html(formatMoney(product.price_compare));
        $('#modal-order').find('.main-discount').html('Tiết kiệm ' + formatMoney(product.discount) + ' ('+product.percent+')');
      }
      $('#modal-order').find('.btn-detail').attr('href', '/san-pham/' + product.handle);
      $('#modal-order').find('.btn-order-product').attr('data-id', product.id);
      var btn_order = '';
      $('#modal-order').find('.in-stock-branch').find('button, ul, p').addClass('hidden');
      if(product.in_stock) {
        $('#modal-order').find('.btn-order-product').addClass('in-stock');
        $('#modal-order').find('.btn-order-product').attr('data-id', product.id);
        $('#modal-order').find('.btn-order-product').attr('href', '/dat-hang/' + product.id);
        $('#modal-order').find('.btn-order-product').html('ĐẶT MUA GIAO HÀNG TẬN NƠI' + '<br/><span>(Không mua không sao)</span>');
        if(product.count_branch_display) {
          $('#modal-order').find('.in-stock-branch').find('.dropdown-toggle, ul').removeClass('hidden');
          var options = '';
          $.each(product.arr_branch_display, function(i,e) {
            options += '<li><div class="left"><image src="'+themeURI+'/img/icon_branch.png"><span>'+e.name+'</span></div><div class="right">'+e.address+'</div></li>';
          });
          $('#modal-order').find('.in-stock-branch').find('ul').html(options);
        } else $('#modal-order').find('.in-stock-branch').find('.in-stock').removeClass('hidden');
      } else {
        $('#modal-order').find('.btn-order-product').html('ĐANG TẠM HẾT HÀNG');
        $('#modal-order').find('.btn-order-product').removeAttr('href');
        $('#modal-order').find('.btn-order-product').removeClass('in-stock');
        $('#modal-order').find('.in-stock-branch').find('.out-stock').removeClass('hidden');
      }

      var list_small_image = '';
      $.each(product.list_image, function(index, elem) {
        var thumb = resizeImage(elem.name, 240);
        var large = resizeImage(elem.name, 1024);
        if (!index) $('#modal-order').find('.large-image-zoom').attr('src', '/uploads/' + large);
        list_small_image += '<div class="item" data-large="/uploads/'+large+'", data-full="/uploads/'+elem.name+'"><img src="/uploads/'+thumb+'"></div>';
      });

      $('#modal-order .wrapper-slider-modal').html('<div class="list-small-image owl-carousel">'+list_small_image+'</div>');
      $('#modal-order .wrapper-slider-modal .list-small-image').owlCarousel({
        items: 6,
        nav: true,
        loop: false,
        dots: false,
        animateOut: 'fadeOut',
        navText: ['<i class="fa fa-angle-left"></i>', '<i class="fa fa-angle-right"></i>'],
        navElement: 'span'
      });
    }
  });
});

$('#modal-order').on('click', '.item', function() {
  var full = $(this).attr('data-full');
  if (full) $('#modal-order').find('.large-image-zoom').attr('src', full);
});
