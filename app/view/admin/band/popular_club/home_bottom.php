var growlPesan = '<h4>Error</h4><p>Tidak dapat diproses, silakan coba beberapa saat lagi!</p>';
var growlType = 'danger';
var drTable = {};
var ieid = '';
var gettype = ''
App.datatables();

let user_role = $("#user_role").val();

function gritter(pesan,jenis="info"){
	$.bootstrapGrowl(pesan, {
		type: jenis,
		delay: 2500,
		allow_dismiss: true
	});
}
$(document).ready(function() {

	$('#iecdate, #ieedate').datepicker();
	$("#iecdate, #ieedate").change(function(){
		$('.datepicker').hide(); <!-- hide datepicker after select a date -->
	});

	if(jQuery('#drTable').length>0){
		drTable = jQuery('#drTable')
		.on('preXhr.dt', function ( e, settings, data ){
			NProgress.start();
		}).DataTable({
				"columnDefs"		: [{
										"targets": [0, 1, 9, 10], <!-- hide column -->
										"visible": false,
										"searchable": false
									}],
				"order"				: [[ 4, "asc" ]], <!-- by Muhammad Sofi 11 January 2022 9:47 | add & edit input priority, show priority in datatable -->
				"responsive"	  	: true,
				"bProcessing"		: true,
				"bServerSide"		: true, // hide search bar
				"bFilter"       	: false,
				"sAjaxSource"		: "<?=base_url("api_admin/band/popular_club/"); ?>",
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
							var current_club_name = $('#drTable').DataTable().row(currentRow).data()[2];
							var priority = $('#drTable').DataTable().row(currentRow).data()[4];
							var current_club_id = $('#drTable').DataTable().row(currentRow).data()[9];
							var is_active = $('#drTable').DataTable().row(currentRow).data()[10];
							var start_date = $('#drTable').DataTable().row(currentRow).data()[5];
							var end_date = $('#drTable').DataTable().row(currentRow).data()[6];
							ieid = id;
							
							$("#current_club_name").val(current_club_name);
							$("#change_priority").val(priority);
							$("#club_choosed").val(current_club_id);
							$("#change_is_active").val(is_active);
							$("#iecdate").datepicker('setDate', start_date).val(start_date);
							$("#ieedate").datepicker('setDate', end_date).val(end_date);

							<!-- show modal -->
							$("#modal_change_club").modal("show");
						});
						fnCallback(response);
					}).error(function (response, status, headers, config) {
						NProgress.done();
						gritter("<h4>Error</h4><p>Cant fetch data right now, please try again later</p>",'warning');
					});
				},
		});
		$('.dataTables_filter input').attr('placeholder', 'Search').css({'width':'250px', 'display':'inline-block'});
	}

	$("#select_club").select2({
		//placeholder: "--Select Club--",
		ajax: { 
			url: "<?= base_url('api_admin/band/popular_club/get_club') ?>",
			type: "post",
			dataType: 'json',
			delay: 250,
			data: function (params) {
				return {
					search: params.term, // search term
				};
			},
			processResults: function (response) {
				response.unshift({id: '', text: '===== Cancel your selection ====='})
				return {
					results: response
				};
			},
		},
		//minimumInputLength: 3,
	}).change(function(){
		$("#club_choosed").val($("#select_club option:selected").val());
	});

	$("#select_popular_club").select2({
		//placeholder: "--Select Club--",
		ajax: { 
			url: "<?= base_url('api_admin/band/popular_club/get_club') ?>",
			type: "post",
			dataType: 'json',
			delay: 250,
			data: function (params) {
				return {
					search: params.term, // search term
				};
			},
			processResults: function (response) {
				response.unshift({id: '', text: '===== Cancel your selection ====='})
				return {
					results: response
				};
			},
		},
		//minimumInputLength: 3,
	}).change(function(){
		$("#club_choosed_id").val($("#select_popular_club option:selected").val());
	});

	$(".button_check_club").on("click",function(e){
		e.preventDefault();
		let club_id = $("#club_choosed").val();
		setTimeout(function(){
			window.open('<?=base_url_admin('band/group/detail_group_post/')?>' + club_id, "_blank");
		},333);
	});

	$("#modal_change_club").on("hidden.bs.modal",function(e){
		//$("#select_club").val('').trigger('change');
		$("#modal_change_club").find("form").trigger("reset");
		//$(".container-change-club").hide();
	});

	$("#form_change_club").on("submit",function(e){
		e.preventDefault();
		NProgress.start();
		var url = '<?=base_url("api_admin/band/popular_club/change_club/")?>' + ieid;
		$.ajax({
			type: $(this).attr('method'),
			url: url,
			data: new FormData(this),
			processData: false,
			contentType: false,
			success: function(respon){
				if(respon.status==200){
					gritter('<h4>Success</h4><p>Data changed successfully</p>','success');
					setTimeout(function(){
						NProgress.done();
						drTable.ajax.reload();
						$("#modal_change_club").modal("hide");
					},1000);
				}else{
					NProgress.done();
					gritter('<h4>Failed</h4><p>'+respon.message+'</p>','danger');
					$("#modal_change_club").modal("hide");
				}
			},
			error:function(){
				setTimeout(function(){
					$("#modal_change_club").modal("hide");
					NProgress.done();
					gritter('<h4>Error</h4><p>Cant change right now, please try again later</p>','warning');
				}, 666);
				return false;
			}
		});
	})

	$("#form_add_popular_club_to_homepage").on("submit",function(e){
		e.preventDefault();
		NProgress.start();
		var url = '<?=base_url("api_admin/band/popular_club/add_popular_club_to_homepage/")?>';
		$.ajax({
			type: $(this).attr('method'),
			url: url,
			data: new FormData(this),
			processData: false,
			contentType: false,
			success: function(respon){
				if(respon.status==200){
					gritter('<h4>Success</h4><p>Data added successfully</p>','success');
					setTimeout(function(){
						NProgress.done();
						drTable.ajax.reload();
						$("#modal_add_popular_club_to_homepage").modal("hide");
					},1000);
				}else{
					NProgress.done();
					gritter('<h4>Failed</h4><p>'+respon.message+'</p>','danger');
					//$("#modal_add_popular_club_to_homepage").modal("hide");
				}
			},
			error:function(){
				setTimeout(function(){
					$("#modal_add_popular_club_to_homepage").modal("hide");
					NProgress.done();
					gritter('<h4>Error</h4><p>Cant change right now, please try again later</p>','warning');
				}, 666);
				return false;
			}
		});
	})

	$("#form_change_and_reorder_popular_club").on("submit",function(e){
		e.preventDefault();
		NProgress.start();
		var url = '<?=base_url("api_admin/band/popular_club/change_and_reorder_popular_club/")?>' + ieid;
		$.ajax({
			type: $(this).attr('method'),
			url: url,
			data: new FormData(this),
			processData: false,
			contentType: false,
			success: function(respon){
				if(respon.status==200){
					gritter('<h4>Success</h4><p>Data changed successfully</p>','success');
					setTimeout(function(){
						NProgress.done();
						drTable.ajax.reload();
						$("#modal_change_and_reorder_popular_club").modal("hide");
					},1000);
				}else{
					NProgress.done();
					gritter('<h4>Failed</h4><p>'+respon.message+'</p>','danger');
					$("#modal_change_and_reorder_popular_club").modal("hide");
				}
			},
			error:function(){
				setTimeout(function(){
					$("#modal_change_and_reorder_popular_club").modal("hide");
					NProgress.done();
					gritter('<h4>Error</h4><p>Cant change right now, please try again later</p>','warning');
				}, 666);
				return false;
			}
		});
	})

	$("#button_change_and_reorder_popular_club").on("click", function() {
		$("#modal_change_and_reorder_popular_club").modal("show")
	})

	$("#button_add_to_popular_club_homepage").on("click", function() {
		$("#modal_add_popular_club_to_homepage").modal("show")
	})

	$("#button_change_club").on('click', function() {
		$(".container-change-club").show();
	})
})