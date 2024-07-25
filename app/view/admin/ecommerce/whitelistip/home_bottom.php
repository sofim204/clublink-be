<!-- by Muhammad Sofi 13 January 2022 16:11 | remodel on sponsored menu -->
var growlPesan = '<h4>Error</h4><p>Cannot be proceed. Please try again later!</p>';
var growlType = 'danger';
var drTable = {};
var ieid = '';

App.datatables();
function gritter(pesan,jenis="info"){
	$.bootstrapGrowl(pesan, {
		type: jenis,
		delay: 2500,
		allow_dismiss: true
	});
}

$(document).ready(function(){
	if(jQuery('#drTable').length>0){
		drTable = jQuery('#drTable')
		.on('preXhr.dt', function ( e, settings, data ){
			NProgress.start();
		}).DataTable({
			"columnDefs"		: [{
									"targets": [1], <!-- hide column -->
									"visible": false,
									"searchable": false
								}],	
			"order"				: [[ 1, "desc" ]],
			"responsive"	  	: true,
			"bProcessing"		: true,
			"bServerSide"		: true,
			//"searching"			: true,
			"sAjaxSource"		: "<?=base_url("api_admin/ecommerce/whitelistip/"); ?>",
			"fnServerParams": function ( aoData ) {
				aoData.push(
					{ "name": "type", "value": $("#ifiltertype").val() }
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
						var currentRow = $(this).closest("tr");
						var id = $('#drTable').DataTable().row(currentRow).data()[1]; <!-- to get data from specific column, change this "data()[id_column]" -->
						ieid = id;

						$("#modal_option").modal("show");
						
					});
					fnCallback(response);
				}).error(function (response, status, headers, config) {
					NProgress.start();
					gritter('<h4>Error</h4><p>Cannot fetch data, try again later</p>','warning');
					return false;
				});
			},
		});
		$('.dataTables_filter input').attr('placeholder', 'Search IP Address').css({'width':'250px', 'display':'inline-block'}); <!-- show searchbox + add styling -->
	}


	$("#add_data").on("click", function(e){
		e.preventDefault();
		$("#modal_add_whitelist").modal("show");
	});

	$("#modal_add_whitelist").on("hidden.bs.modal",function(e){
		$("#modal_add_whitelist").find("form").trigger("reset");
	});

	$("#form_add_whitelist").on("submit",function(e){
		e.preventDefault();
		var fd = new FormData($(this)[0]);
		var url = '<?=base_url("api_admin/ecommerce/whitelistip/tambah/")?>';
		NProgress.start();
		$.ajax({
			type: $(this).attr('method'),
			url: url,
			data: fd,
			processData: false,
			contentType: false,
			success: function(respon){
				NProgress.done();
				if(respon.status==200){
					gritter('<h4>Success</h4><p>Data added successfully!</p>','success');
					$("#modal_add_whitelist").modal("hide");
					drTable.ajax.reload(null, false);
				}else{
					gritter('<h4>Failed</h4><p>'+respon.message+'</p>','danger');
				}
			},
			error:function(){
				NProgress.done();
				gritter('<h4>Error</h4><p>Cant add data right now, please try again later</p>','warning');
				return false;
			}
		});
	});

	$("#btn_delete_data").on("click",function(e){
		e.preventDefault();
		var c = confirm('Are you sure to delete?');
		if(c){
			var url = '<?=base_url('api_admin/ecommerce/whitelistip/hapus/')?>'+ieid;
			NProgress.start();
			$.get(url).done(function(respon){
				NProgress.done();
				if(respon.status==200){
					gritter('<h4>Success</h4><p>Data has been deleted</p>','success');
				}else{
					gritter('<h4>Failed</h4><p>'+respon.message+'</p>','danger');
				}
				drTable.ajax.reload(null, false);
				$("#modal_option").modal("hide");
			}).fail(function() {
				NProgress.done();
				gritter('<h4>Error</h4><p>Cant deleted data right now, please try again later</p>','warning');
			});
		}
	});

	$("#ifiltertype").on("change",function(e){
		drTable.ajax.reload();
	});

});