
var growlPesan = '<h4>Error</h4><p>Tidak dapat diproses, silakan coba beberapa saat lagi!</p>';
var growlType = 'danger';
var api_url = '<?php echo base_url('api_admin/alamatongkir/'); ?>';
var drTable = {};
var ieid = '';
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
			"sAjaxSource"		: "<?php  echo base_url("api_admin/alamatongkir/kecamatan/"); ?>",
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
						var url = '<?php echo base_url(); ?>api_admin/alamatongkir/kecamatan/detail/'+id;
						$.get(url).done(function(response){
							if(response.status==100 || response.status=='100'){
								var dta = response.result;
								ieid = dta.id;
								$("#ieid").val(dta.id);
								$("#iekabkota_id").val(dta.kabkota_id);
                $("#ienama_kecamatan").val(dta.nama_kecamatan);
                $("#ielatitude").val(dta.latitude);
								$("#ielongitude").val(dta.longitude);

								//$("#modal_edit").modal("show");
								$("#modal_option").modal("show");
								$("#iekabkota_id").html('<option value="'+dta.kabkota_id+'">'+dta.kabkota_id+'</option>');

								getProvinsi(dta.provinsi);

							}else{
								growlType = 'danger';
								growlPesan = '<h4>Error</h4><p>Tidak dapat mengambil detail data</p>';
								$.bootstrapGrowl(growlPesan, {
									type: growlType,
									delay: 2500,
									allow_dismiss: true
								});
							}
						});
					});
					fnCallback(response);
				}).error(function (response, status, headers, config) {
					$("#modal-preloader").modal("hide");
					//console.log(response, response.responseText);
					//$('body').addClass('loaded');
					alert("Error");
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

	//listener
	$("#iprovinsi").off("change");
	$("#iprovinsi").on("change",function(e){
		e.preventDefault();
		var provinsi_id = $(this).val();
		getKabkota(provinsi_id);
	});
	$("#ikabkota_id").off("change");
	$("#ikabkota_id").on("change",function(e){
		e.preventDefault();
		var kabkota_id = $(this).val();
	});

	//trigger once
	getProvinsi();

});
$("#modal_tambah").on("hidden.bs.modal",function(e){
	$("#modal_tambah").find("form").trigger("reset");
});
function getProvinsi(nama_provinsi=""){
	var url = api_url+'provinsi/get/';
	$("#iprovinsi").empty();
	$("#ieprovinsi").empty();
	$("#iprovinsi").html('<option value="">Loading...</option>');
	$.get(url).done(function(hasil){

		if(hasil.status == 100 || hasil.status == "100"){
			var isi = '<option value="">--Pilih--</option>';
			var isi2 = '';
			var selected = '';
			$.each(hasil.result.provinsi,function(key,val){
				if(val.nama_provinsi == nama_provinsi) selected = val.id;
				isi += '<option value="'+val.id+'" data-value="'+val.nama_provinsi+'">'+val.nama_provinsi+'</option>';
				isi2 += '<option value="'+val.id+'" data-value="'+val.nama_provinsi+'">'+val.nama_provinsi+'</option>';
			});
			$("#iprovinsi").html(isi);
			$("#ieprovinsi").html(isi2);
			if(selected != '') $("#ieprovinsi").val(selected);
			$("#inama_kabkota").trigger("change");
			$("#ienama_kabkota").trigger("change");
		}else{
			alert('Error');
		}
	});
}

function getKabkota(provinsi_id="",kabkota_nama=""){
	var url = api_url+'kabkota/get/'+provinsi_id;
	$("#ikabkota_id").empty();
	$("#iekabkota_id").empty();
	$("#ikabkota_id").html('<option value="">Loading...</option>');
	$.get(url).done(function(hasil){
		if(hasil.status == 100 || hasil.status == "100"){
			var isi = '<option value="">--Pilih--</option>';
			var isi2 = '<option value="'+kabkota_nama+'">'+kabkota_nama+'</option>';
			var selected = '';
			var nama_kabkota = $("#nama_kabkota option:selected").text();
			$.each(hasil.result.kabkota,function(key,val){
				if(val.kabkota == nama_kabkota) selected = val.id;
				isi += '<option value="'+val.id+'" data-value="'+val.nama_kabkota+'">'+val.nama_kabkota+'</option>';

				isi2 += '<option value="'+val.id+'" data-value="'+val.nama_kabkota+'">'+val.nama_kabkota+'</option>';
			});
			$("#ikabkota_id").html(isi);
			$("#iekabkota_id").html(isi2);
			if(selected != '') $("#iekabkota_id").val(selected);
		}
	});
}


$("#ftambah").on("submit",function(e){
	e.preventDefault();

	var fd = new FormData($(this)[0]);
	//if(fd.has('kabkota_id')){
	//	fd.delete('kabkota_id');
	//	fd.append('kabkota_id',$("#ikabkota_id option:selected").text());
	//}

	var url = '<?php echo base_url("api_admin/alamatongkir/kecamatan/tambah/"); ?>';
	$.ajax({
		type: 'post',
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
	//listener
	$("#ieprovinsi").off("change");
	$("#ieprovinsi").on("change",function(e){
		e.preventDefault();
		var provinsi_id = $(this).val();
		getKabkota(provinsi_id);
	});
	$("#iekabkota_id").off("change");
	$("#iekabkota_id").on("change",function(e){
		e.preventDefault();
		var kabkota_id = $(this).val();
	});

});
$("#modal_edit").on("hidden.bs.modal",function(e){
	$("#modal_edit").find("form").trigger("reset");
});

$("#fedit").on("submit",function(e){
	e.preventDefault();
	var fd = new FormData($(this)[0]);
	var url = '<?php echo base_url("api_admin/alamatongkir/kecamatan/edit/"); ?>'+ieid;
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
$("#ahapus").on("click",function(e){
	e.preventDefault();
	var id = ieid;
	if(id){
		var c = confirm('apakah anda yakin?');
		if(c){
			var url = '<?php echo base_url('api_admin/alamatongkir/kecamatan/hapus/'); ?>'+id;
			$.get(url).done(function(response){
				if(response.status=="100" || response.status==100){
					$("#modal_option").modal("hide");
					growlType = 'success';
					growlPesan = '<h4>Berhasil</h4><p>Data berhasil dihapus</p>';
				}else{
					growlType = 'danger';
					growlPesan = '<h4>Gagal</h4><p>'+response.message+'</p>';
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

$("#bhapus").on("click",function(e){
	e.preventDefault();
	$("#ahapus").trigger("click");
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
		//$("#modal_edit").modal("show");
		alert('masih dalam pengembangan');
	},333);
});

//provinsi
