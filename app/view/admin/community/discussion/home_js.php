var growlPesan = '<h4>Error</h4><p>Tidak dapat diproses, silakan coba beberapa saat lagi!</p>';
var growlType = 'danger';
var drTable = {};
var ieid = '';
App.datatables();

function updateCkEditor(){
	for (instance in CKEDITOR.instances) {
		CKEDITOR.instances[instance].updateElement();
		//$("#"+instance).val(CKEDITOR.instances[instance].getData());
	}
}
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
function gritter(pesan,jenis="info"){
	$.bootstrapGrowl(pesan, {
		type: jenis,
		delay: 2500,
		allow_dismiss: true
	});
}


if(jQuery('#drTable').length>0){
	drTable = jQuery('#drTable')
	.on('preXhr.dt', function ( e, settings, data ){
		NProgress.start();
	}).DataTable({
			"order"				: [[ 0, "desc" ]],
			"responsive"	  	: true,
			"bProcessing"		: true,
			"bServerSide"		: true,
			"sAjaxSource"		: "<?=base_url("api_admin/community/discussion/"); ?>",
			"fnServerData"	: function (sSource, aoData, fnCallback, oSettings) {
				oSettings.jqXHR = $.ajax({
					dataType 	: 'json',
					method 		: 'POST',
					url 		: sSource,
					data 		: aoData
				}).success(function (response, status, headers, config) {
					NProgress.done();
					console.log(response);
					$('#drTable > tbody').off('click', 'tr');
					$('#drTable > tbody').on('click', 'tr', function (e) {
						e.preventDefault();
						var id = $(this).find("td").html();
						ieid = id;
						$("#modal_option").modal("show");
					});
					fnCallback(response);
				}).error(function (response, status, headers, config) {
					NProgress.done();
					gritter("<h4>Error</h4><p>Cant fetch data right now, please try again later</p>",'warning');
				});
			},
	});
	$('.dataTables_filter input').attr('placeholder', 'Search category name');
}

// ===========================================================================
// ||                                                                       ||
// ||                               A D D                                   ||
// ||                                                                       ||
// ===========================================================================

$("#atambah").on("click",function(e){
	e.preventDefault();
	$("#modal_tambah").modal("show");
});
//change icon
$("#aicon_change").on("click",function(e){
	e.preventDefault();
	$("#modal_option").modal("hide");
	setTimeout(function(){
		$("#ficon_change").trigger("reset");
		$("#modal_icon_change").modal("show");
	},500);
});
//listener on modal tambah show
$("#modal_tambah").on("shown.bs.modal",function(e){
	$("#inama").off("keyup");
	$("#inama").on("keyup",function(e){
		e.preventDefault();
		var x = $(this).val();
		x = x.trim();
		$("#imeta_title").val(x);
		var slug = convertToSlug(x);
		var code = convertToCode(x);
		var lastFour = code.substr(code.length - 4);
		lastFour = lastFour.toUpperCase();
		$("#islug").val(slug);
		$("#igslug").html('http://<?=base_url(); ?>'+slug);
		$("#imtitle").val(x);
		$("#igmtitle").html(x);
	});
	$("#islug").off("change");
	$("#islug").on("change",function(e){
		var slug = $(this).val();
		$("#igslug").html('http://<?=base_url(); ?>'+slug);
	});

	$("#imdescription").off("keyup");
	$("#imdescription").on("keyup",function(e){
		e.preventDefault();
		var x = $(this).val();
		x = x.trim();
		$("#igmdescription").html(x);
	});

	$("#imkeyword").off("keyup");
	$("#imkeyword").on("keyup",function(e){
		e.preventDefault();
		var x = $(this).val();
		x = x.trim();
		$("#igmkeyword").html(x);
	});
	$("#iutype").trigger('change');
});

$("#modal_tambah").on("hidden.bs.modal",function(e){
	$("#modal_tambah").find("form").trigger("reset");
});

