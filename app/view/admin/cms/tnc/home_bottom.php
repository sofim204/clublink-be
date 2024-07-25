<!-- START by Muhammad Sofi 19 January 2022 12:05 | move layout tnc from edit to home -->
function gritter(pesan,jenis='info'){
	$.bootstrapGrowl(pesan, {
		type: jenis,
		delay: 2500,
		allow_dismiss: true
	});
}

<!-- by Muhammad Sofi 19 January 2022 10:23 | change height of textarea ckeditor -->
CKEDITOR.replace('itnc', {
	height: 450,
	resize_enabled: false
});

CKEDITOR.replace('itnc_indonesia', {
	height: 450,
	resize_enabled: false
});

NProgress.start();
setTimeout(function(){

	var url = '<?=base_url()?>api_admin/cms/tnc/';
	//NProgress.start();
	$.get(url).done(function(response){
		NProgress.done();
		if(response.status==200){
			var dta = response.data;
			CKEDITOR.instances['itnc'].setData(dta.content);
		} else {
			gritter('<h4>Failed</h4><p>'+response.message+'</p>','danger');
		}
	});

}, 1000); <!-- reduce timing when load data -->

NProgress.start();
setTimeout(function(){

	var url = '<?=base_url()?>api_admin/cms/tnc/indonesia';
	//NProgress.start();
	$.get(url).done(function(response){
		NProgress.done();
		if(response.status==200){
			var dta = response.data;
			CKEDITOR.instances['itnc_indonesia'].setData(dta.content);
		} else {
			gritter('<h4>Failed</h4><p>'+response.message+'</p>','danger');
		}
	});

}, 1000); <!-- reduce timing when load data -->

$("#tnc_form").on("submit",function(e){
	e.preventDefault();
	NProgress.start();
	<!-- gritter('<h4>Processing</h4><p>Please wait...</p>','info'); -->
	for ( instance in CKEDITOR.instances ) CKEDITOR.instances[instance].updateElement();
	var fd = new FormData($(this)[0]);
	var url = '<?=base_url("api_admin/cms/tnc/edit/")?>';
	$.ajax({
		type: $(this).attr('method'),
		url: url,
		data: fd,
		processData: false,
		contentType: false,
		success: function(response){
			NProgress.done();
			if(response.status==200){
				gritter('<h4>Success</h4><p>Terms and Condition Updated Successfully!</p>','success');
				CKEDITOR.instances['itnc'].setData(response.content);
			}else{
				gritter('<h4>Failed</h4><p>'+response.message+'</p>','danger');
			}
		},
		error:function(){
			NProgress.done();
			gritter('<h4>Error</h4><p>Cant change data right now, please try again later</p>','warning');
			return false;
		}
	});
});

$("#tnc_form_indonesia").on("submit",function(e){
	e.preventDefault();
	NProgress.start();
	<!-- gritter('<h4>Processing</h4><p>Please wait...</p>','info'); -->
	for ( instance in CKEDITOR.instances ) CKEDITOR.instances[instance].updateElement();
	var fd = new FormData($(this)[0]);
	var url = '<?=base_url("api_admin/cms/tnc/edit_indonesia/")?>';
	$.ajax({
		type: $(this).attr('method'),
		url: url,
		data: fd,
		processData: false,
		contentType: false,
		success: function(response){
			NProgress.done();
			if(response.status==200){
				gritter('<h4>Success</h4><p>Syarat dan Ketentuan Updated Successfully!</p>','success');
				CKEDITOR.instances['itnc'].setData(response.content);
			}else{
				gritter('<h4>Failed</h4><p>'+response.message+'</p>','danger');
			}
		},
		error:function(){
			NProgress.done();
			gritter('<h4>Error</h4><p>Cant change data right now, please try again later</p>','warning');
			return false;
		}
	});
});
<!-- END by Muhammad Sofi 19 January 2022 12:05 | move layout tnc from edit to home -->
