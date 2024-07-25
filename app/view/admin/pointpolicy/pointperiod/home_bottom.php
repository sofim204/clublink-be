$("#page-content").on("submit",".form-setup",function(e){
	e.preventDefault();
	var c = confirm("Are you sure?");
	if(c){
		NProgress.start();
		var fd = new FormData($(this)[0]);
		var url = $(this).attr("action");
		$.ajax({
			type: $(this).attr('method'),
			url: url,
			data: fd,
			processData: false,
			contentType: false,
			success: function(respon){
				NProgress.done();
				if(respon.status == 200){
					growlType = 'success';
					growlPesan = '<h4>Success</h4><p>'+respon.message+'</p>';
					setTimeout(function(){
						$.bootstrapGrowl(growlPesan, {
							type: growlType,
							delay: 2500,
							allow_dismiss: true
						});
					}, 666);
				}else{
					growlType = 'danger';
					growlPesan = '<h4>Failed</h4><p>'+respon.message+'</p>';
					setTimeout(function(){
						$.bootstrapGrowl(growlPesan, {
							type: growlType,
							delay: 2500,
							allow_dismiss: true
						});
					}, 666);
				}
			},
			error: function(){
				NProgress.done();
				growlPesan = '<h4>Error</h4><p>Cannot process data right now, please try again later</p>';
				growlType = 'warning';
				setTimeout(function(){
					$.bootstrapGrowl(growlPesan, {
						type: growlType,
						delay: 2500,
						allow_dismiss: true
					});
				}, 666);
				return false;
			}
		});
	}
});

<!-- by Muhammad Sofi 2 February 2022 09:24 | add Maintenance App configuration -->
var cc = <?=json_encode($leaderboard_point)?>;
$.each(cc,function(k,v){
	$("#fs_"+k).val(v);
});