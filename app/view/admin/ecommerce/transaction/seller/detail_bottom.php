var rating_teks = '';
var rating_nilai = 0;

function gritter(growlPesan,growlType='info'){
  $.bootstrapGrowl(growlPesan, {
    type: growlType,
    delay: 2500,
    allow_dismiss: true
  });
}
var is_po = 0;
$("#acekstok_proses_btn").on("click",function(e){
  e.preventDefault();
  is_po = 0;
  $("#cekstok_modal").modal('show');
  var url = '<?=base_url('api_admin/ecommerce/order/cekstok_produk/'.$order->id)?>';
  $.get(url).done(function(res){
    if(res.status == 100 || res.status == '100'){
      var h = '';
      $.each(res.result.produk,function(key,val){
        var rasa = '-';
        if(val.rasa.length>0) rasa = val.rasa;
        var ukuran = '-';
        if(val.ukuran.length>0) ukuran = val.ukuran;
        var warna = '-';
        if(val.warna.length>0) warna = val.warna;

        var stok_status = 'PO <i class="fa fa-times text-danger"></i> ';
        var qty = eval(val.qty);
        var stok = eval(val.stok);
        if(stok-qty>0){
          stok_status = 'QC <i class="fa fa-check text-success"></i> ';
        }else{
          is_po++;
        }
        h += '<tr>';
        h += '<td><h4>'+val.nama+'</h4>'+val.sku+', rasa: '+rasa+', ukuran: '+ukuran+', warna: '+warna+'</td>';
        h += '<td>'+val.qty+' Pcs</td>';
        h += '<td>'+val.stok+' Pcs</td>';
        h += '<td>'+stok_status+'</td>';
        h += '</tr>';
      });
      $("#cekstok_modal_tabel tbody").html(h);
    }

  }).fail(function(){
    NProgress.done();
    gritter('<h4>Error</h4><p>Cant fetch stock status right now, please try again later</p>','danger');
  });
  $("#cekstok_modal_po_btn").show();
  $("#cekstok_modal_qc_btn").hide();
  if(!is_po){
    $("#cekstok_modal_po_btn").hide();
    $("#cekstok_modal_qc_btn").show();
  }
  $("#cekstok_modal_batal_btn").off("click");
  $("#cekstok_modal_batal_btn").on("click",function(e){
    e.preventDefault();
    $("#cekstok_modal").modal('hide');
  });
  $("#cekstok_modal_qc_btn").on("click",function(e){
    e.preventDefault();
    var api_url = '<?=base_url('api_admin/ecommerce/order/proses/'.$order->id)?>';
    var fd = {};
    fd.id = '<?=$order->id?>';
    fd.utype = 'order_qc';
    NProgress.start();
    $.post(api_url,fd).done(function(dt){
      NProgress.done
      if(dt.status == 200){
        $("#cekstok_modal").modal("hide");
        gritter('<h4>Successful</h4><p>Order status has changed</p>','success');
        setTimeout(function(){
          window.location = '<?=base_url_admin('ecommerce/order/detail/'.$order->id)?>';
        },3000);
      }else{
        gritter('<h4>Failed</h4><p>Error: '+dt.message+'</p>','danger');
      }
    }).fail(function(){
      NProgress.done();
      gritter('<h4>Error</h4><p>Cant change order status right now, please try again later</p>','danger');
    });
  });
});
$("#a_order_proses_packing_menu").on("click",function(e){
  e.preventDefault();
  var c = confirm('Are you sure?');
  if(c){
    var api_url = '<?=base_url('api_admin/ecommerce/order/proses/'.$order->id)?>';
    var fd = {};
    fd.id = '<?=$order->id?>';
    fd.utype = 'order_packing';
    NProgress.start();
    $.post(api_url,fd).done(function(dt){
      NProgress.done();
      if(dt.status == 200){
        $("#cekstok_modal").modal("hide");
        gritter('<h4>Successful</h4><p>Order status changed</p>','success');
        setTimeout(function(){
          window.location = '<?=base_url_admin('ecommerce/order/detail/'.$order->id)?>';
        },3000);
      }else{
        gritter('<h4>Failed</h4><p>Error: '+dt.message+'</p>','danger');
      }
    }).fail(function(){
      NProgress.done();
      gritter('<h4>Error</h4><p>Cant change order status right now, please try again later</p>','danger');
    });
  }
});

