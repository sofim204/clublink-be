
function gritter(judul,isi){
  jQuery.gritter.add({
    title: judul,
    text: isi,
    image: '<?php echo base_url(); ?>favicon.png',
    sticky: false,
    time: ''
  });
}

$("#fregister").on("submit",function(evt){
  evt.preventDefault();
  $("#fregister_warning").hide();
  $("#fregister_info").hide();

  var url_email = '<?php echo base_url('api_web/account/email_check'); ?>';
  var url = '<?php echo base_url('api_web/account/register'); ?>';

  var fdata = $(this).serialize();

  //console.log(fdata);
  if($("#fregister #iemail").val().length<=4){
    gritter('Perhatian','Isi Email dengan lengkap');
    $("#fregister #iemail").focus();
    return false;
  }
  if($("#fregister #itelp").val().length<=5){
    gritter('Perhatian','Isi Telp dengan lengkap');
    $("#fregister #itelp").focus();
    return false;
  }

  //check email
  $.post(url_email,{email:$("#fregister #iemail").val()}).done(function(dt){
    if(dt.status == 100 || dt.status=='100'){
      $.post(url,fdata).done(function(response){
        if(response.status=="100"){
          gritter('Berhasil','Pendaftaran berhasil, menuju ke halaman dashboard...');
          setTimeout(function (){
            window.location = response.result.redirect_url;
          },3000);
        }else{
          gritter('Gagal',response.message);
        }
      }).fail(function(err){
        gritter('Error','Saat ini proses pendaftaran tidak dapat dilakukan, silakan coba beberapa saat lagi');
      });
    }else{
      gritter('Perhatian','Email sudah digunakan, silakan coba email yang lain');
    }
  });

});
