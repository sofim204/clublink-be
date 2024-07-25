var ieid = '<?=$pelanggan->id?>';
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
		NProgress.start();
	}).DataTable({
			"order"					: [[ 0, "desc" ]],
			"responsive"	  : true,
			"bProcessing"		: true,
			"bServerSide"		: true,
			"sAjaxSource"		: "<?=base_url("api_admin/summary/seller/product/1/")?>",
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
					});
					fnCallback(response);
				}).error(function (response, status, headers, config) {
					gritter('<h4>Error</h4><p>Cannot get seller product</p>','warning');
					NProgress.done();
				});
			},
	});
	$('.dataTables_filter input').attr('placeholder', 'Cari');
}

$("#bactivated").on('click',function(e){
	e.preventDefault();
	NProgress.start();
	$.get("<?=base_url("api_admin/ecommerce/pelanggan/activated/")?>"+ieid).done(function(dt){
		NProgress.done();
		$("#modal_option").modal("hide");
		if(dt.status == "200"){
			gritter("<h4>Success</h4><p>User activated.</p>",'success');
      window.location.reload();
		}else{
			gritter("<h4>Failed</h4><p>"+dt.message+"</p>",'danger');
		}
	}).fail(function(e){
		NProgress.done();
		gritter("<h4>Error</h4><p>Cant change user activation right now, please try again.</p>",'warning');
	})
});

$("#bdeactivated").on('click',function(e){
	e.preventDefault();
	NProgress.start();
	$.get("<?=base_url("api_admin/ecommerce/pelanggan/deactivated/")?>"+ieid).done(function(dt){
		NProgress.done();
		$("#modal_option").modal("hide");
		if(dt.status == "200"){
			gritter("<h4>Success</h4><p>User deactivated.</p>",'success');
      window.location.reload();
		}else{
			gritter("<h4>Failed</h4><p>"+dt.message+"</p>",'danger');
		}
	}).fail(function(e){
		NProgress.done();
		gritter("<h4>Error</h4><p>Cant change user activation right now, please try again.</p>",'warning');
	})
});

$("#bemail_konfirmasi").on('click',function(e){
	e.preventDefault();
	var c = confirm('Are you sure?');
	if(c){
		NProgress.start();
		$.get("<?=base_url("api_admin/ecommerce/pelanggan/email_konfirmasi/")?>"+ieid).done(function(dt){
			NProgress.done();
			$("#modal_option").modal("hide");
			if(dt.status == "200"){
				gritter("<h4>Success</h4><p>Registration confirmation link email has been sent</p>",'success');
        //window.location.reload();
			}else{
				gritter("<h4>Failed</h4><p>"+dt.message+"</p>",'danger');
			}
		}).fail(function(e){
			NProgress.done();
			gritter("<h4>Error</h4><p>Cant send email right now, please try again.</p>",'warning');
		});
	}
});


$("#bemail_lupa").on('click',function(e){
	e.preventDefault();
	var c = confirm('Are you sure?');
	if(c){
		NProgress.start();
		$.get("<?=base_url("api_admin/ecommerce/pelanggan/email_lupa/")?>"+ieid).done(function(dt){
			NProgress.done();
			$("#modal_option").modal("hide");
			if(dt.status == "200"){
				gritter("<h4>Success</h4><p>Reset password link has been sent</p>",'success');
        //window.location.reload();
			}else{
				gritter("<h4>Failed</h4><p>"+dt.message+"</p>",'danger');
			}
		}).fail(function(e){
			NProgress.done();
			gritter("<h4>Error</h4><p>Cant send email right now, please try again.</p>",'warning');
		});
	}
});

function getSummary(){
  var url = '<?=base_url("api_admin/summary/seller/index/".$pelanggan->id)?>';
  NProgress.start();
  $.get(url).done(function(dt){
    NProgress.done();
    if(dt.status == 200){
      $("#seller-order-count").html(""+dt.data.order_count);
      $("#seller-sales-total").html("$"+dt.data.sales_total);
      $("#seller-product-count").html(dt.data.product_count);
      $("#seller-freeproduct-count").html(dt.data.freeproduct_count);
      $("#seller-rejected-count").html(""+dt.data.rejected_count);
      $("#seller-confirmed-count").html(""+dt.data.confirmed_count);
    }
  }).fail(function(){
    NProgress.done();
  })
}
getSummary();
