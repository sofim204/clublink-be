var ieid='';
var growlPesan = '<h4>Error</h4><p>Tidak dapat diproses, silakan coba beberapa saat lagi!</p>';
var growlType = 'danger';
var drTable = {};
function gritter(pesan,jenis='info'){
	$.bootstrapGrowl(pesan,{
		type: jenis,
		delay: 2500,
		allow_dismiss: true
	});
}
App.datatables();
if(jQuery('#drTable').length>0){
	drTable = jQuery('#drTable')
	.on('preXhr.dt', function ( e, settings, data ){
		NProgress.start();
	}).DataTable({
			"order"					: [[ 0, "asc" ]],
			"responsive"	  : true,
			"bProcessing"		: true,
			"bServerSide"		: true,
			"sAjaxSource"		: "<?=base_url("api_admin/erpmaster/modules/")?>",
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
						var url = '<?=base_url()?>api_admin/erpmaster/modules/detail/?id='+encodeURIComponent(ieid);
						$.get(url).done(function(response){
							if(response.status==200){
								var dt = response.data;
								console.log(dt.identifier);
								//input nilai awal
								$("#ienation_code").val(dt.nation_code);
								$("#iename").val(dt.name);
								$("#ieidentifier").val(dt.identifier);
								$("#iechildren_identifier").prepend('<option value="'+dt.children_identifier+'">'+dt.children_identifier+'</option>');
								$("#iechildren_identifier").val(dt.children_identifier);
								$("#ielevel").val(dt.level);
								$("#ieis_active").val(dt.is_active);
								$("#ieis_visible").val(dt.is_visible);
								$("#ieis_default ").val(dt.is_default );
								$("#iepriority").val(dt.priority);
								$("#ieutype").val(dt.utype);
								$("#iefa_icon").val(dt.fa_icon);
								$("#iehas_submenu").val(dt.has_submenu);
								$("#iepath").val(dt.path);
								//tampilkan modal
								$("#modal_edit").modal("show");
							}else{
								growlType = 'info';
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
					NProgress.done();
					alert("Error");
				});
			}
	});
	$('.dataTables_filter input').attr('placeholder', 'Cari');
}
$("#atambah").on("click",function(e){
	e.preventDefault();
	$("#modal_tambah").modal("show");
});
$("#modal_tambah").on("shown.bs.modal",function(e){
	//
});
$("#modal_tambah").on("hidden.bs.modal",function(e){
	$("#modal_tambah").find("form").trigger("reset");
});

$("#ftambah").on("submit",function(e){
	e.preventDefault();
	var fd = new FormData($(this)[0]);
	var url = '<?=base_url("api_admin/erpmaster/modules/tambah/")?>';
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
	$("#ielevel").trigger("blur");
});
$("#modal_edit").on("hidden.bs.modal",function(e){
	$("#modal_edit").find("form").trigger("reset");
});
$("#fedit").on("submit",function(e){
	e.preventDefault();
	var fd = new FormData($(this)[0]);
	var url = '<?=base_url("api_admin/erpmaster/modules/edit/?id=")?>'+encodeURIComponent(ieid);
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

//hapus
$("#bhapus").on("click",function(e){
	e.preventDefault();
	if(ieid){
		var c = confirm('Are you sure?');
		if(c){
			var url = '<?=base_url('api_admin/erpmaster/modules/hapus/?id=')?>'+encodeURIComponent(ieid);
			$.get(url).done(function(response){
				if(response.status==200){
					growlType = 'success';
					growlPesan = '<h4>Success</h4><p>Data has deleted</p>';
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
				growlType = 'warning';
				$.bootstrapGrowl(growlPesan,{
					type: growlType,
					delay: 2500,
					allow_dismiss: true
				});
			});
		}
	}
});

$("#ilevel").on("blur",function(e){
	e.preventDefault();
	NProgress.start();
	var v = $(this).val();
	$("#ichildren_identifier").html('<option value="null">-</option>');
	if(v != 0 || v != "0"){
		$.get('<?=base_url('api_admin/erpmaster/modules/get/')?>').done(function(dt){
			if(dt.status == 200){
				var h = '';
				if(dt.data.length>0){
					$.each(dt.data,function(k,v){
						h += '<option value="'+v.identifier+'">'+v.identifier+' ('+v.name+')</option>';
						if(v.level == 0 || v.level == "0") h+= '-';
						if(v.level == 1 || v.level == "1") h+= '--';
						if(v.level == 2 || v.level == "2") h+= '---';
						h +=''+v.name+'</option>';
					});
				}
				$("#ichildren_identifier").append(h);
			}else{
				console.log("Error");
			}
			NProgress.done();
		}).fail(function(){
			NProgress.done();
		});
	}
});
$("#ielevel").on("change",function(e){
	e.preventDefault();
	var v = $(this).val();
	$("#iechildren_identifier").html('<option value="null">-</option>');
	if(v != 0 || v != "0"){
		$.get('<?=base_url('api_admin/erpmaster/modules/get/')?>').done(function(dt){
			if(dt.status == 200){
				var h = '';
				if(dt.result.length>0){
					$.each(dt.result,function(k,v){
						h += '<option value="'+v.identifier+'">';
						if(v.level == 0 || v.level == "0") h+= '-';
						if(v.level == 1 || v.level == "1") h+= '--';
						if(v.level == 2 || v.level == "2") h+= '---';
						h +=''+v.name+'</option>';
					});
				}
				$("#iechildren_identifier").append(h);
			}
		});
	}
});

$("#areload").on("click",function(e){
	e.preventDefault();
	NProgress.start();
	$.get('<?=base_url('api_admin/erpmaster/modules/reload/')?>').done(function(dt){
		NProgress.done();
		if(dt.status == 200){
			gritter('<h4>Success</h4><p>Session has been reset, please wait...</p>','success');
			setTimeout(function(){
				window.location.reload();
			},2000);
		}else{
			NProgress.done();
			gritter('Gagal','danger');
			//window.location='<?=base_url_admin('login')?>';
		}
	});
});
