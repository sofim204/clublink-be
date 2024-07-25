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

	function defaultMonthYear() {
		let date_by_year_month = $("#reset_year_month").val();
		let year_today = date_by_year_month.substring(0, 4);
		let month_today = date_by_year_month.substring(5, 7);
		$("#month_from_date").val(month_today);
		$("#year_from_date").val(year_today);
		$("#month_to_date").val(month_today);
		$("#year_to_date").val(year_today);
	}

	defaultMonthYear();

	<!-- initialize datepicker -->
	//$('#flcdate_start, #flcdate_end, #cdate_delete_start, #cdate_delete_end').datepicker();
    //$('#flcdate_start, #flcdate_end, #cdate_delete_start, #cdate_delete_end').datepicker('setDate', 'today').val("");

	//$("#flcdate_start, #flcdate_end, #cdate_delete_start, #cdate_delete_end").change(function(){
	//	$('.datepicker').hide(); <!-- hide datepicker after select a date -->
	//});

	if(jQuery('#drTable').length>0){
		drTable = jQuery('#drTable')
		.on('preXhr.dt', function ( e, settings, data ){
			NProgress.start();
		}).DataTable({
			"columnDefs"		: [{
									"targets": [1], <!-- hide column -->
									"visible": true,
									"searchable": false
								}],
			"order"				: [[ 0, "asc" ]],
			"responsive"	  	: true,
			"bProcessing"		: true,
			"bServerSide"		: true,
			"searching"			: false,
			"sAjaxSource"		: "<?=base_url("api_admin/offer_detail/buyer/"); ?>",
			"fnServerParams": function ( aoData ) {
				aoData.push(
					{ "name": "cdate_start", "value": $("#flcdate_start").val() },
					{ "name": "cdate_end", "value": $("#flcdate_end").val() },
					{ "name": "b_user_id_buyer", "value": $("#id_buyer_type_offer").val() },
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
					});
					fnCallback(response);
				}).error(function (response, status, headers, config) {
					NProgress.start();
					gritter('<h4>Error</h4><p>Cannot fetch data, try again later</p>','warning');
					return false;
				});
			},
		});
		//$('.dataTables_filter input').attr('placeholder', 'Search Log').css({'width':'250px', 'display':'inline-block'}); <!-- show searchbox + add styling -->
	}

	$("#reset-filter").on("click",function(e){
		e.preventDefault();
		$("#flcdate_start, #flcdate_end").val($("#reset_year_month").val());
    	defaultMonthYear();
		drTable.ajax.reload();
	});

	//filter data on change
	$("#flcdate_start, #flcdate_end, #flpath").change(function() {
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