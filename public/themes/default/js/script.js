var sort_product = false;
var load_page = false;
var reload_page = false;
var send_ajax = false;

var themeURI = $('header').data('uri');

$(window).on('load', function() {
  $("img").unveil();
});

updateCartIcon();

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
  if (quantity < 1) toastr.error('Số lượng tối thiểu là 1!');
  else addToCart($(this).attr('data-variant'), quantity);
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


function addToCart(variant_id, quantity) {
  $.ajax({
    type: 'POST',
    url: '/api/addToCart',
    data: {
      variant_id: variant_id,
      quantity: quantity
    },
    success: function(json) {
      if (!json.code) {
        toastr.success('Thêm vào giỏ hàng thành công');
        updateCartIcon();
      }
      else if (json.code == -1) {
        toastr.error('Hiện phiên bản ' + json.variant + ' chỉ còn ' + json.in_stock  + ' sản phẩm.');
      }
      else toastr.error('Có lỗi xảy ra, xin vui lòng thử lại');
    }
  });
}

function updateCart(variant_id, quantity, subTotal) {
  $.ajax({
    type: 'PUT',
    url: '/api/updateCart',
    data: {
      variant_id: variant_id,
      quantity: quantity
    },
    success: function(json) {
      if (!json.code) {
        $('.total-amount').html(formatMoney(json.total));
        if (subTotal) $('.product-detail[data-id="'+variant_id+'"]').find('.main-price').html(formatMoney(subTotal));
      }
      else if (json.code == -1) {
        toastr.error('Hiện phiên bản ' + json.variant + ' chỉ còn ' + json.in_stock  + ' sản phẩm.');
      }
      else toastr.error('Có lỗi xảy ra, xin vui lòng thử lại');
    }
  });
}

$('.btn-remove-item-cart').click(function() {
  var id = $(this).attr('data-id');
  if (confirm('Xóa sản phẩm')) {
    $.ajax({
      type: 'DELETE',
      url: '/api/deleteCart',
      data: {
        variant_id: id
      },
      success: function(json) {
        if (!json.code) {
          toastr.success('Đã xóa sản phẩm khỏi giỏ hàng');
          location.reload();
        } else toastr.error('Có lỗi xảy ra, xin vui lòng thử lại');
      }
    });
  }
});

function updateCartIcon() {
  $.ajax({
    type : 'GET',
    url : '/api/getInfoCart',
    success : function(json) {
      if (!json.code) {
        var result = json.data;
        var total_price = 0;
        var total_quantity = 0;
        if (!result.length) {
          $('.fa-shopping-bag').addClass('anti-click');
          $('.minicart').removeClass('clicked');
          // closeMiniCart();
          $('.fa-shopping-bag span.quantity').text(parseInt(total_quantity));
          $('.fa-shopping-bag span.quantity').hide();
          return;
        }
        var pathname = window.location.pathname;
        if (pathname == "/cart" || pathname == "/checkout") {
          $('.fa-shopping-bag').addClass('anti-click');
        }
        else {
          $('.fa-shopping-bag').removeClass('anti-click');
        }
        $('.minicart .minicart-product').remove();
        $.each(result, function(index, value) {
          total_price += value.subTotal;
          total_quantity += parseInt(value.quantity);
          var data = {};
          data.featured_image = resizeImage(value.image, 100);
          data.title = value.title;
          data.variant = value.variant;
          data.quantity = value.quantity;
          data.variant_id = value.variant_id;
          data.price = value.subTotal;
          var cart_item =
          "<div class='minicart-product'>" +
            "<div class='avatar left'>" +
              "<div class='div-background product-img' style='background-image:url(/uploads/" + data.featured_image + ")'></div>" +
            "</div>" +
            "<div class='cart-item-detail left'>" +
              "<div class='product-variant product-name'>"+ data.title +"</div>"  +
              "<div class='product-variant'>"+ data.variant  +"</div>" +
              "<div class='product-variant quantity-select'>" +
                "<div class='quantity-btn btn-minus'><i class='fa fa-minus' aria-hidden='true'></i></div>" +
                "<input class='' name='cart-item-qty' value="+ data.quantity +" min='1' type='number' style='background-color: #fff; text-align: center; font-weight: bold;' data-variant-id="+ data.variant_id +" />" +
                "<div class='quantity-btn btn-plus'><i class='fa fa-plus' aria-hidden='true'></i></div>" +
              "</div>" +
              "<div class='product-variant price'>"+ data.price +"</div>" +
              "<div class='remove-btn'><i class='fa fa-times remove-item-from-cart' aria-hidden='true' data-variant-id="+data.variant_id+"></i></div>" +
            "</div>" +
            "<div class='clearfix'></div>" +
          "</div>";
          $('.minicart .minicart-detail').append(cart_item);
        });
        $('.fa-shopping-bag span.quantity').text(parseInt(total_quantity));
        $('.fa-shopping-bag span.quantity').show();
        $('.minicart .cart .price').html(total_price + 'đ');
      }
    }
  });
}

$('.minicart').on('click', '.remove-item-from-cart', function() {
  var variantId = $(this).attr('data-variant-id');
  $.ajax({
    type : 'DELETE',
    url: '/api/deleteCart',
    data: {
      variant_id: variantId
    },
    success: function(json) {
      if (!json.code) {
        toastr.success('Đã xóa sản phẩm khỏi giỏ hàng');
        $('.minicart .minicart-product').remove();
        updateCartIcon();
        if (window.location.pathname == '/cart' || window.location.pathname == '/checkout') {
          location.reload();
        }
      } else toastr.error('Có lỗi xảy ra, xin vui lòng thử lại');
    }
  });
});

$('.minicart').on('click', '.btn-plus', function(){
  var curQty = $(this).parent().find('input[name="cart-item-qty"]').val();
  var variant_id = $(this).parent().find('input[name="cart-item-qty"]').attr('data-variant-id');
  $(this).parent().find('input[name="cart-item-qty"]').val(parseInt(curQty) + 1).change();
});
$('.minicart').on('click', '.btn-minus', function(){
  var curQty = $(this).parent().find('input[name="cart-item-qty"]').val();
  var variant_id = $(this).parent().find('input[name="cart-item-qty"]').attr('data-variant-id');
  if (curQty > 1) {
    $(this).parent().find('input[name="cart-item-qty"]').val(parseInt(curQty) - 1).change();
  }
  else if (curQty == 1) {
    $(this).addClass('disabled');
  }
});

$(document).on('change', 'input[name=cart-item-qty]', function() {
  quantity = $(this).val();
  variant_id = $(this).attr('data-variant-id');
  $.ajax({
    type: 'PUT',
    url: '/api/updateCart',
    data: {
      variant_id: variant_id,
      quantity: quantity
    },
    success: function(json) {
      if (!json.code) {
        updateCartIcon();
        if (window.location.pathname == '/cart' || window.location.pathname == '/checkout') {
          location.reload();
        }
      }
      else if (json.code == -1) {
        toastr.error('Hiện phiên bản ' + json.variant + ' chỉ còn ' + json.in_stock  + ' sản phẩm.');
      }
      else toastr.error('Có lỗi xảy ra, xin vui lòng thử lại');
    }
  });
});

$('.fa-shopping-bag').on('click', function(){
  var minicart = $(document).find(".minicart");
  if (minicart.hasClass("clicked")) {
    minicart.removeClass("clicked");
  } else {
    minicart.addClass("clicked");
  }
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

$('.btn-checkout-done').click(function() {
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
