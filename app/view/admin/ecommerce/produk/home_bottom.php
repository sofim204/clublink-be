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
			"columns"		: [
				null,
				{ "width": "128px"},
		    	{ "width": "20%"},
		    	{ "width": "25%"},
				null,
				null,
				null,
				null,
				null
		  ],
			"scrollX"			: true,
			"order"				: [[ 0, "desc" ]],
			"responsive"	  	: true,
			"bProcessing"		: true,
			"bServerSide"		: true,
			"sAjaxSource"		: "<?=base_url("api_admin/ecommerce/produk")?>",
			"fnServerParams": function ( aoData ) {
				aoData.push(
					{ "name": "from_date", "value": $("#ifcdate_start").val() },
					{ "name": "to_date", "value": $("#ifcdate_end").val() },
					{ "name": "b_kategori_id", "value": $("#ifb_kategori_id").val() },
					{ "name": "price_min", "value": $("#ifprice_min").val() },
					{ "name": "price_max", "value": $("#ifprice_max").val() },
					{ "name": "b_kondisi_id", "value": $("#ifb_kondisi_id").val() },
					{ "name": "courier_service", "value": $("#if_courier_service").val() },
					{ "name": "free_ship", "value": $("#iffree_ship").val() },
					{ "name": "produk_status", "value": $("#ifproduk_status").val() },

					//by Donny Dennison - 8 february 2021 16:44 
					//add product type column in product menu
					{ "name": "produk_type", "value": $("#ifproduk_type").val() }

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
	$('.dataTables_filter input').attr('placeholder', 'Search product, seller name').css({'width':'250px', 'display':'inline-block'}); <!-- show searchbox + add styling -->
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
		
		//by Donny Dennison - 8 february 2021 16:44
		//add product type column in product menu
		$("#ifproduk_type").val("");

		drTable.ajax.reload();
	})
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
				growlPesan = '<h4>Success</h4><p>Data added successfully!</p>';
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
			growlPesan = '<h4>Error</h4><p>Proses tambah data tidak bisa dilakukan, coba beberapa saat lagi</p>';
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
				growlPesan = '<h4>Success</h4><p>Data changed successfully!</p>';
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
			growlPesan = '<h4>Error</h4><p>Proses ubah data tidak bisa dilakukan, coba beberapa saat lagi</p>';
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

//hapus
$("#bhapus").on("click",function(e){
	e.preventDefault();
	if(ieid){
		var c = confirm('Are you sure?');
		if(c){
			var url = '<?=base_url('api_admin/ecommerce/produk/hapus/')?>'+ieid;
			$.get(url).done(function(response){
				if(response.status==200){
					growlType = 'success';
					growlPesan = '<h4>Success</h4><p>Data has deleted</p>';
					$("#modal_option").modal('hide');
				}else{
					growlType = 'danger';
					growlPesan = '<h4>Failed</h4><p>'+response.message+'</p>';
				}
				drTable.ajax.reload();
				$("#modal_edit").modal("hide");
				$.bootstrapGrowl(growlPesan,{
					type: growlType,
					delay: 2500,
					allow_dismiss: true
				});
			}).fail(function() {
				growlPesan = '<h4>Error</h4><p>Proses penghapusan tidak bisa dilakukan, coba beberapa saat lagi</p>';
				growlType = 'danger';
				$.bootstrapGrowl(growlPesan,{
					type: growlType,
					delay: 2500,
					allow_dismiss: true
				});
			});
		}
	}
});

//option
$("#aedit").on("click",function(e){
	e.preventDefault();
	$("#modal_option").modal("hide");
	setTimeout(function(){
		//quick edit
		//$("#modal_edit").modal("show");
		window.location = '<?=base_url_admin('ecommerce/produk/edit/')?>'+ieid;
	},333);
});

//detail
$("#adetail").on("click",function(e){
	e.preventDefault();
	$("#modal_option").modal("hide");
	setTimeout(function(){
		//$("#modal_edit").modal("show");
		//alert('masih dalam pengembangan');
		//window.location ='<?=base_url_admin('ecommerce/produk/detail/')?>'+ieid;
		window.open("<?=base_url_admin('ecommerce/produk/detail/')?>"+ieid, "_blank");
	},333);
});

