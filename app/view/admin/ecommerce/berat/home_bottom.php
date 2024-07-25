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
			"scrollX"				: true,
			"order"					: [[ 4, "asc" ]],
			"responsive"	  : true,
			"bProcessing"		: true,
			"bServerSide"		: true,
			"sAjaxSource"		: "<?=base_url("api_admin/ecommerce/berat/")?>",
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
						var url = '<?=base_url()?>api_admin/ecommerce/berat/detail/'+ieid;
						NProgress.start();
						$.get(url).done(function(response){
							NProgress.done();
							if(response.status==200){
								var dta = response.data;
								//input nilai awal
								$("#ienama").val(dta.nama);
								$("#ienilai").val(dta.nilai);
								$("#ieprioritas").val(dta.prioritas);
								$("#ieis_active").val(dta.is_active);
								//tampilkan modal
								$("#modal_option").modal("show");
							}else{
								gritter('<h4>Failed</h4><p>'+response.message+'</p>','danger');
							}
						});
					});
					fnCallback(response);
				}).error(function (response, status, headers, config) {
					NProgress.done();
					gritter('<h4>Failed</h4><p>Cant fetch data right now, please try again later</p>','warning');
				});
			},
	});
	$('.dataTables_filter input').attr('placeholder', 'Search');
}
$("#aedit").on("click",function(e){
	e.preventDefault();
	$("#modal_option").modal("hide");
	setTimeout(function(){
		$("#modal_edit").modal("show");
	},333);
});
$("#aedit").on("click",function(e){
	e.preventDefault();
	$("#modal_option").modal("hide");
	setTimeout(function(){
		$("#modal_edit").modal("show");
	},333);
});
$("#atambah").on("click",function(e){
	e.preventDefault();
	$("#modal_tambah").modal("show");
});
$("#modal_tambah").on("shown.bs.modal",function(e){
	//
});
$("#modal_tambah").on("hidden.bs.modal",function(e){
	$("#modal_tambah").find("form").trigger("reset");
});
$("#aicon_change").on("click",function(e){
	e.preventDefault();
	$("#modal_option").modal("hide");
	setTimeout(function(){
		$("#modal_icon_change").modal("show");
	},333);
})

$("#ftambah").on("submit",function(e){
	e.preventDefault();
	var nilai = Number($("#inilai").val());
	if(nilai<=0){
		gritter("<h4>Caution</h4><p>Value should greaten by zero</p>");
		$("#inilai").focus();
		return false;
	}
	var prioritas = Number($("#iprioritas").val());
	if(prioritas<=0){
		gritter("<h4>Caution</h4><p>Priority should greaten by zero</p>");
		$("#iprioritas").focus();
		return false;
	}
	NProgress.start();
	var fd = new FormData($(this)[0]);
	var url = '<?=base_url("api_admin/ecommerce/berat/tambah/")?>';
	$.ajax({
		type: $(this).attr('method'),
		url: url,
		data: fd,
		processData: false,
		contentType: false,
		success: function(respon){
			NProgress.done();
			if(respon.status==200){
				drTable.ajax.reload();
				gritter('<h4>Success</h4><p>Data successfully added!</p>','success');
				$("#modal_tambah").modal("hide");
			}else{
				gritter('<h4>Failed</h4><p>'+respon.message+'</p>','danger');
			}
		},
		error:function(){
			NProgress.done();
			gritter('<h4>Error</h4><p>Cant update data right now, please try again later</p>','warning');
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
	var nilai = Number($("#ienilai").val());
	if(nilai<=0){
		gritter("<h4>Caution</h4><p>Value should greaten by zero</p>");
		$("#ienilai").focus();
		return false;
	}
	var prioritas = Number($("#ieprioritas").val());
	if(prioritas<=0){
		gritter("<h4>Caution</h4><p>Priority should greaten by zero</p>");
		$("#ieprioritas").focus();
		return false;
	}
	NProgress.start();
	var fd = new FormData($(this)[0]);
	var url = '<?=base_url("api_admin/ecommerce/berat/edit/")?>'+ieid;
	$.ajax({
		type: $(this).attr('method'),
		url: url,
		data: fd,
		processData: false,
		contentType: false,
		success: function(respon){
			NProgress.done();
			if(respon.status==200){
				gritter('<h4>Success</h4><p>Data changed successfully!</p>','success');
				drTable.ajax.reload();
			}else{
				gritter('<h4>Failed</h4><p>'+respon.message+'</p>','danger');
			}
			$("#modal_edit").modal("hide");
		},
		error:function(){
			NProgress.done();
			gritter('<h4>Error</h4><p>Cant change data right now, please try again later</p>','warning');
			return false;
		}
	});
});

//hapus
$("#bhapus").on("click",function(e){
	e.preventDefault();
	NProgress.start();
	var c = confirm('Are you sure?');
	if(c){
		var url = '<?=base_url('api_admin/ecommerce/berat/hapus/')?>'+ieid;
		$.get(url).done(function(response){
			NProgress.done();
			if(response.status==200){
				gritter('<h4>Success</h4><p>Data removed successfully</p>','success');
			}else{
				gritter('<h4>Failed</h4><p>'+response.message+'</p>','danger');
			}
			drTable.ajax.reload();
			$("#modal_edit").modal("hide");
			$("#modal_option").modal("hide");
		}).fail(function() {
			NProgress.done();
			gritter('<h4>Error</h4><p>Cant remove data right now, please try again later</p>','warning');
		});
	}
});


$("#ficon_change").on("submit",function(e){
	e.preventDefault();
	NProgress.start();
	var url = '<?=base_url("api_admin/ecommerce/berat/change_icon/")?>'+ieid;
	$.ajax({
		type: $(this).attr('method'),
		url: url,
		data: new FormData(this),
		processData: false,
		contentType: false,
		success: function(respon){
			if(respon.status=="200" || respon.status == 200){
				$("#modal_icon_change").modal("hide");
				gritter('<h4>Success</h4><p>Icon changed successfully</p>','success');
				setTimeout(function(){
					NProgress.done();
					drTable.ajax.reload(null,false);
				},3000);
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
});
