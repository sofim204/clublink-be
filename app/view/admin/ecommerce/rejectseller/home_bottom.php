var growlPesan = '<h4>Error</h4><p>Cannot be proceed, please try again later!</p>';
var growlType = 'danger';
var drTable = {};
var ieid = '';
var action = '';

function growlShow(growlPesan,growlType="info"){
	$.bootstrapGrowl(growlPesan, {
		type: growlType,
		delay: 2500,
		allow_dismiss: true
	});
}

App.datatables();

if(jQuery('#drTable').length>0){
	drTable = jQuery('#drTable')
	.on('preXhr.dt', function ( e, settings, data ){
		NProgress.start();
	}).DataTable({
			"oSearch"				: {"sSearch": "<?=$keyword?>"},
			"scrollX"				: true,
			"order"					: [[ 0, "desc" ]],
			"responsive"	  : true,
			"bProcessing"		: true,
			"bServerSide"		: true,
			"sAjaxSource"		: "<?=base_url("api_admin/ecommerce/rejectseller/")?>",
			"fnServerParams": function ( aoData ) {
				aoData.push(
					{ "name": "cdate_start", "value": $("#ifcdate_start").val() },
					{ "name": "cdate_end", "value": $("#ifcdate_end").val() },
					{ "name": "settlement_status", "value": $("#if_settlement_status").val() }
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
					NProgress.done();
					$('#drTable > tbody').off('click', 'button');
					$('#drTable > tbody').on('click', 'button', function (e) {
						e.preventDefault();
						//var id = $(this).find("td").html();
						var id = $(this).attr("data-id");
						ieid = id;
						$("#adetail").attr("href",'<?=base_url_admin('ecommerce/transaction/seller_detail/')?>'+ieid);
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
	<!-- by Muhammad Sofi 21 December 2021 15:26 | increase width of search box -->
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
		$("#if_settlement_status").val("");
		drTable.search( '' ).columns().search( '' ).draw();
		drTable.ajax.reload(null,true);
	});
}

<!-- by Muhammad Sofi 9 February 2022 10:00 | fix button export to excel -->
$("#bdownload_xls").on("click", function(e) {
	e.preventDefault();
	var settlement_status = $("#if_settlement_status").val();
	var cdate_start = $("#ifcdate_start").val();
	var cdate_end = $("#ifcdate_end").val();
	<!-- do checking -->
	if(cdate_start == "" && cdate_end == "") {
		alert("Please, select Date");
	} else if ($("#if_settlement_status").val() === "") {
		alert("Please, select Resolution");
	} else {
		var url ='<?=base_url_admin()?>ecommerce/rejectseller/download_xls/?';
		url += 'settlement_status='+encodeURIComponent(settlement_status);
		url += '&cdate_start='+encodeURIComponent(cdate_start);
		url += '&cdate_end='+encodeURIComponent(cdate_end);
		window.location = url;
	}
});

// options listener

/* $("#arefund").on("click",function(e){
	e.preventDefault();
	action = 'paid_to_buyer';
	var fd = new FormData();
	fd.append("oid",ieid);
	$.ajax({
		url: '<?=base_url('api_admin/ecommerce/payment/process/'); ?>'+(ieid),
		type: "POST",
		data: fd,
		contentType: false,
		cache: false,
		processData:false,
		success: function(data){
			NProgress.done();
			if(data.status == 200){
				growlShow('<h4>Success</h4><p>This order has been marked for refund</p>','success');
				drTable.ajax.reload(null,false);
			}else{
				growlShow('<h4>Failed</h4><p>'+data.message+'</p>','danger');
			}
			$("#modal_option").modal("hide");
		},
		error: function(data){
			NProgress.done();
			growlShow('<h4>Error</h4><p>Cant edit product right now, please try again later</p>','warning');
		}
	});
}); */