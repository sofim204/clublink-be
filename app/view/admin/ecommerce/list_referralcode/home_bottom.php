<!-- by Muhammad Sofi 13 January 2022 16:11 | remodel on sponsored menu -->
var growlPesan = '<h4>Error</h4><p>Cannot be proceed. Please try again later!</p>';
var growlType = 'danger';
var drTable = {};
var drTableDetailList = {};
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

	<!-- initialize datepicker -->
	$('#flcdate_start, #flcdate_end').datepicker();
    $('#flcdate_start, #flcdate_end').datepicker('setDate', 'today').val("");

	$("#flcdate_start, #flcdate_end").change(function(){
		$('.datepicker').hide(); <!-- hide datepicker after select a date -->
	});

	if(jQuery('#drTable').length>0){
		drTable = jQuery('#drTable')
		.on('preXhr.dt', function ( e, settings, data ){
			NProgress.start();
		}).DataTable({
			"columnDefs"		: [{
									"targets": [0], <!-- hide column -->
									"visible": false,
									"searchable": false
								}],
			"order"				: [[ 5, "desc" ]],
			"responsive"	  	: true,
			"bProcessing"		: true,
			"bServerSide"		: true,
			"sAjaxSource"		: "<?=base_url("api_admin/ecommerce/list_referralcode/"); ?>",
			"fnServerParams": function ( aoData ) {
				aoData.push(
					{ "name": "cdate_start", "value": $("#flcdate_start").val() },
					{ "name": "cdate_end", "value": $("#flcdate_end").val() },
					{ "name": "user_status", "value": $("#flis_active").val() }
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
						var id = $('#drTable').DataTable().row(currentRow).data()[0]; <!-- to get data from specific column, change this "data()[id_column]" -->
						ieid = id;
						
						var url = '<?=base_url("api_admin/ecommerce/list_referralcode/detail/")?>'+ieid;
						$.get(url).done(function(response){
							if(response.status==200){
								var dta = response.data;
								$("#id_user_recruiter").val(ieid);

								<!-- tampilkan modal -->
								drTableDetailList.ajax.reload();
								$("#modal_list_recruiter").modal("show");
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
		//$('.dataTables_filter input').attr('placeholder', 'Search Name').css({'width':'250px', 'display':'inline-block'}); <!-- show searchbox + add styling -->
	}


	if(jQuery('#drTableDetailList').length>0){
		drTableDetailList = jQuery('#drTableDetailList')
		.on('preXhr.dt', function ( e, settings, data ){
			NProgress.start();
		}).DataTable({
			"columnDefs"		: [{
									"targets": [0], <!-- hide column -->
									"visible": false,
									"searchable": false
								}],
			"order"				: [[ 3, "desc" ]],
			"responsive"	  	: true,
			"bProcessing"		: true,
			"bServerSide"		: true,
			"sAjaxSource"		: "<?=base_url("api_admin/ecommerce/list_referralcode/detail_list_referral_code/"); ?>",
			"searching"			: false, // hide input search
			"fnServerParams": function ( aoData ) {
				aoData.push(
					{ "name": "id_user_recruiter", "value": $("#id_user_recruiter").val() },
					{ "name": "filter_referral_type", "value": $("#filter_referral_type").val() },
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

	$("#reset-filter").on("click",function(e){
		e.preventDefault();
		$("#flcdate_start, #flcdate_end").val("");
		$("#flis_active").val("");
		drTable.ajax.reload();
	});

	$("#reset-filter-detail").on("click",function(e){
		e.preventDefault();
		$("#filter_referral_type").val("");
		drTableDetailList.ajax.reload();
	});

	$("#filter_referral_type").on("change", function(e) {
		e.preventDefault();
		drTableDetailList.ajax.reload();
	});

	//filter data on change
	$("#flcdate_start, #flcdate_end, #flis_active").change(function() {
		drTable.ajax.reload();
	});

	$("#modal_delete_log").on("hidden.bs.modal",function(e) {
		$('#cdate_delete_start, #cdate_delete_end').datepicker('setDate', 'today').val("");
	});

	$("#btn_delete_log").on("click", () => {
		$("#modal_delete_log").modal("show");
	});

	//start create custom input date by year and month

	$("#flcdate_start").on("click", function() {
		//$("#modal-from-date").modal("show");

		let pos = $(this).offset();
		let width = $(this).width();   
		$("#custom_from_date_container").show();
		$("#custom_from_date_container").css({ "left": (pos.left + 80) + "px", "top": (pos.top - 170) + "px" });
		$("#custom_from_date_container").fadeIn();
	});

	$("#btn_done_from_date").on("click", function() {
		let month_from_date = $("#month_from_date").val();
		let year_from_date = $("#year_from_date").val();
		let input_from_date = year_from_date + '-' + month_from_date;
		$("#flcdate_start").val(input_from_date);
		//$("#modal-from-date").modal("hide");

		$("#custom_from_date_container").hide();
		$("#custom_from_date_container").fadeOut();
	});

	$("#flcdate_end").on("click", function() {
		//$("#modal-to-date").modal("show");

		let pos = $(this).offset();
		let width = $(this).width();   
		$("#custom_to_date_container").show();
		$("#custom_to_date_container").css({ "left": (pos.left + 90) + "px", "top": (pos.top - 170) + "px" });
		$("#custom_to_date_container").fadeIn();
	});

	$("#btn_done_to_date").on("click", function() {
		let month_to_date = $("#month_to_date").val();
		let year_to_date = $("#year_to_date").val();
		let input_to_date = year_to_date + '-' + month_to_date;
		$("#flcdate_end").val(input_to_date);
		//$("#modal-to-date").modal("hide");

		$("#custom_to_date_container").hide();
		$("#custom_to_date_container").fadeOut();
	});

	$(document).mouseup(function(e)  {
		let area_from_date = $("#custom_from_date_container");
		let area_to_date = $("#custom_to_date_container");

		if (!area_from_date.is(e.target) && area_from_date.has(e.target).length === 0) {
			area_from_date.hide();
		}

		if (!area_to_date.is(e.target) && area_to_date.has(e.target).length === 0) {
			area_to_date.hide();
		}
	});

	//end create custom input date by year and month
});