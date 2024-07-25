var growlPesan = '<h4>Error</h4><p>Cannot be proceed, please try again later!</p>';
var growlType = 'danger';
var drTable = {};
var ieid = '';

function getUytpes(){
	return $('input[name=cb_utype]:checkbox:checked').map(function(){
		return this.value;
	}).get().join(",");
}
App.datatables();

if(jQuery('#drTable').length>0){
	drTable = jQuery('#drTable')
	.on('preXhr.dt', function ( e, settings, data ){
		NProgress.start();
	}).DataTable({
			"columns"				: [
		    null,
		    null,
		    null,
		    null,
		    null,
		    null,
		    null,
		    null,
		    null,
			null,
		    null,
		    null,
		    null,

				{ "orderable": false }
	  	],
			"scrollX"				: true,
			"order"					: [[ 0, "desc" ]],
			"responsive"	  : true,
			"bProcessing"		: true,
			"bServerSide"		: true,
			"sAjaxSource"		: "<?=base_url("api_admin/ecommerce/transaction/seller/")?>",
			"fnServerParams": function ( aoData ) {
				aoData.push(
					{ "name": "courier_service", "value": $("#ifcourier_service").val() },
					{ "name": "seller_status", "value": $("#ifseller_status").val() },
					{ "name": "shipment_status", "value": $("#ifshipment_status").val() },
					{ "name": "date_order_min", "value": $("#ifdate_order_min").val() },
					{ "name": "date_order_max", "value": $("#ifdate_order_max").val() },
					{ "name": "order_status", "value": $("#iforder_status").val() }
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
					$('#drTable > tbody').off('click', 'tr');
					$('#drTable > tbody').on('click', 'tr', function (e) {
						e.preventDefault();
						var id = $(this).find("td").html();
						ieid = id;
						$("#adetail").attr("href",'<?=base_url_admin("ecommerce/transaction/seller_detail/")?>'+ieid);

						//by Donny Dennison - 29 april 2021 14:06
        				//add-void-and-refund-2c2p-after-reject-by-seller
						$("#aVoidOrRefund").attr("href",'<?=base_url_admin("api_admin/ecommerce/transaction/voidorrefund/")?>'+ieid);

						$("#modal_option").modal("show");
					});
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
	$('.dataTables_filter input').attr('placeholder', 'Search product name, seller name').css({'width':'250px', 'display':'inline-block'});

	$("#afilter_do").on("click",function(e){
		e.preventDefault();
		drTable.ajax.reload();
	});

	$("#adownload_do").on("click",function(e){
		e.preventDefault();
		var mindate = $("#min").val();
		var maxdate = $("#max").val();
		window.location = '<?=base_url_admin()?>ecommerce/transaction/seller/download_xls_cekstok/?mindate='+encodeURIComponent(mindate)+'&maxdate='+encodeURIComponent(maxdate);
	});
	$("#areset_do").on("click",function(e){
		e.preventDefault();
		$("#ifcourier_service").val('');
		$("#ifseller_status").val('');
		$("#ifshipment_status").val('');
		$("#ifdate_order_min").val('');
		$("#ifdate_order_max").val('');
		$("#iforder_status").val('');
		drTable.ajax.reload();
	});
}



//tambah
$("#atambah").on("click",function(e){
	e.preventDefault();
	$("#modal_tambah").modal("show");
});
$("#modal_tambah").on("shown.bs.modal",function(e){
	$("#inegara").trigger("change");
});
$("#modal_tambah").on("hidden.bs.modal",function(e){
	$("#modal_tambah").find("form").trigger("reset");
});

$("#ftambah").on("submit",function(e){
	e.preventDefault();
	var fd = new FormData($(this)[0]);
	var url = '<?=base_url("api_admin/ecommerce/produk/tambah/")?>';
	$.ajax({
		type: $(this).attr('method'),
		url: url,
		data: fd,
		processData: false,
		contentType: false,
		success: function(respon){
			if(respon.status==200){
				growlPesan = '<h4>Successful</h4><p>Data Added!</p>';
				drTable.ajax.reload();
				growlType = 'success';
				$("#modal_tambah").modal("hide");
			}else{
				growlPesan = '<h4>Failed</h4><p>'+respon.message+'</p>';
				growlType = 'danger';
			}
			setTimeout(function(){
				$.bootstrapGrowl(growlPesan, {
					type: growlType,
					delay: 2500,
					allow_dismiss: true
				});
			}, 666);
		},
		error:function(){
			growlPesan = '<h4>Error</h4><p>Cannot adding data, please try again later</p>';
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

});



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
	var url = '<?=base_url("api_admin/ecommerce/produk/edit/")?>';
	$.ajax({
		type: $(this).attr('method'),
		url: url,
		data: fd,
		processData: false,
		contentType: false,
		success: function(respon){
			if(respon.status==200){
				growlType = 'success';
				growlPesan = '<h4>Successful</h4><p>Data Edited!</p>';
				drTable.ajax.reload();
			}else{
				growlType = 'danger';
				growlPesan = '<h4>Failed</h4><p>'+respon.message+'</p>';
			}
			$("#modal_edit").modal("hide");
			setTimeout(function(){
				$.bootstrapGrowl(growlPesan, {
					type: growlType,
					delay: 2500,
					allow_dismiss: true
				});
			}, 666);
		},
		error:function(){
			growlPesan = '<h4>Error</h4><p>Cannot Edit Data, please try again later</p>';
			growlType = 'danger';
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
});


//option
$("#aedit").on("click",function(e){
	e.preventDefault();
	$("#modal_option").modal("hide");
	setTimeout(function(){
		$("#modal_edit").modal("show");
	},333);
});

//detail
$("#adetail").on("click",function(e){
	e.preventDefault();
	$("#modal_option").modal("hide");
	setTimeout(function(){
		window.location='<?=base_url_admin('ecommerce/transaction/seller_detail/')?>'+ieid;
	},333);
});

//by Donny Dennison - 29 april 2021 14:06
//add-void-and-refund-2c2p-after-reject-by-seller
//void or refund
$("#aVoidOrRefund").on("click",function(e){
	e.preventDefault();
	if (confirm( 'Are you sure Void or Refund the Order?' ) ) {

		$.ajax({
			url: '<?=base_url('api_admin/ecommerce/transaction/voidorrefund/'); ?>'+(ieid),
			type: "POST",
			contentType: false,
			cache: false,
			processData:false,
			success: function(data){
				if(data.status == 200){
					growlType = 'success';
					growlPesan = '<h4>Successful</h4><p>This order has been marked for refund</p>';
					drTable.ajax.reload(null,false);
				}else{
					growlType = 'danger';
					growlPesan = '<h4>Failed</h4><p>'+data.message+'</p>';
					drTable.ajax.reload(null,false);
				}
				$("#modal_option").modal("hide");
				setTimeout(function(){
					$.bootstrapGrowl(growlPesan, {
						type: growlType,
						delay: 2500,
						allow_dismiss: true
					});
				}, 666);
			},
			error: function(data){
				growlPesan = '<h4>Error</h4><p>Cannot Edit Data, please try again later</p>';
				growlType = 'danger';
				setTimeout(function(){
					$.bootstrapGrowl(growlPesan, {
						type: growlType,
						delay: 2500,
						allow_dismiss: true
					});
				}, 666);
				drTable.ajax.reload(null,false);
			}
		});

	}
});
