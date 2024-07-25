var growlPesan = '<h4>Error</h4><p>Tidak dapat diproses, silakan coba beberapa saat lagi!</p>';
var growlType = 'danger';
var api_url = '<?=$api_url?>';
var drTable = {};
var ieid = '';
var ieprovinsi = '';
var iekabkota = '';
var iekecamatan = '';
var stateStatus = false;

function gritter(pesan,jenis='info'){
	$.bootstrapGrowl(pesan, {
		type: jenis,
		delay: 3500,
		allow_dismiss: true
	});
}

App.datatables();

const geturl = window.location.href; //get current url
const lastPageUrl = document.referrer; //get last page url

if(lastPageUrl.includes('ecommerce/pelanggan') || lastPageUrl.includes('ecommerce/pelanggan/detail')) {
	//stateStatus = true;
	//localStorage.getItem("lastpagination");
} else {
	localStorage.setItem("lastpagination", 0);
	//stateStatus = false;
}

var getLastPagination = localStorage.getItem("lastpagination");

if(jQuery('#drTable').length>0){
	drTable = jQuery('#drTable')
	.on('preXhr.dt', function ( e, settings, data ){
		NProgress.start();
	}).DataTable({
			"order"				: [[ 0, "desc" ]],
			//"stateSave"			: stateStatus,
			"responsive"	  	: true,
			"bProcessing"		: true,
			"bServerSide"		: true,
			//"pageLength"		: getLastPagination,
			//"iPageStart"		:2,
			//"iDisplayStart"		: getLastPagination, // from start data, page 1 start from array 0, page 2 start from array 10
			"sAjaxSource"		: "<?=base_url("api_admin/ecommerce/pelanggan/")?>",
			"fnServerParams"	: function ( aoData ) {
				aoData.push(
					{ "name": "is_confirmed", "value": $("#fl_is_confirmed").val() },
					{ "name": "pelanggan_status", "value": $("#fl_pelanggan_status").val() }
				);
			},
			"drawCallback": function( settings ) {
				var table = $('#drTable').DataTable();
				var info = table.page.info();
			//	//console.log(info);
			//	//alert("page " + info.start);
			//	//alert( 'Now on page'+ this.fnPagingInfo().iPage );
				localStorage.setItem("lastpagination", info.start);
			},
			"fnServerData"		: function (sSource, aoData, fnCallback, oSettings) {
				//console.log(aoData);
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
						//var id = $(this).find("td").html();
						var currentRow = $(this).closest("tr");
						var id = $('#drTable').DataTable().row(currentRow).data()[0]; <!-- to get data from specific column, change this "data()[id_column]" -->
						ieid = id;
						var url = '<?=base_url()?>api_admin/ecommerce/pelanggan/detail/'+id;
						NProgress.start();
						$.get(url).done(function(response){
							NProgress.done();
							if(response.status==200){
								var dta = response.data;
								$("#ieid").val(dta.id);
								$("#iekode").val(dta.kode);
								$("#iefnama").val(dta.fnama);
								$("#ieemail").val(dta.email);
								$("#ietelp").val(dta.telp);
								$("#iealamat").html(dta.alamat);
								$("#ieprovinsi").val(dta.provinsi);
								$("#iekelamin").val(dta.kelamin);
								$("#iebdate").val(dta.bdate);
								$("#iekodepos").val(dta.kodepos);
								iekabkota = dta.kabkota;
								iekecamatan = dta.kecamatan;
								$("#ieptype").val(dta.ptype);
								$("#iepoin").val(dta.poin);
								$("#ieis_active").val(dta.is_active);

								$("#ipoin_pelanggan").html(dta.poin);
								$("#isisa_poin").html(dta.poin);

								$("#user_id, #user_id_status").val(dta.id);
								$("#user_email, #user_email_status").val(dta.email);
								$("#status_email_active").val(dta.is_confirmed);
								$("#status_user_active").val(dta.is_active);
								$("#status_permanent_inactive").val(dta.is_permanent_inactive);
								$("#iinactive_text").val(dta.inactive_text);
								$("#status_as_admin").val(dta.is_admin);

								$("#modal_option").modal("show");
							}else{
								gritter('<h4>Failed</h4><p>Cant fetch data right now, please try again later</p>','danger');
							}
						}).fail(function(){
							NProgress.done();
							gritter('<h4>Error</h4><p>Cant fetch data right now, please try again later</p>','Warning');
						});
					});
					fnCallback(response);
				}).error(function (response, status, headers, config) {
					NProgress.done();
					gritter('<h4>Error</h4><p>Cant fetch customer detail right now, please try again later</p>','info');
				});
			},
	});
	$('.dataTables_filter input').attr('placeholder', 'Search by name, email or telp no').css({'width':'250px', 'display':'inline-block'}); <!-- by Muhammad Sofi 29 December 2021 15:00 | resize search box -->
	$("#fl_reset").on("click",function(e){
		e.preventDefault();
		$("#fl_is_confirmed").val("");
		$("#fl_pelanggan_status").val("");
		drTable.search('').columns().search('').draw(); <!-- by Muhammad Sofi 29 December 2021 15:00 | clear search box on click reset button -->
		drTable.ajax.reload(null,true);
	});
	$("#fl_button").on("click",function(e){
		e.preventDefault();
		drTable.ajax.reload(null,true);
	});
}

