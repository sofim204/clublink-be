function gritter(judul,isi){
  jQuery.gritter.add({
		title: judul,
		text: isi,
		image: '<?php echo base_url(); ?>assets/img/comment.png',
		sticky: false,
		time: ''
	});
}
$("#alamat_list").on("click","a#adefault",function(e){
  e.preventDefault();
  var x = confirm('Apakah anda yakin?');
  if(x){
    var id = $(this).attr("data-id");
    var url = '<?php echo base_url('api_web/address/default/'); ?>'+encodeURIComponent(id);
    $.get(url).done(function(dt){
      if(dt.status == '100' || dt.status == 100){
        gritter('Berhasil','Alamat berhasil dihapus');
        setTimeout(function(){
          window.location = '<?php echo base_url('account/address/'); ?>';
        },3000);
      }else{
        gritter('Gagal',dt.message);
      }
    }).fail(function(){
      gritter('Error','Tidak dapat menghapus alamat, coba beberapa saat lagi');
    });
  }
});
$("#alamat_list").on("click","a#adelete",function(e){
  e.preventDefault();
  var x = confirm('Apakah anda yakin?');
  if(x){
    var id = $(this).attr("data-id");
    var url = '<?php echo base_url('api_web/address/delete/'); ?>'+encodeURIComponent(id);
    $.get(url).done(function(dt){
      if(dt.status == '100' || dt.status == 100){
        gritter('Berhasil','Alamat berhasil dihapus');
        setTimeout(function(){
          window.location = '<?php echo base_url('account/address/'); ?>';
        },3000);
      }else{
        gritter('Gagal',dt.message);
      }
    }).fail(function(){
      gritter('Error','Tidak dapat menghapus alamat, coba beberapa saat lagi');
    });
  }
});
