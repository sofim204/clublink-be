<?php if(!isset($is_cart_page)) $is_cart_page = 0; ?>
<?php if(!isset($is_checkout_page)) $is_checkout_page = 0; ?>

var variasi_ukuran = '<?php if(isset($variasi['ukuran'])) if(count($variasi['ukuran'])>0) echo reset($variasi['ukuran']); ?>';
var variasi_warna = '<?php if(isset($variasi['warna'])) if(count($variasi['warna'])>0) echo reset($variasi['warna']); ?>';

var audioElement = document.createElement('audio');
audioElement.setAttribute('src', '<?php echo base_url('assets/snd/add-to-cart.wav'); ?>');
audioElement.addEventListener('ended', function() {
  this.currentTime = 0;
  this.play();
}, false);

var jeda = 1000;
var is_cart_add = 0;
var is_produk_detail = 1;
var is_cart_page = <?php echo $is_cart_page; ?>;
var is_checkout_page = <?php echo $is_checkout_page; ?>;
var is_first = 0;
function cartGetList(){
  setTimeout(function(){
    $("#cart_detail_list").html('');
    $("#cart_subtotal").html('Rp0');
    var url = '<?php echo base_url('api_web/cart/'); ?>';
    $.get(url).done(function(dt){
      if(dt.status == '100' || dt.status == 100){
        var cart = dt.result;
        var details = cart.detail;
        $("#cart_detail_count").html(cart.item_total);
        var cart_subtotal = Number(cart.sub_total);
        cart_subtotal = cart_subtotal.toFixed(2).replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,");
        $("#cart_subtotal").html('Rp'+cart_subtotal);

        var detail_length = details.length;

        //console.log('sub total'+cart_subtotal);
        //console.log('detail length'+detail_length);

        if(detail_length > 0){
          var h1 = '';
          var h2 = '';
          var urutan = 1;

          $.each(details,function(key,val){
            h1 += cartProdukList(val,urutan,detail_length);
            h2 += cartPageProdukList(val,urutan,detail_length);
            urutan++;
          });
          $("#cart_detail_list").html(h1);
          if(is_checkout_page==0) produkListener();

          if(is_cart_page==1 && is_first>0){
            //console.log('Cart Produk Tabel modified');
            $("#cart_produk tbody").empty();
            $("#cart_produk tbody").html(h2);

            $("#cart_page_sub_total").html('Rp'+cart_subtotal);
            cart.diskon_cart = Number(cart.diskon_cart);
            cart.diskon_produk = Number(cart.diskon_produk);
            var cart_diskon = cart.diskon_cart + cart.diskon_produk;
            cart_diskon = cart_diskon.toFixed(2).replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,");
            $("#cart_page_diskon").html('-Rp'+cart_diskon);

            var cart_total = Number(cart.grand_total);
            cart_total = cart_total.toFixed(2).replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,");
            $("#cart_page_total").html('Rp'+cart_total);
          }
          is_first++;

          $("#cart_not_empty").fadeIn('slow');
          $("#cart_is_empty").hide('slow');

        }else{
          $("#cart_not_empty").hide('slow');
          $("#cart_is_empty").fadeIn('slow');
          if((is_cart_page==1) && (is_first>0)){
            window.location = '<?php echo base_url('cart/'); ?>';
          }
        }
        //$("#cart_detail_list").html('<li>Keranjang belanja kosong</li>');
      }else{
        $("#cart_detail_count").html('0');
      }
    });
  }, jeda);
}
function cartProdukList(objProduk,urutan="1",max='0'){
  var is_ganjil = 'odd';
  max = Number(max);
  if(urutan%2==1){
    is_ganjil = 'odd';
  }else{
    is_ganjil = '';
  }
  if(urutan >= max) is_ganjil += ' last';

  var op = objProduk;
  var purl = '<?php echo base_url('produk/'); ?>'+op.slug;
  var harga = Number(op.harga_jadi);
  harga = 'Rp' + harga.toFixed(2).replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,");

  var pp = '';

  if(typeof op.warna !== undefined && op.warna !== null){
    if(typeof op.warna.length !== undefined){
      if(op.warna.length>0) pp += op.warna+' - ';
    }
  }

  if(typeof op.ukuran !== undefined && op.ukuran !== null){
    if(typeof op.ukuran.length !== undefined){
      if(op.ukuran.length>0) pp += op.ukuran;
    }
  }
  //pp = pp.substring(0, pp.length-3);
  pp = pp.toUpperCase();

  var h = '';
  h += '<li id="li_pid_'+op.c_produk_id+'" class="item '+is_ganjil+' ">';
  h += '  <a href="'+purl+'" title="'+op.nama+'" class="product-image">';
  h += '    <img src="'+op.thumb+'" alt="'+op.nama+'" width="65">';
  h += '  </a>';
  h += '  <div class="product-details">';
  h += '    <a id="a_cart_detail_remove" href="#" title="Tidak jadi beli produk ini" class="remove-cart" data-pid="'+op.c_produk_id+'" data-qty=""><i class="pe-7s-close"></i></a>';
  h += '    <p class="product-name"><a href="'+purl+'">'+op.nama+'<br /><small>'+pp+'</small></a></p>';
  h += '    <strong>'+op.qty+'</strong> x <span class="price">'+harga+'</span>';
  h += '  </div>';
  h += '</li>';
  return h;
}
function cartPageProdukList(objProduk,urutan="1"){
  var is_ganjil = 'odd';
  if(urutan%2==1){
    is_ganjil = 'odd';
  }else{
    is_ganjil = '';
  }
  var op = objProduk;
  var purl = '<?php echo base_url('produk/'); ?>'+op.slug;
  var harga = Number(op.harga_jadi);
  var qty = Number(op.qty);
  var diskon_row = Number(op.diskon_row);
  var sub_total = (harga*qty) - diskon_row;
  harga = 'Rp' + harga.toFixed(2).replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,");
  sub_total = 'Rp' + sub_total.toFixed(2).replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,");
  var h = '';
  var pp = '';
  if(typeof op.berat !== undefined && op.berat !== null){
    if(typeof op.berat.length !== undefined){
      //pp += op.berat+' gr, ';
    }
  }
  if(typeof op.warna !== undefined && op.warna !== null){
    if(typeof op.warna.length !== undefined){
      pp += op.warna+' / ';
    }
  }
  if(typeof op.ukuran !== undefined && op.ukuran !== null){
    if(typeof op.ukuran.length !== undefined){
      pp += op.ukuran+' / ';
    }
  }
  if(typeof op.rasa !== undefined && op.rasa !== null){
    if(typeof op.rasa.length !== undefined){
      pp += op.rasa+' / ';
    }
  }

  if(pp.length>2) pp = pp.substring(0, pp.length-2);
  //console.log('PP: '+pp);

  h += '<tr id="tr_id_'+op.c_produk_id+'">';
  h += '  <td class="col-xs-2"><img src="'+op.thumb+'" alt="'+op.nama+'" class="img-responsive"></td>';
  h += '  <td class="col-xs-4 col-md-5"><h4><a href="'+purl+'" target="_blank">'+op.nama+'</a></h4><small>'+pp+'</small></td>';
  h += '  <td class="col-xs-2 text-center"><span>'+harga+'</span></td>';
  h += '  <td class="col-xs-1 col-md-1"><div class="form-group"><input id="i_cart_produk_qty" type="text" class="form-control" value="'+op.qty+'" data-pid="'+op.c_produk_id+'"></div></td>';
  h += '  <td class="col-xs-2 text-center"><span><b>'+sub_total+'</b></span></td>';
  h += '  <td class="col-xs-1 text-center"><a id="a_cart_produk_hapus" href="#" class="btn btn-primary" data-pid="'+op.c_produk_id+'" data-qty="0"><i class="fa fa-times"></i></a></td>';
  h += '</tr>';
  return h;
}
function cartAdd(pid,qty){
  $("#i_fa_loading").show('');
  var url = '<?php echo base_url('api_web/cart/add'); ?>';
  var fd = {};
  fd.pid = pid;
  fd.qty = Number(qty);
  fd.warna = variasi_warna;
  fd.ukuran = variasi_ukuran;
  $.post(url,fd).done(function(dt){
    if(dt.status == '400' || dt.status == 400){
      is_cart_add = 1;
      $("#loginModal").modal("show");
    }else if(dt.status == '100' || dt.status == 100){
      cartGetList();
      var fr = 333;
      setTimeout(function(){
        audioElement.play();
      },512);
      for(var i=0;i < Number(qty); i++){ //>
        setTimeout(function(){

          detailAnimate();
        },(fr*i)+512);
      }
      setTimeout(function(){
        audioElement.pause();
      },760*i);

    }else{
      alert(dt.message);
    }
    $("#i_fa_loading").hide('');
  }).fail(function(dt){
    alert('Maaf produk tidak bisa ditambahkan sekarang');
    $("#i_fa_loading").hide('');
  });
}


