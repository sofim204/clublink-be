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
			"scrollX"				: true,
			"order"					: [[ 1, "desc" ]],
			"responsive"	  : true,
			"bProcessing"		: true,
			"bServerSide"		: true,
			"sAjaxSource"		: "<?=base_url("api_admin/ecommerce/transactionhistory/")?>",
			"fnServerParams": function ( aoData ) {
				aoData.push(
					{ "name": "cdate_start", "value": $("#ifcdate_start").val() },
					{ "name": "cdate_end", "value": $("#ifcdate_end").val() },
					{ "name": "shipment_status", "value": $("#if_shipment_status").val() },
					{ "name": "seller_status", "value": $("#if_seller_status").val() },
					{ "name": "buyer_confirmed", "value": $("#if_buyer_confirmed").val() },
					{ "name": "order_status", "value": $("#if_order_status").val() },
					{ "name": "payment_status", "value": $("#if_payment_status").val() },
					{ "name": "settlement_status", "value": $("#if_settlement_status").val() }
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
						var id = $(this).find("td").html();
						ieid = id;
						$("#aprint_packing_list").attr("href",'<?=base_url_admin("ecommerce/transactionhistory/print_packing_list/")?>'+ieid);
						$("#aprint_faktur").attr("href",'<?=base_url_admin("ecommerce/transactionhistory/print_faktur/")?>'+ieid);
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
	$('.dataTables_filter input').attr('placeholder', 'Search invoice, product name').css({'width':'250px', 'display':'inline-block'});

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
		$("#if_shipment_status").val("");
		$("#if_seller_status").val("");
		$("#if_buyer_confirmed").val("");
		$("#if_order_status").val("");
		$("#if_payment_status").val("");
		$("#if_settlement_status").val("");
		drTable.search( '' ).columns().search( '' ).draw();
		drTable.ajax.reload(null,true);
	});

}

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
		window.location='<?=base_url_admin('ecommerce/transactionhistory/detail/')?>'+ieid;
	},333);
});

$("#bdownload_xls").on("click",function(e){
	e.preventDefault();
	var shipment_status = $("#if_shipment_status").val();
	var seller_status = $("#if_seller_status").val();
	var buyer_confirmed = $("#if_buyer_confirmed").val();
	var order_status = $("#if_order_status").val();
	var payment_status = $("#if_payment_status").val();

	window.location = '<?=base_url_admin()?>ecommerce/transactionhistory/download_xls/?shipment_status='+encodeURIComponent(shipment_status)+'&seller_status='+encodeURIComponent(seller_status)+'&buyer_confirmed='+encodeURIComponent(buyer_confirmed)+'&order_status='+encodeURIComponent(order_status)+'&payment_status='+encodeURIComponent(payment_status);
});
