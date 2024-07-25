var growlPesan = '<h4>Error</h4><p>Cannot be proceed, please try again later!</p>';
var growlType = 'danger';
var drTable = {};
var drTableDetailLog = {};
var mobile_type_history = '';
var detail_date_history = '';

var action = '';

function growlShow(growlPesan,growlType="info"){
	$.bootstrapGrowl(growlPesan, {
		type: growlType,
		delay: 2500,
		allow_dismiss: true
	});
}

App.datatables();

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
			"oSearch"			: {"sSearch": "<?=$keyword?>"},
			"scrollX"			: false,
			"order"				: [[ 0, "desc" ]],
			"responsive"	  	: true,
			"bProcessing"		: true,
			"bServerSide"		: true,
			"sAjaxSource"		: "<?=base_url("api_admin/visitorcount_/")?>",
			"fnServerParams": function ( aoData ) {
				aoData.push(
					{ "name": "cdate_start", "value": $("#ifcdate_start").val() },
					{ "name": "cdate_end", "value": $("#ifcdate_end").val() },
					{ "name": "mobile_type", "value": $("#if_mobile_type").val() }
				);
			},
			"fnServerData"	: function (sSource, aoData, fnCallback, oSettings) {
				//$('body').removeClass('loaded');

				oSettings.jqXHR = $.ajax({
					dataType 	: 'json',
					method 		: 'POST',
					url 		: sSource,
					data 		: aoData
				}).success(function (response, status, headers, config) {
					NProgress.done();
					$('#drTable > tbody').off('click', 'tr');
					$('#drTable > tbody').on('click', 'tr', function (e) {
						e.preventDefault();
						var currentRow = $(this).closest("tr");
						var cdate = $('#drTable').DataTable().row(currentRow).data()[5]; <!-- to get data from specific column, change this "data()[id_column]" -->
						var mob_type = $('#drTable').DataTable().row(currentRow).data()[2]; <!-- to get data from specific column, change this "data()[id_column]" -->
						var url_data = '<?=base_url() ?>api_admin/visitorcount_/getData/' + mob_type + '/' + cdate;
						$.get(url_data).done(function(response){
							if(response.status==200){
								var dta = response.data;
								//let result = dta.cdate;
								//let trim_date = result.substr(0, 10);

								$("#detail_mobile_type").val(dta.mobile_type);
								$("#detail_date").val(dta.cdate);

								mobile_type_history = $("#detail_mobile_type").val();
								detail_date_history = $("#detail_date").val();

								drTableDetailLog.ajax.reload();

								<!-- tampilkan modal -->
								$("#modal_history").modal("show");
							}else{
								gritter('<h4>Failed</h4><p>Cannot fetch data, try again later</p>','info');
							}
						});
					});

					<!-- by Donny Dennisoon - 2 march 2021 17:56 -->
					<!-- count visitor -->
					<!-- START by Donny Dennisoon - 2 march 2021 17:56 -->
					var cdate_start = $("#ifcdate_start").val();
				    var cdate_end = $("#ifcdate_end").val();
				    var mobile_type = $("#if_mobile_type").val();

				    $.ajax({
						url: '<?=base_url("api_admin/visitorcount_/totalvisitbydevice")?>',
						data:{
							start_date:cdate_start,
							end_date:cdate_end,
							mobile_type:mobile_type,
							},
						method: 'POST',
						success: function(data)
						{
							document.getElementById("totalAndroid").innerHTML = parseInt(data.data.totalAndroid).toLocaleString();
							document.getElementById("totalIOS").innerHTML = parseInt(data.data.totalIOS).toLocaleString();

							var totalAll = parseInt(data.data.totalAndroid)+parseInt(data.data.totalIOS);
							
							document.getElementById("totalAll").innerHTML = parseInt(totalAll).toLocaleString();
						}
					});

					/* var x;
					var totalAndroid = 0;
					var totalIOS = 0;
					var totalAll = 0;

					for (x of response.data) {

						if(x[1] == 'android'){

							totalAndroid += parseInt(x[2]);

						}else{

							totalIOS += parseInt(x[2]);

						}

					}

					totalAll = totalAndroid + totalIOS;


					document.getElementById("totalAndroid").innerHTML = totalAndroid.toString();
					document.getElementById("totalIOS").innerHTML = totalIOS.toString();
					document.getElementById("totalAll").innerHTML = totalAll.toString(); */


					<!-- END by Donny Dennisoon - 2 march 2021 17:56 -->

					fnCallback(response);

				}).error(function (response, status, headers, config) {
					NProgress.done();
					growlType = 'danger';
					growlPesan = '<h4>Error</h4><p>Cannot fetch data</p>';
					$.bootstrapGrowl(growlPesan, {
						type: growlType,
						delay: 2500,
						allow_dismiss: true
					});
				});
			},


	});
	$('.dataTables_filter input').attr('placeholder', 'Search mobile type');

	$("#if_action").on("click",function(e){
		e.preventDefault();
		drTable.ajax.reload(null,true);
	})
	$("#afilter_do").on("click",function(e){
		e.preventDefault();
		drTable.ajax.reload();
	});
	$("#if_reset").on("click",function(e){
		e.preventDefault();
		$("#ifcdate_start").val("");
		$("#ifcdate_end").val("");
		$("#if_mobile_type").val(""); <!-- by Muhammad Sofi 21 December 2021 15:26 | set to default value -->
		drTable.search( '' ).columns().search( '' ).draw();
		drTable.ajax.reload(null,true);
	});
}

