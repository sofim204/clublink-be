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
function refreshTabel(){
	drTable.ajax.reload();
}

App.datatables();

if(jQuery('#drTable').length>0){
	drTable = jQuery('#drTable')
	.on('preXhr.dt', function ( e, settings, data ){
		$("#modal-preloader").modal("hide");
		//$("#modal-preloader").modal("show");
	}).DataTable({
			"order"					: [[ 3, "asc" ]],
			"responsive"	  : true,
			"bProcessing"		: true,
			"bServerSide"		: true,
			"sAjaxSource"		: "<?php  echo base_url("api_admin/cms/instagram/"); ?>",
			"fnServerParams": function ( aoData ) {
				aoData.push(
					{ "name": "utype", "value": $('input[name=cb_utype]:checkbox:checked').map(function(){ return this.value; }).get().join(",") }
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
						var url = '<?php echo base_url(); ?>api_admin/cms/instagram/detail/'+id;
						$.get(url).done(function(response){
							if(response.status==100 || response.status=='100'){
								var dta = response.result;
								ieid = dta.id;
								$("#ieid").val(dta.id);
								$("#ieig_media").attr('src',dta.ig_media);
								$("#iepriority").val(dta.priority);
								$("#ieis_active").val(dta.is_active);
								//$("#modal_edit").modal("show");

								$("#adetail").attr("href",'<?=base_url_admin('cms/instagram/detail/')?>'+ieid);
								$("#modal_option").modal("show");
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

	$("#afilter_do").on("click",function(e){
		e.preventDefault();
		drTable.ajax.reload();
	});
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
	var url = '<?php echo base_url("api_admin/cms/instagram/tambah/"); ?>';
	$.ajax({
		type: $(this).attr('method'),
		url: url,
		data: fd,
		processData: false,
		contentType: false,
		success: function(respon){
			if(respon.status=="100" || respon.status == 100){
				growlPesan = '<h4>Berhasil</h4><p>'+respon.message+'</p>';
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
});
$("#modal_edit").on("hidden.bs.modal",function(e){
	$("#modal_edit").find("form").trigger("reset");
});

$("#fedit").on("submit",function(e){
	e.preventDefault();
	var fd = new FormData($(this)[0]);
	var url = '<?php echo base_url("api_admin/cms/instagram/edit/"); ?>'+ieid;
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
$("#bhapus").on("click",function(e){
	e.preventDefault();
	if(ieid){
		var c = confirm('apakah anda yakin?');
		if(c){
			NProgress.start();
			var url = '<?php echo base_url('api_admin/cms/instagram/hapus/'); ?>'+ieid;
			$.get(url).done(function(response){
				NProgress.done();
				if(response.status=="100" || response.status==100){
					gritter('<h4>Berhasil</h4><p>Data berhasil dihapus</p>','success');
				}else{
					gritter('<h4>Gagal</h4><p>'+response.message+'</p>','warning');
				}
				drTable.ajax.reload();
				$("#modal_option").modal("hide");
			}).fail(function() {
				NProgress.done();
				gritter('<h4>Error</h4><p>Proses penghapusan tidak bisa dilakukan, coba beberapa saat lagi</p>','danger');
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
$("#agrab").on("click",function(e){
  var wnd = window.open("https://shafira.thecloudalert.com/instagram/auth/");
});
