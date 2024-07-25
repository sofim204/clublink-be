var ieid = '';
var nation_code = '';
var growlPesan = '<h4>Error</h4><p>Tidak dapat diproses, silakan coba beberapa saat lagi!</p>';
var growlType = 'danger';
var api_url = '<?=base_url('api_admin/akun/'); ?>';
var drTable = {};

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
			"sAjaxSource"		: "<?=base_url("api_admin/akun/pengguna/")?>",
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

						var url = '<?=base_url("api_admin/akun/pengguna/detail/")?>'+ieid;
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
								$("#ienama").val(dta.nama);
								$("#ieemail").val(dta.email);
								$("#ieis_active").val(dta.is_active);
								$("#iefoto").val(dta.foto);
								$("#ieuser_role").val(dta.user_role);

								//form hak akses
								$("#fha_a_pengguna_id").val(dta.id);
								$("#fha_a_pengguna_username").val(dta.username);
								$("#fha_nation_code").val(nation_code);

								//form welcome message
								$("#fewm_nation_code").val(dta.nation_code);
								$("#fewm_id").val(dta.id);
								$("#fewm_welcome_message").val(dta.welcome_message);

								//form edit foto
								$("#fef_nation_code").val(dta.nation_code);
								$("#fef_id").val(dta.id);

								//form edit password
								$("#fpe_nation_code").val(dta.nation_code);
								$("#fpe_id").val(dta.id);

								//tampilkan modal
								//$("#modal_edit").modal("show");
								$("#modal_option").modal("show");
							}else{
								growlType = 'info';
								growlPesan = '<h4>Error</h4><p>Cannot get administrator data</p>';
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
	$('.dataTables_filter input').attr('placeholder', 'Search Admin name');
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
		var url = '<?=base_url('api_admin/akun/pengguna/tambah/');?>';
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

$("#modal_hak_akses").on("shown.bs.modal",function(e){
	$("#form_hak_akses").off("submit");
	$("#form_hak_akses").on("submit",function(e) {
		e.preventDefault();
		var fd = new FormData($(this)[0]);
		var url = api_url + "pengguna/hak_akses/";

		$.ajax({
			type: 'post',
			url: url,
			data: fd,
			processData: false,
			contentType: false,
			success: function(respon) {
				if (respon.status=="200" || respon.status == 200) {
					growlPesan = '<h4>Success</h4><p>'+respon.message+'</p>';
					drTable.ajax.reload();
					growlType = 'success';
					$("#modal_hak_akses").modal("hide");
				}else {
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
	})

	$("#btambah_access").off("click");
	$("#btambah_access").on("click",function(e){
		e.preventDefault();
		$("#form_hak_akses").trigger("submit");
	});
});

$("#modal_edit").on("shown.bs.modal",function(e){
	//
});
$("#modal_edit").on("hidden.bs.modal",function(e){
	$("#modal_edit").find("form").trigger("reset");
});
$("#fedit").on("submit",function(e){
	e.preventDefault();
	var fd = new FormData($(this)[0]);
	var url = '<?=base_url("api_admin/akun/pengguna/edit/"); ?>';
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
	var url = '<?=base_url("api_admin/akun/pengguna/editpass/"); ?>';
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
	var url = '<?=base_url("api_admin/akun/pengguna/edit/"); ?>';
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

//option
$("#ahak_akses").on("click",function(e){
	e.preventDefault();
	$("#modal_option").modal("hide");
	$("#form_hak_akses input[type=checkbox]").prop("checked", false);
	setTimeout(function(){
		$.get(api_url + "pengguna/pengguna_module/"+ieid).done(function(dt){
			$.each(dt,function(k,v){
				$("#"+v).prop("checked",true);
			});
			$("#modal_hak_akses").modal("show");
		}).fail(function(){
			alert("Cannot get user modules");
		});

	},333);
});

//hapus
$("#ahapus").on("click",function(e){
	e.preventDefault();
	var id = ieid;
	if(id){
		var c = confirm('Are you sure?');
		if(c){
			var url = '<?=base_url('api_admin/akun/pengguna/hapus/'); ?>'+id;
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
	$("#modal_option").modal("hide");
	setTimeout(function(){
		//$("#modal_edit").modal("show");
		alert('masih dalam pengembangan');
	},333);
});

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

//edit_foto
$("#bprofil_foto").on("click",function(e){
	e.preventDefault();
	$("#modal_option").modal('hide');
	$("#fef").trigger("reset");
	setTimeout(function(){
		$("#modal_profil_foto").modal('show');
	},333);
});
$("#fef").on("submit",function(e){
	e.preventDefault();
	var fd = new FormData($(this)[0]);
	var url = '<?=base_url('api_admin/akun/pengguna/edit_foto/');?>';
	$.ajax({
		type: 'post',
		url: url,
		data: fd,
		processData: false,
		contentType: false,
		success: function(dt){
			if(dt.status=="200" || dt.status == 200){
				$("#modal_profil_foto").modal("hide");
				setTimeout(function(){
					$.bootstrapGrowl('<h4>Success</h4><p>Profile picture updated</p>', {
						type: 'success',
						delay: 2500,
						allow_dismiss: true
					});
				}, 666);
				drTable.ajax.reload();
			}else{
				setTimeout(function(){
					$.bootstrapGrowl('<h4>Failed</h4><p>'+dt.message+'</p>', {
						type: 'danger',
						delay: 2500,
						allow_dismiss: true
					});
					$("#modal_profil_foto").modal("hide");
				}, 666);
			}
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
$("#btn_foto_reset").on("click",function(e){
	e.preventDefault();
	var c = confirm("Are you sure?");
	if(c){
		$.get("<?=base_url("api_admin/akun/pengguna/foto_reset/")?>"+(ieid)).done(function(dt){
			if(dt.status == 200){
				$.bootstrapGrowl("<h4>Success</h4><p>Administrator display picture has been resetted</p>", {
					type: "info",
					delay: 2500,
					allow_dismiss: true
				});
				drTable.ajax.reload();
			}else{
				$.bootstrapGrowl("<h4>Failed</h4><p>"+dt.message+"</p>", {
					type: "danger",
					delay: 2500,
					allow_dismiss: true
				});
			}
		}).fail(function(){
			$.bootstrapGrowl("<h4>Error</h4><p>Cannot reset profile picture right now, please try again later</p>", {
				type: "warning",
				delay: 2500,
				allow_dismiss: true
			});
		})
	}
});

// detect mime
//https://stackoverflow.com/questions/18299806/how-to-check-file-mime-type-with-javascript-before-upload
function get_mime_type(header){
	switch (header) {
		case "89504e47":
			type = "image/png";
			break;
		//case "47494638":
		//	type = "image/gif";
		//	break;
		case "ffd8ffe0":
		case "ffd8ffe1":
		case "ffd8ffe2":
		case "ffd8ffe3":
		case "ffd8ffe8":
			type = "image/jpeg";
			break;
		case "52494646":
			type= "image/webp";
			break;
		default:
			type = "unknown"; // Or you can use the blob.type as fallback
			break;
	}
	return type;
}
$("#fef_foto").on("change",function(e){
	if (window.FileReader && window.Blob) {
		var control = document.getElementById("fef_foto");
		var i = 0;
		var files = control.files;
		var blob = files[i]; // See step 1 above
		var header = "";
		//console.log("Filename: " + blob.name);
    //console.log("Type: " + blob.type);
    //console.log("Size: " + blob.size + " bytes");
		var fileReader = new FileReader();
		fileReader.onloadend = function(e) {
		  var arr = (new Uint8Array(e.target.result)).subarray(0, 4);
		  for(var i = 0; i < arr.length; i++) {
		     header += arr[i].toString(16);
		  }
			//console.log("Header: "+header);
		  var type = get_mime_type(header);
			console.log("Mime Type: "+type);
			if(type == 'image/webp' || type=='unknown'){
				gritter("<h4>Warning</h4><p>Unsupported image type: "+type+". Please try another file.","warning");
				$("#fef_foto").val('');
			}
		};
		fileReader.readAsArrayBuffer(blob);
	}
});
