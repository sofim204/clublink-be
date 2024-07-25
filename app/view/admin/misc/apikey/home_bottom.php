var ieid = '';
var nation_code = '';
var growlPesan = '<h4>Error</h4><p>Tidak dapat diproses, silakan coba beberapa saat lagi!</p>';
var growlType = 'danger';
var drTable = {};
var det = {};

function gritter(pesan,jenis="info"){
	$.bootstrapGrowl(pesan, {
		type: jenis,
		delay: 3500,
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
			"sAjaxSource"		: "<?=base_url("api_admin/misc/apikey/")?>",
			"fnServerParams": function ( aoData ) {
				aoData.push(
					{ "name": "is_active", "value": $("#fl_is_active").val() }
				);
			},
			"fnServerData"	: function (sSource, aoData, fnCallback, oSettings) {
				//$('body').removeClass('loaded');
				NProgress.done();

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
						ieid = $(this).find("td").html();

						var url = '<?=base_url("api_admin/misc/apikey/detail/")?>'+ieid;
						$.get(url).done(function(response){
							NProgress.done();
							if(response.status==200 || response.status=='200'){
								var dta = response.data;
								//input nilai awal
								nation_code = dta.nation_code;
								$('#ienation_code').val(dta.nation_code);
								$("#ieid").val(dta.id);
								$("#ieid1").val(dta.id);
								$("#ieid2").val(dta.id);
								$("#ieid3").val(dta.id);
								$("#ieusername").val(dta.username);
								$("#ieis_active").val(dta.is_active);

								//form hak akses
								$("#fha_a_pengguna_id").val(dta.id);
								$("#fha_a_pengguna_username").val(dta.username);
								$("#fha_nation_code").val(nation_code);

								//form welcome message
								$("#fewm_nation_code").val(dta.nation_code);
								$("#fewm_id").val(dta.id);

								//form edit foto
								$("#fef_nation_code").val(dta.nation_code);
								$("#fef_id").val(dta.id);

								//form edit password
								$("#fpe_nation_code").val(dta.nation_code);
								$("#fpe_id").val(dta.id);

								//tampilkan modal
								//$("#modal_edit").modal("show");
								$("#modal_option").modal("show");
								$("#dta_username").html(dta.username);
								$("#dta_password").html(dta.password);
								$("#dta_str").html(dta.str);
								$("#dta_code").html(dta.code);
							}else{
								growlType = 'info';
								growlPesan = '<h4>Error</h4><p>Cannot get apikey data</p>';
								$.bootstrapGrowl(growlPesan, {
									type: growlType,
									delay: 2500,
									allow_dismiss: true
								});
							}
						}).fail(function(){
							NProgress.done();
							gritter("<h4>Error</h4><p>Cannot get data, please try again later</p>","warning");
						});
					});
					fnCallback(response);
				}).error(function (response, status, headers, config) {
					NProgress.done();
					gritter("<h4>Error</h4><p>Cannot get data, please try again later</p>","warning");
				});
			},
	});
	$('.dataTables_filter input').attr('placeholder', 'Search username or password');
	$("#fl_reset").on("click",function(e){
		e.preventDefault();
		$("#fl_is_active").val("");
		drTable.ajax.reload();
	});
	$("#fl_button").on("click",function(e){
		e.preventDefault();
		if($("#fl_is_active").val().length>0){
			drTable.order([5, 'asc']).ajax.reload();
		}else{
			drTable.ajax.reload();
		}
	});
}
$("#atambah").on("click",function(e){
	e.preventDefault();
	$("#modal_tambah").modal("show");
});
$("#modal_tambah").on("shown.bs.modal",function(e){
	//
	$("#ftambah").off("submit");
	$("#ftambah").on("submit",function(e){
		e.preventDefault();

		var p1 = $("#ipassword").val();
		var p2 = $("#irepassword").val();
		if(p1 != p2){
			$.bootstrapGrowl('Password tidak sama, ulangi', {
				type: 'danger',
				delay: 2500,
				allow_dismiss: true
			});
			$("#ipassword").focus();
			return false;
		}

		var fd = new FormData($(this)[0]);
		var url = '<?=base_url('api_admin/misc/apikey/tambah/');?>';
		$.ajax({
			type: 'post',
			url: url,
			data: fd,
			processData: false,
			contentType: false,
			success: function(response){
				if(response.status=="200" || response.status == 200){
					growlPesan = '<h4>Success</h4><p>'+response.message+'</p>';
					drTable.ajax.reload();
					growlType = 'success';
					$("#modal_tambah").modal("hide");
				}else{
					growlPesan = '<h4>Failed</h4><p>'+response.message+'</p>';
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
				growlPesan = '<h4>Error</h4><p>Cannot add new data right now, please try again later</p>';
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
	$("#btambah_submit").off("click");
	$("#btambah_submit").on("click",function(e){
		e.preventDefault();
		$("#ftambah").trigger("submit");
	});
});

$("#modal_tambah").on("hidden.bs.modal",function(e){
	$("#modal_tambah").find("form").trigger("reset");
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
	var url = '<?=base_url("api_admin/misc/apikey/edit/"); ?>';
	$.ajax({
		type: $(this).attr('method'),
		url: url,
		data: fd,
		processData: false,
		contentType: false,
		success: function(respon){
			if(respon.status=="200" || respon.status == 200){
				growlType = 'success';
				growlPesan = '<h4>Success</h4><p>Data has changed successfully!</p>';
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
			growlPesan = '<h4>Error</h4><p>Cannot process data right now, please try again later</p>';
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
$("#modal_edit_password").on("shown.bs.modal",function(e){
	//
});
$("#modal_edit_password").on("hidden.bs.modal",function(e){
	$("#modal_edit_password").find("form").trigger("reset");
});
$("#fpe").on("submit",function(e){
	e.preventDefault();
	var p1 = $("#fpe_newpassword").val();
	var p2 = $("#fpe_renewpassword").val();
	if(p1.length <= 4){ //>
		$.bootstrapGrowl('Passowrd too short', {
			type: 'danger',
			delay: 2500,
			allow_dismiss: true
		});
		$("#fpe_newpassword").focus();
		return false;
	}
	if(p1 != p2){
		$.bootstrapGrowl('Password not same', {
			type: 'danger',
			delay: 2500,
			allow_dismiss: true
		});
		$("#fpe_newpassword").focus();
		return false;
	}
	var fd = new FormData($(this)[0]);
	var url = '<?=base_url("api_admin/misc/apikey/editpass/"); ?>';
	$.ajax({
		type: $(this).attr('method'),
		url: url,
		data: fd,
		processData: false,
		contentType: false,
		success: function(respon){
			if(respon.status=="200" || respon.status == 200){
				growlType = 'success';
				growlPesan = '<h4>Success</h4><p>Password has changed successfully!</p>';
				drTable.ajax.reload();
			}else{
				growlType = 'danger';
				growlPesan = '<h4>Failed</h4><p>'+respon.message+'</p>';
			}
			$("#modal_edit_password").modal("hide");
			setTimeout(function(){
				$.bootstrapGrowl(growlPesan, {
					type: growlType,
					delay: 2500,
					allow_dismiss: true
				});
			}, 666);
		},
		error:function(){
			growlPesan = '<h4>Error</h4><p>Cannot process data right now, please try again later</p>';
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
$("#modal_edit_wm").on("shown.bs.modal",function(e){
	//
});
$("#modal_edit_wm").on("hidden.bs.modal",function(e){
	$("#modal_edit_wm").find("form").trigger("reset");
});
$("#fewm").on("submit",function(e){
	e.preventDefault();
	var fd = new FormData($(this)[0]);
	var url = '<?=base_url("api_admin/misc/apikey/edit/"); ?>';
	$.ajax({
		type: $(this).attr('method'),
		url: url,
		data: fd,
		processData: false,
		contentType: false,
		success: function(respon){
			if(respon.status=="200" || respon.status == 200){
				growlType = 'success';
				growlPesan = '<h4>Success</h4><p>Data has changed successfully!</p>';
				drTable.ajax.reload();
			}else{
				growlType = 'danger';
				growlPesan = '<h4>Failed</h4><p>'+respon.message+'</p>';
			}
			$("#modal_edit_wm").modal("hide");
			setTimeout(function(){
				$.bootstrapGrowl(growlPesan, {
					type: growlType,
					delay: 2500,
					allow_dismiss: true
				});
			}, 666);
		},
		error:function(){
			growlPesan = '<h4>Error</h4><p>Cannot process data right now, please try again later</p>';
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
$("#ahapus").on("click",function(e){
	e.preventDefault();
	var id = ieid;
	if(id){
		var c = confirm('Are you sure?');
		if(c){
			var url = '<?=base_url('api_admin/misc/apikey/hapus/'); ?>'+id;
			$.get(url).done(function(response){
				if(response.status=="200" || response.status==200){
					$("#modal_option").modal("hide");
					growlType = 'success';
					growlPesan = '<h4>Success</h4><p>Data has been deleted</p>';
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
				growlPesan = '<h4>Error</h4><p>Cannot process data right now, please try again later</p>';
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
	$("#modal_option").modal('hide');
	setTimeout(function(){
		$("#det_username").html()
		$("#modal_detail").modal('show');
	},666)
})

//edit_password
$("#aedit_password").on("click",function(e){
	e.preventDefault();
	$("#modal_option").modal('hide');
	$("#modal_edit_password").modal('show');
});

//edit_welcomemessage
$("#aedit_wm").on("click",function(e){
	e.preventDefault();
	$("#modal_option").modal('hide');
	$("#modal_edit_wm").modal('show');
});
