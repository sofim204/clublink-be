
var growlPesan = '<h4>Error</h4><p>Tidak dapat diproses, silakan coba beberapa saat lagi!</p>';
var growlType = 'danger';
var api_url = '<?php echo base_url('api_admin/erpmaster/'); ?>';
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
			"sAjaxSource"		: "<?php  echo base_url("api_admin/erpmaster/jnt/"); ?>",
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
						var url = '<?php echo base_url(); ?>api_admin/erpmaster/jnt/detail/'+id;
						$.get(url).done(function(response){
							if(response.status==100 || response.status=='100'){
								var dta = response.result;
								ieid = dta.id;
								$("#ieid").val(dta.id);
								$("#ieorigin").val(dta.origin);
								$("#iekabkota").val(dta.kabkota);
                $("#iekecamatan").val(dta.kecamatan);
                $("#iereg_tarif").val(dta.reg_tarif);

								//$("#modal_edit").modal("show");
								$("#modal_option").modal("show");
								$("#iekabkota").html('<option value="'+dta.kabkota+'">'+dta.kabkota+'</option>');
								$("#iekecamatan").html('<option value="'+dta.kecamatan+'">'+dta.kecamatan+'</option>');

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
	$("#ikabkota").off("change");
	$("#ikabkota").on("change",function(e){
		e.preventDefault();
		var kabkota_id = $(this).val();
		getKecamatan(kabkota_id);
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
	$("#ikabkota").empty();
	$("#iekabkota").empty();
	$("#ikabkota").html('<option value="">Loading...</option>');
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
			$("#ikabkota").html(isi);
			$("#iekabkota").html(isi2);
			if(selected != '') $("#iekabkota").val(selected);
			$("#ikecamatan").trigger("change");
			$("#iekecamatan").trigger("change");
		}
	});
}

function getKecamatan(kabkota_id=""){
	var url = api_url+'kecamatan/get/'+kabkota_id;
	$("#ikecamatan").empty();
	$("#iekecamatan").empty();
	$("#ikecamatan").html('<option value="">Loading...</option>');
	$.get(url).done(function(hasil){
		if(hasil.status == 100 || hasil.status == "100"){
			var isi = '<option value="">--Pilih--</option>';
			var isi2 = '';
			var selected = '';
			var nama_kecamatan = $("#nama_kecamatan option:selected").text();
			$.each(hasil.result.kecamatan,function(key,val){
				if(val.kecamatan == nama_kecamatan) selected = val.id;
				isi += '<option value="'+val.id+'" data-value="'+val.nama_kecamatan+'">'+val.nama_kecamatan+'</option>';

				isi2 += '<option value="'+val.id+'" data-value="'+val.nama_kecamatan+'">'+val.nama_kecamatan+'</option>';
			});
			$("#ikecamatan").html(isi);
			$("#iekecamatan").html(isi2);
			if(selected != '') $("#iekecamatan").val(selected);
		}
	});
}


$("#ftambah").on("submit",function(e){
	e.preventDefault();
	var fd = new FormData($(this)[0]);
	//var fd = $(this).serialize();
	//var fd = {};
	//fd.origin = $("#iorigin").val();
	//fd.provinsi = $("#iprovinsi option:select").text();
	if(fd.has('provinsi')){
		fd.delete('provinsi');
		fd.append('provinsi',$("#iprovinsi option:selected").text());
	}
	if(fd.has('kabkota')){
		fd.delete('kabkota');
		fd.append('kabkota',$("#ikabkota option:selected").text());
	}
	if(fd.has('kecamatan')){
		fd.delete('kecamatan');
		fd.append('kecamatan',$("#ikecamatan option:selected").text());
	}

	var url = '<?php echo base_url("api_admin/erpmaster/jnt/tambah/"); ?>';
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
	$("#iekabkota").off("change");
	$("#iekabkota").on("change",function(e){
		e.preventDefault();
		var kabkota_id = $(this).val();
		getKecamatan(kabkota_id);
	});

});
$("#modal_edit").on("hidden.bs.modal",function(e){
	$("#modal_edit").find("form").trigger("reset");
});

$("#fedit").on("submit",function(e){
	e.preventDefault();
	var fd = new FormData($(this)[0]);
	var url = '<?php echo base_url("api_admin/erpmaster/jnt/edit/"); ?>'+ieid;
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
			var url = '<?php echo base_url('api_admin/erpmaster/jnt/hapus/'); ?>'+id;
			$.get(url).done(function(response){
				if(response.status=="100" || response.status==100){
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
