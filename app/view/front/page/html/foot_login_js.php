<?php
if(!isset($login_redirect)) $login_redirect = 0;
?>
var login_redirect = <?php echo $login_redirect; ?>;

$("#loginModal").on("hidden.bs.modal",function(evt){
  $("#flogin_info").hide("slow");
  $("#flogin_warning").hide("slow");
});

$("#flogin").on("submit",function(evt){
  evt.preventDefault();
  var url = '<?php echo base_url('api_web/account/auth'); ?>';
  var fdata = {};
  fdata.email = $("#flogin")[0].email.value;
  fdata.password = $("#flogin")[0].password.value;
  $.post(url,fdata).done(function(data){
    if(data.status=="100"){
      $("#flogin_info").html('<strong>Login Berhasil</strong> Silakan tunggu...');
      $("#flogin_info").slideDown("slow");

      $("#loginModal").modal("hide");
      $("#b_cart_add").trigger("click");
			setTimeout(function(){window.location.reload();},3000);
    }else{
      $("#flogin_warning").html('<strong>Login Gagal</strong> Sepertinya emailnya belum terdaftar atau kombinasi email dengan passwordnya tidak cocok, silakan coba lagi');
      $("#flogin_warning").slideDown("slow");
    }
    }).fail(function(err){
    //alert('Saat ini tidak bisa login dulu, silakan coba beberapa saat lagi');
    $("#flogin_warning").html('<strong>Login Gagal</strong> Saat ini tidak bisa login dulu, silakan coba beberapa saat lagi');
    $("#flogin_warning").slideDown("slow");
  });
});