$("#a_order_proses_kirim_menu").on("click",function(e){
  e.preventDefault();
  $("#modal_order_proses_kirim").modal("show");
});

$("#a_order_proses_kirim").on("click",function(e){
  e.preventDefault();
  var inoresi = $("#input_order_noresi").val();
  var api_url = '<?=base_url('api_admin/ecommerce/order/proses/'.$order->id)?>';
  var fd = {};
  fd.id = '<?=$order->id?>';
  fd.utype = 'order_kirim';
  NProgress.start();
  $.post(api_url,fd).done(function(dt){
    NProgress.done();
    if(dt.status == 200){
      $("#modal_order_proses_kirim").modal("hide");
      gritter('<h4>Successful</h4><p>Order status has changed</p>','success');
      setTimeout(function(){
        if(inoresi.length>1){
          var api_url = '<?=base_url('api_admin/ecommerce/order/proses/'.$order->id)?>';
          var fd = {};
          fd.id = '<?=$order->id?>';
          fd.utype = 'order_selesai';
          fd.noresi = inoresi;
          $.post(api_url,fd).done(function(dt){
            if(dt.status == 200){
              gritter('<h4>Congratulation</h4><p>Purchasing has done</p>','success');
              setTimeout(function(){
                window.location = '<?=base_url_admin('ecommerce/order/detail/'.$order->id)?>';
              },3000);
            }else{
              gritter('<h4>Failed</h4><p>'+dt.message+'</p>','danger');
            }
          });
        }else{
          window.location = '<?=base_url_admin('ecommerce/order/detail/'.$order->id)?>';
        }

      },3000);
    }else{
      gritter('<h4>Failed</h4><p>'+dt.message+'</p>','danger');
    }
  }).fail(function(){
    NProgress.done();
    gritter('<h4>Error</h4><p>Cant change order status right now, please try again later</p>','danger');
  });
});

$("#aorder_batalkan").on("click",function(e){
  e.preventDefault();
  var c = confirm('Batalkan orderan ini?');
  if(c){
    var api_url = '<?=base_url('api_admin/ecommerce/order/proses/'.$order->id)?>';
    var fd = {};
    fd.id = '<?=$order->id?>';
    fd.utype = 'order_batal';
    NProgress.start();
    $.post(api_url,fd).done(function(dt){
      NProgress.done();
      if(dt.status == 200){
        $("#cekstok_modal").modal("hide");
        gritter('<h4>Successful</h4><p>Order status has changed</p>','success');
        setTimeout(function(){
          window.location = '<?=base_url_admin('ecommerce/order/detail/'.$order->id)?>';
        },3000);
      }else{
        gritter('<h4>Failed</h4><p>Error: '+dt.message+'</p>','danger');
      }
    }).fail(function(){
      NProgress.done();
      gritter('<h4>Error</h4><p>Cant change order status right now, please try again later</p>','danger');
    });
  }
});

//rating

