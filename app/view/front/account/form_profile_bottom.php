function getProvinsi(){
	var url = 'http://bandros.id/ongkir/provinsi';
	$.get(url).done(function(hasil){
		if(hasil.status == 1 || hasil.status == "1"){
			$("#sprovinsi").empty();
			$("#sprovinsi").html('<option>Loading...</option>');
			var isi = '';
			$.each(hasil.result,function(key,val){
				isi += '<option value="'+val.id+'">'+val.nama_provinsi+'</option>';
			});
			$("#sprovinsi").html(isi);
			
			//setelah di input harus di trigger ulang
			$("#sprovinsi").trigger("change");
			
			$("#iprovinsi").val($(this).val());
			$("#skabkota").trigger("change");
		}
	});
}
function getKabkota(){
	var provinsi_id = $("#sprovinsi").val();
	var url = 'http://bandros.id/ongkir/kabkota/?provinsi_id='+provinsi_id;
	$.get(url).done(function(hasil){
		if(hasil.status == 1 || hasil.status == "1"){
			$("#skabkota").empty();
			$("#skabkota").html('<option>Loading...</option>');
			var isi = '';
			$.each(hasil.result,function(key,val){
				isi += '<option value="'+val.id+'">'+val.nama_kabkota+'</option>';
			});
			$("#skabkota").html(isi);
			
			//setelah di input harus di trigger ulang
			$("#skabkota").trigger("change");
			
			$("#ikabkota").val($("#skabkota").val());
			$("#skecamatan").trigger("change");
		}
	});
}
function getKecamatan(){
	var kabkota_id = $("#skabkota").val();
	var url = 'http://bandros.id/ongkir/kecamatan/?kabkota_id='+kabkota_id;
	$.get(url).done(function(hasil){
		console.log(hasil);
		if(hasil.status == 1 || hasil.status == "1"){
			$("#skecamatan").empty();
			$("#skecamatan").html('<option>Loading...</option>');
			var isi = '';
			$.each(hasil.result,function(key,val){
				isi += '<option value="'+val.id+'">'+val.nama_kecamatan+'</option>';
			});
			
			$("#skecamatan").html(isi);
			
			//setelah di input harus di trigger ulang
			$("#skecamatan").trigger("change");
			$("#ikecamatan").val($("#skecamatan").val());
		}
	});
}
$("#inegara").on("change",function(evt){
	evt.preventDefault();
	var nilai = $(this).val();
	if(nilai == "IDN" || nilai == "ID"){
	
		$("#sprovinsi").show();
		$("#label_sprovinsi").show();
		$("#iprovinsi").hide();
		$("#label_iprovinsi").hide();
		
		$("#skabkota").show();
		$("#label_skabkota").show();
		$("#ikabkota").hide();
		$("#label_ikabkota").hide();
		
		$("#skecamatan").show();
		$("#label_skecamatan").show();
		$("#ikecamatan").hide();
		$("#label_ikecamatan").hide();
		
		getProvinsi();
		$("#sprovinsi").trigger("change");
	}else{
		$("#sprovinsi").hide();
		$("#label_sprovinsi").hide();
		$("#iprovinsi").show();
		$("#label_iprovinsi").show();
		
		$("#skabkota").hide();
		$("#label_skabkota").hide();
		$("#ikabkota").show();
		$("#label_ikabkota").show();
		
		$("#skecamatan").hide();
		$("#label_skecamatan").hide();
		$("#ikecamatan").show();
		$("#label_ikecamatan").show();
	}
	$("#inegara").val(nilai);
});
$("#sprovinsi").on("change",function(e){
	e.preventDefault();
	getKabkota();
});
$("#skabkota").on("change",function(e){
	e.preventDefault();
	getKecamatan();
});
$("#skecamatan").on("change",function(e){
	$("#ikecamatan").val($(this).val());
});
setTimeout(function(){
	$("#inegara").trigger("change");
},1000);