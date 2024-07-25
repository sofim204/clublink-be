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

$(document).ready(function() {	
	<!-- get general location -->
	$("#select_general_location").select2({
		placeholder: "--Select Location--",
		allowClear: true, <!-- add x button to clear value -->
		ajax: { 
			url: "<?= base_url('api_admin/community/leaderboard/getGeneralLocation') ?>",
			type: "post",
			dataType: 'json',
			delay: 250,
			data: function (params) {
				return {
					search: params.term, // search term
				};
			},
			processResults: function (response) {
				return {
					results: response
				};
			}
		},
		sorter: function(data) {
			return data.sort(function(a, b) {
				return a.text < b.text ? -1 : a.text > b.text ? 1 : 0;
			});
		}
	}).change(function() {
		drTable.ajax.reload();
    });

	<!-- table leaderboard point area -->
	if (!$.fn.dataTable.isDataTable('#drTable')) { <!-- fixing bug Cannot reinitialise DataTable -->
		drTable = jQuery('#drTable')
		.on('preXhr.dt', function (e, settings, data){
			NProgress.start();
		}).DataTable({
			"columnDefs"		: [{
									"targets": [1], <!-- hide column -->
									"visible": false,
									"searchable": false
								}],
			"lengthMenu"		: [[10, 20, 50, 100], ['top ' + 10, 'top ' + 20, 'top ' + 50, 'top ' + 100]], <!-- change page length -->
			//"order"				: [[ 2, "desc" ]],
			"responsive"	  	: true,
			"bProcessing"		: true,
			"bServerSide"		: true,
			//"searching"			: false, <!-- hide search box datatable -->
			"language"			: { searchPlaceholder: "Search User" }, <!-- show placeholder in search box -->
			"sAjaxSource"		: "<?=base_url("api_admin/community/leaderboard/"); ?>",
				"fnServerParams": function ( aoData ) {
					aoData.push(
						{ "name": "id_kelurahan", "value": $("#select_general_location option:selected").val()},
					);
				},
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
						//var id = $(this).find("td").html();
						var currentRow = $(this).closest("tr");
						var id = $('#drTable').DataTable().row(currentRow).data()[1]; <!-- to get data from specific column, change this "data()[id_column]" -->
						ieid = id
						//alert(ieid);
					});
					fnCallback(response);
				}).error(function (response, status, headers, config) {
					NProgress.done();
					gritter("<h4>Error</h4><p>Can't fetch data right now, please try again later</p>", "warning");
				});
			},
		});
	}

	// clear filter 
	$("#reset-filter").on("click",function(e){
		e.preventDefault();
		$("#select_general_location").val('').trigger("change");
		drTable.ajax.reload();
	});
});