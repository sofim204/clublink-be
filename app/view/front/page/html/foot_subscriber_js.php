$("#form_subscriber_footer").on("submit",function(e){
  e.preventDefault();
  var email = $("#isubscriber_email").val();
  var telp = $("#isubscriber_telp").val();
  if(email.length>4 && telp.length>4){
    var fd = {};
    fd.email = email;
    fd.telp = telp;
    $.post('<?=base_url()?>api_web/langganan/daftar/',fd).done(function(dta){
      if(dta.status == 100 || dta.status == '100'){
        alert('Langganan berhasil');
      }else{
        alert("Error: "+dta.message);
      }
    }).fail(function(dt){
      alert('Untuk saat ini tidak dapat mendaftarkan langganan email, cobalah beberapa saat lagi');
    });
  }else{
    alert('Silakan lengkapi email atau nomor teleponnya');
    return false;
  }
})
