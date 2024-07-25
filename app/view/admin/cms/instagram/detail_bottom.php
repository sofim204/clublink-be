var growlPesan = '<h4>Error</h4><p>Tidak dapat diproses, silakan coba beberapa saat lagi!</p>';
var growlType = 'danger';
var drTable = {};
var ieid = '';
function gritter(gp,gt="info"){
	$.bootstrapGrowl(gp, {
		type: gt,
		delay: 2500,
		allow_dismiss: true
	});
}

window.toPlainFloat = function(mny){
	return mny.replace( /^\D+/g, '').split('.').join("");
}

App.datatables();

if(jQuery('#drTable').length>0){
	drTable = jQuery('#drTable')
	.on('preXhr.dt', function ( e, settings, data ){
		$("#modal-preloader").modal("hide");
		//$("#modal-preloader").modal("show");
	}).DataTable({
			"order"					: [[ 1, "asc" ]],
			"responsive"	  : true,
			"bProcessing"		: true,
			"bServerSide"		: true,
			"sAjaxSource"		: "<?php  echo base_url("api_web/finance/target/detail_index/"); ?>",
			"fnServerParams": function ( aoData ) {
				aoData.push(
					{ "name": "e_target_id", "value": '<?=$detail->id?>' }
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
						var url = '<?php echo base_url(); ?>api_web/finance/target/detail_detail/'+id;
						$.get(url).done(function(response){
							if(response.status==100 || response.status=='100'){
								var dta = response.result;
								ieid = dta.id;
								$("#ieid").val(dta.id);
								$("#iee_target_id").val('<?=$detail->id?>');
								$("#ieprioritas").val(dta.prioritas);
								$("#ienama").val(dta.nama);
								$("#ieharga").val(dta.harga);
								$("#iehharga").val(dta.harga);
								$("#ieqty").val(dta.qty);
								$("#iesubtotal").html(dta.subtotal);
								$("#ieis_closed").val(dta.is_closed);

								//$("#modal_edit").modal("show");
								$("#modal_option").modal("show");
								priceFormat();
								$("#ieqty").trigger("change");

							}else{
								gritter('<h4>Error</h4><p>Tidak dapat mengambil detail data</p>','danger');
							}
						});
					});
					fnCallback(response);
				}).error(function (response, status, headers, config) {
					gritter('<h4>Error</h4><p>Tidak dapat mengambil data tabel</p>','warning');
				});
			},
	});
	$('.dataTables_filter input').attr('placeholder', 'Cari');
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
	var url = '<?php echo base_url("api_web/finance/target/detail_tambah/"); ?>';
	$.ajax({
		type: $(this).attr('method'),
		url: url,
		data: fd,
		processData: false,
		contentType: false,
		success: function(respon){
			if(respon.status=="100" || respon.status == 100){
				growlPesan = '<h4>Berhasil</h4><p>Proses tambah data telah berhasil!</p>';
				drTable.ajax.reload();
				growlType = 'success';
				$("#modal_tambah").modal("hide");
				$("#dtotal").html(respon.result.total);
				$("#dtotal_belum").html(respon.result.total_belum);
				$("#dtotal_sudah").html(respon.result.total_sudah);
				priceFormat();

				//pie
				var random = (respon.result.total_sudah / respon.result.total)*100;
				random = Math.ceil(random);
				$("#target_pie").data('easyPieChart').update(random);
				$("#target_pie").find('span').text(random + '%');
			}else{
				growlPesan = '<h4>Gagal</h4><p>'+respon.message+'</p>';
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
	var url = '<?php echo base_url("api_web/finance/target/detail_edit/"); ?>'+ieid;
	$.ajax({
		type: $(this).attr('method'),
		url: url,
		data: fd,
		processData: false,
		contentType: false,
		success: function(respon){
			if(respon.status=="100" || respon.status == 100){
				growlType = 'success';
				growlPesan = '<h4>Berhasil</h4><p>Proses ubah data telah berhasil!</p>';
				drTable.ajax.reload();
				$("#dtotal").html(respon.result.total);
				$("#dtotal_belum").html(respon.result.total_belum);
				$("#dtotal_sudah").html(respon.result.total_sudah);
				priceFormat();

				//pie
				var random = (respon.result.total_sudah / respon.result.total)*100;
				random = Math.ceil(random);
				$("#target_pie").data('easyPieChart').update(random);
				$("#target_pie").find('span').text(random + '%');
			}else{
				growlType = 'danger';
				growlPesan = '<h4>Gagal</h4><p>'+respon.message+'</p>';
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
		var c = confirm('apakah anda yakin?');
		if(c){
			NProgress.start();
			var url = '<?php echo base_url('api_web/finance/target/detail_hapus/'); ?>'+ieid;
			$.get(url).done(function(respon){
				NProgress.done();
				if(respon.status=="100" || respon.status==100){
					gritter('<h4>Berhasil</h4><p>Data berhasil dihapus</p>','success');
					$("#dtotal").html(respon.result.total);
					$("#dtotal_belum").html(respon.result.total_belum);
					$("#dtotal_sudah").html(respon.result.total_sudah);
					priceFormat();

					//pie
					var random = (respon.result.total_sudah / respon.result.total)*100;
					random = Math.ceil(random);
					$("#target_pie").data('easyPieChart').update(random);
					$("#target_pie").find('span').text(random + '%');
				}else{
					gritter('<h4>Gagal</h4><p>'+respon.message+'</p>','danger');
				}
				drTable.ajax.reload();
				$("#modal_option").modal("hide");
			}).fail(function() {
				NProgress.done();
				gritter('<h4>Error</h4><p>Proses penghapusan tidak bisa dilakukan, coba beberapa saat lagi</p>','warning');
			});
		}
	}
});

//option
$("#aedit").on("click",function(e){
	e.preventDefault();
	$("#modal_option").modal("hide");
	setTimeout(function(){
		$("#modal_edit").modal("show");
	},333);
});

//uang
function priceFormat(){
	$(".uang-rupiah").unpriceFormat();
	$(".uang-rupiah").priceFormat({
		prefix: 'Rp',
		centsSeparator: ',',
		thousandsSeparator: '.',
		centsLimit: 0
	});
}
priceFormat();

$("#iharga").on("blur",function(e){
	e.preventDefault();
	$("#ihharga").val(toPlainFloat($(this).val()));
});
$("#ieharga").on("blur",function(e){
	e.preventDefault();
	$("#iehharga").val(toPlainFloat($(this).val()));
});
function isubtotal(){
	var harga = Number($("#ihharga").val());
	var qty = Number($("#iqty").val());
	var sbtl = harga * qty;
	$("#isubtotal").val(sbtl);
	priceFormat();
}
$("#iharga").on("blur",function(e){
	isubtotal();
});
$("#iqty").on("change",function(e){
	isubtotal();
});
function iesubtotal(){
	var harga = Number($("#iehharga").val());
	var qty = Number($("#ieqty").val());
	var sbtl = harga * qty;
	$("#iesubtotal").val(sbtl);
	priceFormat();
}
$("#ieharga").on("blur",function(e){
	iesubtotal();
});
$("#ieqty").on("change",function(e){
	iesubtotal();
});
