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
	$('#flcdate_start, #flcdate_end, #cdate_delete_start, #cdate_delete_end').datepicker();
    $('#flcdate_start, #flcdate_end, #cdate_delete_start, #cdate_delete_end').datepicker('setDate', 'today').val("");

	$("#flcdate_start, #flcdate_end, #cdate_delete_start, #cdate_delete_end").change(function(){
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
			"order"				: [[ 2, "desc" ]],
			"responsive"	  	: true,
			"bProcessing"		: true,
			"bServerSide"		: true,
			"sAjaxSource"		: "<?=base_url("api_admin/misc/semelog/"); ?>",
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

	$("#modal_delete_log").on("hidden.bs.modal",function(e) {
		$('#cdate_delete_start, #cdate_delete_end').datepicker('setDate', 'today').val("");
	});

	$("#btn_delete_log").on("click", () => {
		$("#modal_delete_log").modal("show");
	});

	$("#btn_delete_log_modal").on("click", (e) => {
		e.preventDefault();

		let from_date = $("#cdate_delete_start").val();
		let to_date = $("#cdate_delete_end").val();

		if(from_date.length == 0 || to_date.length == 0) {
			Swal.fire({
				background: "#383434",
				color: "#FFFFFF",
  				width: 400,
				icon: "warning",
				title: "Date can't be empty",
				confirmButtonText: "OKAY",
			});
		} else {
			Swal.fire({
				title: 'Are you sure to delete?',
				background: "#383434",
				color: "#FFFFFF",
				width: 500,
				imageUrl: 'https://imagizer.imageshack.com/img923/3084/HkPhWR.jpg',
				//imageUrl: 'https://i.postimg.cc/rFHWctZT/areyousure.jpg',
				imageWidth: 200,
				showCancelButton: true,
				confirmButtonText: 'YES',
				cancelButtonText: 'NO',
				reverseButtons: true
			}).then( (result) => {
				if (result.isConfirmed) {
					var url = '<?=base_url("api_admin/misc/semelog/delete_log/")?>' + from_date + '/' + to_date;
					$.get(url).done(function(response){
						if(response.status == 200){
							gritter('<h4>Success</h4><p>Log successfuly deleted</p>','success');
							drTable.ajax.reload();
							$("#modal_delete_log").modal("hide");
						} else {
							gritter('<h4>Failed</h4><p>'+response.message+'</p>','warning');
						}
					}).fail(function() {
						gritter('<h4>Error</h4><p>Cant delete data right now, please try again later</p>','warning');
					});
				} else if(result.dismiss === Swal.DismissReason.cancel) { }
			});
		}
	});
});