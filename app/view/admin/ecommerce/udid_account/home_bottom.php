<!-- by Muhammad Sofi 13 January 2022 16:11 | remodel on sponsored menu -->
var growlPesan = '<h4>Error</h4><p>Cannot be proceed. Please try again later!</p>';
var growlType = 'danger';
var drTable = {};
var drTableDetail = {};
var referral = '';

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
			//"columnDefs"		: [{
			//						"targets": [1], <!-- hide column -->
			//						"visible": false,
			//						"searchable": false
			//					}],	
			"order"				: [[ 1, "desc" ]],
			"responsive"	  	: true,
			"bProcessing"		: true,
			"bServerSide"		: true,
			//"searching"			: true,
			"sAjaxSource"		: "<?=base_url("api_admin/ecommerce/udid_account/"); ?>",
			"fnServerParams": function ( aoData ) {
				aoData.push(
					{ "name": "type_multilanguage", "value": $("#fltype_multilanguage").val() }
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
						var udid_field = $('#drTable').DataTable().row(currentRow).data()[1]; <!-- to get data from specific column, change this "data()[id_column]" -->
						device_id = udid_field;

						var url = '<?=base_url("api_admin/ecommerce/udid_account/detail/")?>' + device_id;
						$.get(url).done(function(response){
							if(response.status==200){
								var dta = response.data;
								$("#detail_udid").val(device_id);

								<!-- tampilkan modal -->
								drTableDetail.ajax.reload();
								$("#modal_detail").modal("show");
							}else{
								gritter('<h4>Failed</h4><p>Cannot fetch data, try again later</p>','info');
							}
						});
						
					});
					fnCallback(response);
				}).error(function (response, status, headers, config) {
					NProgress.start();
					gritter('<h4>Error</h4><p>Cannot fetch data, try again later</p>','warning');
					return false;
				});
			},
		});
		$('.dataTables_filter input').attr('placeholder', 'Search UDID').css({'width':'250px', 'display':'inline-block'}); <!-- show searchbox + add styling -->
	}

	if(jQuery('#drTableDetail').length>0){
		drTableDetail = jQuery('#drTableDetail')
		.on('preXhr.dt', function ( e, settings, data ){
			NProgress.start();
		}).DataTable({
			//"columnDefs"		: [{
			//						"targets": [1], <!-- hide column -->
			//						"visible": false,
			//						"searchable": false
			//					}],
			"order"				: [[ 0, "asc" ]],
			"responsive"	  	: true,
			"bProcessing"		: true,
			"bServerSide"		: true,
			"sAjaxSource"		: "<?=base_url("api_admin/ecommerce/udid_account/detail_list_udid_account/"); ?>",
			//"searching"			: true, // hide input search
			"fnServerParams": function ( aoData ) {
				aoData.push(
					{ "name": "detail_udid", "value": $("#detail_udid").val() },
					//{ "name": "filter_referral_type", "value": $("#filter_referral_type").val() },
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

					fnCallback(response);
				}).error(function (response, status, headers, config) {
					NProgress.start();
					gritter('<h4>Error</h4><p>Cannot fetch data, try again later</p>','warning');
					return false;
				});
			},
		});
		$('.dataTables_filter input').attr('placeholder', 'Search text').css({'width':'250px', 'display':'inline-block'}); <!-- show searchbox + add styling -->
	}

});