//tambah
$("#atambah").on("click",function(e){
	e.preventDefault();
	$("#modal_tambah").modal("show");
});
$("#modal_tambah").on("shown.bs.modal",function(e){
	$("#iprovinsi").trigger("change");
});
$("#modal_tambah").on("hidden.bs.modal",function(e){
	$("#modal_tambah").find("form").trigger("reset");
});
$("#modal_change_status_permanent_inactive").on("hidden.bs.modal",function(e){
	$("#modal_change_status_permanent_inactive").find("form").trigger("reset");
});
$("#modal_change_status_admin").on("hidden.bs.modal",function(e){
	$("#modal_change_status_admin").find("form").trigger("reset");
});

$("#ftambah").on("submit",function(e){
	e.preventDefault();
	var fd = new FormData($(this)[0]);
	var url = '<?=base_url("api_admin/ecommerce/pelanggan/tambah/")?>';
	NProgress.start();
	$.ajax({
		type: $(this).attr('method'),
		url: url,
		data: fd,
		processData: false,
		contentType: false,
		success: function(respon){
			NProgress.done();
			if(respon.status==200){
				gritter('<h4>Success</h4><p>Customer added successfully!</p>','success');
				$("#modal_tambah").modal("hide");
				drTable.ajax.reload('',false);
			}else{
				gritter('<h4>Failed</h4><p>'+respon.message+'</p>','danger');
			}
		},
		error:function(){
			NProgress.done();
			gritter('<h4>Error</h4><p>Cant add new customer right now, please try again later</p>','warning');
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
	var url = '<?=base_url("api_admin/ecommerce/pelanggan/edit/")?>'+ieid;
	NProgress.start();
	$.ajax({
		type: $(this).attr('method'),
		url: url,
		data: fd,
		processData: false,
		contentType: false,
		success: function(respon){
			NProgress.done();
			if(respon.status==200){
				gritter('<h4>Success</h4><p>Customer data has changed successfully</p>','success');
				drTable.ajax.reload('',false);
				$("#modal_edit").modal("hide");
			}else{
				gritter('<h4>Failed</h4><p>'+respon.message+'</p>','danger');
			}
		},
		error:function(){
			NProgress.done();
			gritter('<h4>Error</h4><p>Cant change customer data right now, please try again later</p>','warning');
			return false;
		}
	});
});

$("#bdownload_xls").on("click",function(e){
	e.preventDefault();
	var is_confirmed = $("#fl_is_confirmed").val();
	var is_active = $("#fl_pelanggan_status").val();
	var url = '<?=base_url_admin()?>ecommerce/pelanggan/download_xls/?';
	url += 'is_confirmed='+encodeURIComponent(is_confirmed);
	url += '&is_active='+encodeURIComponent(is_active);
	window.location = url;
});

$("#detail_xls").on("click",function(e){
	e.preventDefault();
	var is_confirmed = $("#fl_is_confirmed").val();
	var is_active = $("#fl_pelanggan_status").val();
	var url = '<?=base_url_admin()?>ecommerce/pelanggan/downloaddetail_xls/?';
	url += 'is_confirmed='+encodeURIComponent(is_confirmed);
	url += '&is_active='+encodeURIComponent(is_active);
	window.location = url;
});

//hapus
$("#bhapus2").on("click",function(e){
	$("#bhapus").trigger("click");
});
$("#bhapus").on("click",function(e){
	e.preventDefault();
	var c = confirm('Are you sure?');
	if(c){
		var url = '<?=base_url('api_admin/ecommerce/pelanggan/hapus/')?>'+ieid;
		NProgress.start();
		$.get(url).done(function(respon){
			NProgress.done();
			if(respon.status==200){
				gritter('<h4>Success</h4><p>Customer has been deleted</p>','success');
			}else{
				gritter('<h4>Failed</h4><p>'+respon.message+'</p>','danger');
			}
			drTable.ajax.reload('',false);
			$("#modal_option").modal("hide");
		}).fail(function() {
			NProgress.done();
			gritter('<h4>Error</h4><p>Cant deleted customer right now, please try again later</p>','warning');
		});
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

//detail
$("#adetail").on("click",function(e){
	e.preventDefault();
	$("#modal_option").modal("hide");
	setTimeout(function(){
		//$("#modal_edit").modal("show");
		//window.location = '<?=base_url_admin('ecommerce/pelanggan/detail/')?>'+ieid;

		window.open("<?=base_url_admin('ecommerce/pelanggan/detail/')?>"+ieid, "_blank");
	},333);
});

//customer as buyer
$("#cbuyer").on("click",function(e){
	e.preventDefault();
	$("#modal_option").modal("hide");
	setTimeout(function(){
		//$("#modal_edit").modal("show");
		//alert('masih dalam pengembangan');
		window.location = '<?=base_url_admin('ecommerce/pelanggan/cbuyer/')?>'+ieid;
	},333);
});

//customer as seller
$("#cseller").on("click",function(e){
	e.preventDefault();
	$("#modal_option").modal("hide");
	setTimeout(function(){
		//$("#modal_edit").modal("show");
		//alert('masih dalam pengembangan');
		window.location = '<?=base_url_admin('ecommerce/pelanggan/cseller/')?>'+ieid;
	},333);
});

//transaction list
$("#transaction").on("click",function(e){
	e.preventDefault();
	$("#modal_option").modal("hide");
	setTimeout(function(){
		//$("#modal_edit").modal("show");
		//alert('masih dalam pengembangan');
		window.location = '<?=base_url_admin('ecommerce/pelanggan/transaction/')?>'+ieid;
	},333);
});

//alamat fill in
$("#aredeem").on("click",function(e){
	e.preventDefault();
	$("#modal_option").modal("hide");
	setTimeout(function(e){
		$("#modal_redeem_poin").modal("show");
	},666);
});

$("#inominal_poin").on("change",function(e){
	//e.preventDefault();
	var poin = parseInt($("#ipoin_pelanggan").html());
	var redeem = parseInt($("#inominal_poin").val());
	$("#isisa_poin").html(poin-redeem);
});

$("#fredeem_poin").on("submit",function(e){
	var poin = parseInt($("#ipoin_pelanggan").html());
	var redeem = parseInt($("#inominal_poin").val());
	console.log('poin: '+poin);
	console.log('redeem: '+redeem);
	if(redeem>poin){
		alert("Nominal redeem tidak bisa melebihi nominal poin yang ada");
		return false;
	}
	var c = confirm('Are you sure?');
	if(c){
		var fd = new FormData();
		fd.append("poin",(poin - redeem));
		var url = '<?=base_url("api_admin/ecommerce/pelanggan/edit/")?>'+ieid;
		$.ajax({
			type: $(this).attr('method'),
			url: url,
			data: fd,
			processData: false,
			contentType: false,
			success: function(respon){
				if(respon.status==200){
					gritter('<h4>Success</h4><p>Redeem poin has been successfully!</p>','success');
					drTable.ajax.reload('',false);
					$("#modal_redeem_poin").modal("hide");
				}else{
					gritter('<h4>Failed</h4><p>'+respon.message+'</p>','danger');
				}
			},
			error:function(){
				gritter('<h4>Error</h4><p>Proses redeem tidak bisa dilakukan, coba beberapa saat lagi</p>','warning');
				return false;
			}
		});
	}else{
		$("#modal_redeem_poin").modal("hide");
	}
});

$("#modal_redeem_poin").on("hidden.bs.modal",function(e){
	$("#ipoin_pelanggan").html('0');
	$("#inominal_poin").val('0');
});


//change icon
$("#aimage_change").on("click",function(e){
	e.preventDefault();
	$("#modal_option").modal("hide");
	setTimeout(function(){
		$("#modal_image_change").modal("show");
	},500);
});
$("#fimage_change").on("submit",function(e){
	e.preventDefault();
	NProgress.start();
	var url = '<?=base_url("api_admin/ecommerce/pelanggan/image_change/")?>'+ieid;
	$.ajax({
		type: $(this).attr('method'),
		url: url,
		data: new FormData(this),
		processData: false,
		contentType: false,
		success: function(respon){
			if(respon.status=="200" || respon.status == 200){
				gritter('<h4>Success</h4><p>Icon changed successfully</p>','success');
				setTimeout(function(){
					NProgress.done();
					drTable.ajax.reload();
					$("#modal_icon_change").modal("hide");
				},3000);
			}else{
				NProgress.done();
				gritter('<h4>Failed</h4><p>'+respon.message+'</p>','danger');
				$("#modal_icon_change").modal("hide");
			}
		},
		error:function(){
			setTimeout(function(){
				$("#modal_icon_change").modal("hide");
				NProgress.done();
				gritter('<h4>Error</h4><p>Cant change icon right now, please try again later</p>','warning');
			}, 666);
			return false;
		}
	});
});
$("#bactivated").on('click',function(e){
	e.preventDefault();
	NProgress.start();
	$.get("<?=base_url("api_admin/ecommerce/pelanggan/activated/")?>"+ieid).done(function(dt){
		NProgress.done();
		$("#modal_option").modal("hide");
		drTable.ajax.reload(null,false);
		if(dt.status == "200"){
			gritter("<h4>Success</h4><p>User activated.</p>",'success');
		}else if(dt.status == 999){
			gritter("<h4>Warning</h4><p>"+dt.message+"</p>",'warning');
		}else{
			gritter("<h4>Failed</h4><p>"+dt.message+"</p>",'danger');
		}
	}).fail(function(e){
		NProgress.done();
		gritter("<h4>Error</h4><p>Cant change user activation right now, please try again.</p>",'warning');
	})
});

$("#bdeactivated").on('click',function(e){
	e.preventDefault();
	NProgress.start();
	$.get("<?=base_url("api_admin/ecommerce/pelanggan/deactivated/")?>"+ieid).done(function(dt){
		NProgress.done();
		$("#modal_option").modal("hide");
		drTable.ajax.reload(null,false);
		if(dt.status == "200"){
			gritter("<h4>Success</h4><p>User deactivated.</p>",'success');
		}else if(dt.status == 999){
			gritter("<h4>Warning</h4><p>"+dt.message+"</p>",'warning');
		}else{
			gritter("<h4>Failed</h4><p>"+dt.message+"</p>",'danger');
		}
	}).fail(function(e){
		NProgress.done();
		gritter("<h4>Error</h4><p>Cant change user activation right now, please try again.</p>",'warning');
	})
});

$("#bemail_konfirmasi").on('click',function(e){
	e.preventDefault();
	var c = confirm('Are you sure?');
	if(c){
		NProgress.start();
		$.get("<?=base_url("api_admin/ecommerce/pelanggan/email_konfirmasi/")?>"+ieid).done(function(dt){
			NProgress.done();
			$("#modal_option").modal("hide");
			drTable.ajax.reload(null,false);
			if(dt.status == "200"){
				gritter("<h4>Success</h4><p>Registration confirmation link email has been sent</p>",'success');
			}else{
				gritter("<h4>Failed</h4><p>"+dt.message+"</p>",'danger');
			}
		}).fail(function(e){
			NProgress.done();
			gritter("<h4>Error</h4><p>Cant send email right now, please try again.</p>",'warning');
		});
	}
});


$("#bemail_lupa").on('click',function(e){
	e.preventDefault();
	var c = confirm('Are you sure?');
	if(c){
		NProgress.start();
		$.get("<?=base_url("api_admin/ecommerce/pelanggan/email_lupa/")?>"+ieid).done(function(dt){
			NProgress.done();
			$("#modal_option").modal("hide");
			drTable.ajax.reload(null,false);
			if(dt.status == "200"){
				gritter("<h4>Success</h4><p>Reset password link has been sent</p>",'success');
			}else{
				gritter("<h4>Failed</h4><p>"+dt.message+"</p>",'danger');
			}
		}).fail(function(e){
			NProgress.done();
			gritter("<h4>Error</h4><p>Cant send email right now, please try again.</p>",'warning');
		});
	}
});
<!-- change verification status -->
$("#b_change_verif_status").on('click', function() {
	$("#modal_option").modal("hide");
	setTimeout(function(){
		$("#modal_change_verification_status").modal("show");
	}, 300);
});

<!-- change status -->
$("#b_change_status").on('click', function() {
	$("#modal_option").modal("hide");
	setTimeout(function(){
		$("#modal_change_status").modal("show");
	}, 300);
});

<!-- change status permanent inactive-->
$("#b_change_status_permanent_inactive").on('click', function() {
	$("#modal_option").modal("hide");
	setTimeout(function(){
		$("#modal_change_status_permanent_inactive").modal("show");
	}, 300);
});

<!-- change status admin -->
$("#b_change_status_admin").on('click', function() {
	$("#modal_option").modal("hide");
	setTimeout(function(){
		$("#modal_change_status_admin").modal("show");
	}, 300);
});

$("#form_change_verification_status").on("submit",function(e){
	e.preventDefault();
	NProgress.start();
	var fd = new FormData($("#form_change_verification_status")[0]);
	var url = '<?=base_url("api_admin/ecommerce/pelanggan/edit_status_verification_user/")?>' +ieid;
	$.ajax({
		url: url,
		type: 'POST',
		mimeType : "multipart/form-data",
		dataType: 'json',
		processData: false,
		contentType: false,
		data: fd
	}).done(function(respon) {
		NProgress.done();
		if(respon.status==200){
			gritter('<h4>Success</h4><p>Data edited successfuly</p>','success');
			$("#modal_change_verification_status").modal("hide");
			drTable.ajax.reload(null, false);  <!-- by Muhammad Sofi 28 January 2022 18:38 | Prevent table reload to first page after edit data -->
		}else{
			gritter('<h4>Failed</h4><p>'+respon.message+'</p>','danger');
		}
		$("#modal_edit").modal("hide");
	}).fail(function(){
		NProgress.done();
		setTimeout(function(){
			gritter('<h4>Error</h4><p>Cannot edit data right now, please try again later</p>','warning');
		}, 666);
		return false;
	});
});

$("#form_change_status_point").on("submit",function(e){
	e.preventDefault();
	NProgress.start();
	var fd = new FormData($("#form_change_status_point")[0]);
	var url = '<?=base_url("api_admin/ecommerce/pelanggan/edit_status_get_point/")?>' +ieid;
	$.ajax({
		url: url,
		type: 'POST',
		mimeType : "multipart/form-data",
		dataType: 'json',
		processData: false,
		contentType: false,
		data: fd
	}).done(function(respon) {
		NProgress.done();
		if(respon.status==200){
			gritter('<h4>Success</h4><p>Data edited successfuly</p>','success');
			$("#modal_change_status_point").modal("hide");
			drTable.ajax.reload(null, false);  <!-- by Muhammad Sofi 28 January 2022 18:38 | Prevent table reload to first page after edit data -->
		}else{
			gritter('<h4>Failed</h4><p>'+respon.message+'</p>','danger');
		}
		$("#modal_edit").modal("hide");
	}).fail(function(){
		NProgress.done();
		setTimeout(function(){
			gritter('<h4>Error</h4><p>Cannot edit data right now, please try again later</p>','warning');
		}, 666);
		return false;
	});
});

$("#form_change_status_permanent_inactive").on("submit",function(e){
	e.preventDefault();
	NProgress.start();
	var fd = new FormData($("#form_change_status_permanent_inactive")[0]);
	var url = '<?=base_url("api_admin/ecommerce/pelanggan/edit_status_permanent_inactive/")?>' +ieid;
	$.ajax({
		url: url,
		type: 'POST',
		mimeType : "multipart/form-data",
		dataType: 'json',
		processData: false,
		contentType: false,
		data: fd
	}).done(function(respon) {
		NProgress.done();
		if(respon.status==200){
			gritter('<h4>Success</h4><p>'+respon.message+'</p>','success');
			$("#modal_change_status_permanent_inactive").modal("hide");
			drTable.ajax.reload(null, false);  <!-- by Muhammad Sofi 28 January 2022 18:38 | Prevent table reload to first page after edit data -->
		} else if(respon.status==201){
			gritter('<h4>Success</h4><p>'+respon.message+'</p>','info');
		} else {
			gritter('<h4>Failed</h4><p>'+respon.message+'</p>','danger');
		}
		$("#modal_edit").modal("hide");
	}).fail(function(){
		NProgress.done();
		setTimeout(function(){
			gritter('<h4>Error</h4><p>Cannot edit data right now, please try again later</p>','warning');
		}, 666);
		return false;
	});
});

$("#form_change_status").on("submit",function(e){
	e.preventDefault();
	NProgress.start();
	var fd = new FormData($("#form_change_status")[0]);
	var url = '<?=base_url("api_admin/ecommerce/pelanggan/edit_status_active_user/")?>' +ieid;
	$.ajax({
		url: url,
		type: 'POST',
		mimeType : "multipart/form-data",
		dataType: 'json',
		processData: false,
		contentType: false,
		data: fd
	}).done(function(respon) {
		NProgress.done();
		if(respon.status==200){
			gritter('<h4>Success</h4><p>Data edited successfuly</p>','success');
			$("#modal_change_status").modal("hide");
			drTable.ajax.reload(null, false);  <!-- by Muhammad Sofi 28 January 2022 18:38 | Prevent table reload to first page after edit data -->
		}else{
			gritter('<h4>Failed</h4><p>'+respon.message+'</p>','danger');
		}
		$("#modal_edit").modal("hide");
	}).fail(function(){
		NProgress.done();
		setTimeout(function(){
			gritter('<h4>Error</h4><p>Cannot edit data right now, please try again later</p>','warning');
		}, 666);
		return false;
	});
});

<!-- delete account -->
$("#b_delete_account").on("click",function(e){
	e.preventDefault();
	if(ieid){
		var c = confirm('Are you sure to delete this account?');
		if(c){
			NProgress.start();
			var url = '<?=base_url('api_admin/ecommerce/pelanggan/delete_user_data/')?>'+ieid;
			$.get(url).done(function(response){
				NProgress.done();
				if(response.status==200){
					gritter('<h4>Success</h4><p>Data successfuly deleted</p>','success');
					drTable.ajax.reload();
					$("#modal_option").modal("hide");
				}else{
					gritter('<h4>Failed</h4><p>'+response.message+'</p>','warning');
				}
			}).fail(function() {
				NProgress.done();
				gritter('<h4>Error</h4><p>Cant delete data right now, please try again later</p>','warning');
			});
		}
	}
});

$("#form_change_status_admin").on("submit",function(e){
	e.preventDefault();
	NProgress.start();
	var fd = new FormData($("#form_change_status_admin")[0]);
	var url = '<?=base_url("api_admin/ecommerce/pelanggan/edit_status_as_admin/")?>' +ieid;
	$.ajax({
		url: url,
		type: 'POST',
		mimeType : "multipart/form-data",
		dataType: 'json',
		processData: false,
		contentType: false,
		data: fd
	}).done(function(respon) {
		NProgress.done();
		if(respon.status==200){
			gritter('<h4>Success</h4><p>Data edited successfuly</p>','success');
			$("#modal_change_status_admin").modal("hide");
			drTable.ajax.reload(null, false);  <!-- by Muhammad Sofi 28 January 2022 18:38 | Prevent table reload to first page after edit data -->
			$("#modal_change_status_admin").find("form").trigger("reset");
		}else{
			gritter('<h4>Failed</h4><p>'+respon.message+'</p>','danger');
		}
		$("#modal_edit").modal("hide");
	}).fail(function(){
		NProgress.done();
		setTimeout(function(){
			gritter('<h4>Error</h4><p>Cannot edit data right now, please try again later</p>','warning');
		}, 666);
		return false;
	});
});