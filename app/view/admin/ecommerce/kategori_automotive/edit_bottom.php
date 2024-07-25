function updateCkEditor(){
	for (instance in CKEDITOR.instances) {
		CKEDITOR.instances[instance].updateElement();
		//$("#"+instance).val(CKEDITOR.instances[instance].getData());
	}
}

function gritter(pesan,jenis="info"){
	$.bootstrapGrowl(pesan, {
		type: jenis,
		delay: 2500,
		allow_dismiss: true
	});
}

//submit form
$("#fedit").on("submit",function(e){
	e.preventDefault();
	NProgress.start();
	//if using ckeditor
	updateCkEditor();
	//get al value from form as fd formdata object
	var fd = new FormData($(this)[0]);
	var url = '<?=base_url("api_admin/ecommerce/kategori_automotive/edit/".$kategori_data->id); ?>';

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
					window.location = '<?=base_url_admin('ecommerce/kategori_automotive/')?>';
				},1000);
			}else{
				gritter('<h4>Failed</h4><p>'+respon.message+'</p>','danger');
			}
			setTimeout(function(){
				NProgress.done();
			}, 666);
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
$katdata = $kategori_data;
foreach($katdata as $ky=>&$kv){
	if($ky=='deskripsi'){
		//$kv = $this->seme_purifier->richtext($kv);
	}else{
		//$kv = $this->__f($kv);
	}
}
?>
var data_fill = <?=json_encode($katdata)?>;
$.each(data_fill,function(k,v){
	$("#ie"+k).val(v);
});
