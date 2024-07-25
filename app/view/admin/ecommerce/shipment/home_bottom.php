var growlPesan = '<h4>Error</h4><p>Cannot be proceed, please try again later!</p>';
var growlType = 'danger';
var drTable = {};
var ieid = '';
var d_order_id = 0;
var c_produk_id = 0;
var shipment_status = 'process';
<!-- by Muhammad Sofi 9 February 2022 10:00 | get current shipment status data in modal -->
var order_id_for_shipment_status = '';
var produk_id_for_shipment_status = '';

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
			"columns"		: [
				null,
				null,
		    { "width": "40%"},
		    { "width": "30%"},
				null,
				null,
				null,
				null,
				null,
				null,
				null
		  ],
			"scrollX"				: true,
			"order"					: [[ 0, "desc" ]],
			"responsive"	  : true,
			"bProcessing"		: true,
			"bServerSide"		: true,
			"sAjaxSource"		: "<?=base_url("api_admin/ecommerce/shipment/")?>",
			"fnServerParams": function ( aoData ) {
				aoData.push(
					{ "name": "courier_service", "value": $("#ifcourier_service").val() },
					{ "name": "seller_status", "value": $("#ifseller_status").val() },
					{ "name": "shipment_status", "value": $("#ifshipment_status").val() },
					{ "name": "delivery_date", "value": $("#ifdelivery_date").val() }
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
						//d_order_id = $($(this).find("td")[0]).html();
						//c_produk_id = $($(this).find("td")[1]).html();
						//shipment_status = $($(this).find("td")[7]).html();

						//$("#imcs_change_status").val(shipment_status);

						var currentRow = $(this).closest("tr");
						var d_order_id = $('#drTable').DataTable().row(currentRow).data()[0];
						var c_produk_id = $('#drTable').DataTable().row(currentRow).data()[1];
						<!-- by Muhammad Sofi 9 February 2022 10:00  | get current shipment status data in modal -->
						order_id_for_shipment_status = d_order_id;
						produk_id_for_shipment_status = c_produk_id;
						console.log();
						$("#form_change_status_alert").hide();
						$("#aprint_packing_list").attr("href",'<?=base_url_admin("ecommerce/shipment/print_packing_list/")?>'+ieid);
						$("#bdownload_waybill").attr("href",'<?=base_url_admin("ecommerce/waybill/download/")?>'+d_order_id+'/'+c_produk_id);
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
	$('.dataTables_filter input').attr('placeholder', 'Search Product Name');

	$("#afilter_do").on("click",function(e){
		e.preventDefault();
		drTable.ajax.reload();
	});
	$("#areset_do").on("click",function(e){
		e.preventDefault();
		$("#ifcourier_service").val('');
		$("#ifseller_status").val('');
		$("#ifshipment_status").val('');
		$("#ifdate_order_min").val('');
		$("#ifdate_order_max").val('');
		$("#ifdelivery_date").val('');
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
		window.location='<?=base_url_admin('ecommerce/shipment/detail/')?>'+ieid;
	},333);
});

