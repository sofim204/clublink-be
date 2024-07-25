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
	if(jQuery('#drTable').length>0){
		drTable = jQuery('#drTable')
		.on('preXhr.dt', function ( e, settings, data ){
			NProgress.start();
		}).DataTable({
			"order"				: [[ 0, "desc" ]],
			"responsive"	  	: true,
			"bProcessing"		: true,
			"bServerSide"		: true,
			"sAjaxSource"		: "<?=base_url("api_admin/misc/udid/"); ?>",
			"fnServerParams": function ( aoData ) {
				aoData.push(
					{ "name": "type_multilanguage", "value": $("#fltype_multilanguage").val() }
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
						var url = '<?=base_url("api_admin/misc/udid/detail/")?>'+ieid;
						$.get(url).done(function(response){
							if(response.status==200){
								var dta = response.data;
								$("#ieid").val(dta.id);
								$("#ieudid").val(dta.ui_id);
								$("#iename").val(dta.nama);

								<!-- tampilkan modal -->
								$("#modal_edit").modal("show");
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
		$('.dataTables_filter input').attr('placeholder', 'Search text');
	}

	<!-- START by Muhammad Sofi 27 January 2022 16:42 | adding form add data -->
	
	$("#addnew").click(function(){
		$("#modal_add").modal("show");
	});

	$("#modal_add").on("hidden.bs.modal",function(e){
		$("#modal_add").find("form").trigger("reset");
	});

	$("#form_add_data").on("submit",function(e){
		e.preventDefault();
		NProgress.start();
		var fd = new FormData($(this)[0]);
		var url = '<?=base_url("api_admin/misc/udid/add/")?>';
		$.ajax({
			type: $(this).attr('method'),
			url: url,
			data: fd,
			processData: false,
			contentType: false,
			success: function(respon){
				NProgress.done();
				if(respon.status==200){
					gritter('<h4>Success</h4><p>Data Added successfuly</p>','success');
					$("#modal_add").modal("hide");
					drTable.ajax.reload();
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
	<!-- END by Muhammad Sofi 27 January 2022 16:42 | adding form add data -->

	<!-- edit -->
	$("#modal_edit").on("shown.bs.modal",function(e){
	});

	$("#modal_edit").on("hidden.bs.modal",function(e){
		$("#modal_edit").find("form").trigger("reset");
	});

	$("#form_edit_data").on("submit",function(e){
		e.preventDefault();
		NProgress.start();
		var fd = new FormData($("#form_edit_data")[0]);
		var url = '<?=base_url("api_admin/misc/udid/edit/")?>' +ieid;
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

	$("#bdelete").on("click",function(e){
		e.preventDefault();
		if(ieid){
			var c = confirm('Are you sure?');
			if(c){
				NProgress.start();
				var url = '<?=base_url('api_admin/misc/udid/delete/')?>'+ieid;
				$.get(url).done(function(response){
					NProgress.done();
					$("#modal_edit").modal("hide");
					if(response.status==200){
						gritter('<h4>Success</h4><p>Data successfuly deleted</p>','success');
						drTable.ajax.reload();
						$("#modal_edit").modal("hide");
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

	$("#reset-filter").on("click",function(e){
		e.preventDefault();
		drTable.search('').columns().search('').draw();
		drTable.ajax.reload();
	});
});