$("#aset_showhp").on("click",function(e){
	e.preventDefault();
	NProgress.start();
	$.get("<?=base_url("api_admin/ecommerce/produk/homepage_show/")?>"+ieid).done(function(dt){
		NProgress.done();
		if(dt.status == 200){
			gritter("<h4>Success</h4><p>Product now visible in homepage</p>",'success');
		}else{
			gritter("<h4>Failed</h4><p>"+dt.message+"</p>",'danger');
		}
		$("#modal_option").modal("hide");
		drTable.ajax.reload(null,false);
	}).fail(function(){
		NProgress.done();
		gritter("<h4>Failed</h4><p>Cant change product status right now, please try again later</p>",'danger');
	});
});

$("#aset_hidehp").on("click",function(e){
	e.preventDefault();
	NProgress.start();
	$.get("<?=base_url("api_admin/ecommerce/produk/homepage_hide/")?>"+ieid).done(function(dt){
		NProgress.done();
		if(dt.status == 200){
			gritter("<h4>Success</h4><p>Product has removed from homepage</p>",'success');
		}else{
			gritter("<h4>Failed</h4><p>"+dt.message+"</p>",'danger');
		}
		$("#modal_option").modal("hide");
		drTable.ajax.reload(null,false);
	}).fail(function(){
		NProgress.done();
		gritter("<h4>Failed</h4><p>Cant change product status right now, please try again later</p>",'danger');
	});
});

$("#aset_publish").on("click",function(e){
	e.preventDefault();
	NProgress.start();
	$.get("<?=base_url("api_admin/ecommerce/produk/publish/")?>"+ieid).done(function(dt){
		NProgress.done();
		if(dt.status == 200){
			gritter("<h4>Success</h4><p>Product published</p>",'success');
		}else{
			gritter("<h4>Failed</h4><p>"+dt.message+"</p>",'danger');
		}
		$("#modal_option").modal("hide");
		drTable.ajax.reload(null,false);
	}).fail(function(){
		NProgress.done();
		gritter("<h4>Failed</h4><p>Cant change product status right now, please try again later</p>",'danger');
	});
});

$("#aset_draft").on("click",function(e){
	e.preventDefault();
	NProgress.start();
	$.get("<?=base_url("api_admin/ecommerce/produk/draft/")?>"+ieid).done(function(dt){
		NProgress.done();
		if(dt.status == 200){
			gritter("<h4>Success</h4><p>Product set as draft</p>",'success');
		}else{
			gritter("<h4>Failed</h4><p>"+dt.message+"</p>",'danger');
		}
		$("#modal_option").modal("hide");
		drTable.ajax.reload(null,false);
	}).fail(function(){
		NProgress.done();
		gritter("<h4>Failed</h4><p>Cant change product status right now, please try again later</p>",'danger');
	});
});

$("#aset_active").on("click",function(e){
	e.preventDefault();
	NProgress.start();
	$.get("<?=base_url("api_admin/ecommerce/produk/active/")?>"+ieid).done(function(dt){
		NProgress.done();
		if(dt.status == 200){
			gritter("<h4>Success</h4><p>Product activated</p>",'success');
		}else{
			gritter("<h4>Failed</h4><p>"+dt.message+"</p>",'danger');
		}
		$("#modal_option").modal("hide");
		drTable.ajax.reload(null,false);
	}).fail(function(){
		NProgress.done();
		gritter("<h4>Failed</h4><p>Cant change product status right now, please try again later</p>",'danger');
	});
});

$("#aset_inactive").on("click",function(e){
	e.preventDefault();
	NProgress.start();
	$.get("<?=base_url("api_admin/ecommerce/produk/inactive/")?>"+ieid).done(function(dt){
		NProgress.done();
		if(dt.status == 200){
			gritter("<h4>Success</h4><p>Product inactivated</p>",'success');
		}else{
			gritter("<h4>Failed</h4><p>"+dt.message+"</p>",'danger');
		}
		$("#modal_option").modal("hide");
		drTable.ajax.reload(null,false);
	}).fail(function(){
		NProgress.done();
		gritter("<h4>Failed</h4><p>Cant change product status right now, please try again later</p>",'danger');
	});
});

$(document).ready(function() {
	<!-- initialize datepicker -->
	$('#ifcdate_start, #ifcdate_end').datepicker();
    $('#ifcdate_start, #ifcdate_end').datepicker('setDate', 'today').val("");

	$("#ifcdate_start, #ifcdate_end").change(function(){
		$('.datepicker').hide(); <!-- hide datepicker after select a date -->
	});
});