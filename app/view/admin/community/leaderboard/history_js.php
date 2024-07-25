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
	<!-- initialize datepicker -->
	$('#ifstart_date').datepicker();
    $('#ifstart_date').datepicker('setDate', 'today').val("");

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
		//var data = $("#select_general_location option:selected").val(); <!-- .text to get all data -->
		drTable.ajax.reload();
    });

	$("#ifstart_date").change(function(){
		drTable.ajax.reload();
		$('.datepicker').hide(); <!-- hide datepicker after select a date -->
	});

	<!--  table leaderboard point area -->
	if (!$.fn.dataTable.isDataTable('#drTable')) { <!-- fixing bug Cannot reinitialise DataTable -->
		drTable = jQuery('#drTable')
		.on('preXhr.dt', function (e, settings, data){
			NProgress.start();
		}).DataTable({
			"columnDefs"		: [{
									'targets': 0,
									'checkboxes': {'selectRow': true},
									"targets": [1], <!-- hide column -->
									"visible": false,
									"searchable": false
								}],
			"select"			: {'style': 'multi'},
			"order"				: [[ 0, "desc" ]],
			"responsive"	  	: true,
			"bProcessing"		: true,
			"bServerSide"		: true,
			"searching"			: false, <!-- hide search box datatable -->
			//"language"			: { searchPlaceholder: "Search Leaderboard History" }, <!-- show placeholder in search box -->
			"sAjaxSource"		: "<?=base_url("api_admin/community/leaderboard/getleaderboardpointhistory"); ?>",
				"fnServerParams": function ( aoData ) {
					aoData.push(
						{ "name": "start_date", "value": $("#ifstart_date").val()},
						{ "name": "id_kelurahan_history", "value": $("#select_general_location option:selected").val()},
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
						var currentRow = $(this).closest("tr");
						var id = $('#drTable').DataTable().row(currentRow).data()[1]; <!-- to get data from specific column, change this "data()[id_column]" -->
						ieid = id
						//alert("id = " + ieid);
					});
					fnCallback(response);
				}).error(function (response, status, headers, config) {
					NProgress.done();
					gritter("<h4>Error</h4><p>Cannot fetch data right now, please try again later</p>", "warning");
				});
			},
		});
		$('.dataTables_filter input[type="search"]').attr('placeholder', 'Search Leaderboard History').css({'width':'250px', 'display':'inline-block'}); <!-- show search box + add styling -->
	}

	// clear filter 
	$("#reset-filter").on("click",function(e){
		e.preventDefault();
		$('#ifstart_date').datepicker('setDate', 'today').val("");
		$("#select_general_location").val('').trigger("change");
		$("#id_kelurahan_history").val("");
		drTable.ajax.reload();
	});
});