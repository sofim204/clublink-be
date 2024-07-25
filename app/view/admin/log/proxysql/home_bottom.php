<!-- popUp();
function popUp() {
  let text;
  let pin = prompt("Please enter PIN:", "");
  var url = '<?=base_url("api_admin/log/proxysql/security/"); ?>';
  if (pin) {
	$.ajax({
        url: url,
        type: 'POST',
        data: { pin: pin },
        success: function(respon){
			if(respon.status=="200" || respon.status == 200){

			}else{
				growlType = 'danger';
				growlPesan = '<h4>Failed</h4><p>'+respon.message+'</p>';
				window.location.href = "<?=base_url_admin(); ?>";
			}
		},
		error:function(){
			growlPesan = '<h4>Error</h4><p>Cannot process data right now, please try again later</p>';
			window.location.href = "<?=base_url_admin(); ?>";
		}
    })
  } else {
	growlPesan = '<h4>Error</h4><p>Cannot process data right now, please try again later</p>';
	window.location.href = "<?=base_url_admin(); ?>";
  }
} -->