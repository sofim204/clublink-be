$(function(){ Login.init(); });
$(document).ready(function(){
	$("#form-login").on("submit",function(evt){
		evt.preventDefault();
		$("#flogin_info").slideDown();
		$("#bsubmit").addClass("fa-spin");
		var url = '<?php echo base_url('login/auth'); ?>';
		var fd = {};
		fd.username = $("#iusername").val();
		fd.password = $("#ipassword").val();
		$.post(url,fd).done(function(h){
			var hasil = h.result;
			console.log(hasil);
			$("#flogin_info").html('<i class="fa fa-spin fa-sync"></i> Loading...');
			if(hasil.status == "100" || hasil.status == 100){
				$("#flogin_info").html('<strong>Berhasil</strong> Silakan tunggu...');
				setTimeout(function(){
					$("#bsubmit").removeClass("fa-spin");
					window.location = hasil.redirect_url;
				},3000);
			}else{
				$("#flogin_info").html('<strong>Gagal</strong> '+hasil.message);
				setTimeout(function(){
					$("#bsubmit").removeClass("fa-spin");
					window.location = hasil.redirect_url;
				},3000);
			}
			
		});
	});
});