$("#b_cart_add").on("click",function(e){
  e.preventDefault();
  var pid = $(this).attr('data-pid');
  var qty = $("#i_cart_qty").val();
  cartAdd(pid,qty);
});
function detailAnimate(){
  $('html, body').animate({scrollTop: '0px'}, 300);
  var cart = $('.shoppingcart-inner');
  //var imgtodrag = $('.product-full').find('img').eq(0);
  var imgtodrag = false;
  if (imgtodrag) {
    var imgclone = imgtodrag.clone()
        .offset({
        top: imgtodrag.offset().top,
        left: imgtodrag.offset().left
    })
        .css({
        'opacity': '0.5',
            'position': 'absolute',
            'height': '350px',
            'width': '350px',
            'z-index': '100',
    })
        .appendTo($('body'))
        .animate({
        'top': cart.offset().top + 10,
            'left': cart.offset().left + 10,
            'width': 75,
            'height': 75
    }, 1000, 'easeInOutExpo');

    setTimeout(function () {
        //cart.effect("shake", {
        //  direction: "up",
        //  times: 1,
        //  distance: 10
        //}, 200);
    }, 1500);

    imgclone.animate({
        'width': 0,
        'height': 0
    }, function () {
        $(this).detach();
    });
  }else{
    $(".btn-top-cart-show").trigger("click");
  }
}
function removeFromCartAjax(pid,qty="0"){
  var url = '<?php echo base_url('api_web/cart/remove/'); ?>';
  url += ''+encodeURIComponent(pid);
  qty = Number(qty);
  if(qty>0){
    url += '/'+encodeURIComponent(qty);
  }
  //console.log(url);
  $.get(url).done(function(dta){
    //console.log(dta);
    if(dta.status == 100 || dta.status == '100'){
      $('#li_pid_'+pid).hide('slow');
      is_first++;
      cartGetList();
    }else{
      alert(dta.message);
    }
  }).fail(function(dt){
    alert('Maaf produk ini tidak dapat dikeluarkan dari daftar pembelian,\r silakan coba beberapa saat lagi');
  });
}
function produkListener(){
  $("#cart_detail_list").off("click",'a#a_cart_detail_remove');
  $("#cart_detail_list").on("click","a#a_cart_detail_remove",function(e){
    e.preventDefault();
    var pid = $(this).attr('data-pid');
    var qty = $(this).attr('data-qty');
    var x = confirm('Keluarkan dari daftar belanjaan?');
    if(x){
      removeFromCartAjax(pid,qty);
    }
  });
}