if(jQuery('#drTableDetailLog').length>0){
	drTableDetailLog = jQuery('#drTableDetailLog')
	.on('preXhr.dt', function ( e, settings, data ){
		NProgress.start();
	}).DataTable({
		//"columnDefs"		: [{
		//						searchable: false,
		//						orderable: false,
		//						targets: 0,
		//					}],	
		"order"				: [[ 0, "desc" ]],
		"responsive"	  	: true,
		"bProcessing"		: true,
		"bServerSide"		: true,
		"searching"			: false, // hide input search
		//"sAjaxSource"		: url_detail,
		"sAjaxSource"		: "<?=base_url("api_admin/visitorcount_/detail_data_log"); ?>",
		"fnServerParams": function ( aoData ) {
			aoData.push(
				{ "name": "mobile_type", "value": $("#detail_mobile_type").val() },
				{ "name": "detail_date", "value": $("#detail_date").val() }
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

				var url_total = '<?=base_url() ?>api_admin/visitorcount_/getTotal/' + mobile_type_history + '/' + detail_date_history;

				$.get(url_total).done(function(response){
					if(response.status==200){
						var dta = response.data;
						$("#detail_total").val(dta.totalUser);

					}else{
						gritter('<h4>Failed</h4><p>Cannot fetch data, try again later</p>','info');
					}
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
$('#ifcdate_start, #ifcdate_end').datepicker();
$('#ifcdate_start, #ifcdate_end').datepicker('setDate', 'today').val("");

$("#ifcdate_start, #ifcdate_end").change(function(){
	$('.datepicker').hide(); <!-- hide datepicker after select a date -->
});

$("#bdownload_xls").on("click", function(e) {
	e.preventDefault();
	var mobile_type = $("#if_mobile_type").val();
	var cdate_start = $("#ifcdate_start").val();
	var cdate_end = $("#ifcdate_end").val();
	<!-- do checking -->
	//if(cdate_start == "" && cdate_end == "") {
	//	alert("Please, select Date");
	//} else if ($("#if_mobile_type").val() === "") {
	//	alert("Please, select Mobile type");
	//} else {
		var url ='<?=base_url_admin()?>visitorcount_/download_xls/?';
		url += 'mobile_type='+encodeURIComponent(mobile_type);
		url += '&cdate_start='+encodeURIComponent(cdate_start);
		url += '&cdate_end='+encodeURIComponent(cdate_end);
		window.location = url;
	//}
});
