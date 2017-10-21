function checkFilter(a){return a.indexOf("brand=")>-1||a.indexOf("size=")>-1||a.indexOf("bag=")>-1||a.indexOf("material=")>-1||a.indexOf("special=")>-1||a.indexOf("price=")>-1||a.indexOf("color=")>-1}function renderFilter(){var a="",e=!1,t=[],i={};i.page=page,i.collection_id=$(".collection").data("id");var n=$(document).find('input[name="q"]').val();if(n){a+="q="+n;var r={};r.type="search",r.value=n,t.push(r),e=!0}if($(".filter-sidebar").find(".item").each(function(){var i=$(this).data("type"),n=$(this);$(this).find("li.active").length&&(a+=e?"&"+i+"=":i+"=",n.find("li.active").each(function(){a+=$(this).data("value")+"+";var e={};e.type=i,e.value=$(this).data("value"),t.push(e)}),e=!0,a=a.substr(0,a.length-1))}),$(".item-sort").find("li.active").length){var o=$(".item-sort").find("li.active").attr("data-value");a+=e?"&sort="+o:"sort="+o;var r={};r.type="sort",r.value=o,t.push(r)}page>1&&(a+=e?"&page="+page:"page="+page);var d=location.pathname+"?"+a;if(i.url=a,window.history.pushState("filter","Title",d),$(".list-filter").hide(),t.length){var l="";$.each(t,function(a,e){if("search"==e.type)l+="<span>Từ khóa: "+e.value+"</span>";else if("sort"==e.type){var t=$(".item-sort").find("li.active").text();l+='<span data-type="'+e.type+'">'+t+'<i class="fa fa-times btn-remove-filter"></i></span>'}else"brand"==page_type&&"brand"==e.type?l+="<span>"+e.value+"</span>":l+='<span data-type="'+e.type+'">'+e.value+'<i class="fa fa-times btn-remove-filter"></i></span>'}),l+='<span class="label btn-remove-all-filter">Xóa tất cả</span>',$(".list-filter").html(l),$(".list-filter").show()}$.post("/api/filter",i,function(a){"empty"!=a?($(".list-product .row").html(a),$(".list-product").show(),$(".no-product").addClass("hidden"),$("img").unveil(),initPagination()):($(".list-product").hide(),$(".no-product").removeClass("hidden"),$(".collection-pagination").hide())})}function formatMoney(a){return a&&(a=a.toString().replace(/(\d)(?=(\d\d\d)+(?!\d))/g,"$1,")+"đ"),a}function resizeImage(a,e){if(a){var t=a.split(".").pop(),i="."+t,n="_"+e+i;return a.replace(i,n)}return a}function calcShippingPrice(){if($(".checkout").find(".shipping").data("price")){var a=$('select[name="subregion"]').val(),e=4e4,t=$('select[name="subregion"]').find('option[value="'+a+'"]').attr("data-price");t&&"null"!=t&&(e=t),$(".checkout").find(".shipping").attr("data-price",e),$(".checkout").find(".shipping").find("span.orange").html(formatMoney(e));var i=$(".checkout").find(".main-price").data("price"),n=parseInt(i)+parseInt(e);$(".checkout").find(".total").attr("data-price",n),$(".checkout").find(".total").find("span.orange").html(formatMoney(n))}}function validatePhone(a){if(a){a=a.replace(/\s/g,"");var e=/^[0-9-+]+$/;if(a.length>9&&a.length<12&&e.test(a))return!0}return!1}function showModalOrder(){var a=$(this).attr("data-id");a&&$.get("/api/san-pham/modal/"+a,function(a){if("empty"==a)toastr.error("Sản phẩm không tồn tại");else{$("#modal-order").find(".modal-content").html(a),$("#modal-order").modal("show");$("#modal-order .list-small-image").owlCarousel({items:6,nav:!0,loop:!1,dots:!1,animateOut:"fadeOut",navText:['<i class="fa fa-angle-left"></i>','<i class="fa fa-angle-right"></i>'],navElement:"span"});$("#modal-order").find(".item-variant").find("img").each(function(){var a=$(this).attr("data-src");$(this).attr("src",a)}),setTimeout(function(){$("#modal-order .owl-carousel-variant").owlCarousel({margin:0,nav:!0,dots:!1,navText:['<img src="/images/left-arrow-angle.png">','<img src="/images/right-arrow-angle.png">'],navElement:"span",items:5})},500)}})}function addToCart(a,e){$.ajax({type:"POST",url:"/api/addToCart",data:{variant_id:a,quantity:e},success:function(a){a.code?toastr.error("Có lỗi xảy ra, xin vui lòng thử lại"):toastr.success("Thêm vào giỏ hàng thành công")}})}function updateCart(a,e,t){$.ajax({type:"PUT",url:"/api/updateCart",data:{variant_id:a,quantity:e},success:function(e){e.code?toastr.error("Có lỗi xảy ra, xin vui lòng thử lại"):($(".total-amount").html(formatMoney(e.total)),t&&$('.product-detail[data-id="'+a+'"]').find(".main-price").html(formatMoney(t)))}})}function runVariantCarousel(){$(".variant-carousel").length&&$(".variant-carousel").owlCarousel({loop:!1,nav:!0,dots:!1,navText:['<i class="fa fa-angle-left"></i>','<i class="fa fa-angle-right"></i>'],navElement:"span",items:4})}function initPagination(){$(".collection-pagination").hide();var a,e,t=$(document).find('input[name="collection-filter"]');t.length&&t.attr("data-total")&&t.attr("data-page")?(e=t.attr("data-total"),a=t.attr("data-page")):(a=$(document).find("#pagination").data("page_number"),e=$(document).find("#pagination").data("total")),parseInt(e)>1&&($(".collection-pagination").show(),$(document).find("#pagination").twbsPagination("destroy"),$(document).find("#pagination").twbsPagination({totalPages:parseInt(e),visiblePages:5,startPage:parseInt(a),initiateStartPageClick:!1,first:'<i class="fa fa-angle-double-left" aria-hidden="true"></i>',prev:'<i class="fa fa-angle-left" aria-hidden="true"></i>',next:'<i class="fa fa-angle-right" aria-hidden="true"></i>',last:'<i class="fa fa-angle-double-right" aria-hidden="true"></i>',onPageClick:function(a,e){var t=location.href,i=location.search;i&&i.indexOf("?page=")?(t.indexOf("&page=")>-1&&(t=t.substring(0,t.indexOf("&page="))),e>1&&(t=t+"&page="+e)):t=1==e?location.pathname:location.pathname+"?page="+e,location.href=t}}))}var sort_product=!1,load_page=!1,reload_page=!1,send_ajax=!1,themeURI=$("header").data("uri");if($(window).on("load",function(){$("img").unveil()}),$("ul.list-variant").on("click",".item-variant",function(){var a=$(this).data("id");$(".list-image .item").hide(),$('.list-image .item[data-id="'+a+'"]').show(),$(this).find("img").addClass("active"),$(this).siblings().find("img").removeClass("active"),$(".btn-order-product").attr("data-variant",$(this).data("id")).addClass("in-stock")}),$(document).on("click",".btn-order-product.in-stock",function(){var a=$('input[name="quantity"]').val();addToCart($(this).attr("data-variant"),a)}),$(".slider-homepage .index-slider-carousel").length&&$(".slider-homepage .index-slider-carousel").owlCarousel({nav:!1,dots:!0,loop:!0,items:1,animateOut:"fadeOut"}),$(".celebs .index-slider-carousel").length&&$(".celebs .index-slider-carousel").owlCarousel({nav:!1,dots:!0,loop:!0,items:5,animateOut:"fadeOut"}),$(".product .slide-small-image").length){var zoomImage=$(".large-image-zoom");if($(".large-image-zoom").length)var zoomImage=zoomImage.elevateZoom();$(".slide-small-image").slick({lazyLoad:"ondemand",slidesToShow:5,slidesToScroll:1,vertical:!0,prevArrow:'<span><i class="fa fa-angle-up"></i></span>',nextArrow:'<span><i class="fa fa-angle-down"></i></span>'}),$(document).on("click",".slick-slide",function(){$(".slide-small-image").find(".slick-slide.active").removeClass("active"),$(this).addClass("active");var a=$(this).attr("data-large"),e=$(this).attr("data-full");$(document).find(".zoomContainer").remove(),zoomImage.removeData("elevateZoom"),zoomImage.attr("src",a),zoomImage.data("zoom-image",e),zoomImage.elevateZoom()})}initPagination(),$(window).scroll(function(){$("ul.search-result").hide(),$(this).scrollTop()>10?$("#toTop").fadeIn():$("#toTop").fadeOut()}),$("#toTop").click(function(){return $("html, body").animate({scrollTop:0},600),!1}),$(window).click(function(a){$(a.target).closest("form.search").length>0||$("ul.search-result").hide()});var searchRequest=null;$("input[name=q]").keyup(function(){var a=$(this).val();if(0===a.length)return void $("ul.search-result").hide();null!=searchRequest&&searchRequest.abort(),searchRequest=$.get("/api/san-pham/search?q="+a,function(a){$("ul.search-result").html("");$("#search-product").html();a.code?$("ul.search-result").append("<li><a>Không tìm thấy kết quả phù hợp</a></li>"):$.each(a.data,function(a,e){var t={};t.title=e.title,t.handle=e.handle,t.featured_image="/static/img/default_image.png",e.featured_image&&(t.featured_image=e.featured_image,t.featured_image="/uploads/"+resizeImage(t.featured_image,240)),t.price=formatMoney(e.price),t.price_compare="",e.price_compare&&parseInt(e.price_compare)>parseInt(e.price)&&(t.price_compare=formatMoney(e.price_compare));var i=tmpl("search-product",t);$("ul.search-result").append(i),$("ul.search-result").show()}),$("ul.search-result").show()})}),$(window).resize(function(){$(".blank-div").outerWidth($(".content-div").outerWidth()),$(".blank-div").outerHeight($(".content-div").outerHeight())}),$(".blank-div").outerWidth($(".content-div").outerWidth()),$(".blank-div").outerHeight($(".content-div").outerHeight());var page=1,page_type=$(document).find('input[name="page_type"]').val();if(page_type&&"brand"==page_type){var brand=$(document).find('input[name="page_type"]').data("brand");$(".filter-sidebar").find('.item[data-type="brand"]').find('li[data-value="'+brand+'"]').addClass("active")}if(location.search.length){var render=!1,search=location.search;search=search.substring(1,search.length),search=decodeURIComponent(search);var arr=search.split("&");arr.forEach(function(a){if(checkFilter(a)){render=!0;var e=a.split("=");if(e[1].indexOf("+"))for(var t=e[1].split("+"),i=0;i<t.length;i++)$(".filter-sidebar").find('.item[data-type="'+e[0]+'"]').find('li[data-value="'+t[i]+'"]').addClass("active");else $(".filter-sidebar").find('.item[data-type="'+e[0]+'"]').find('li[data-value="'+e[1]+'"]').addClass("active")}else if(a.indexOf("page=")>-1)page=a.replace("page=","");else if(a.indexOf("sort=")>-1){var e=a.split("=");$(".item-sort").find('li[data-value="'+e[1]+'"]').addClass("active"),render=!0}else if(a.indexOf("q=")>-1){var e=a.split("=");$(document).find('input[name="q"]').val(e[1]),render=!0}}),render&&renderFilter()}if($(".filter-sidebar .item").on("click","li",function(){var a=$(this).closest(".item").data("type");$(this).hasClass("active")?$(this).removeClass("active"):("price"==a&&$(this).closest("ul").find("li.active").removeClass("active"),$(this).addClass("active")),renderFilter(),page=1}),$(".item-sort").on("click","li",function(){$(".item-sort").find("li.active").removeClass("active"),$(this).addClass("active"),renderFilter()}),$(".list-filter").on("click",".btn-remove-filter",function(){var a=$(this).parent(),e=a.attr("data-type"),t=a.text();"sort"==e?$(".item-sort").find("li.active").removeClass("active"):$(".filter-sidebar").find('.item[data-type="'+e+'"]').find('li[data-value="'+t+'"]').removeClass("active"),$(this).remove(),renderFilter()}),$(".list-filter").on("click",".btn-remove-all-filter",function(){$(".filter-sidebar").find("li.active").removeClass("active"),$(".item-sort").find("li.active").removeClass("active"),$("list-filter").hide(),renderFilter()}),$(document).on("click",".dropdown-menu",function(a){a.stopPropagation()}),$(document).on("change",'select[name="region"]',function(){var a=$(this).val();$.get("/api/region?region_id="+a,function(a){a.code||($('select[name="subregion"]').html(""),$.each(a.data,function(a,e){$('select[name="subregion"]').append('<option data-price="'+e.shipping_price+'" value="'+e.id+'">'+e.name+"</option>")}),calcShippingPrice())})}),$(".btn-checkout").click(function(){var a={};return $(document).find(".error").removeClass("error"),a.name=$('input[name="name"]').val(),a.name?(a.phone=$('input[name="phone"]').val(),a.phone?validatePhone(a.phone)?(a.region=$('select[name="region"]').val(),a.region?(a.address=$('input[name="address"]').val(),a.address?(a.subregion=$('select[name="subregion"]').val(),a.shipping_price=0,a.discount=0,a.payment_method="cod",$(this).addClass("disabled"),void $.ajax({type:"POST",url:"/api/orders",data:a,success:function(a){a.code?toastr.error("Có lỗi xảy ra, xin vui lòng thử lại"):setTimeout(function(){location.href="/dat-hang-thanh-cong"},1e3)}})):($('input[name="address"]').addClass("error"),toastr.error("Vui lòng nhập địa chỉ"),!1)):($('select[name="region"]').addClass("error"),toastr.error("Vui lòng chọn tỉnh/thành phố"),!1)):(toastr.error("Vui lòng nhập đúng số điện thoại"),!1):($('input[name="phone"]').addClass("error"),toastr.error("Vui lòng nhập số điện thoại"),!1)):($('input[name="name"]').addClass("error"),toastr.error("Vui lòng nhập họ tên"),!1)}),$(document).on("click",'span[data-role="remove"]',function(a){a.stopPropagation()}),$(".sale-off-title").on("click",function(){$(this).hasClass("active")?($(this).removeClass("active"),$(this).find("input").prop("checked",!1)):($(this).addClass("active"),$(this).find("input").prop("checked",!0))}),$(".sidebar").on("click",".i-add",".i-subtract",function(a){a.preventDefault();var e=$(this).closest(".item");e.find(".item-child").length&&(e.hasClass("open")||e.hasClass("active")?(e.find(".item-child").slideUp(),e.removeClass("open"),setTimeout(function(){e.removeClass("active")},500)):($(".sidebar").find(".item.open").find(".item-child").slideUp(),$(".sidebar").find(".item.active").find(".item-child").slideUp(),$(".sidebar").find(".item.open").removeClass("open"),$(".sidebar").find(".item.active").removeClass("active"),e.addClass("open"),e.find(".item-child").slideDown()))}),$(document).on("click",".btn-order",showModalOrder),$("#modal-order").on("click",".owl-item",function(){var a=$(this).find("img").attr("data-large");$("#modal-order").find(".slider-large-image").find("img").attr("src",a)}),$(".btn-plus").click(function(){var a=$(this).data("id"),e=$(this).data("price"),t=$(this).closest(".quantity-select").find('input[name="quantity"]'),i=parseInt(t.val())+1;t.val(i);var n=parseInt(e)*parseInt(i);a&&i&&updateCart(a,i,n)}),$(".btn-minus").click(function(){var a=$(this).data("id"),e=$(this).data("price"),t=$(this).closest(".quantity-select").find('input[name="quantity"]');if(t.val()>1){var i=parseInt(t.val())-1;t.val(i);var n=parseInt(e)*parseInt(i);a&&i&&updateCart(a,i,n)}}),$(".btn-remove-item-cart").click(function(){var a=$(this).attr("data-id");confirm("Xóa sản phẩm")&&$.ajax({type:"DELETE",url:"/api/deleteCart",data:{variant_id:a},success:function(a){a.code?toastr.error("Có lỗi xảy ra, xin vui lòng thử lại"):(toastr.success("Thêm vào giỏ hàng thành công"),location.reload())}})}),$(document).on("click",".item-thumb",function(){var a=$(this).attr("data-large"),e=$(this).closest(".item-product-loop"),t=e.find(".main-image");t.attr("src",a).fadeIn(5e3),t.fadeOut(0,function(){t.attr("src",a)}).fadeIn(700),e.find(".title a").attr("href","/san-pham/"+$(this).attr("data-handle")),e.find(".title a").html($(this).attr("data-title")),e.find(".main-price").html($(this).attr("data-price")),e.find(".price-compare").addClass("hidden"),e.find(".percent").addClass("hidden"),$(this).attr("data-price_compare")&&parseInt($(this).attr("data-display_discount"))&&(e.find(".price-compare").removeClass("hidden"),e.find(".percent").removeClass("hidden"),e.find(".price-compare").html($(this).attr("data-price_compare")),e.find(".percent").html("-"+$(this).attr("data-percent")))}),$(".news-slider-carousel").length&&$(".news-slider-carousel").owlCarousel({nav:!1,autoplay:3e3,dots:!0,loop:!0,items:1}),$(".news-slider-carousel-mobile").length&&$(".news-slider-carousel-mobile").owlCarousel({nav:!1,autoplay:3e3,dots:!0,loop:!0,items:1}),$(".owl-carousel-product-mobile").length)var owl_large_image=$(".owl-carousel-product-mobile").owlCarousel({margin:10,nav:!1,dots:!0,lazyLoad:!0,items:1});$(".owl-carousel-large-image").length&&$(".owl-carousel-variant").owlCarousel({margin:0,nav:!0,dots:!1,navText:['<img src="/images/left-arrow-angle.png">','<img src="/images/right-arrow-angle.png">'],navElement:"span",items:5}),$(".product-slider-carousel").length&&$(".product-slider-carousel").owlCarousel({nav:!1,dots:!0,loop:!0,items:1,animateOut:"fadeOut"}),$(".owl-carousel-variant").length&&$(".owl-carousel-variant").owlCarousel({margin:5,nav:!0,dots:!1,navText:['<img src="/images/left-arrow-angle.png">','<img src="/images/right-arrow-angle.png">'],navElement:"span",items:5}),$("form.subscribe").submit(function(a){a.preventDefault();var e=$(this).find('input[name="email_subscribe"]').val();$.post("/api/subscribe",{email:e},function(a){a.code?-1==a.code?alert("Email đã tồn tại"):alert("Có lỗi xảy ra, xin vui lòng thử lại"):(alert("Đăng ký thành công, mia sẽ gửi email cho bạn khi có thông tin khuyến mãi"),$('input[name="email_subscribe"]').val(""))})}),$(document).on("click",".item-variant-modal",function(){var a=$(this).data("id");$.get("/api/san-pham/variant/"+a,function(a){if(!a.code){product=a.data,$("#modal-order").find(".main-title-product").html(product.title),$("#modal-order").find(".main-price-product").html(formatMoney(product.price)),$("#modal-order").find(".main-discount").addClass("hidden"),$("#modal-order").find(".main-price-compare").addClass("hidden"),product.display_discount&&($("#modal-order").find(".main-discount").removeClass("hidden"),$("#modal-order").find(".main-price-compare").removeClass("hidden"),$("#modal-order").find(".main-price-compare").html(formatMoney(product.price_compare)),$("#modal-order").find(".main-discount").html("Tiết kiệm "+formatMoney(product.discount)+" ("+product.percent+")")),$("#modal-order").find(".btn-detail").attr("href","/san-pham/"+product.handle),$("#modal-order").find(".btn-order-product").attr("data-id",product.id);if($("#modal-order").find(".in-stock-branch").find("button, ul, p").addClass("hidden"),product.in_stock)if($("#modal-order").find(".btn-order-product").addClass("in-stock"),$("#modal-order").find(".btn-order-product").attr("data-id",product.id),$("#modal-order").find(".btn-order-product").attr("href","/dat-hang/"+product.id),$("#modal-order").find(".btn-order-product").html("ĐẶT MUA GIAO HÀNG TẬN NƠI<br/><span>(Không mua không sao)</span>"),product.count_branch_display){$("#modal-order").find(".in-stock-branch").find(".dropdown-toggle, ul").removeClass("hidden");var e="";$.each(product.arr_branch_display,function(a,t){e+='<li><div class="left"><image src="'+themeURI+'/img/icon_branch.png"><span>'+t.name+'</span></div><div class="right">'+t.address+"</div></li>"}),$("#modal-order").find(".in-stock-branch").find("ul").html(e)}else $("#modal-order").find(".in-stock-branch").find(".in-stock").removeClass("hidden");else $("#modal-order").find(".btn-order-product").html("ĐANG TẠM HẾT HÀNG"),$("#modal-order").find(".btn-order-product").removeAttr("href"),$("#modal-order").find(".btn-order-product").removeClass("in-stock"),$("#modal-order").find(".in-stock-branch").find(".out-stock").removeClass("hidden");var t="";$.each(product.list_image,function(a,e){var i=resizeImage(e.name,240),n=resizeImage(e.name,1024);a||$("#modal-order").find(".large-image-zoom").attr("src","/uploads/"+n),t+='<div class="item" data-large="/uploads/'+n+'", data-full="/uploads/'+e.name+'"><img src="/uploads/'+i+'"></div>'}),$("#modal-order .wrapper-slider-modal").html('<div class="list-small-image owl-carousel">'+t+"</div>"),$("#modal-order .wrapper-slider-modal .list-small-image").owlCarousel({items:6,nav:!0,loop:!1,dots:!1,animateOut:"fadeOut",navText:['<i class="fa fa-angle-left"></i>','<i class="fa fa-angle-right"></i>'],navElement:"span"})}})}),$("#modal-order").on("click",".item",function(){var a=$(this).attr("data-full");a&&$("#modal-order").find(".large-image-zoom").attr("src",a)});