function removeFromCart(pid,qty="0"){
  var url = '<?php echo base_url('api_web/cart/remove/'); ?>';
  url += ''+encodeURIComponent(pid);
  qty = Number(qty);
  if(qty>0){
    url += '/'+encodeURIComponent(qty);
  }
  //console.log(url);
  $.get(url).done(function(dta){
    //console.log(dta);
    if(dta.status == 100 || dta.status == '100'){
      if(qty==0){
        $('#tr_id_'+pid).hide('slow');
      }else{

      }
      cartGetList();
    }else{
      alert(dta.message);
    }
  }).fail(function(dt){
    alert('Maaf produk ini tidak dapat dikeluarkan dari daftar pembelian,\r silakan coba beberapa saat lagi');
  });
}
function changeQtyCart(pid,qty="0"){
  var url = '<?php echo base_url('api_web/cart/change/'); ?>';
  url += ''+encodeURIComponent(pid);
  qty = Number(qty);
  url += '/'+encodeURIComponent(qty);
  //console.log(url);
  $.get(url).done(function(dta){
    //console.log(dta);
    if(dta.status == 100 || dta.status == '100'){
      cartGetList();
    }else{
      alert(dta.message);
    }
  }).fail(function(dt){
    alert('Maaf produk ini tidak dapat dikeluarkan dari daftar pembelian,\r\n silakan coba beberapa saat lagi');
  });
}
function cartListener(){
  $("#cart_produk").off("click","#a_cart_produk_hapus");
  $("#cart_produk").on("click","#a_cart_produk_hapus",function(e){
    e.preventDefault();
    var pid = $(this).attr('data-pid');
    var qty = $(this).attr('data-qty');
    var x = confirm('Keluarkan dari daftar belanjaan?');
    if(x){
      removeFromCart(pid,qty);
    }
  });

  $("#cart_produk").off("change","#i_cart_produk_qty");
  $("#cart_produk").on("change","#i_cart_produk_qty",function(e){
    e.preventDefault();
    is_first++;
    var pid = $(this).attr('data-pid');
    var qty = $(this).val();
    pid = Number(pid);
    qty = Number(qty);
    if(qty>0){
      changeQtyCart(pid,qty);
      $(this).val(qty);
    }else{
    }
  });
}

