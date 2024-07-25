var login_try = 0;

$(function(){ Login.init(); });

function gritter(pesan,jenis="info"){
	$.bootstrapGrowl(pesan, {
		type: jenis,
		delay: 3500,
		allow_dismiss: true
	});
}


$("#form-login").on("submit",function(evt){
	evt.preventDefault();
	login_try++;
	//$("#flogin_info").slideDown();
	$("#bsubmit").addClass("fa-spin");
	var url = '<?=base_url_admin('login/auth'); ?>';
	var fd = {};
	fd.username = $("#iusername").val();
	fd.password = $("#ipassword").val();
	if(fd.username.length<=3){
		$("#iusername").focus();
		gritter("<h4>Info</h4><p>Username too short</p>",'info');
		return false;
	}
	if(fd.password.length<=4){
		$("#ipassword").focus();
		gritter("<h4>Info</h4><p>Password too short</p>",'info');
		return false;
	}
	NProgress.start();
	$.post(url,fd).done(function(result){
		
		var hasil = result.data;
		if(result.status == "100" || result.status == 200){
			NProgress.done();
			gritter("<h4>Success</h4><p>Please wait while redirecting to dashboard</p>",'success');
			setTimeout(function(){
				$("#bsubmit").removeClass("fa-spin");
				window.location = hasil.redirect_url;
			},1000);
		}else{
			gritter("<h4>Failed</h4><p>"+result.message+"</p>",'danger');
			//$("#flogin_info").html('<strong>Failed</strong> '+result.message);
			setTimeout(function(){
				NProgress.done();
				$("#bsubmit").removeClass("fa-spin");
				if(login_try>2){
					window.location = hasil.redirect_url;
				}
			},1000);
		}
	}).fail(function(){
		gritter("<h4>Error</h4><p>Cant do login right now, please try again later</p>",'warning');
		NProgress.done();
	});
});
