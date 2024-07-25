var ieid = '<?=$order->id?>';

function gritter(pesan,jenis='info'){
  $.bootstrapGrowl(pesan, {
    type: jenis,
    delay: 3500,
    allow_dismiss: true
  });
}

$("#bactivated").on('click',function(e){
  e.preventDefault();
  NProgress.start();
  $.get("<?=base_url("api_admin/ecommerce/transactionhistory/activated/")?>"+ieid).done(function(dt){
    NProgress.done();
    $("#modal_option").modal("hide");
    if(dt.status == "200"){
      gritter("<h4>Success</h4><p>User activated.</p>",'success');
      window.location.reload();
    }else{
      gritter("<h4>Failed</h4><p>"+dt.message+"</p>",'danger');
    }
  }).fail(function(e){
    NProgress.done();
    gritter("<h4>Error</h4><p>Cant change user activation right now, please try again.</p>",'warning');
  })
});

$("#bdeactivated").on('click',function(e){
  e.preventDefault();
  NProgress.start();
  $.get("<?=base_url("api_admin/ecommerce/transactionhistory/deactivated/")?>"+ieid).done(function(dt){
    NProgress.done();
    $("#modal_option").modal("hide");
    if(dt.status == "200"){
      gritter("<h4>Success</h4><p>User deactivated.</p>",'success');
      window.location.reload();
    }else{
      gritter("<h4>Failed</h4><p>"+dt.message+"</p>",'danger');
    }
  }).fail(function(e){
    NProgress.done();
    gritter("<h4>Error</h4><p>Cant change user activation right now, please try again.</p>",'warning');
  })
});

$("#bemail_konfirmasi").on('click',function(e){
  e.preventDefault();
  var c = confirm('Are you sure?');
  if(c){
    NProgress.start();
    $.get("<?=base_url("api_admin/ecommerce/transactionhistory/email_konfirmasi/")?>"+ieid).done(function(dt){
      NProgress.done();
      $("#modal_option").modal("hide");
      if(dt.status == "200"){
        gritter("<h4>Success</h4><p>Registration confirmation link email has been sent</p>",'success');
        //window.location.reload();
      }else{
        gritter("<h4>Failed</h4><p>"+dt.message+"</p>",'danger');
      }
    }).fail(function(e){
      NProgress.done();
      gritter("<h4>Error</h4><p>Cant send email right now, please try again.</p>",'warning');
    });
  }
});


$("#bemail_lupa").on('click',function(e){
  e.preventDefault();
  var c = confirm('Are you sure?');
  if(c){
    NProgress.start();
    $.get("<?=base_url("api_admin/ecommerce/transactionhistory/email_lupa/")?>"+ieid).done(function(dt){
      NProgress.done();
      $("#modal_option").modal("hide");
      if(dt.status == "200"){
        gritter("<h4>Success</h4><p>Reset password link has been sent</p>",'success');
        //window.location.reload();
      }else{
        gritter("<h4>Failed</h4><p>"+dt.message+"</p>",'danger');
      }
    }).fail(function(e){
      NProgress.done();
      gritter("<h4>Error</h4><p>Cant send email right now, please try again.</p>",'warning');
    });
  }
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