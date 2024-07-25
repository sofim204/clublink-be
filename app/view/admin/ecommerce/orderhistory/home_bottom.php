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
		$("#modal-preloader").modal("hide");
		//$("#modal-preloader").modal("show");
	}).DataTable({
			"scrollX"				: true,
			"order"					: [[ 0, "desc" ]],
			"responsive"	  : true,
			"bProcessing"		: true,
			"bServerSide"		: true,
			"sAjaxSource"		: "<?=base_url("api_admin/ecommerce/orderhistory/")?>",
			"fnServerParams": function ( aoData ) {
				aoData.push(
					//{ "name": "utype", "value": $('input[name=cb_utype]:checkbox:checked').map(function(){ return this.value; }).get().join(",") },
					//{ "name": "tglmin", "value": $("#min").val() },
					//{ "name": "tglmax", "value": $("#max").val() },
					{ "name": "order_status", "value": $("#if_order_status").val() },
					{ "name": "payment_status", "value": $("#if_payment_status").val() }
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
					console.log(response);
					$("#modal-preloader").modal("hide");
					//$('body').addClass('loaded');
					$('#drTable > tbody').off('click', 'tr');
					$('#drTable > tbody').on('click', 'tr', function (e) {
						e.preventDefault();
						var id = $(this).find("td").html();
						ieid = id;
						$("#aprint_packing_list").attr("href",'<?=base_url_admin("ecommerce/orderhistory/print_packing_list/")?>'+ieid);
						$("#aprint_faktur").attr("href",'<?=base_url_admin("ecommerce/orderhistory/print_faktur/")?>'+ieid);
						$("#modal_option").modal("show");
					});
					fnCallback(response);
				}).error(function (response, status, headers, config) {
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
	$('.dataTables_filter input').attr('placeholder', 'Search');

	$("#if_action").on("click",function(e){
		e.preventDefault();
		drTable.ajax.reload(null,true);
	})
	$("#afilter_do").on("click",function(e){
		e.preventDefault();
		drTable.ajax.reload();
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
		window.location='<?php echo base_url_admin('ecommerce/orderhistory/detail/')?>'+ieid;
	},333);
});

$("#bdownload_xls").on("click",function(e){
	e.preventDefault();
	var order_status = $("#if_order_status").val();
	var payment_status = $("#if_payment_status").val();

	window.location = '<?=base_url_admin()?>ecommerce/orderhistory/download_xls/?order_status='+encodeURIComponent(order_status)+'&payment_status='+encodeURIComponent(payment_status);
});
