var growlPesan = '<h4>Error</h4><p>Cannot be proceed, please try again later!</p>';
var growlType = 'danger';
var drTable = {};
var ieid = '';
var action = '';

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
			"oSearch"				: {"sSearch": "<?=$keyword?>"},
			"scrollX"				: true,
			"order"					: [[ 0, "desc" ]],
			"responsive"	  : true,
			"bProcessing"		: true,
			"bServerSide"		: true,
			"sAjaxSource"		: "<?=base_url("api_admin/ecommerce/rejectbuyer/")?>",
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
						$("#aprint_packing_list").attr("href",'<?=base_url_admin("ecommerce/rejectbuyer/print_packing_list/")?>'+ieid);
						$("#aprint_faktur").attr("href",'<?=base_url_admin("ecommerce/rejectbuyer/print_faktur/")?>'+ieid);
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
	<!-- by Muhammad Sofi 21 December 2021 15:06 | increase width of search box -->
	//$('.dataTables_filter input').attr('placeholder', 'Search invoice, product name');
	$('.dataTables_filter input[type="search"]').attr('placeholder', 'Search invoice, product name').css({'width':'250px', 'display':'inline-block'}); <!-- show search box + add styling -->

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

	/* $('#drTable').on('change', 'select', function (e) {
	    var val = $(e.target).val();
	    var text = $(e.target).find("option:selected").text(); //only time the find is required
	    var name = $(e.target).attr('name');
	    var id = eval($("#ieid").val());
	    alert(ieid);
	}); */

}


//option
$("#aedit").on("click",function(e){
	e.preventDefault();
	$("#modal_option").modal("hide");
	setTimeout(function(){
		$("#modal_edit").modal("show");
	},333);
});

<!-- by Muhammad Sofi 9 February 2022 10:00 | fix button export to excel -->
$("#bdownload_xls").on("click", function(e) {
	e.preventDefault();
	var settlement_status = $("#if_settlement_status").val();
	var cdate_start = $("#ifcdate_start").val();
	var cdate_end = $("#ifcdate_end").val();
	<!-- do checking -->
	if(cdate_start == "" ) {
		alert("Please, select From Order Date");
	} else if(cdate_end == "" ) {
		alert("Please, select To Order Date");
	} else if ($("#if_settlement_status").val() === "") {
		alert("Please, select Resolution");
	} else {
		var url ='<?=base_url_admin()?>ecommerce/rejectbuyer/download_xls/?';
		url += 'settlement_status='+encodeURIComponent(settlement_status);
		url += '&cdate_start='+encodeURIComponent(cdate_start);
		url += '&cdate_end='+encodeURIComponent(cdate_end);
		window.location = url;
	}
});

$("#apayseller").on("click",function(e){
	e.preventDefault();
	var c = confirm('This action will cancel the buyer rejection, are you sure?');
	if(c){
		action = 'paid_to_seller';
		$.get('<?=base_url('api_admin/ecommerce/rejectbuyer/set_status_settlement/')?>'+ieid+"/"+action+"/").done(function(dt){
			if(dt.status == 200){
				$("#modal_option").modal("hide");
				NProgress.done();
				growlType = 'success';
				growlPesan = '<h4>Success</h4><p>Data processed successfuly</p>';
				$.bootstrapGrowl(growlPesan, {
					type: growlType,
					delay: 2500,
					allow_dismiss: true
				});
				drTable.ajax.reload(null,false);
			}else if(dt.status == 900){
				$("#modal_option").modal("hide");
				NProgress.done();
				growlType = 'warning';
				growlPesan = '<h4>Failed</h4><p>'+dt.message+'</p>';
				$.bootstrapGrowl(growlPesan, {
					type: growlType,
					delay: 2500,
					allow_dismiss: true
				});
			}else{
				$("#modal_option").modal("hide");
				NProgress.done();
				growlType = 'danger';
				growlPesan = '<h4>Failed</h4><p>'+dt.message+'</p>';
				$.bootstrapGrowl(growlPesan, {
					type: growlType,
					delay: 2500,
					allow_dismiss: true
				});
			}

		}).fail(function(){
			NProgress.done();
			growlType = 'danger';
			growlPesan = '<h4>Error</h4><p>Cannot process data</p>';
			$.bootstrapGrowl(growlPesan, {
				type: growlType,
				delay: 2500,
				allow_dismiss: true
			});
		});
	}else{
		$("#modal_option").modal("hide");
	}
	
});

