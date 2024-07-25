var growlPesan = '<h4>Error</h4><p>Tidak dapat diproses, silakan coba beberapa saat lagi!</p>';
var growlType = 'danger';
var drTable = {};
var ieid = '';
App.datatables();
function gritter(pesan,jenis='info'){
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
			"order"					: [[ 0, "desc" ]],
			"responsive"	  : true,
			"bProcessing"		: true,
			"bServerSide"		: true,
			"sAjaxSource"		: "<?=base_url("api_admin/ecommerce/bulksale/")?>",
			"fnServerParams": function ( aoData ) {
				aoData.push(
					{ "name": "scdate", "value": $("#ifcdate_min").val() },
					{ "name": "ecdate", "value": $("#ifcdate_max").val() },
					{ "name": "svdate", "value": $("#ifvdate_min").val() },
					{ "name": "evdate", "value": $("#ifvdate_max").val() },
					{ "name": "action_status", "value": $("#if_action_status").val() },
					{ "name": "is_agent", "value": $("#if_is_agent").val() }
				);
			},
			"fnServerData"	: function (sSource, aoData, fnCallback, oSettings) {
				oSettings.jqXHR = $.ajax({
					dataType 	: 'json',
					method 		: 'POST',
					url 		: sSource,
					data 		: aoData
				}).success(function (response, status, headers, config) {
					console.log(response);
					NProgress.done();
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
					gritter('<h4>Error</h4><p>Cant fetch product data right now, please try again later</p>','warning');
				});
			},
	});
	$('.dataTables_filter input').attr('placeholder', 'Search Product Name');
	$("#if_action").on("click",function(e){
		e.preventDefault();
		drTable.ajax.reload(null,true);
	})
}

//tambah
$("#atambah").on("click",function(e){
	e.preventDefault();
	$("#modal_tambah").modal("show");
});
$("#modal_tambah").on("shown.bs.modal",function(e){
	$("#inegara").trigger("change");
});
$("#modal_tambah").on("hidden.bs.modal",function(e){
	$("#modal_tambah").find("form").trigger("reset");
});

