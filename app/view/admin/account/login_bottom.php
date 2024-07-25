$(function(){ Login.init(); });
var login_try = 0;
$("#form-login").on("submit",function(evt){
	evt.preventDefault();
	login_try++;
	$("#flogin_info").slideDown();
	$("#bsubmit").addClass("fa-spin");
	var url = '<?php echo base_url_admin('login/auth'); ?>';
	var fd = {};
	fd.username = $("#iusername").val();
	fd.password = $("#ipassword").val();
	if(fd.username.length<=3){
		$("#iusername").focus();
		return false;
	}
	if(fd.username.length<=4){
		$("#ipassword").focus();
		return false;
	}
	NProgress.start();
	$.post(url,fd).done(function(result){
		var hasil = result.data;
		console.log(hasil);
		$("#flogin_info").html('<i class="fa fa-spin fa-sync"></i> Loading...');
		if(result.status == "100" || result.status == 100){
			$("#flogin_info").html('<strong>Success</strong> Please wait...');
			setTimeout(function(){
				NProgress.done();
				$("#bsubmit").removeClass("fa-spin");
				window.location = hasil.redirect_url;
			},3000);
		}else{
			$("#flogin_info").html('<strong>Failed</strong> '+result.message);
			setTimeout(function(){
				NProgress.done();
				$("#bsubmit").removeClass("fa-spin");
				if(login_try>2){
					window.location = hasil.redirect_url;
				}
			},3000);
		}

	}).fail(function(){
		$("#flogin_info").html('<strong>Error</strong> Please check connection and try again');
		NProgress.done();
	});
});
