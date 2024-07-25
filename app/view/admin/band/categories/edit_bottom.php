function gritter(pesan,jenis="info"){
	$.bootstrapGrowl(pesan, {
		type: jenis,
		delay: 2500,
		allow_dismiss: true
	});
}

$(document).ready(function() {	
	function getQueryParams(url) {
		const queryString = url.split('?')[1];
		const params = {};
		if (queryString) {
			queryString.split('&').forEach((param) => {
				const [key, value] = param.split('=');
				params[key] = decodeURIComponent(value);
			});
		}
		return params;
	}

	const queryParams = getQueryParams(window.location.href);
	const typeOfCategory = queryParams.type;

	if(typeOfCategory == "category") {
		$('.title-subcategories').addClass('hidden')
	} else if(typeOfCategory == "sub_category") {
		$('.title-subcategories').removeClass('hidden')
	}
});

//submit form
$("#fedit").on("submit",function(e){
	e.preventDefault();
	NProgress.start();

	//get al value from form as fd formdata object
	var fd = new FormData($(this)[0]);
	var url = '<?=base_url("api_admin/band/categories/edit/".$category_data->id); ?>';

	$.ajax({
		type: $(this).attr('method'),
		url: url,
		data: fd,
		processData: false,
		contentType: false,
		success: function(respon){
			NProgress.done();
			if(respon.status==200){
				gritter('<h4>Success</h4><p>Data successfully changed</p>','success');
				setTimeout(function(){
					window.location = '<?=base_url_admin('band/categories/')?>';
				},500);
			}else{
				gritter('<h4>Failed</h4><p>'+respon.message+'</p>','danger');
			}
		},
		error:function(){
			NProgress.done();
			setTimeout(function(){
				gritter('<h4>Error</h4><p>Cant change data right now, please try again later</p>','warning');
			}, 666);
			return false;
		}
	});

});

//fill data
<?php
$katdata = $category_data;
foreach($katdata as $ky=>&$kv){
	if($ky=='deskripsi'){
		//$kv = $this->seme_purifier->richtext($kv);
	}else{
		//$kv = $this->__f($kv);
	}
	// by Muhammad Sofi 11 January 2022 13:33 | read special character like &#38 ;
	if($ky == 'nama' || $ky == 'indonesia' || $ky == 'korea' || $ky == 'thailand') {
		$kv = html_entity_decode($kv, ENT_QUOTES);
	}
}	
?>
var data_fill = <?=json_encode($katdata)?>;
$.each(data_fill,function(k,v){
	$("#ie"+k).val(v);
});