var ieid = '<?=$chat_room_id?>';
var growlPesan = '<h4>Error</h4><p>Cannot be proceed, please try again later!</p>';
var growlType = 'danger';
var drTable = {};

function gritter(pesan,jenis='info'){
  $.bootstrapGrowl(pesan, {
    type: jenis,
    delay: 3500,
    allow_dismiss: true
  });
}

$("#fsendchat").on("submit",function(e){
  e.preventDefault();
  NProgress.start();
  var fd = new FormData($(this)[0]);

  var productCustomer1 = $('#productCustomer1').val();

  var orderBuyerCustomer1 = $('#orderBuyerCustomer1').val();

  var orderSellerCustomer1 = $('#orderSellerCustomer1').val();

  var isSelected = 0;

  if(productCustomer1){
    isSelected += 1;
  }

  if(orderBuyerCustomer1){
    isSelected += 1;
  }

  if(orderSellerCustomer1){
    isSelected += 1;
  }

  if( document.getElementById("files").files.length != 0 ){
    isSelected += 1;
  }
  
  if(isSelected >= 2 ){

    NProgress.done();
    growlPesan = '<h4>Failed</h4><p></p>';
    growlType = 'danger';
    gritter('<h4>Failed</h4><p>Can only attach one kind at a time</p>','danger');
    return false;

  }

  var url = '<?=base_url("api_admin/crm/chat_admin/sendMessage/"); ?>'+ieid;
  $.ajax({
    type: $(this).attr('method'),
    url: url,
    data: fd,
    processData: false,
    contentType: false,
    success: function(respon){
      NProgress.done();
      if(respon.status==200){
        $('#fsendchat')[0].reset(); // Clear the form
        gritter('<h4>Success</h4><p>Message has been send!</p>','success');
        window.location.reload();
      }else{
        growlPesan = '<h4>Failed</h4><p></p>';
        growlType = 'danger';
        gritter('<h4>Failed</h4><p>'+respon.message+'</p>','danger');
      }
    },
    error:function(){
      NProgress.done();
      gritter('<h4>Error</h4><p>Cannot add chat right now, please try again later</p>','warning');
      return false;
    }
  });
});
$(document).ready(function() {
  if (window.File && window.FileList && window.FileReader) {
    $("#files").on("change", function(e) {
      var files = e.target.files,
      filesLength = files.length;
      var fv = $(this).clone();
      fv.removeAttr("id").removeAttr("name").attr("name","files[]").addClass("hidden");
      for (var i = 0; i < filesLength; i++) {
        var f = files[i];
        var fileReader = new FileReader();
        fileReader.onload = (function(e) {
          var file = e.target;
          $("#attachments").empty();
          $("#attachments").append(
            $("<span>").addClass("pip").append(
              $("<img>").attr("src",e.target.result).addClass("imageThumb")
            ).append(
              $("<br>")
            ).append(
              $("<span>").addClass("remove").text("Remove")
            ).append(fv)
          );
          $(".remove").click(function(){
            $(this).parent(".pip").remove();
          });

          // Old code here
          /*$("<img></img>", {
          class: "imageThumb",
          src: e.target.result,
          title: file.name + " | Click to remove"
        }).insertAfter("#files").click(function(){$(this).remove();});*/

      });
      fileReader.readAsDataURL(f);
    }
  });
} else {
  alert("Your browser doesn't support to File API");
}
});

//Js scroll always on bottom
var messageBody = document.querySelector('#scroll-chat-down');
messageBody.scrollTop = messageBody.scrollHeight - messageBody.clientHeight;
// end Js scroll always on bottom

$(".btn-complain-cancel").on("click",function(e){
  e.preventDefault();
  var c = confirm("Are you sure?");
  if(c){
    NProgress.start();
    var nation_code = $(this).attr("data-nation_code");
    var d_order_id = $(this).attr("data-d_order_id");
    var d_order_detail_id = $(this).attr("data-d_order_detail_id");
    var d_order_detail_item_id = $(this).attr("data-d_order_detail_item_id");
    $.get('<?=base_url("api_admin/crm/chat_admin/cancel/")?>'+nation_code+'/'+d_order_id+'/'+d_order_detail_id+'/'+d_order_detail_item_id+'/').done(function(dt){
      NProgress.done();
      window.location.reload();
    }).fail(function(){
      NProgress.done();
      gritter('<h4>Error</h4><p>Cannot take action right now, please try again later</p>','warning');
      return false;
    })
  }
});