$("#ftambah").on("submit",function(e){
	e.preventDefault();
	var fd = new FormData($(this)[0]);
	var url = '<?=base_url("api_admin/ecommerce/bulksale/tambah/")?>';
	$.ajax({
		type: $(this).attr('method'),
		url: url,
		data: fd,
		processData: false,
		contentType: false,
		success: function(respon){
			if(respon.status==200){
				growlPesan = '<h4>Success</h4><p>Data added successfully!</p>';
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



//edit
$("#modal_edit").on("shown.bs.modal",function(e){
	//
});
$("#modal_edit").on("hidden.bs.modal",function(e){
	$("#modal_edit").find("form").trigger("reset");
});

$("#fedit").on("submit",function(e){
	e.preventDefault();
	var fd = new FormData($(this)[0]);
	var url = '<?=base_url("api_admin/ecommerce/bulksale/edit/")?>';
	$.ajax({
		type: $(this).attr('method'),
		url: url,
		data: fd,
		processData: false,
		contentType: false,
		success: function(respon){
			if(respon.status==200){
				growlType = 'success';
				growlPesan = '<h4>Success</h4><p>Data changed successfully!</p>';
				drTable.ajax.reload();
			}else{
				growlType = 'danger';
				growlPesan = '<h4>Failed</h4><p>'+respon.message+'</p>';
			}
			$("#modal_edit").modal("hide");
			setTimeout(function(){
				$.bootstrapGrowl(growlPesan, {
					type: growlType,
					delay: 2500,
					allow_dismiss: true
				});
			}, 666);
		},
		error:function(){
			growlPesan = '<h4>Error</h4><p>Proses ubah data tidak bisa dilakukan, coba beberapa saat lagi</p>';
			growlType = 'danger';
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

//hapus
$("#bhapus").on("click",function(e){
	e.preventDefault();
	if(ieid){
		var c = confirm('Are you sure?');
		if(c){
			var url = '<?=base_url('api_admin/ecommerce/bulksale/hapus/')?>'+ieid;
			$.get(url).done(function(response){
				if(response.status==200){
					growlType = 'success';
					growlPesan = '<h4>Success</h4><p>Data has deleted</p>';
					$("#modal_option").modal('hide');
				}else{
					growlType = 'danger';
					growlPesan = '<h4>Failed</h4><p>'+response.message+'</p>';
				}
				drTable.ajax.reload();
				$("#modal_edit").modal("hide");
				$.bootstrapGrowl(growlPesan,{
					type: growlType,
					delay: 2500,
					allow_dismiss: true
				});
			}).fail(function() {
				growlPesan = '<h4>Error</h4><p>Proses penghapusan tidak bisa dilakukan, coba beberapa saat lagi</p>';
				growlType = 'danger';
				$.bootstrapGrowl(growlPesan,{
					type: growlType,
					delay: 2500,
					allow_dismiss: true
				});
			});
		}
	}
});

//option
$("#aedit").on("click",function(e){
	e.preventDefault();
	$("#modal_option").modal("hide");
	setTimeout(function(){
		//quick edit
		//$("#modal_edit").modal("show");
		window.location = '<?=base_url_admin('ecommerce/bulksale/edit/')?>'+ieid;
	},333);
});

//detail
$("#av_detail").on("click",function(e){
	e.preventDefault();
	$("#modal_option").modal("hide");
	setTimeout(function(){
		//$("#modal_edit").modal("show");
		//alert('masih dalam pengembangan');
		window.location ='<?=base_url_admin('ecommerce/bulksale/detail/')?>'+ieid;
	},333);
});

$("#aset_visited").on("click",function(e){
	e.preventDefault();
	NProgress.start();
	$.get("<?=base_url("api_admin/ecommerce/bulksale/set_visited/")?>"+ieid).done(function(dt){
		NProgress.done();
		if(dt.status == 200){
			gritter("<h4>Success</h4><p>BulkSale marked as Visited</p>",'success');
		}else{
			gritter("<h4>Failed</h4><p>Failed to change bulksale status</p>",'danger');
		}
		$("#modal_option").modal("hide");
		drTable.ajax.reload(null,false);
	}).fail(function(){
		NProgress.done();
		gritter("<h4>Failed</h4><p>Cant change bulksale status right now, please try again later</p>",'danger');
	});
});

$("#aset_pending").on("click",function(e){
	e.preventDefault();
	NProgress.start();
	$.get("<?=base_url("api_admin/ecommerce/bulksale/set_pending/")?>"+ieid).done(function(dt){
		NProgress.done();
		if(dt.status == 200){
			gritter("<h4>Success</h4><p>BulkSale now pending</p>",'success');
		}else{
			gritter("<h4>Failed</h4><p>Failed to change bulksale status</p>",'danger');
		}
		$("#modal_option").modal("hide");
		drTable.ajax.reload(null,false);
	}).fail(function(){
		NProgress.done();
		gritter("<h4>Failed</h4><p>Cant change bulksale status right now, please try again later</p>",'danger');
	});
});

$("#aset_completed").on("click",function(e){
	e.preventDefault();
	NProgress.start();
	$.get("<?=base_url("api_admin/ecommerce/bulksale/set_completed/")?>"+ieid).done(function(dt){
		NProgress.done();
		if(dt.status == 200){
			gritter("<h4>Success</h4><p>BulkSale Completed</p>",'success');
		}else{
			gritter("<h4>Failed</h4><p>Failed to change bulksale status</p>",'danger');
		}
		$("#modal_option").modal("hide");
		drTable.ajax.reload(null,false);
	}).fail(function(){
		NProgress.done();
		gritter("<h4>Failed</h4><p>Cant change bulksale status right now, please try again later</p>",'danger');
	});
});

$("#aset_leaved").on("click",function(e){
	e.preventDefault();
	NProgress.start();
	$.get("<?=base_url("api_admin/ecommerce/bulksale/set_leaved/")?>"+ieid).done(function(dt){
		NProgress.done();
		if(dt.status == 200){
			gritter("<h4>Success</h4><p>BulkSale has been leaved</p>",'success');
		}else{
			gritter("<h4>Failed</h4><p>Failed to change bulksale status</p>",'danger');
		}
		$("#modal_option").modal("hide");
		drTable.ajax.reload(null,false);
	}).fail(function(){
		NProgress.done();
		gritter("<h4>Failed</h4><p>Cant change bulksale status right now, please try again later</p>",'danger');
	});
});

$("#aset_active").on("click",function(e){
	e.preventDefault();
	NProgress.start();
	$.get("<?=base_url("api_admin/ecommerce/bulksale/active/")?>"+ieid).done(function(dt){
		NProgress.done();
		if(dt.status == 200){
			gritter("<h4>Success</h4><p>Product activated</p>",'success');
		}else{
			gritter("<h4>Failed</h4><p>Failed to change bulksale status</p>",'danger');
		}
		$("#modal_option").modal("hide");
		drTable.ajax.reload(null,false);
	}).fail(function(){
		NProgress.done();
		gritter("<h4>Failed</h4><p>Cant change bulksale status right now, please try again later</p>",'danger');
	});
});


$("#aset_inactive").on("click",function(e){
	e.preventDefault();
	NProgress.start();
	$.get("<?=base_url("api_admin/ecommerce/bulksale/inactive/")?>"+ieid).done(function(dt){
		NProgress.done();
		if(dt.status == 200){
			gritter("<h4>Success</h4><p>Product inactivated</p>",'success');
		}else{
			gritter("<h4>Failed</h4><p>Failed deactivating product</p>",'danger');
		}
		$("#modal_option").modal("hide");
		drTable.ajax.reload(null,false);
	}).fail(function(){
		NProgress.done();
		gritter("<h4>Failed</h4><p>Cant change bulksale status right now, please try again later</p>",'danger');
	});
});

$("#areset_do").on("click",function(e){
	e.preventDefault();
	$("#ifcdate_min").val("");
	$("#ifcdate_max").val("");
	$("#ifvdate_min").val("");
	$("#ifvdate_max").val("");
	$("#if_is_agent").val("");
	$("#if_action_status").val("");
	$("#if_action_status").val("");
	drTable.search( '' ).columns().search( '' ).draw();
	drTable.ajax.reload(null,true);
});
