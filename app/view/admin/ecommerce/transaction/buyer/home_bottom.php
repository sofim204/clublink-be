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
			"order"					: [[ 0, "desc" ]],
			"responsive"	  : true,
			"bProcessing"		: true,
			"bServerSide"		: true,
			"sAjaxSource"		: "<?=base_url("api_admin/ecommerce/transaction/buyer/")?>",
			"fnServerParams": function ( aoData ) {
				aoData.push(
					{ "name": "cdate_start", "value": $("#ifcdate_start").val() },
					{ "name": "cdate_end", "value": $("#ifcdate_end").val() },
					{ "name": "payment_gateway", "value": $("#if_payment_gateway").val() },
					{ "name": "order_status", "value": $("#if_order_status").val() },
					{ "name": "payment_status", "value": $("#if_payment_status").val() }
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
	$('.dataTables_filter input').attr('placeholder', 'Search invoice, buyer name').css({'width':'250px', 'display':'inline-block'});

	$("#afilter_do").on("click",function(e){
		e.preventDefault();
		var cdate_start = $("#ifcdate_start").val();
		var cdate_end = $("#ifcdate_end").val();
		if(cdate_start != "" || cdate_end != ""){
			drTable.order([2,'desc']);
		}
		drTable.ajax.reload();
	});

	$("#adownload_do").on("click",function(e){
		e.preventDefault();
		var mindate = $("#min").val();
		var maxdate = $("#max").val();
		window.location = '<?=base_url_admin()?>ecommerce/transaction/buyer/download_xls_cekstok/?mindate='+encodeURIComponent(mindate)+'&maxdate='+encodeURIComponent(maxdate);
	});
	$("#areset_do").on("click",function(e){
		e.preventDefault();
		$("#ifcdate_start").val("");
		$("#ifcdate_end").val("");
		$("#if_payment_gateway").val("");
		$("#if_order_status").val("");
		$("#if_payment_status").val("");
		drTable.search( '' ).columns().search( '' ).draw();
		drTable.ajax.reload(null,true);
	});
}

//detail
$("#adetail").on("click",function(e){
	e.preventDefault();
	$("#modal_option").modal("hide");
	setTimeout(function(){
		window.location='<?=base_url_admin('ecommerce/transaction/buyer_detail/')?>'+ieid;
	},333);
});
