var drTable = {};
var drTableHighlight = {};
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
			url: "<?= base_url('api_admin/community/highlight/getGeneralLocation') ?>",
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
		var data = $("#select_general_location option:selected").val(); <!-- .text to get all data -->
		$("#id_kelurahan").val(data);
		$("#id_kelurahan_modal").val(data);
		drTable.ajax.reload();
		//drTableHighlight.ajax.reload();
    });

	$("#ifstart_date").change(function(){
		drTable.ajax.reload();
		$('.datepicker').hide(); <!-- hide datepicker after select a date -->
	});

	<!-- table highlight community -->
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
			"order"				: [[ 1, "desc" ]],
			"responsive"	  	: true,
			"bProcessing"		: true,
			"bServerSide"		: true,
			"language"			: { searchPlaceholder: "Search Highlight Post" }, <!-- show placeholder in search box -->
			"sAjaxSource"		: "<?=base_url("api_admin/community/highlight/"); ?>",
				"fnServerParams": function ( aoData ) {
					aoData.push(
						{ "name": "id_kelurahan", "value": $("#id_kelurahan").val()},
						{ "name": "start_date", "value": $("#ifstart_date").val()},
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
						ieid = id;
						$("#modal_options_highlight").modal("show");
					});
					fnCallback(response);
				}).error(function (response, status, headers, config) {
					NProgress.done();
					gritter("<h4>Error</h4><p>Cannot fetch data right now, please try again later</p>", "warning");
				});
			},
		});
	}

	<!-- show modal for highlight community -->
	//jQuery("#list_community").click(function() {
	//	$("#id_kelurahan").val();
	//	if($("#id_kelurahan").val() == '') {
	//		alert("Choose General Location first");
	//		$('#select_general_location').select2('open');
	//	} else {
	//		$("#modal_highlight_community").modal("show");
	//	}
	//});

	<!-- by Muhammad Sofi 21 January 2022 22:46 | add highlight from community from many general location -->
	$("#list_community").click(function() { 
		$("#modal_highlight_community").modal("show");
	});

	$("#modal_highlight_community").on("shown.bs.modal",function(e){
		//
	});

	$("#modal_highlight_community").on('hidden.bs.modal', function (e) { 
		drTableHighlight.ajax.reload();
	});

	<!-- modal table community post -->
	if (!$.fn.dataTable.isDataTable('#drTableHighlight')) { <!-- fixing bug Cannot reinitialise DataTable -->
		drTableHighlight = jQuery('#drTableHighlight')
		.on('preXhr.dt', function ( e, settings, data ){
			NProgress.start();
		}).DataTable({
			"columnDefs"		: [{
									'targets': 0,
									'checkboxes': {'selectRow': true} 
									}],
			"select"			: {'style': 'multi'},
			"order"				: [[ 0, "desc" ]],
			"responsive"	  	: true,
			"bProcessing"		: true,
			"bServerSide"		: true,
			//"language"			: {searchPlaceholder: "Search Community Post"}, <!-- show placeholder in search box -->
			"sAjaxSource"		: "<?=base_url("api_admin/community/highlight/getcommunitydata"); ?>",
				"fnServerParams": function ( aoData ) {
					aoData.push(
						//{ "name": "from_date", "value": $("#ifcdate_start").val() },
						//{ "name": "to_date", "value": $("#ifcdate_end").val() },
						//{ "name": "status", "value": $("#ifproduk_status").val() },
						{ "name": "id_kelurahan_modal", "value": $("#id_kelurahan_modal").val() },
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
					$('#drTableHighlight > tbody').off('click', 'tr');
					$('#drTableHighlight > tbody').on('click', 'tr', function (e) {
						e.preventDefault();
						$(this).toggleClass('selected');
						var id = $(this).find("td").html();
						ieid = id;				
					});
					fnCallback(response);
				}).error(function (response, status, headers, config) {
					NProgress.done();
					gritter("<h4>Error</h4><p>Cannot fetch data right now, please try again later</p>", "warning");
				});
			},
		});
		$('.dataTables_filter input[type="search"]').attr('placeholder', 'Search Community Post').css({'width':'250px', 'display':'inline-block'});
	}

	// clear filter 
	$("#reset-filter").on("click",function(e){
		e.preventDefault();
		$('#ifstart_date').datepicker('setDate', 'today').val("");
		$("#select_general_location").val('').trigger("change");
		$("#id_kelurahan").val("");
		drTable.search('').columns().search('').draw(); <!-- by Muhammad Sofi 21 December 15:22 | delete typed text in search box -->
		drTable.ajax.reload();
		drTableHighlight.ajax.reload();
	});

	// set inactive highlight community
	$("#ainactive").on("click",function(e){
		e.preventDefault();
		var url = '<?=base_url(); ?>api_admin/community/highlight/setInactive/'+ ieid;
		$.get(url).done(function(response){
			if(response.message == 'success'){
				gritter('<h4>Success</h4><p>Highlight post successfully inactive!</p>', 'success');
				setTimeout(function(){
					window.location = '<?=base_url_admin('community/highlight')?>';
				},1200);
			} else {
				gritter("<h4>Error</h4><p>Cannot fetch data</p>", "danger");
				setTimeout(function(){
					window.location = '<?=base_url_admin('community/highlight')?>';
				},1200);
			}
		});
	});

	// set delete highlight community
	$("#adelete").on("click",function(e){
		e.preventDefault();
		<!-- by Muhammad Sofi 21 January 2022 18:38 | add delete confirmation alert -->
		if(ieid){
			var confirmation = confirm('Are you sure to delete?');
			if(confirmation) {
				NProgress.start();
				var url = '<?=base_url(); ?>api_admin/community/highlight/setDelete/'+ ieid;
				$.get(url).done(function(response){
					NProgress.done();
					if(response.message == 'success'){
						gritter('<h4>Success</h4><p>Highlight post successfully deleted!</p>', 'success');
						setTimeout(function(){
							window.location = '<?=base_url_admin('community/highlight')?>';
						},1200);
					} else {
						gritter("<h4>Error</h4><p>Cannot fetch data</p>", "danger");
						setTimeout(function(){
							window.location = '<?=base_url_admin('community/highlight')?>';
						},1200);
					}
				});
			}
		}
	});

	// START by Muhammad Sofi 2 February 2022 11:22 | comment unused code

	// by Muhammad Sofi 14 December 2021 17:32 | add function to change from manual to automatic 
	// $("#asetSystem").on("click",function(e){
	// 	e.preventDefault();
	// 	var kelurahan = $("#id_kelurahan").val();
	// 	if(kelurahan == '') {
	// 		alert("please, filter by General Location first");
	// 		$("#modal_options_highlight").modal("hide");
	// 		setTimeout(function(){
	// 			$('#select_general_location').select2('open');
	// 		}, 300);
	// 	} else {
	// 		var gl = $("#select_general_location option:selected").text();
	// 		var showAlert = confirm("You Choose Post From " + gl + "\n" + "Change to Automatic System?");
	// 		if (showAlert) {
	// 			var url = '<?=base_url(); ?>api_admin/community/highlight/setSystemAutomatic/'+ kelurahan;
	// 			$.get(url).done(function(response){
	// 				if(response.message == 'success'){
	// 					gritter('<h4>Success</h4><p>System Already Changed on this General Location</p>', 'success');
	// 					setTimeout(function(){
	// 						window.location = '<?=base_url_admin('community/highlight')?>';
	// 					},1200);
	// 				} else {
	// 					gritter("<h4>Error</h4><p>Cannot fetch data</p>", "danger");
	// 					setTimeout(function(){
	// 						window.location = '<?=base_url_admin('community/highlight')?>';
	// 					},1200);
	// 				}
	// 			});	
	// 		}
	// 	}
	// });

	// END by Muhammad Sofi 2 February 2022 11:22 | comment unused code

	$('#bbtnAll').click( function () {
		var row_length = drTableHighlight.rows('.selected').data().length;
		var kelurahan = $("#id_kelurahan_modal").val();
		<!-- check if post is not selected -->
		if(!row_length > 0) {
			alert("please, select the community post first");
		}
		$.post("<?=base_url() ?>api_admin/community/highlight/getCountPostalDistrict/"+ kelurahan, { }, 
		function(response) {
			<!-- alert(response.jumlahPostalDistrict); -->
			if(response.jumlahPostalDistrict < 10) { <!-- by Muhammad Sofi 7 December 2021 15:34 | change limit insert to 10 highlight post -->
				for (var i = 0; i < row_length; i++) {
					var idcom = $.map(drTableHighlight.rows('.selected').data(), function (item) {
						return item[0];
					});
					$.post("<?=base_url(); ?>api_admin/community/highlight/addData/"+ idcom[i], { }, 
					function(data) {
						if(data.status == 200) {
							setTimeout(function(){
								window.location = '<?=base_url_admin('community/highlight')?>';
							},500);
						} else {
							gritter("<h4>Error</h4><p>Cannot fetch data right now, please try again later</p>", "warning");
						}	
					}, "json");
				};
				gritter('<h4>Success</h4><p>Promote to Highlight succeed!</p>', 'success');
			} else {
				gritter('<h4>Error</h4><p>Only 10 highlight post allowed in this general location</p>', 'danger');
				//setTimeout(function() {
				//	window.location = '<?=base_url_admin('community/highlight')?>';
				//},2000);
			}
		}, "json");

		<!-- Using ajax -->
		//for (var i = 0; i < row_length; i++) { 
		//	var idcom = $.map(drTableHighlight.rows('.selected').data(), function (item) {
		//		return item[0];
		//	});
		//	$.ajax({
		//		type: "POST",
		//		url: '<?=base_url() ?>api_admin/community/highlight/addData/' +idcom[i],
		//		dataType: "JSON",
		//		data: JSON.stringify(idcom[i]),
		//		success: function() {
		//			gritter('<h4>Success</h4><p>Promote to Highlight succeed!</p>','success');
		//			setTimeout(function(){
		//				window.location = '<?=base_url_admin('community/highlight')?>';
		//			},1200);
		//		},
		//		error: function() {
		//			gritter("<h4>Error</h4><p>Cannot fetch data</p>", "danger");
		//		} 
		//	});
		//}
	});
});