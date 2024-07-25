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

	$('#from_cdate, #to_cdate').datepicker();
	$('#from_cdate, #to_cdate').datepicker('setDate', 'today').val("");

	$("#from_cdate, #to_cdate").change(function() {
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
			"sAjaxSource"		: "<?=base_url("api_admin/misc/sellondownload/"); ?>",
			"fnServerParams": function ( aoData ) {
				aoData.push(
					{ "name": "from_date", "value": $("#from_cdate").val() },
					{ "name": "to_date", "value": $("#to_cdate").val() },
				);
			},
			"fnServerData"	: function (sSource, aoData, fnCallback, oSettings) {
				oSettings.jqXHR = $.ajax({
					dataType 	: 'json',
					method 		: 'POST',
					url 		: sSource,
					data 		: aoData
				}).success(function (response, status, headers, config) {
					//console.log(response);
					NProgress.done();
					$('#drTable > tbody').off('click', 'tr');
					$('#drTable > tbody').on('click', 'tr', function (e) {
						e.preventDefault();
						var currentRow = $(this).closest("tr");
						var id = $('#drTable').DataTable().row(currentRow).data()[0]; <!-- to get data from specific column, change this "data()[id_column]" -->

						//$("#modal_edit").modal("show");
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

	$("#filter_data").on("click", function(e) {
		e.preventDefault();
		drTable.ajax.reload();
	});

	$("#reset_data").on("click", function(e) {
		$('#from_cdate, #to_cdate').datepicker('setDate', 'today').val("");
		drTable.ajax.reload();
	});  

	$("#refresh_table").on("click",function(e){
		e.preventDefault();
		drTable.ajax.reload(null, false);
	});
	
	$("#btn_create_qrcode").click(function(){
		$("#modal_create_qrcode").modal("show");
	});

	$("#modal_create_qrcode").on("hidden.bs.modal",function(e){
		$("#modal_create_qrcode").find("form").trigger("reset");
		$("#display_qrcode").attr("src", "");
		$(".qrcode_container").addClass('hidden')
	});

	$("#form_create_qrcode").on("submit",function(e){
		e.preventDefault();
		NProgress.start();
		var fd = new FormData($(this)[0]);
		var url = '<?=base_url("api_admin/misc/sellondownload/create_qrcode/")?>';
		$.ajax({
			type: $(this).attr('method'),
			url: url,
			data: fd,
			processData: false,
			contentType: false,
			success: function(respon){
				NProgress.done();
				if(respon.status==200){
					gritter('<h4>Success</h4><p>Data Created successfuly</p>','success');
					$(".qrcode_container").removeClass('hidden')
					$("#display_qrcode").attr("src", "<?=base_url()?>" + respon.data.qrcode.url);
				} else {
					gritter('<h4>Failed</h4><p>'+respon.message+'</p>','danger');
				}
			},
			error:function(){
				NProgress.done();
				gritter('<h4>Error</h4><p>Cant Add data right now, please try again later</p>','warning');
				return false;
			}
		});
	});
});