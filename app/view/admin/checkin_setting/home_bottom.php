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

	<!-- initialize datepicker -->
	$('#start_date_event, #end_date_event').datepicker();

	<!-- set date to first date every month -->
	//var d_day = new Date();
	//	d_day.setMonth(d_day.getMonth(), 1);
	//let datefirst = d_day.toISOString().split('T')[0];

    //$('#start_date_event, #end_date_event').datepicker('setDate', datefirst).val("");

	$('#start_date_event, #end_date_event').datepicker('setDate', 'today').val("");

	$("#start_date_event, #end_date_event").change(function(){
		$('.datepicker').hide(); <!-- hide datepicker after select a date -->
	});

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
			"sAjaxSource"		: "<?=base_url("api_admin/checkin_setting/"); ?>",
			"fnServerParams": function ( aoData ) {
				aoData.push(
					{ "name": "cdate_start", "value": $("#flcdate_start").val() },
					{ "name": "cdate_end", "value": $("#flcdate_end").val() },
					{ "name": "path", "value": $("#flpath").val() }
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
						ieid = id

						var url = '<?=base_url("api_admin/checkin_setting/detail/")?>' + ieid;
						$.get(url).done(function(response){
							if(response.status==200){
								var dta = response.data;
								$("#ieid").val(dta.id);
								$("#iestatus").val(dta.is_active);

								<!-- show modal -->
								$("#modal_option").modal("show");
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
		$('.dataTables_filter input').attr('placeholder', 'Search Log').css({'width':'250px', 'display':'inline-block'}); <!-- show searchbox + add styling -->
	}

	$("#reset-filter").on("click",function(e){
		e.preventDefault();
		$("#flcdate_start").val("");
		$("#flcdate_end").val("");
		$("#flpath").val('').trigger("change");
		drTable.search('').columns().search('').draw();
		drTable.ajax.reload();
	});

	//filter data on change
	$("#flcdate_start, #flcdate_end, #flpath").change(function() {
		drTable.ajax.reload();
	});

	$("#modal_create_new_event").on("hidden.bs.modal",function(e) {
		$('#start_date_event, #end_date_event').datepicker('setDate', 'today').val("");
		$('#month_period').val("");
	});

	$("#btn_create_new_event").on("click", () => {
		$("#modal_create_new_event").modal("show");
	});

	$("#form_add_data").on("submit",function(e){
		e.preventDefault();
		NProgress.start();
		var fd = new FormData($("#form_add_data")[0]);
		var url = '<?=base_url("api_admin/checkin_setting/add/")?>';
		$.ajax({
			url: url,
			type: 'POST',
			mimeType : "multipart/form-data",
			dataType: 'json',
			processData: false,
			contentType: false,
			data: fd
		}).done(function(respon) {
			NProgress.done();
			if(respon.status==200){
				drTable.ajax.reload();
				$("#modal_create_new_event").modal("hide");
				gritter( '<h4>Success</h4><p>Event has been added successfuly</p>','success');
			}else{
				gritter( '<h4>Failed</h4><p>'+respon.message+'</p>','danger');
			}
		}).fail(function() {
			NProgress.done();
			gritter('<h4>Error</h4><p>Cannot add data right now, please try again later</p>','warning');
			return false;
		});
	});

	$("#modal_edit").on("shown.bs.modal",function(e){
	});

	$("#modal_edit").on("hidden.bs.modal",function(e){
		$("#modal_edit").find("form").trigger("reset");
	});

	$("#form_edit_data").on("submit",function(e){
		e.preventDefault();
		NProgress.start();
		var fd = new FormData($("#form_edit_data")[0]);
		var url = '<?=base_url("api_admin/checkin_setting/edit/")?>' +ieid;
		$.ajax({
			url: url,
			type: 'POST',
			mimeType : "multipart/form-data",
			dataType: 'json',
			processData: false,
			contentType: false,
			data: fd
		}).done(function(respon) {
			NProgress.done();
			if(respon.status==200){
				gritter('<h4>Success</h4><p>Data edited successfuly</p>','success');
				drTable.ajax.reload(null, false);  <!-- by Muhammad Sofi 28 January 2022 18:38 | Prevent table reload to first page after edit data -->
			}else{
				gritter('<h4>Failed</h4><p>'+respon.message+'</p>','danger');
			}
			$("#modal_edit").modal("hide");
		}).fail(function(){
			NProgress.done();
			setTimeout(function(){
				gritter('<h4>Error</h4><p>Cannot edit data right now, please try again later</p>','warning');
			}, 666);
			return false;
		});
	});

	$("#btn_delete_data").on("click",function(e){
		e.preventDefault();
		if(ieid){
			var c = confirm('Are you sure?');
			if(c){
				NProgress.start();
				var url = '<?=base_url('api_admin/checkin_setting/delete/')?>'+ieid;
				$.get(url).done(function(response){
					NProgress.done();
					if(response.status==200){
						gritter('<h4>Success</h4><p>Data successfuly deleted</p>','success');
						drTable.ajax.reload(null, false);
						$("#modal_option").modal("hide");
					}else{
						gritter('<h4>Failed</h4><p>'+response.message+'</p>','warning');
					}
				}).fail(function() {
					NProgress.done();
					gritter('<h4>Error</h4><p>Cant delete data right now, please try again later</p>','warning');
				});
			}
		}
	});

	$("#month_period").on("keyup", () => {
		let abc = $("#start_date_event").val();

		// original
		//var myDate = new Date(abc);
		//let period = $("#month_period").val();
		//let int_period = parseInt(period, 10);
		//let datea = myDate.getFullYear(); 
		//let dateb = (myDate.getMonth() + 1 + int_period).toString().padStart(2,0);
		//let datec = myDate.getDate().toString().padStart(2,0);
		//let datea = myDate.setYear(myDate.getFullYear()); 
		//let dateb = myDate.setMonth((myDate.getMonth() + 1 + int_period).toString().padStart(2,0));
		//let datec = myDate.setDate(myDate.getDate().toString().padStart(2,0));
		//let result = datea+'-'+dateb+'-'+ datec
		//alert(result)
		//end original

		//quiet simple
		var start_date = new Date($("#start_date_event").val());
		let period = $("#month_period").val();
		let int_period = parseInt(period, 10);
		start_date.setMonth( start_date.getMonth() + int_period);
		let end_date = start_date.toISOString().split('T')[0];
		//alert(end_date)
		$("#end_date_event").val(end_date);
	});

	$("#btn_edit_data").on("click", () => {
		$("#modal_option").modal("hide");
		setTimeout(function() {
			$("#modal_edit").modal("show");
		}, 500);
	});

});

$("#page-content").on("submit",".form-setup",function(e){
	e.preventDefault();
	var c = confirm("Are you sure?");
	if(c){
		NProgress.start();
		var fd = new FormData($(this)[0]);
		var url = $(this).attr("action");
		$.ajax({
			type: $(this).attr('method'),
			url: url,
			data: fd,
			processData: false,
			contentType: false,
			success: function(respon){
				NProgress.done();
				if(respon.status == 200){
					growlType = 'success';
					growlPesan = '<h4>Success</h4><p>'+respon.message+'</p>';
					setTimeout(function(){
						$.bootstrapGrowl(growlPesan, {
							type: growlType,
							delay: 2500,
							allow_dismiss: true
						});
					}, 666);
				}else{
					growlType = 'danger';
					growlPesan = '<h4>Failed</h4><p>'+respon.message+'</p>';
					setTimeout(function(){
						$.bootstrapGrowl(growlPesan, {
							type: growlType,
							delay: 2500,
							allow_dismiss: true
						});
					}, 666);
				}
			},
			error: function(){
				NProgress.done();
				growlPesan = '<h4>Error</h4><p>Cannot process data right now, please try again later</p>';
				growlType = 'warning';
				setTimeout(function(){
					$.bootstrapGrowl(growlPesan, {
						type: growlType,
						delay: 2500,
						allow_dismiss: true
					});
				}, 666);
				return false;
			}
		});
	}
});

var cc = <?=json_encode($leaderboard_point)?>;
$.each(cc,function(k,v){
	$("#fs_"+k).val(v);
});