$(".btn-solve-buyer").on("click",function(e){
  e.preventDefault();
  var c = confirm("Are you sure?");
  if(c){
    NProgress.start();
    var nation_code = $(this).attr("data-nation_code");
    var d_order_id = $(this).attr("data-d_order_id");
    var d_order_detail_id = $(this).attr("data-d_order_detail_id");
    var d_order_detail_item_id = $(this).attr("data-d_order_detail_item_id");
    $.get('<?=base_url("api_admin/crm/chat_admin/solved_to_buyer/")?>'+nation_code+'/'+d_order_id+'/'+d_order_detail_id+'/'+d_order_detail_item_id+'/').done(function(dt){
      NProgress.done();
      window.location.reload();
    }).fail(function(){
      NProgress.done();
      gritter('<h4>Error</h4><p>Cannot take action right now, please try again later</p>','warning');
      return false;
    })
  }
});

$(".btn-solve-seller").on("click",function(e){
  e.preventDefault();
  var c = confirm("Are you sure?");
  if(c){
    NProgress.start();
    var nation_code = $(this).attr("data-nation_code");
    var d_order_id = $(this).attr("data-d_order_id");
    var d_order_detail_id = $(this).attr("data-d_order_detail_id");
    var d_order_detail_item_id = $(this).attr("data-d_order_detail_item_id");
    $.get('<?=base_url("api_admin/crm/chat_admin/solved_to_seller/")?>'+nation_code+'/'+d_order_id+'/'+d_order_detail_id+'/'+d_order_detail_item_id+'/').done(function(dt){
      NProgress.done();
      window.location.reload();
    }).fail(function(){
      NProgress.done();
      gritter('<h4>Error</h4><p>Cannot take action right now, please try again later</p>','warning');
      return false;
    })
  }
});

$(document).ready(function() {
  
  $(".clearSelect2").on("click",function(e){
    e.preventDefault();
    
    $('#productCustomer1').val(null).trigger('change');
    
    $('#orderBuyerCustomer1').val(null).trigger('change');

    $('#orderSellerCustomer1').val(null).trigger('change');
  });

  $("#productCustomer1").select2({
    ajax: { 
      url: "<?= base_url('api_admin/ecommerce/produk/getproductajax') ?>",
      type: "post",
      dataType: 'json',
      delay: 250,
      data: function (params) {
        return {
          search: params.term, // search term
          user_id: '<?= $chat_room->b_user_id_2 ?>' // user id customer 1
        };
      },
      processResults: function (response) {
        return {
          results: response
        };
      }
    }
  });

  $("#orderBuyerCustomer1").select2({
    ajax: { 
      url: "<?= base_url('api_admin/ecommerce/transaction/getinvoiceajax') ?>",
      type: "post",
      dataType: 'json',
      delay: 250,
      data: function (params) {
        return {
          search: params.term, // search term
          user_id_buyer: '<?= $chat_room->b_user_id_2 ?>', // user id customer 1
          user_id_seller: 0
        };
      },
      processResults: function (response) {
        return {
          results: response
        };
      }
    }
  });

  $("#orderSellerCustomer1").select2({
    ajax: { 
      url: "<?= base_url('api_admin/ecommerce/transaction/getinvoiceajax') ?>",
      type: "post",
      dataType: 'json',
      delay: 250,
      data: function (params) {
        return {
          search: params.term, // search term
          user_id_buyer: 0, 
          user_id_seller: '<?= $chat_room->b_user_id_2 ?>' // user id customer 1
        };
      },
      processResults: function (response) {
        return {
          results: response
        };
      }
    }
  });

});