//change Status
$("#imcs_change_status").on("change",function(e){
  e.preventDefault();
  var cs = $("#imcs_change_status").val();
  if(cs=='leaved' || cs=='rejected'){
    $("#div_reason").show("slow");
  }else{
    $("#imcs_reason").html("");
    $("#div_reason").hide();
  }
});
$("#bchange_status").on("click",function(e){
  e.preventDefault();
  $("#div_reason").hide();
	$("#modal_option").modal("hide");
	setTimeout(function(){
		<!-- by Muhammad Sofi 9 February 2022 10:00  | get current shipment status data in modal -->
		//var url = '<?=base_url("api_admin/ecommerce/shipment/getShipmentStatus/")?>'+ order_id_for_shipment_status, produk_id_for_shipment_status;
		var url = '<?=base_url() ?>api_admin/ecommerce/shipment/getShipmentStatus?d_order_id=' +order_id_for_shipment_status+'&produk_id='+produk_id_for_shipment_status;
		//alert(url);
		$.get(url).done(function(response){
			if(response.status=="200") {
				var dta = response.data;
				$("#imcs_change_status").val(dta.shipment_status);
			}
		});
		$("#modal_change_status").modal("show");
	},200); <!-- by Muhammad Sofi 9 February 2022 10:00  | reduce load time show modal -->
});
$("#form_change_status").on("submit",function(e){
  e.preventDefault();
  var cs = $("#imcs_change_status").val();
	var url = '<?=base_url("api_admin/ecommerce/shipment/change_status/")?>'+d_order_id+'/'+c_produk_id;
  var fd = new FormData($(this)[0]);
  $("#form_change_status_alert").hide("fast");
  NProgress.start();
  $.ajax({
		type: $(this).attr('method'),
		url: url,
		data: fd,
		processData: false,
		contentType: false,
		success: function(dt){
      if(dt.status == 200){
        //$("#form_change_status_alert").removeClass("alert-warning");
        //$("#form_change_status_alert").addClass("alert-success");
        //$("#form_change_status_alert").html("<h4>Success</h4><p>Shipment status changed successfully</p>");
				$.bootstrapGrowl("<h4>Success</h4><p>Shipment status changed successfully</p>", {
					type: "success",
					delay: 2500,
					allow_dismiss: true
				});
				$("#modal_change_status").modal("hide");
				//$("#modal_option").modal("hide");
        setTimeout(function(){
		      NProgress.done();
          //window.location.reload();
					drTable.ajax.reload(null,false);
        },1000);
      }else{
	      NProgress.done();
				$("#form_change_status_alert").show("slow");
        $("#form_change_status_alert").removeClass("alert-success");
        $("#form_change_status_alert").addClass("alert-warning");
        $("#form_change_status_alert").html(dt.message);
      }
    }, error: function(){
      $("#form_change_status_alert").show("slow");
      $("#form_change_status_alert").removeClass("alert-success");
      $("#form_change_status_alert").addClass("alert-warning");
      $("#form_change_status_alert").html("Cant processed data right now, please try again later");
      NProgress.done();
    }
  });
});

//change Tracking
$("#imct_change_tracking").on("change",function(e){
  e.preventDefault();
  var cs = $("#imct_tracking_id").val();
  if(cs=='leaved' || cs=='rejected'){
    $("#div_reason").show("slow");
  }else{
    $("#imcs_reason").html("");
    $("#div_reason").hide();
  }
});
$("#bchange_tracking").on("click",function(e){
  e.preventDefault();
	$("#modal_option").modal("hide");
	$.get('<?=base_url("api_admin/ecommerce/shipment/detail/")?>'+d_order_id+'/'+c_produk_id).done(function(dt){
		$("#imct_tracking_id").val(dt.data.shipment_tranid);
		setTimeout(function(){
			$("#modal_change_tracking").modal("show");
		},666);
	})
	
});
$("#form_change_tracking").on("submit",function(e){
  e.preventDefault();
  var cs = $("#imcs_change_tracking").val();
	var url = '<?=base_url("api_admin/ecommerce/shipment/change_tracking/")?>'+d_order_id+'/'+c_produk_id;
  var fd = new FormData($(this)[0]);
  $("#form_change_tracking_alert").hide("fast");
  NProgress.start();
  $.ajax({
		type: $(this).attr('method'),
		url: url,
		data: fd,
		processData: false,
		contentType: false,
		success: function(dt){
      if(dt.status == 200){
        //$("#form_change_tracking_alert").removeClass("alert-warning");
        //$("#form_change_tracking_alert").addClass("alert-success");
        //$("#form_change_tracking_alert").html("<h4>Success</h4><p>Shipment status changed successfully</p>");
				$.bootstrapGrowl("<h4>Success</h4><p>Shipment tracking number successfully  changed</p>", {
					type: "success",
					delay: 2500,
					allow_dismiss: true
				});
				$("#modal_change_tracking").modal("hide");
				//$("#modal_option").modal("hide");
        setTimeout(function(){
		      NProgress.done();
          //window.location.reload();
					drTable.ajax.reload(null,false);
        },1000);
      }else{
	      NProgress.done();
				$("#form_change_tracking_alert").show("slow");
        $("#form_change_tracking_alert").removeClass("alert-success");
        $("#form_change_tracking_alert").addClass("alert-warning");
        $("#form_change_tracking_alert").html(dt.message);
      }
    }, error: function(){
      $("#form_change_tracking_alert").show("slow");
      $("#form_change_tracking_alert").removeClass("alert-success");
      $("#form_change_tracking_alert").addClass("alert-warning");
      $("#form_change_tracking_alert").html("Cant processed data right now, please try again later");
      NProgress.done();
    }
  });
});
