<!-- global variable -->
var type_homelist = ""

function gritter(pesan,jenis="info"){
	$.bootstrapGrowl(pesan, {
		type: jenis,
		delay: 2500,
		allow_dismiss: true
	});
}
$(document).ready(function() {

	<!-- get url -->
	const url = window.location.href;
	const split_url = url.split("/")
	<!-- assign to global variable -->
	const gettype = split_url[8]
	type_homelist = gettype
	if(gettype != "sub_category") {
		$('.show-subcategories, .image-banner-container').addClass('hidden');
	} else {
		$('.show-subcategories, .image-banner-container').removeClass('hidden');
	}

	//submit form
	$("#fedit").on("submit",function(e) {
		e.preventDefault();
		NProgress.start();

		//get al value from form as fd formdata object
		var fd = new FormData($(this)[0]);
		var url = '<?=base_url("api_admin/band/homelist/edit/".$homelist_data->id); ?>';

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
					setTimeout(function() {
						window.location = '<?=base_url_admin('band/homelist/')?>';
					},500);
				}else{
					gritter('<h4>Failed</h4><p>'+respon.message+'</p>','danger');
				}
			},
			error: function(){
				NProgress.done();
				setTimeout(function(){
					gritter('<h4>Error</h4><p>Cant change data right now, please try again later</p>','warning');
				}, 666);
				return false;
			}
		});
	});

	$("#select_sub_category").select2({
		//placeholder: "--Select Category--",
		ajax: { 
			url: "<?= base_url('api_admin/band/categories/getListCategory/') ?>" + type_homelist,
			type: "post",
			dataType: 'json',
			delay: 250,
			data: function (params) {
				return {
					search: params.term, // search term
				};
			},
			processResults: function (response) {
				response.unshift({id: '', text: '===== Cancel your selection ====='})
				return {
					results: response
				};
			}
		}
	}).on("change", function(){
		console.log($(this))
		$("#iei_group_sub_category_id").val($(this).val())
	});
})

//fill data
<?php
	$katdata = $homelist_data;	
?>
var data_fill = <?=json_encode($katdata)?>;
console.log(data_fill)
$.each(data_fill, function(k,v){
	$("#ie"+k).val(v);
});