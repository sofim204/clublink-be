
var api_url = '<?=base_url('api_admin/shipment/qxpress'); ?>';
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
		$("#modal-preloader").modal("hide");
		//$("#modal-preloader").modal("show");
	}).DataTable({
			"order"					: [[ 1, "asc" ]],
			"responsive"	  : true,
			"bProcessing"		: true,
			"bServerSide"		: true,
			"sAjaxSource"		: "<?=base_url("api_admin/shipment/qxpress_volume"); ?>",
			"fnServerData"	: function (sSource, aoData, fnCallback, oSettings) {
				//$('body').removeClass('loaded');

				oSettings.jqXHR = $.ajax({
					dataType 	: 'json',
					method 		: 'POST',
					url 		: sSource,
					data 		: aoData
				}).success(function (response, status, headers, config) {
					console.log(response);
					$("#modal-preloader").modal("hide");
					//$('body').addClass('loaded');
					$('#drTable > tbody').off('click', 'tr');
					$('#drTable > tbody').on('click', 'tr', function (e) {
						e.preventDefault();
						var id = $(this).find("td").html();
						var url = '<?=base_url(); ?>api_admin/shipment/qxpress_volume/detail/'+id;
						$.get(url).done(function(response){
							if(response.status==200){
								var dta = response.data;
								ieid = dta.id;
								$("#ienation_code").val(dta.nation_code);
								$("#ieid").val(dta.id);
                $("#ielength_max").val(dta.length_max);
								$("#ielengthunit").val(dta.length_unit);
                $("#iecost").val(dta.cost);


								//$("#modal_edit").modal("show");
								$("#modal_option").modal("show")

							}else{
								gritter('<h4>Error</h4><p>Cannot fetch data from server</p>','danger');
							}
						});
					});
					fnCallback(response);
				}).error(function (response, status, headers, config) {
					gritter('<h4>Error</h4><p>Cannot fetch data from server, please try again later</p>','warning');
					return false;
				});
			},
	});
	$('.dataTables_filter input').attr('placeholder', 'Search length max');
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
	//var fd = $(this).serialize();
	//var fd = {};
	//fd.origin = $("#iorigin").val();
	//fd.propinsi = $("#ipropinsi option:select").text();

	var url = '<?=base_url("api_admin/shipment/qxpress_volume/tambah/"); ?>';
	$.ajax({
		type: $(this).attr('method'),
		url: url,
		data: fd,
		processData: false,
		contentType: false,
		success: function(dt){
			if(dt.status == 200){
				gritter('<h4>Success</h4><p>Data successfully added!</p>','success');
				drTable.ajax.reload();
				$("#modal_tambah").modal("hide");
			}else{
				gritter('<h4>Failed</h4><p>'+dt.message+'</p>','danger');
			}
		},
		error:function(){
			gritter('<h4>Error</h4><p>Cannot add new data right now, please try again later</p>','success');
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
	var url = '<?=base_url("api_admin/shipment/qxpress_volume/edit/"); ?>'+ieid;
	$.ajax({
		type: $(this).attr('method'),
		url: url,
		data: fd,
		processData: false,
		contentType: false,
		success: function(respon){
			if(respon.status == 200){
				gritter('<h4>Success</h4><p>Data changed successfully</p>','success');
				drTable.ajax.reload();
			}else{
				gritter('<h4>Failed</h4><p>'+respon.message+'</p>','danger');
			}
			$("#modal_edit").modal("hide");
		},
		error:function(){
			gritter('<h4>Error</h4><p>Cannot edit data right now, please try again later</p>','warning');
			return false;
		}
	});
});

//hapus
$("#ahapus").on("click",function(e){
	e.preventDefault();
	var id = ieid;
	if(id){
		var c = confirm('Are you sure?');
		if(c){
			var url = '<?=base_url('api_admin/shipment/qxpress_volume/hapus/'); ?>'+id;
			$.get(url).done(function(response){
				if(response.status==200){
					gritter('<h4>Success</h4><p>Data has been deleted successfully</p>','success');
				}else{
					gritter('<h4>Failed</h4><p>'+response.message+'</p>','Danger');
				}
				drTable.ajax.reload();
				$("#modal_option").modal("hide");
			}).fail(function() {
				gritter('<h4>Error</h4><p>Cannot deleting data right now, please try again later</p>','warning');
			});
		}
	}
});

$("#bhapus").on("click",function(e){
	e.preventDefault();
	$("#ahapus").trigger("click");
});

//option
$("#aedit").on("click",function(e){
	e.preventDefault();
	$("#modal_option").modal("hide");
	setTimeout(function(){
		$("#modal_edit").modal("show");
	},333);
});

//detail
$("#adetail").on("click",function(e){
	e.preventDefault();
	$("#modal_option").modal("hide");
	setTimeout(function(){
		//$("#modal_edit").modal("show");
		alert('masih dalam pengembangan');
	},333);
});

//provinsi
