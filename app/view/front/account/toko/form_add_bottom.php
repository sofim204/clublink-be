//alert("ok");

function gritter(judul,isi){
  jQuery.gritter.add({
		title: judul,
		text: isi,
		image: '<?php echo base_url(); ?>assets/img/ji-char/smile.png',
		sticky: false,
		time: ''
	});
}

$("#f_store_add").on("submit",function(e){
  e.preventDefault();
  //alert("ok");

  var fd=new FormData($(this)[0]);

  $.ajax({
    url: '<?php echo base_url("api_web/toko/add"); ?>',
    data: fd,
    cache: false,
    contentType: false,
    processData: false,
    method: 'POST',
    success: function(dt){
        if(dt.status=="100" || dt.status==100){
          //alert("berhasil");
          gritter("Berhasil","Toko Berhasil Ditambahkan");
        }
        else{
          gritter("Gagal", dt.message);
        }
    },
    error: function(){
      gritter("Error","Untuk saat ini tidak dapat menambahkan toko silahkan coba beberapa saat lagi");
    }
});

});
