var media_target_div = 'dgaleri_items';
var media_single = 0;
var media_name = 'image[]';
var media_caption = 0;
var media_id = '';
var folder_id = '';
var galeri_item_count = 0;

function gritter(pesan,jenis="info"){
	$.bootstrapGrowl(pesan, {
		type: jenis,
		delay: 3500,
		allow_dismiss: true
	});
}

function updateCkEditor(){
	for (instance in CKEDITOR.instances) {
		CKEDITOR.instances[instance].updateElement();
		//$("#"+instance).val(CKEDITOR.instances[instance].getData());
	}
}
//form control

$("#iutype").on("change",function(e){
	e.preventDefault();
	var v = $(this).val();
	if(v.toLowerCase() == 'kategori' || v.toLowerCase() == 'tag'){
		$("#ib_kategori_id").val('null');
		$("#ib_kategori_id").prop('disabled',1);
	}else{
		$("#ib_kategori_id").removeAttr('disabled');
	}
});

// function genKode(){
// 	var n = $("#inama").val().toUpperCase().replace(/[^\w\s]/gi, '');
// 	var ns = n.split(" ");
// 	if(ns.length>=2){
// 		n = ns[0].charAt(0)+ns[1].charAt(0);
// 	}else{
// 		n = n.slice(0,2);
// 	}
// 	var u = $("#iutype option:selected").attr('data-kode').toUpperCase();
// 	var p = '';
// 	if($("#ib_kategori_id option:selected").attr('data-kode') !== undefined){
// 		p = $("#ib_kategori_id option:selected").attr('data-kode').toUpperCase().slice(0,2);
// 	}
// 	$("#ikode").val(p+n+u);
// }
// $("#inama").on("blur",function(e){e.preventDefault(); genKode()});
// $("#iutype").on("blur",function(e){e.preventDefault(); genKode()});
// $("#ib_kategori_id").on("blur",function(e){e.preventDefault(); genKode()});

//end form control


//seo Start

function convertToSlug(Text){
	return Text
		.toString().toLowerCase()
		.replace(/\s+/g, '-')           // Replace spaces with -
		.replace(/[^\w\-]+/g, '')       // Remove all non-word chars
		.replace(/\-\-+/g, '-')         // Replace multiple - with single -
		.replace(/^-+/, '')             // Trim - from start of text
		.replace(/-+$/, '');
}
function convertToKeyword(Text){
	return Text
		.toString().toLowerCase()
		.replace(/\s+/g, ',')           // Replace spaces with -
		//.replace(/[^\w\-]+/g, '')       // Remove all non-word chars
		.replace(/\-\-+/g, ',')         // Replace multiple - with single -
		.replace(/^-+/, '')             // Trim - from start of text
		.replace(/-+$/, '');
}
function convertToCode(Text){
	return Text
		.toString().toLowerCase()
		.replace(/\s+/g, ''+makeid())           // Replace spaces with -
		.replace(/[^\w\-]+/g, '')       // Remove all non-word chars
		.replace(/\-\-+/g, ''+makeid())         // Replace multiple - with single -
		.replace(/^-+/, '')             // Trim - from start of text
		.replace(/-+$/, '');
}
function makeid(){
	var i=0
	var text = "";
	var possible = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
	for(i=0;1>i;i++){
		text += possible.charAt(Math.floor(Math.random() * possible.length));
		return text;
	}
}

//seo end

//submit form
$("#fupload").on("submit",function(e){
	e.preventDefault();
	var ifupload = $("#ifupload").val();
	var iseller = $("#iseller").val();
	if (iseller == '' || iseller == null) {
		alert('Email must be filled')
	} else if (ifupload == '' || ifupload == null) {
		alert('File must be filled')
	} else {
		NProgress.start();
		//get al value from form as fd formdata object
		var fd = new FormData($(this)[0]);
		var url = '<?=base_url("api_admin/ecommerce/produk/upload_xls/"); ?>';

		$.ajax({
			type: $(this).attr('method'),
			url: url,
			data: fd,
			processData: false,
			contentType: false,
			success: function(respon){
				NProgress.done();
				if(respon.status==200){
					gritter('<h4>Success</h4><p>New Data successfully added</p>','success');
					setTimeout(function(){
						window.location = '<?=base_url_admin('ecommerce/produk/')?>';
					},500);
				}else{
					gritter('<h4>Failed</h4><p>'+respon.message+'</p>','danger');
					alert(respon.message);
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
	}
});

<!-- MODAL VALIDATION -->



//edit
$("#bcheck_email").on("click",function(e){
	//
	var email = $("#iseller").val();
	if (email == "") {
		alert('Email must be filled');
	} else{
		var url = '<?=base_url(); ?>api_admin/ecommerce/produk/checkEmail/'+email;
		NProgress.start();

		$("#modal_validation").modal("show");

		$("#ivunama").html("<i>Loading . . .</i>");
		$("#ivuemail").html("<i>Loading . . .</i>");
		$("#ivusignup_method").html("<i>Loading . . .</i>");
		$.get(url).done(function(response){
			NProgress.done();
			if(response.status==200){
				var dta = response.data;
				$("#ivunama").html("<b>"+dta.fnama+"</b>");
				$("#ivuemail").html("<b>"+dta.email+"</b>");
				$("#ivusignup_method").text(dta.register_from);
			}else{
				$("#ivunama").html("<i>Data Not found (Data tidak ditemukan)</i>");
				$("#ivuemail").html("<i>Data Not found (Data tidak ditemukan)</i>");
				$("#ivusignup_method").html("<i>Data Not found (Data tidak ditemukan)</i>");
				gritter('<h4>Error</h4><p>Invalid ID or ID has been deleted</p>','danger');
				setTimeout(function(){
					$("#modal_validation").modal("hide");
					$("#modal_validation").find("form").trigger("reset");
				}, 2000);
			}
		});
	}	
});
$("#modal_validation").on("hidden.bs.modal",function(e){

	$("#ivunama").text("");
	$("#ivuemail").text("");
	$("#ivusignup_method").text("");

	$("#modal_validation").find("form").trigger("reset");
});



