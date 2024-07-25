var growlPesan = '<h4>Error</h4><p>Tidak dapat diproses, silakan coba beberapa saat lagi!</p>';
var growlType = 'danger';
var drTable = {};
var ieid = '';
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
			"sAjaxSource"		: "<?=base_url("api_admin/crm/produkreport/daftar_laporan/$produk->id")?>",
			"fnServerParams": function ( aoData ) {
				aoData.push(
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
						$("#modal_option").modal("show");
					});
					fnCallback(response);
				}).error(function (response, status, headers, config) {
					NProgress.done();
					gritter('<h4>Error</h4><p>Cant fetch product data right now, please try again later</p>','warning');
				});
			},
	});
	$('.dataTables_filter input').attr('placeholder', 'Search description');
	$("#bfilter").on("click",function(e){
		e.preventDefault();
		if($("#ifprice_min").val().length > 0 && $("#ifprice_max").val().length > 0){
			drTable.order([4, 'asc']).ajax.reload();
		}else if($("#ifprice_min").val().length > 0 && $("#ifprice_max").val().length == 0){
			drTable.order([4, 'asc']).ajax.reload();
		}else if($("#ifprice_min").val().length == 0 && $("#ifprice_max").val().length > 0){
			drTable.order([4, 'desc']).ajax.reload();
		}else{
			drTable.ajax.reload();
		}
	});
	$("#fl_reset").on("click",function(e){
		e.preventDefault();
		$("#ifb_kategori_id").val("");
		$("#ifprice_min").val("");
		$("#ifprice_max").val("");
		$("#ifb_kondisi_id").val("");
		$("#if_courier_service").val("");
		$("#if_free_ship").val("");
		$("#ifb_kondisi_id").val("");
		$("#ifproduk_status").val("");
		drTable.ajax.reload();
	})
}

//takedown Action
$("#btakedown").on("click",function(e){
	e.preventDefault();
	var url = '<?=base_url("api_admin/crm/produkreport/takedown/$produk->id/")?>'
	$.get(url).done(function(response){
		if(response.status==200){
			growlType = 'success';
			growlPesan = '<h4>Success</h4><p>Product has been takedown</p>';
		}else{
			growlType = 'danger';
			growlPesan = '<h4>Failed</h4><p>'+response.message+'</p>';
		}
		$.bootstrapGrowl(growlPesan,{
			type: growlType,
			delay: 2500,
			allow_dismiss: true
		});
		setTimeout(function(){
			window.location.reload();
		},1000)
	}).fail(function(){
		growlPesan = '<h4>Error</h4><p>Failed, please try later</p>';
		growlType = 'danger';
		$.bootstrapGrowl(growlPesan,{
			type: growlType,
			delay: 2500,
			allow_dismiss: true
		});
	})
});

//ignore action
$("#bignore").on("click", function(e){
	e.preventDefault();
	var url = '<?=base_url("api_admin/crm/produkreport/ignore/$produk->id/")?>'
	$.get(url).done(function(response){
		if(response.status==200){
			growlType = 'success';
			growlPesan = '<h4>Success</h4><p>Product has been ignore</p>';
		}else{
			growlType = 'danger';
			growlPesan = '<h4>Failed</h4><p>'+response.message+'</p>';
		}
		$.bootstrapGrowl(growlPesan,{
			type: growlType,
			delay: 2500,
			allow_dismiss: true
		});
		setTimeout(function(){
			window.location.reload();
		},1000)
	}).fail(function(){
		growlPesan = '<h4>Error</h4><p>Failed, please try later</p>';
		growlType = 'danger';
		$.bootstrapGrowl(growlPesan,{
			type: growlType,
			delay: 2500,
			allow_dismiss: true
		});
	})
});