$("#b_cart_list_to").on("click tap",function(e){
  e.preventDefault();
  var x = $("#cart_subtotal").html();
  //console.log(x);
  if(x == 'Rp0' || x == 'Rp0.00' || x == 'Rp 0.00'){

    var y = confirm('Daftar belanjaan masih kosong, ingin menambahkan?');
    if(y){
      window.location = '<?php echo base_url('produk'); ?>';
    }
  }else{
    window.location = '<?php echo base_url('cart'); ?>';
  }
});
$("#b_checkout_to").on("click tap",function(e){
  e.preventDefault();
  var x = $("#cart_subtotal").html();
  //console.log(x);
  if(x == 'Rp0' || x == 'Rp0.00' || x == 'Rp 0.00'){

    var y = confirm('Daftar belanjaan masih kosong, ingin menambahkan?');
    if(y){
      window.location = '<?php echo base_url('produk'); ?>';
    }
  }else{
    window.location = '<?php echo base_url('checkout'); ?>';
  }
});



//call at least once
cartGetList();
if(is_cart_page>0){
  cartListener();
}

//offcanvas
$(".top-style-select").off("click",".cart-select");
$(".top-style-select").on("click",".cart-select",function(e){
  e.preventDefault();
  $(".top-style-select .cart-select").removeClass("active");
  $(this).addClass('active');
});
$(".btn-top-cart-show").on("click",function(e){
  e.preventDefault();
  $('body').toggleClass('off-canvas-active');
});
$(document).on('mouseup touchend', function(event) {
  var offCanvas = $('.off-canvas')
  if (!offCanvas.is(event.target) && offCanvas.has(event.target).length === 0) {
    $('body').removeClass('off-canvas-active')
  }
});
$("#offcanvas_cart_close").on('click',function(event){
  $(document).trigger('touchend');
})
