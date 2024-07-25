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
			"columnDefs"		: [{
									"targets": [1], <!-- hide column -->
									"visible": false,
									"searchable": false
								}],	
			"order"				: [[ 0, "asc" ]],
			"responsive"	  	: true,
			"bProcessing"		: true,
			"bServerSide"		: true,
			"searching"			: false,
			"sAjaxSource"		: "<?=base_url("api_admin/ecommerce/install_trace/"); ?>",
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
						var referral_field = $('#drTable').DataTable().row(currentRow).data()[2]; <!-- to get data from specific column, change this "data()[id_column]" -->
						referral = referral_field;

						var url = '<?=base_url("api_admin/ecommerce/install_trace/detail/")?>' + referral;
						$.get(url).done(function(response){
							if(response.status==200){
								var dta = response.data;
								$("#detail_referral").val(referral);

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
		$('.dataTables_filter input').attr('placeholder', 'Search text');
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
			"sAjaxSource"		: "<?=base_url("api_admin/ecommerce/install_trace/detail_list_install_trace/"); ?>",
			"searching"			: false, // hide input search
			"fnServerParams": function ( aoData ) {
				aoData.push(
					{ "name": "detail_referral", "value": $("#detail_referral").val() },
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
		$('.dataTables_filter input').attr('placeholder', 'Search text');
	}

});