$("#ftambah").on("submit",function(e){
	e.preventDefault();

	//if using ckeditor
	updateCkEditor();
	//get al value from form as fd formdata object
	var fd = new FormData($(this)[0]);
	var url = '<?=base_url("api_admin/produk/discussion/tambah/"); ?>';

	$.ajax({
		type: $(this).attr('method'),
		url: url,
		data: fd,
		processData: false,
		contentType: false,
		success: function(respon){
			if(respon.status==200){
				growlPesan = '<h4>Success</h4><p>Proses tambah data telah berhasil!</p>';
				drTable.ajax.reload();
				growlType = 'success';
				$("#modal_tambah").modal("hide");
			}else{
				growlPesan = '<h4>Failed</h4><p>'+respon.message+'</p>';
				growlType = 'danger';
			}
			setTimeout(function(){
				$.bootstrapGrowl(growlPesan, {
					type: growlType,
					delay: 2500,
					allow_dismiss: true
				});
			}, 666);
		},
		error:function(){
			growlPesan = '<h4>Error</h4><p>Proses tambah data tidak bisa dilakukan, coba beberapa saat lagi</p>';
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

});

// ===========================================================================
// ||                                                                       ||
// ||                              E D I T                                  ||
// ||                                                                       ||
// ===========================================================================

$("#modal_edit").on("shown.bs.modal",function(e){
	//
	var url = '<?=base_url(); ?>api_admin/produk/discussion/detail/'+ieid;
	$.get(url).done(function(response){
		if(response.status==200){
			var dta = response.data;
			$("#ieid").val(dta.id);
			$("#ieutype").val(dta.utype);
			$("#iekode").val(dta.kode);
			$("#ieb_kategori_id").val(dta.b_kategori_id);
			$("#ienama").val(dta.nama);
			$("#ieslug").val(dta.slug);
			$("#iemtitle").val(dta.mtitle);
			$("#iemkeyword").val(dta.mkeyword);
			$("#iemdescription").val(dta.mdescription);

			$("#ieprioritas").val(dta.prioritas);
			$("#ieis_visible").val(dta.is_visible);
			$("#ieis_active").val(dta.is_active);
			setTimeout(function(){
				CKEDITOR.instances.iedeskripsi.setData(dta.deskripsi);
			},2000);
		}else{
			gritter('<h4>Error</h4><p>Tidak dapat mengambil detail data</p>','danger');
		}
	});
});
$("#modal_edit").on("hidden.bs.modal",function(e){
	$("#modal_edit").find("form").trigger("reset");
});

$("#fedit").on("submit",function(e){
	e.preventDefault();
	var fd = new FormData($(this)[0]);
	var url = '<?=base_url("api_admin/produk/discussion/edit/"); ?>'+ieid;
	$.ajax({
		type: $(this).attr('method'),
		url: url,
		data: fd,
		processData: false,
		contentType: false,
		success: function(respon){
			if(respon.status==200){
				gritter('<h4>Success</h4><p>Proses ubah data telah berhasil!</p>','success');
				drTable.ajax.reload();
				$("#modal_edit").modal("hide");
			}else{
				gritter('<h4>Failed</h4><p>'+respon.message+'</p>','warning');
			}
		},
		error:function(){
			gritter('<h4>Error</h4><p>Proses ubah data tidak bisa dilakukan, coba beberapa saat lagi</p>','danger');
			return false;
		}
	});
});

// ===========================================================================
// ||                                                                       ||
// ||                            D E L E T E                                ||
// ||                                                                       ||
// ===========================================================================

$("#bhapus").on("click",function(e){
	e.preventDefault();
	if(ieid){
		var c = confirm('Are you sure?');
		if(c){
			NProgress.start();
			var url = '<?=base_url('api_admin/community/discussion/hapus/'); ?>'+ieid;
			$.get(url).done(function(response){
				NProgress.done();
				if(response.status==200){
					gritter('<h4>Success</h4><p>Data successfully deleted</p>','success');
				}else{
					gritter('<h4>Failed</h4><p>'+response.message+'</p>','danger');
				}
				drTable.ajax.reload();
				$("#modal_option").modal("hide");
				$("#modal_edit").modal("hide");
			}).fail(function() {
				NProgress.done();
				gritter('<h4>Error</h4><p>Cant deteled data right now, please try again later</p>','danger');
			});
		}
	}
});

//option
$("#aedit").on("click",function(e){
	e.preventDefault();
	$("#modal_option").modal("hide");
	setTimeout(function(){
		//$("#modal_edit").modal("show");
		window.location = '<?=base_url_admin('community/discussion/edit/');?>'+ieid;
	},333);
});


// ===========================================================================
// ||                                                                       ||
// ||                            D E T A I L                                ||
// ||                                                                       ||
// ===========================================================================

//$("#adetail").on("click",function(e){
//	e.preventDefault();
//	$("#modal_option").modal("hide");
//	setTimeout(function(){
//		$("#modal_edit").modal("show");
//		//alert('masih dalam pengembangan');
//	},333);
//});

$("#adetail").on("click",function(e){
	e.preventDefault();
	$("#modal_option").modal("hide");
	setTimeout(function(){
		//$("#modal_edit").modal("show");
		//alert('masih dalam pengembangan');
		window.location ='<?=base_url_admin('community/discussion/detail/')?>'+ieid;
	},333);
});

$("#iutype").on("change",function(e){
	e.preventDefault();
	var v = $(this).val();
	if(v.toLowerCase() == 'kategori' || v.toLowerCase() == 'tag'){
		$("#ib_kategori_id").prop('disabled',1);
	}else{
		$("#ib_kategori_id").removeAttr('disabled');
	}
});

function genKode(){
	var n = $("#inama").val().toUpperCase().replace(/[^\w\s]/gi, '');
	var ns = n.split(" ");
	if(ns.length>=2){
		n = ns[0].charAt(0)+ns[1].charAt(0);
	}else{
		n = n.slice(0,2);
	}
	var u = $("#iutype option:selected").attr('data-kode').toUpperCase();
	var p = '';
	if($("#ib_kategori_id option:selected").attr('data-kode') !== undefined){
		p = $("#ib_kategori_id option:selected").attr('data-kode').toUpperCase().slice(0,2);
	}
	$("#ikode").val(p+n+u);
}
$("#inama").on("blur",function(e){e.preventDefault(); genKode()});
$("#iutype").on("blur",function(e){e.preventDefault(); genKode()});
$("#ib_kategori_id").on("blur",function(e){e.preventDefault(); genKode()});

function genKodeEdit(){
	var n = $("#ienama").val().toUpperCase().replace(/[^\w\s]/gi, '');
	var ns = n.split(" ");
	if(ns.length>=2){
		n = ns[0].charAt(0)+ns[1].charAt(0);
	}else{
		n = n.slice(0,2);
	}

	var u = $("#ieutype option:selected").attr('data-kode').toUpperCase();
	var p = $("#ieb_kategori_id option:selected").attr('data-kode').toUpperCase().slice(0,2);
	$("#iekode").val(p+n+u+ieid);
}
$("#ienama").on("blur",function(e){e.preventDefault(); genKodeEdit()});
$("#ieutype").on("blur",function(e){e.preventDefault(); genKodeEdit()});
$("#ieb_kategori_id").on("blur",function(e){e.preventDefault(); genKodeEdit()});

$("#ficon_change").on("submit",function(e){
	e.preventDefault();
	NProgress.start();
	var url = '<?=base_url("api_admin/community/discussion/change_icon/")?>'+ieid;
	$.ajax({
		type: $(this).attr('method'),
		url: url,
		data: new FormData(this),
		processData: false,
		contentType: false,
		success: function(respon){
			if(respon.status==200){
				gritter('<h4>Success</h4><p>Icon changed successfully</p>','success');
				setTimeout(function(){
					NProgress.done();
					drTable.ajax.reload();
					$("#modal_icon_change").modal("hide");
				},1000);
			}else{
				NProgress.done();
				gritter('<h4>Failed</h4><p>'+respon.message+'</p>','danger');
				$("#modal_icon_change").modal("hide");
			}
		},
		error:function(){
			setTimeout(function(){
				$("#modal_icon_change").modal("hide");
				NProgress.done();
				gritter('<h4>Error</h4><p>Cant change icon right now, please try again later</p>','warning');
			}, 666);
			return false;
		}
	});
})

$("#areport").on('click',function(e){
	e.preventDefault();
	NProgress.start();
	$.get("<?=base_url("api_admin/community/discussion/report/")?>"+ieid).done(function(dt){
		NProgress.done();
		$("#modal_option").modal("hide");
		drTable.ajax.reload(null,false);
		if(dt.status == "200"){
			gritter("<h4>Success</h4><p>Discussion Reported.</p>",'success');
		}else if(dt.status == 999){
			gritter("<h4>Warning</h4><p>"+dt.message+"</p>",'warning');
		}else{
			gritter("<h4>Failed</h4><p>"+dt.message+"</p>",'danger');
		}
	}).fail(function(e){
		NProgress.done();
		gritter("<h4>Error</h4><p>Cant change user report right now, please try again.</p>",'warning');
	})
});