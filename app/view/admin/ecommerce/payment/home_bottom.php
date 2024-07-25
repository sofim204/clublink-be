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
		"sAjaxSource"		: "<?=base_url("api_admin/ecommerce/payment/")?>",
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
				NProgress.done();
				$("#drTable tbody").off("change",'input[type="checkbox"]');
				$("#drTable tbody").on("change",'input[type="checkbox"]',function(e){
					var c = 0;
					$.each($("#drTable tbody .input-payment-selected"),function(k,v){
						if($(v).prop("checked")){ c++; }
					});
					$("#payment-selected-count").html(c);
				});

				$("#drTable tbody").off("click",".btn-payment-now");
				$("#drTable tbody").on("click",".btn-payment-now",function(e){
					e.preventDefault();
					NProgress.start();
					var oid = $(this).attr("data-id");
					var fd = {};
					fd.oid = oid;
					$.post("<?=base_url('api_admin/ecommerce/payment/process/')?>"+oid,fd).done(function(dt){
						NProgress.done();
						if(dt.status == 200){
							//drTable.ajax.reload(null,false);
							drTable.ajax.reload();
							growlType = 'success';
							growlPesan = '<h4>Success</h4><p>Payment processed succesfully</p>';
							$.bootstrapGrowl(growlPesan, {
								type: growlType,
								delay: 2500,
								allow_dismiss: true
							});
						}else{
							growlType = 'danger';
							growlPesan = '<h4>Failed</h4><p>'+dt.message+'</p>';
							$.bootstrapGrowl(growlPesan, {
								type: growlType,
								delay: 2500,
								allow_dismiss: true
							});
						}
					}).fail(function(dt){
						NProgress.done();
						growlType = 'warning';
						growlPesan = '<h4>Error</h4><p>Cannot fetch data</p>';
						$.bootstrapGrowl(growlPesan, {
							type: growlType,
							delay: 2500,
							allow_dismiss: true
						});
					});
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
	 'columnDefs': [{
	   	'targets': 0,
	   	'searchable': false,
	   	'orderable': false,
	   	'className': 'dt-body-center',
	   	'render': function (data, type, full, meta){
	       	return '<input type="checkbox" name="oids[]" class="input-payment-selected" value="' + $('<div/>').text(data).html() + '">';
	   	},
		},
		{
			'targets': 2,
			'width': "200px"
		},
		{
			'targets': 3,
			'width': "300px"
		},
		{
 	   	'targets': 8,
 	   	'searchable': true,
 	   	'orderable': true
		}]
	});
	<!-- by Muhammad Sofi 21 December 2021 15:06 | increase width of search box -->
	//$('.dataTables_filter input').attr('placeholder', 'Search invoice, product name');
	$('.dataTables_filter input[type="search"]').attr('placeholder', 'Search invoice, product name').css({'width':'250px', 'display':'inline-block'}); <!-- show searchbox + add styling -->

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
		$("#if_seller_status").val("");
		$("#if_buyer_confirmed").val("");
		$("#if_settlement_status").val("");
		drTable.search( '' ).columns().search( '' ).draw();
		drTable.ajax.reload(null,true);
	});

	// Handle click on "Select all" control
 	$('#drTable-select-all').on('click', function(){
  	// Get all rows with search applied
 	 var rows = drTable.rows({ 'search': 'applied' }).nodes();
  	// Check/uncheck checkboxes for all rows in the table
  	$('input[type="checkbox"]', rows).prop('checked', this.checked);
		setTimeout(function(){
			var c = 0;
			$.each($("#drTable tbody .input-payment-selected"),function(k,v){
				if($(v).prop("checked")){ c++; }
			});
			$("#payment-selected-count").html(c);
		},333);
 	});
 	// Handle click on checkbox to set state of "Select all" control
 	$('#drTable tbody').on('change', 'input[type="checkbox"]', function(){
		// If checkbox is not checked
		if(!this.checked){
			var el = $('#drTable-select-all').get(0);
			// If "Select all" control is checked and has 'indeterminate' property
			if(el && el.checked && ('indeterminate' in el)){
				// Set visual state of "Select all" control
				// as 'indeterminate'
				el.indeterminate = true;
			}
		}
 	});

 	// Handle form submission event
	$('#form-drTable').on('submit', function(e){
		var form = this;
		// Iterate over all checkboxes in the table
		table.$('input[type="checkbox"]').each(function(){
			// If checkbox doesn't exist in DOM
			if(!$.contains(document, this)){
				// If checkbox is checked
				if(this.checked){
					// Create a hidden element
					$(form).append(
						$('<input>')
							.attr('type', 'hidden')
							.attr('name', this.name)
							.val(this.value)
					);
				}
			}
		});
	});

	$("#btn-payment-submit").on("click",function(e){
		e.preventDefault();
		$("#fupdate-status-settlement").trigger("submit");
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
		window.location='<?=base_url_admin('ecommerce/payment/detail/')?>'+ieid;
	},333);
});

