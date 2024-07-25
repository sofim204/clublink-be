//media

var media_id = '';
var folder_id = '';
var media_name = '';
var galeri_item_count = 0;
var image_list_count = 0;

window.toPlainFloat = function(mny){
	return mny.replace( /^\D+/g, '').split('.').join("");
}

function gritter(pesan,jenis='info'){
	$.bootstrapGrowl(pesan, {
		type: jenis,
		delay: 2500,
		allow_dismiss: true
	});
}

function growlShow(pesan,type='danger'){
	$.bootstrapGrowl(pesan, {
		type: type,
		delay: 2500,
		allow_dismiss: true
	});
}

//SEO
function convertToSlug(title){
	return title
		.toString().toLowerCase()
		.replace(/\s+/g, '-')           // Replace spaces with -
		.replace(/[^\w\-]+/g, '')       // Remove all non-word chars
		.replace(/\-\-+/g, '-')         // Replace multiple - with single -
		.replace(/^-+/, '')             // Trim - from start of title
		.replace(/-+$/, '');
}


//form
$("#fedit").on("submit",function(e){
	e.preventDefault();
	NProgress.start();
	gritter('<h4>Processing</h4><p>Please wait...</p>','info');
	for ( instance in CKEDITOR.instances ) CKEDITOR.instances[instance].updateElement();
	var fd = new FormData($(this)[0]);

	$.ajax({
		url: '<?=base_url('api_admin/ecommerce/produkwanted/edit/'.$produkwanted->id); ?>',
		type: "POST",
		data: fd,
		contentType: false,
		cache: false,
		processData:false,
		success: function(data){
			NProgress.done();
			if(data.status == '100' || data.status == 100){
				growlShow('<h4>Success</h4><p>Product has changed..</p>','success');
				setTimeout(function(){
					window.location = '<?=base_url_admin('ecommerce/produkwanted/'); ?>';
				},3000);
			}else{
				growlShow('<h4>Failed</h4><p>'+data.message+'</p>','danger');
			}
		},
		error: function(data){
			NProgress.done();
			growlShow('<h4>Error</h4><p>Cant edit product right now, please try again later</p>','warning');
		}
	});
});


$("a.btn-hidden-block").on("click",function(e){
	e.preventDefault();
	$(this).parent().parent().next().slideToggle('slow');
});

$("#buser_search").on("click",function(e){
	e.preventDefault();
	$("#user_search_modal").modal("show");
});

$("#user_search_modal_form").on("submit",function(e){
	e.preventDefault();
	NProgress.start();
	var tb = $("#user_search_table");
	$(tb).find("tbody").empty();
	var keyword = $("#user_search_modal_input").val();
	$.get("<?=base_url("api_admin/ecommerce/pelanggan/cari/")?>?keyword="+encodeURIComponent(keyword)).done(function(respon){
		NProgress.done();
		$.each(respon.data.pelanggan,function(kdt,vdt){
			var h = '<tr id="trid_'+vdt.id+'" class="user_search_table_tr">';
			h += '<td>'+vdt.id+'</td>';
			h += '<td>'+vdt.fnama+'</td>';
			h += '<td>'+vdt.email+'</td>';
			h += '<td><button type="button" class="btn btn-default btn-customer-choose"  data-id="'+vdt.id+'" data-fnama="'+vdt.fnama+'" data-alamat="'+vdt.alamat+'" data-kecamatan="'+vdt.kecamatan+'" data-kabkota="'+vdt.kabkota+'" data-provinsi="'+vdt.provinsi+'" data-negara="'+vdt.negara+'" data-latitude="'+vdt.latitude+'" data-longitude="'+vdt.longitude+'">Choose</button></td>';
			h += '</tr>';
			$(tb).find("tbody").append(h);
		});
		setTimeout(function(){
			var tbb = $("#user_search_table tbody");
			$(tbb).off("click",".btn-customer-choose");
			$(tbb).on("click",".btn-customer-choose",function(ev){
				ev.preventDefault();
				var did = $(this).attr("data-id");

				$("#ieb_user_fnama").val($(this).attr("data-fnama"));
				$("#ieb_user_id").val($(this).attr("data-id"));
				$("#iealamat").val($(this).attr("data-alamat"));
				$("#iekecamatan").val($(this).attr("data-kecamatan"));
				$("#iekabkota").val($(this).attr("data-kabkota"));
				$("#ieprovinsi").val($(this).attr("data-provinsi"));
				$("#ienegara").val($(this).attr("data-negara"));
				$("#ielatitude").val($(this).attr("data-latitude"));
				$("#ielongitude").val($(this).attr("data-longitude"));
				$("#iekodepos").val($(this).attr("data-kodepos"));

				$("#user_search_modal").modal("hide");
			});
		},333);
	}).fail(function(){
		NProgress.done();
		gritter("<h4>Error</h4><p>Cant search customer right now, please try again later</p>");
	})
});