(function($, window) {
    var Starrr;
    Starrr = (function() {
			Starrr.prototype.defaults = {
				rating: void 0,
				numStars: 5,
				change: function(e, value) {}
			};

			function Starrr($el, options) {
            var i, _, _ref,
                _this = this;

            this.options = $.extend({}, this.defaults, options);
            this.$el = $el;
            _ref = this.defaults;
            for (i in _ref) {
                _ = _ref[i];
                if (this.$el.data(i) != null) {
                    this.options[i] = this.$el.data(i);
                }
            }
            this.createStars();
            this.syncRating();
            this.$el.on('mouseover.starrr', 'i', function(e) {
                return _this.syncRating(_this.$el.find('i').index(e.currentTarget) + 1);
            });
            this.$el.on('mouseout.starrr', function() {
                return _this.syncRating();
            });
            this.$el.on('click.starrr', 'i', function(e) {
                return _this.setRating(_this.$el.find('i').index(e.currentTarget) + 1);
            });
            this.$el.on('starrr:change', this.options.change);
        }

        Starrr.prototype.createStars = function() {
            var _i, _ref, _results;

            _results = [];
            for (_i = 1, _ref = this.options.numStars; 1 <= _ref ? _i <= _ref : _i >= _ref; 1 <= _ref ? _i++ : _i--) {
                _results.push(this.$el.append("<i class='fa fa-star-o'></i>"));
            }
            return _results;
        };

        Starrr.prototype.setRating = function(rating) {
            if (this.options.rating === rating) {
                rating = void 0;
            }
            this.options.rating = rating;
            this.syncRating();
            return this.$el.trigger('starrr:change', rating);
        };

        Starrr.prototype.syncRating = function(rating) {
            var i, _i, _j, _ref;

            rating || (rating = this.options.rating);
            if (rating) {
                for (i = _i = 0, _ref = rating - 1; 0 <= _ref ? _i <= _ref : _i >= _ref; i = 0 <= _ref ? ++_i : --_i) { //>
                    this.$el.find('i').eq(i).removeClass('fa-star-o').addClass('fa-star');
                }
            }
            if (rating && rating < 5) {
                for (i = _j = rating; rating <= 4 ? _j <= 4 : _j >= 4; i = rating <= 4 ? ++_j : --_j) { // >
                    this.$el.find('i').eq(i).removeClass('fa-star').addClass('fa-star-o');
                }
            }
            if (!rating) {
                return this.$el.find('i').removeClass('fa-star').addClass('fa-star-o');
            }
        };

        return Starrr;

    })();
    return $.fn.extend({
        starrr: function() {
            var args, option;

            option = arguments[0], args = 2 <= arguments.length ? __slice.call(arguments, 1) : []; //>
            return this.each(function() {
                var data;

                data = $(this).data('star-rating');
                if (!data) {
                    $(this).data('star-rating', (data = new Starrr($(this), option)));
                }
                if (typeof option === 'string') {
                    return data[option].apply(data, args);
                }
            });
        }
    });
})(window.jQuery, window);

$(function() {
    return $(".starrr").starrr();
});

$('#stars').on('starrr:change', function(e, value){
	$('#count').html(value);
	rating_nilai = eval(value);
	console.log('rating_nilai: '+rating_nilai);
});

$('#stars-existing').on('starrr:change', function(e, value){
	$('#count-existing').html(value);
});

$("#arating_modal").on("click",function(e){
	e.preventDefault();
	$("#modal_rating").modal("show");
});

$("#dbtn_rating").on("click",".btn-rating-teks",function(e){
	rating_teks = $(this).attr("data-teks");
	console.log('rating_teks: '+rating_teks);
});
$("#arating_submit").on("click",function(e){
	e.preventDefault();
	NProgress.start();
	var fd = {};
	fd.rating_nilai = rating_nilai;
	fd.rating_teks = rating_teks;
	var url = '<?=base_url('api_admin/ecommerce/order/rating/'.$order->id);?>';
	$.post(url,fd).done(function(dt){
		NProgress.done();
		$("#modal_rating").modal("hide");
		setTimeout(function(){
			window.location.reload(true);
		},2000);
		if(dt.status == 200){

		}else{

		}
	});
});

<!-- by Muhammad Sofi 8 February 2022 13:58 | add check if chat room id is empty, hide button -->
$chat_all = $("#value_room_chat_all").val();
$chat_admin_seller = $("#value_room_admin_seller").val();
$chat_admin_buyer = $("#value_room_admin_buyer").val();

if($chat_all == '0') {
  $("#btn_open_chat_all").hide();
}

if($chat_admin_seller == '0') {
  $("#btn_chat_seller").hide();
}

if($chat_admin_buyer == '0') {
  $("#btn_chat_buyer").hide();
}