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
$("#ftambah").on("submit",function(e){
	e.preventDefault();
	NProgress.start();
	//if using ckeditor
	updateCkEditor();
	//get al value from form as fd formdata object
	var fd = new FormData($(this)[0]);
	var url = '<?=base_url("api_admin/game/listing/tambah/"); ?>';

	$.ajax({
		type: $(this).attr('method'),
		url: url,
		data: fd,
		processData: false,
		contentType: false,
		success: function(respon){
			NProgress.done();
			if(respon.status==200){
				gritter('<h4>Success</h4><p>New game successfully added</p>','success');
				setTimeout(function(){
					window.location = '<?=base_url_admin('game/listing/')?>';
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