$("#arefund").on("click",function(e){
	e.preventDefault();
	action = 'paid_to_buyer';
	$.get('<?=base_url('api_admin/ecommerce/rejectbuyer/set_status_settlement/')?>'+ieid+"/"+action+"/").done(function(dt){
		if(dt.status == 200){
			$("#modal_option").modal("hide");
			NProgress.done();
			growlType = 'success';
			growlPesan = '<h4>Success</h4><p>Data processed successfuly</p>';
			$.bootstrapGrowl(growlPesan, {
				type: growlType,
				delay: 2500,
				allow_dismiss: true
			});
			drTable.ajax.reload(null,false);
		}else if(dt.status == 900){
			$("#modal_option").modal("hide");
			NProgress.done();
			growlType = 'warning';
			growlPesan = '<h4>Failed</h4><p>'+dt.message+'</p>';
			$.bootstrapGrowl(growlPesan, {
				type: growlType,
				delay: 2500,
				allow_dismiss: true
			});
		}else{
			$("#modal_option").modal("hide");
			NProgress.done();
			growlType = 'danger';
			growlPesan = '<h4>Failed</h4><p>'+dt.message+'</p>';
			$.bootstrapGrowl(growlPesan, {
				type: growlType,
				delay: 2500,
				allow_dismiss: true
			});
		}

	}).fail(function(){
		NProgress.done();
		growlType = 'danger';
		growlPesan = '<h4>Error</h4><p>Cannot process data</p>';
		$.bootstrapGrowl(growlPesan, {
			type: growlType,
			delay: 2500,
			allow_dismiss: true
		});
	});
});

$("#aabort").on("click",function(e){
	e.preventDefault();
	NProgress.start();
	action = 'wait';
	$.get('<?=base_url('api_admin/ecommerce/rejectbuyer/set_status_settlement/')?>'+ieid+"/"+action+"/").done(function(dt){
		if(dt.status == 200){
			$("#modal_option").modal("hide");
			NProgress.done();
			growlType = 'success';
			growlPesan = '<h4>Success</h4><p>Data processed successfuly</p>';
			$.bootstrapGrowl(growlPesan, {
				type: growlType,
				delay: 2500,
				allow_dismiss: true
			});
			drTable.ajax.reload(null,false);
		}else if(dt.status == 900){
			$("#modal_option").modal("hide");
			NProgress.done();
			growlType = 'warning';
			growlPesan = '<h4>Failed</h4><p>'+dt.message+'</p>';
			$.bootstrapGrowl(growlPesan, {
				type: growlType,
				delay: 2500,
				allow_dismiss: true
			});
		}else{
			$("#modal_option").modal("hide");
			NProgress.done();
			growlType = 'danger';
			growlPesan = '<h4>Failed</h4><p>'+dt.message+'</p>';
			$.bootstrapGrowl(growlPesan, {
				type: growlType,
				delay: 2500,
				allow_dismiss: true
			});
		}

	}).fail(function(){
		NProgress.done();
		growlType = 'danger';
		growlPesan = '<h4>Error</h4><p>Cannot process data</p>';
		$.bootstrapGrowl(growlPesan, {
			type: growlType,
			delay: 2500,
			allow_dismiss: true
		});
	});
});


$('#drTable > tbody').on('change', 'select', function (e) {
	e.preventDefault();
	var id = $(this).attr("data-id");
	ieid = id;
	var val = $(e.target).val();
  var text = $(e.target).find("option:selected").text(); //only time the find is required
  var name = $(e.target).attr('name');
  var payment_status = $("#payment_status_update").val();
	var fd = new FormData();
	var url = '<?=base_url("api_admin/ecommerce/rejectbuyer/updateStatusCancellation/"); ?>'+ieid;
	$.ajax({
		type: $(this).attr('method'),
		url: url,
		data: fd,
		processData: false,
		contentType: false,
		success: function(respon){
			if(respon.status=="200" || respon.status == 200){
				growlType = 'success';
				growlPesan = '<h4>Success</h4><p>Operation completed!</p>';
				drTable.ajax.reload();
			}else{
				growlType = 'danger';
				growlPesan = '<h4>Failed</h4><p>'+respon.message+'</p>';
			}
			//$("#modal_edit").modal("hide");
			setTimeout(function(){
				$.bootstrapGrowl(growlPesan, {
					type: growlType,
					delay: 2500,
					allow_dismiss: true
				});
			}, 666);
		},
		error:function(){
			growlPesan = '<h4>Error</h4><p>Cannot process data right now, please try again alter</p>';
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
