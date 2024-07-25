$("#aagree").on("click",function(evt){
	evt.preventDefault();
	var url = '<?php echo base_url('api_web/account/do_agree/');?>';
	$.get(url).done(function(dta){
		if(dta.status=="100" || dta.status==100){
			alert('terimakasih...');
		}else{
		}
		setTimeout(function(){
			window.location = '<?php echo base_url('account/dashboard'); ?>';
		},3000);
	});
});