var growlPesan = '<h4>Error</h4><p>Tidak dapat diproses, silakan coba beberapa saat lagi!</p>';
var growlType = 'danger';
//var ieid = '$(this).find("td").html()';
var ieid = '';
var drTable = {};
App.datatables();
function gritter(pesan,jenis='info'){
	$.bootstrapGrowl(pesan, {
		type: jenis,
		delay: 2500,
		allow_dismiss: true
	});
} 

if(jQuery('#drTable').length>0){
	drTable = jQuery('#drTable')
	.on('preXhr.dt', function ( e, settings, data ){
		$("#modal-preloader").modal("hide");
		//$("#modal-preloader").modal("show");
	}).DataTable({
		"columnDefs"	 : [{
								"targets": [1], <!-- hide column -->
								"visible": false,
								"searchable": false
							 }],	
		"order"			 : [[ 0, "asc" ]],
      "responsive"    : true,
      "bProcessing"   : true,
      "bServerSide"   : true,
      "sAjaxSource"   : "<?=base_url("api_admin/crm/outbounding/link_detail/".$ieid); ?>",
      "fnServerData"  : function (sSource, aoData, fnCallback, oSettings) {
			oSettings.jqXHR = $.ajax({
				dataType  : 'json',
				method    : 'POST',
				url     : sSource,
				data    : aoData
			}).success(function (response, status, headers, config) {
				console.log(response);
				$("#modal-preloader").modal("hide");
				//$('body').addClass('loaded');
				$('#drTable > tbody').off('click', 'tr');
				$('#drTable > tbody').on('click', 'tr', function (e) {
					e.preventDefault();
					var currentRow = $(this).closest("tr");
					var id = $('#drTable').DataTable().row(currentRow).data()[1]; <!-- to get data from specific column, change this "data()[id_column]" -->
					ieid = id;
					var url = '<?=base_url(); ?>api_admin/crm/outbounding/show_edit/'+ieid;
					$.get(url).done(function(response){
						if(response.status==200){
							var dta = response.data;
							ieid = dta.id;
							$("#ieid").val(dta.id);
							$("#ieoutboundid").val(dta.c_outbound_id);
							$("#iname").val(dta.name);
							$("#iurl").val(dta.url);
							$("#itype").val(dta.type);
						}
					});
            	$("#modal_option").modal("show");
				});
				fnCallback(response);
			}).error(function (response, status, headers, config) {
				$("#modal-preloader").modal("hide");
				//console.log(response, response.responseText);
				//$('body').addClass('loaded');
				alert("Error");
			});
      },
  	});
  	$('.dataTables_filter input').attr('placeholder', 'Search...');
}

//edit
$("#modal_edit").on("shown.bs.modal",function(e){
	//

});
$("#modal_edit").on("hidden.bs.modal",function(e){
  	$("#modal_edit").find("form").trigger("reset");
});

$("#fedit").on("submit",function(e){
	e.preventDefault();
	var fd = new FormData($(this)[0]);
	var id = $(this).find("td").html();
	var url = '<?=base_url("api_admin/crm/outbounding/editDetail/"); ?>'+ieid;
	$.ajax({
		type: $(this).attr('method'),
		url: url,
		data: fd,
		processData: false,
		contentType: false,
		success: function(respon){
			if(respon.status == 200){
				gritter('<h4>Success</h4><p>Data changed successfully</p>','success');
				drTable.ajax.reload();
			} else {
				gritter('<h4>Failed</h4><p>'+respon.message+'</p>','danger');
			}
			$("#modal_edit").modal("hide");
		},
		error:function(){
			gritter('<h4>Error</h4><p>Cannot edit data right now, please try again later</p>','warning');
			return false;
		}
	});
});

//active
$("#active").on("click",function(e){
	e.preventDefault();
	var id = ieid;
	if(id){
		var c = confirm('Are you sure to active this chat?');
		if(c){
			var url = '<?=base_url('api_admin/crm/discuss/active/'); ?>'+id;
			$.get(url).done(function(response){
				if(response.status==200){
					gritter('<h4>Success</h4><p>Data has been actived successfully</p>','success');
				} else {
					gritter('<h4>Failed</h4><p>'+response.message+'</p>','Danger');
				}
				drTable.ajax.reload();
				$("#modal_option").modal("hide");
			}).fail(function() {
				gritter('<h4>Error</h4><p>Cannot activated data right now, please try again later</p>','warning');
			});
		}
	}
});

//hapus
$("#bhapus").on("click",function(e){
  e.preventDefault();
  if(ieid){
		var c = confirm('Are you sure?');
		if(c){
			NProgress.start();
			var url = '<?=base_url('api_admin/crm/outbounding/hapusDetail/')?>'+ieid;
			$.get(url).done(function(response) {
				NProgress.done();
				$("#modal_edit").modal("hide");
				if(response.status==200) {
					gritter('<h4>Success</h4><p>Data successfuly deleted</p>','success');
					drTable.ajax.reload();
					$("#modal_option").modal("hide");
				}else{
					gritter('<h4>Failed</h4><p>'+response.message+'</p>','warning');
					$("#modal_option").modal("hide");
				}
			}).fail(function() {
				NProgress.done();
				gritter('<h4>Error</h4><p>Cant delete data right now, please try again later</p>','warning');
			});
		}
	}
});

//option
$("#aedit").on("click",function(e){
	e.preventDefault();
	$("#modal_option").modal("hide");
	setTimeout(function(){
		$("#modal_edit").modal("show");
	},333);
});

