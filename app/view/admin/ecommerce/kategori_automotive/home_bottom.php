var growlPesan = '<h4>Error</h4><p>Tidak dapat diproses, silakan coba beberapa saat lagi!</p>';
var growlType = 'danger';
var drTable = {};
var ieid = '';
App.datatables();

let user_role = $("#user_role").val();

function gritter(pesan,jenis="info"){
	$.bootstrapGrowl(pesan, {
		type: jenis,
		delay: 2500,
		allow_dismiss: true
	});
}

if(jQuery('#drTable').length>0){
	drTable = jQuery('#drTable')
	.on('preXhr.dt', function ( e, settings, data ){
		NProgress.start();
	}).DataTable({
		<!-- START by Muhammad Sofi 21 December 2021 15:06 | add row number -->
		"columnDefs"		: [{
								"targets": [1], <!-- hide column -->
								"visible": false,
								"searchable": false
							}],
			<!-- END by Muhammad Sofi 21 December 2021 15:06 | add row number -->	
		"order"				: [[ 4, "asc" ]],
		"responsive"	  	: true,
		"bProcessing"		: true,
		"bServerSide"		: true,
		"sAjaxSource"		: "<?=base_url("api_admin/ecommerce/kategori_automotive/"); ?>",
		"fnServerData"		: function (sSource, aoData, fnCallback, oSettings) {
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
					/*var id = $(this).find("td").html();
					ieid = id;*/
					var currentRow = $(this).closest("tr");
					var id = $('#drTable').DataTable().row(currentRow).data()[1]; <!-- to get data from specific column, change this "data()[id_column]" -->
					ieid = id
					if(user_role != "marketing") {
						$("#modal_option").modal("show");
					}
				});
				fnCallback(response);
			}).error(function (response, status, headers, config) {
				NProgress.done();
				gritter("<h4>Error</h4><p>Cannot fetch data right now, please try again later</p>", 'warning');
			});
		},
	});
	$('.dataTables_filter input').attr('placeholder', 'Search brand name');
}

//option
$("#aedit").on("click",function(e){
	e.preventDefault();
	$("#modal_option").modal("hide");
	setTimeout(function(){
		//$("#modal_edit").modal("show");
		window.location = '<?=base_url_admin('ecommerce/kategori_automotive/edit/');?>'+ieid;
	},333);
});

//change icon
$("#aicon_change").on("click",function(e){
	e.preventDefault();
	$("#modal_option").modal("hide");
	setTimeout(function(){
		$("#ficon_change").trigger("reset");
		$("#modal_icon_change").modal("show");
	},500);
});

$("#ficon_change").on("submit",function(e){
	e.preventDefault();
	NProgress.start();
	var url = '<?=base_url("api_admin/ecommerce/kategori_automotive/change_icon/")?>'+ieid;
	$.ajax({
		type: $(this).attr('method'),
		url: url,
		data: new FormData(this),
		processData: false,
		contentType: false,
		success: function(respon){
			if(respon.status==200){
				gritter('<h4>Success</h4><p>Icon changed successfully</p>','success');
				setTimeout(function(){
					NProgress.done();
					drTable.ajax.reload();
					$("#modal_icon_change").modal("hide");
				},1000);
			} else {
				NProgress.done();
				gritter('<h4>Failed</h4><p>'+respon.message+'</p>','danger');
				$("#modal_icon_change").modal("hide");
			}
		},
		error:function(){
			setTimeout(function(){
				$("#modal_icon_change").modal("hide");
				NProgress.done();
				gritter('<h4>Error</h4><p>Cant change icon right now, please try again later</p>','warning');
			}, 666);
			return false;
		}
	});
});

//hapus
$("#bhapus").on("click",function(e){
	e.preventDefault();
	if(ieid){
		var c = confirm('Are you sure to delete this brand?');
		if(c){
			NProgress.start();
			var url = '<?=base_url('api_admin/ecommerce/kategori_automotive/hapus/'); ?>'+ieid;
			$.get(url).done(function(response){
				NProgress.done();
				if(response.status==200){
					gritter('<h4>Success</h4><p>Data successfully deleted</p>','success');
				}else{
					gritter('<h4>Failed</h4><p>'+response.message+'</p>','danger');
				}
				drTable.ajax.reload();
				$("#modal_option").modal("hide");
			}).fail(function() {
				NProgress.done();
				gritter('<h4>Error</h4><p>Cant deteled data right now, please try again later</p>','danger');
			});
		}
	}
});