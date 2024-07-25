var type = "category"

function gritter(pesan,jenis="info"){
	$.bootstrapGrowl(pesan, {
		type: jenis,
		delay: 3500,
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
	const typeOfCategory = queryParams.add;

	if(typeOfCategory == "category") {
		$('.show-subcategories, .title-subcategories').addClass('hidden')
	} else if(typeOfCategory == "sub_category") {
		$('.show-subcategories, .title-subcategories').removeClass('hidden')
	}
});

$("#select_category").select2({
	//placeholder: "--Select Category--",
	ajax: { 
		url: "<?= base_url('api_admin/band/categories/getListCategory/') ?>" + type,
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
});

//submit form
$("#ftambah").on("submit",function(e){
	e.preventDefault();
	NProgress.start();
	//if using ckeditor
	//get al value from form as fd formdata object
	var fd = new FormData($(this)[0]);
	var url = '<?=base_url("api_admin/band/categories/tambah/"); ?>';

	$.ajax({
		type: $(this).attr('method'),
		url: url,
		data: fd,
		processData: false,
		contentType: false,
		success: function(respon){
			NProgress.done();
			if(respon.status==200){
				gritter('<h4>Success</h4><p>Data successfully added</p>','success');
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
				gritter('<h4>Error</h4><p>Cant create data right now, please try again later</p>','warning');
			}, 666);
			return false;
		}
	});

});