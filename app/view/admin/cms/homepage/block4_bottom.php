<?php
$galeri_item_count = 0;
$imgs = explode(",",$homepage_data->slider_list);
$imgs_max = count($imgs);
if(is_array($imgs) && $imgs_max) $galeri_item_count = $imgs_max;
?>
var growlPesan = '<h4>Error</h4><p>Tidak dapat diproses, silakan coba beberapa saat lagi!</p>';
var growlType = 'danger';
var drTable = {};
var ieid = '';
var form_jenis = 'tambah';

App.datatables();
if (typeof growlShow === "function") { }else{
	function growlShow(pesan,type='danger'){
		$.bootstrapGrowl(pesan, {
			type: type,
			delay: 2500,
			allow_dismiss: true
		});
	}
}
if (typeof gritter === "function") { }else{
  function gritter(pesan,type='danger'){
  	growlShow(pesan,type);
  }
}
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
			"sAjaxSource"		: "<?php  echo base_url("api_admin/cms/homepage/"); ?>",
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
						NProgress.start();
						var id = $(this).find("td").html();
						ieid = id;
						//$("#modal_option").modal("show");
						$.get("<?=base_url('api_admin/cms/homepage/detail/')?>"+ieid).done(function(dt){
							NProgress.done();
							$.each(dt.result,function(k,v){
								$("#ie"+k).val(v);
							});
							$("#modal_edit").modal("show");
							$("#iepromo_jenis").trigger("change");
							$("#iepromo_nilai").val(dt.result.promo_nilai);
						}).fail(function(){
							NProgress.done();
							gritter('<h4>Error</h4><p>Tidak dapat mengmabil detail data</p>','warning');
						});
					});
					fnCallback(response);
				}).error(function (response, status, headers, config) {
					gritter('<h4>Error</h4><p>Tidak dapat mengambil data tabel</p>');
				});
			},
	});
	$('.dataTables_filter input').attr('placeholder', 'Cari');
}

//homepage data listener
function saveHomePageData(){
	NProgress.start();
	for (instance in CKEDITOR.instances) {
		CKEDITOR.instances[instance].updateElement();
		//$("#"+instance).val(CKEDITOR.instances[instance].getData());
	}

	var fd = {};
	fd.block4_enable = $("#ihp_block4_enable").val();
	fd.block4_teks = $("#ihp_block4_teks").val();
	fd.block4_youtube_id = $("#ihp_block4_youtube_id").val();

  $.post('<?=base_url('api_admin/cms/homepage/block4_data/')?>',fd).done(function(dt){
		NProgress.done();
		if(dt.status == 100 || dt.status == '100'){
			gritter('<h4>Berhasil</h4><p>Pengaturan homepage berhasil disimpan</p>','success');
		}else{
			gritter('<h4>Error</h4><p>'+dt.message+'</p>');
		}
  }).fail(function(f){
		NProgress.done();
		gritter('<h4>Error</h4><p>Tidak dapat menyimpan ke database, cobalah beberapa saat lagi</p>','warning');
  });
}
$("#asimpan").on("click",function(){
	saveHomePageData();
});
$("#acompile").on("click",function(){
	NProgress.start();
	$.get('<?=base_url('api_admin/cms/homepage/compile/')?>').done(function(dt){
		NProgress.done();
		gritter('<h4>Berhasil</h4><p>Tampilan homepage sudah berhasil dikompilasi</p>','info');
	}).fail(function(){
		NProgress.done();
		gritter('<h4>Error</h4><p>Membuat homepage tidak dapat dilakukan sekarang, cobalah beberapa saat lagi</p>','danger');
	});
});
