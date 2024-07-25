function gritter(judul,isi){
  jQuery.gritter.add({
		title: judul,
		text: isi,
		image: '<?php echo base_url(); ?>assets/img/ji-char/smile.png',
		sticky: false,
		time: ''
	});
}
$("#fpassword").on("submit",function(e){
  var p1 = $("#ipassword").val();
  var p2 = $("#repassword").val();
  if(p1 != p2){
    gritter('Perhatian','Password baru dengan password konfirmasi tidak cocok');
    $("#ipassword").focus();
    return false;
  }
});