$("#bdownload_xls").on("click",function(e){
	e.preventDefault();
	var seller_status = $("#if_seller_status").val();
	var buyer_confirmed = $("#if_buyer_confirmed").val();
	var settlement_status = $("#if_settlement_status").val();
	var cdate_start = $("#ifcdate_start").val();
	var cdate_end = $("#ifcdate_end").val();
	var url = '<?=base_url_admin()?>ecommerce/payment/download_xls/?';
	url += 'seller_status='+encodeURIComponent(seller_status);
	url += '&buyer_confirmed='+encodeURIComponent(buyer_confirmed);
	url += '&settlement_status='+encodeURIComponent(settlement_status);
	url += '&cdate_start='+encodeURIComponent(cdate_start);
	url += '&cdate_end='+encodeURIComponent(cdate_end);
	window.location = url;
});

<!-- by Donny Dennison - 19 January 2020 11:17 -->
<!-- add seller settlement download excel -->
$("#sellerSettlementDownload_xls").on("click",function(e){
	e.preventDefault();
	var seller_status = $("#if_seller_status").val();
	var buyer_confirmed = $("#if_buyer_confirmed").val();
	var settlement_status = $("#if_settlement_status").val();
	var cdate_start = $("#ifcdate_start").val();
	var cdate_end = $("#ifcdate_end").val();
	var url = '<?=base_url_admin()?>ecommerce/payment/seller_settlement_download_xls/?';
	url += 'seller_status='+encodeURIComponent(seller_status);
	url += '&buyer_confirmed='+encodeURIComponent(buyer_confirmed);
	url += '&settlement_status='+encodeURIComponent(settlement_status);
	url += '&cdate_start='+encodeURIComponent(cdate_start);
	url += '&cdate_end='+encodeURIComponent(cdate_end);
	window.location = url;
});

$("#bpg_xls").on("click",function(e){
	e.preventDefault();
	var seller_status = $("#if_seller_status").val();
	var buyer_confirmed = $("#if_buyer_confirmed").val();
	var settlement_status = $("#if_settlement_status").val();
	var cdate_start = $("#ifcdate_start").val();
	var cdate_end = $("#ifcdate_end").val();
	var url = '<?=base_url_admin()?>ecommerce/payment/pg_xls/?';
	url += 'seller_status='+encodeURIComponent(seller_status);
	url += '&buyer_confirmed='+encodeURIComponent(buyer_confirmed);
	url += '&settlement_status='+encodeURIComponent(settlement_status);
	url += '&cdate_start='+encodeURIComponent(cdate_start);
	url += '&cdate_end='+encodeURIComponent(cdate_end);
	window.location = url;
});

$("#fupdate-status-settlement").on("submit",function(e){
	e.preventDefault();
	var c = confirm("Are you sure?");
	if(c){
		NProgress.start();
		var fd = new FormData($(this)[0]);
		var url = '<?=base_url("api_admin/ecommerce/payment/mass_process/")?>';
		$.ajax({
			type: $(this).attr('method'),
			url: url,
			data: fd,
			processData: false,
			contentType: false,
			success: function(respon){
				NProgress.done();
				if(respon.status == 200){
					drTable.ajax.reload();
					growlType = 'info';
					growlPesan = '<h4>Success</h4><p>'+respon.message+'</p>';
					setTimeout(function(){
						$.bootstrapGrowl(growlPesan, {
							type: growlType,
							delay: 2500,
							allow_dismiss: true
						});
					}, 666);
				}else{
					growlType = 'danger';
					growlPesan = '<h4>Failed</h4><p>'+respon.message+'</p>';
					setTimeout(function(){
						$.bootstrapGrowl(growlPesan, {
							type: growlType,
							delay: 2500,
							allow_dismiss: true
						});
					}, 666);
				}
			},
			error: function(){
				NProgress.done();
				growlPesan = '<h4>Error</h4><p>Cannot process data right now, please try again later</p>';
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
	}
});
