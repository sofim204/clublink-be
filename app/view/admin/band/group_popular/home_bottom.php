var growlPesan = '<h4>Error</h4><p>Tidak dapat diproses, silakan coba beberapa saat lagi!</p>';
var growlType = 'danger';
var drTable = {};
var ieid = '';
App.datatables();

let user_role = $("#user_role").val();

function gritter(pesan,jenis="info"){
	$.bootstrapGrowl(pesan, {
		type: jenis,
		delay: 2500,
		allow_dismiss: true
	});
}

$(document).ready(function() {

	$('#icdate, #iedate').datepicker();
	$('#icdate, #iedate').datepicker('setDate', 'today').val('');
	$("#icdate, #iedate").change(function(){
		$('.datepicker').hide(); <!-- hide datepicker after select a date -->
	});

	if(jQuery('#drTable').length>0){
		drTable = jQuery('#drTable')
		.on('preXhr.dt', function ( e, settings, data ){
			NProgress.start();
		}).DataTable({
				"columnDefs"		: [{
										"targets": [0,1], <!-- hide column -->
										"visible": false,
										"searchable": false
									}],
				"order"				: [[ 8, "desc" ]], <!-- by Muhammad Sofi 11 January 2022 9:47 | add & edit input priority, show priority in datatable -->
				"responsive"	  	: true,
				"bProcessing"		: true,
				"bServerSide"		: true,
				"sAjaxSource"		: "<?=base_url("api_admin/band/group/popular_club"); ?>",
				"fnServerData"	: function (sSource, aoData, fnCallback, oSettings) {
					oSettings.jqXHR = $.ajax({
						dataType 	: 'json',
						method 		: 'POST',
						url 		: sSource,
						data 		: aoData
					}).success(function (response, status, headers, config) {
						NProgress.done();
						console.log(response);
						$('#drTable > tbody').off('click', 'tr');
						$('#drTable > tbody').on('click', 'tr', function (e) {
							e.preventDefault();
							var currentRow = $(this).closest("tr");
							var id = $('#drTable').DataTable().row(currentRow).data()[1]; <!-- to get data from specific column, change this "data()[id_column]" -->
							ieid = id;
							$("#breport").hide();
							var is_take_down = $('#drTable').DataTable().row(currentRow).data()[12];
							var is_active = $('#drTable').DataTable().row(currentRow).data()[14];
							var club_name = $('#drTable').DataTable().row(currentRow).data()[3];
							var total_people = $('#drTable').DataTable().row(currentRow).data()[6];

							if (is_take_down == 1 || is_active == 0) {
								$("#breport").hide();
							}else{
								$("#breport").show();
							}

							<!-- pass data -->
							$("#club_name").val(club_name)
							$("#club_choosed_id").val(id)
							$("#total_people_text").val(total_people)

							$("#modal_option").modal("show");
						});
						fnCallback(response);
					}).error(function (response, status, headers, config) {
						NProgress.done();
						gritter("<h4>Error</h4><p>Cant fetch data right now, please try again later</p>",'warning');
					});
				},
		});
		$('.dataTables_filter input').attr('placeholder', 'Search by group name').css({'width':'250px', 'display':'inline-block'});
	}

	$("#button_add_to_popular_club_homepage").on("click", function() {
		$("#modal_option").modal("hide")
		setTimeout(function(){
			$("#modal_add_popular_club_to_homepage").modal("show");
		}, 200);
	})

	$("#form_add_popular_club_to_homepage").on("submit",function(e){
		e.preventDefault();
		let start_date = $('#icdate').val()
		let end_date = $('#iedate').val()

		if(start_date == "" || end_date == "") {
			alert("please, choose date first")
		} else {
			NProgress.start();
			var url = '<?=base_url("api_admin/band/popular_club/add_popular_club_to_homepage/")?>';
			$.ajax({
				type: $(this).attr('method'),
				url: url,
				data: new FormData(this),
				processData: false,
				contentType: false,
				success: function(respon){
					if(respon.status==200){
						gritter('<h4>Success</h4><p>Data added successfully</p>','success');
						setTimeout(function(){
							NProgress.done();
							drTable.ajax.reload();
							$("#modal_add_popular_club_to_homepage").modal("hide");
						},1000);
					}else{
						NProgress.done();
						gritter('<h4>Failed</h4><p>'+respon.message+'</p>','danger');
					}
				},
				error:function(){
					setTimeout(function(){
						$("#modal_add_popular_club_to_homepage").modal("hide");
						NProgress.done();
						gritter('<h4>Error</h4><p>Cant change right now, please try again later</p>','warning');
					}, 666);
					return false;
				}
			});
		}
	})

	$("#button_check_club").on("click",function(e){
		e.preventDefault();
		setTimeout(function(){
			window.open('<?=base_url_admin('band/group/detail_group_post/')?>' + ieid, "_blank");
		},333);
	});

	$("#modal_add_popular_club_to_homepage").on("hidden.bs.modal",function(e){
		$("#modal_add_popular_club_to_homepage").find("form").trigger("reset");
	});
})

//tambah
$("#atambah").on("click",function(e){
	e.preventDefault();
	$("#modal_tambah").modal("show");
});
//change icon
$("#aicon_change").on("click",function(e){
	e.preventDefault();
	$("#modal_option").modal("hide");
	setTimeout(function(){
		$("#ficon_change").trigger("reset");
		$("#modal_icon_change").modal("show");
	},500);
});
//listener on modal tambah show
$("#modal_tambah").on("shown.bs.modal",function(e){
	$("#inama").off("keyup");
	$("#inama").on("keyup",function(e){
		e.preventDefault();
		var x = $(this).val();
		x = x.trim();
		$("#imeta_title").val(x);
		var slug = convertToSlug(x);
		var code = convertToCode(x);
		var lastFour = code.substr(code.length - 4);
		lastFour = lastFour.toUpperCase();
		$("#islug").val(slug);
		$("#igslug").html('http://<?=base_url(); ?>'+slug);
		$("#imtitle").val(x);
		$("#igmtitle").html(x);
	});
	$("#islug").off("change");
	$("#islug").on("change",function(e){
		var slug = $(this).val();
		$("#igslug").html('http://<?=base_url(); ?>'+slug);
	});

	$("#imdescription").off("keyup");
	$("#imdescription").on("keyup",function(e){
		e.preventDefault();
		var x = $(this).val();
		x = x.trim();
		$("#igmdescription").html(x);
	});

	$("#imkeyword").off("keyup");
	$("#imkeyword").on("keyup",function(e){
		e.preventDefault();
		var x = $(this).val();
		x = x.trim();
		$("#igmkeyword").html(x);
	});
	$("#iutype").trigger('change');
});

$("#modal_tambah").on("hidden.bs.modal",function(e){
	$("#modal_tambah").find("form").trigger("reset");
});

$("#ftambah").on("submit",function(e){
	e.preventDefault();

	//get al value from form as fd formdata object
	var fd = new FormData($(this)[0]);
	var url = '<?=base_url("api_admin/produk/kategori/tambah/"); ?>';

	$.ajax({
		type: $(this).attr('method'),
		url: url,
		data: fd,
		processData: false,
		contentType: false,
		success: function(respon){
			if(respon.status==200){
				growlPesan = '<h4>Success</h4><p>Proses tambah data telah berhasil!</p>';
				drTable.ajax.reload();
				growlType = 'success';
				$("#modal_tambah").modal("hide");
			}else{
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



$("#fedit").on("submit",function(e){
	e.preventDefault();
	var fd = new FormData($(this)[0]);
	var url = '<?=base_url("api_admin/produk/kategori/edit/"); ?>'+ieid;
	$.ajax({
		type: $(this).attr('method'),
		url: url,
		data: fd,
		processData: false,
		contentType: false,
		success: function(respon){
			if(respon.status==200){
				gritter('<h4>Success</h4><p>Proses ubah data telah berhasil!</p>','success');
				drTable.ajax.reload();
				$("#modal_edit").modal("hide");
			}else{
				gritter('<h4>Failed</h4><p>'+respon.message+'</p>','warning');
			}
		},
		error:function(){
			gritter('<h4>Error</h4><p>Proses ubah data tidak bisa dilakukan, coba beberapa saat lagi</p>','danger');
			return false;
		}
	});
});

//report
$("#breport").on("click",function(e){
	e.preventDefault();
	if(ieid){
		var c = confirm('Are you sure report this data?');
		if(c){
			NProgress.start();
			var url = '<?=base_url('api_admin/band/group/report/'); ?>'+ieid;
			$.get(url).done(function(response){
				NProgress.done();
				if(response.status==200){
					gritter('<h4>Success</h4><p>Data successfully report</p>','success');
				}else{
					gritter('<h4>Failed</h4><p>'+response.message+'</p>','danger');
				}
				drTable.ajax.reload(null, false);
				$("#modal_option").modal("hide");
				$("#modal_edit").modal("hide");
			}).fail(function() {
				NProgress.done();
				gritter('<h4>Error</h4><p>Cant report data right now, please try again later</p>','danger');
			});
		}
	}
});

//option
$("#aedit").on("click",function(e){
	e.preventDefault();
	$("#modal_option").modal("hide");
	setTimeout(function(){
		//$("#modal_edit").modal("show");
		window.location = '<?=base_url_admin('band/group/edit/');?>'+ieid;
	},333);
});

//detail
$("#adetail").on("click",function(e){
	e.preventDefault();
	$("#modal_option").modal("hide");
	setTimeout(function(){
		//window.location='<?=base_url_admin('band/group/detail_group_post/')?>'+ieid;
		window.open('<?=base_url_admin('band/group/detail_group_post/')?>'+ieid, "_blank");
	},333);
});
$("#iutype").on("change",function(e){
	e.preventDefault();
	var v = $(this).val();
	if(v.toLowerCase() == 'kategori' || v.toLowerCase() == 'tag'){
		$("#ib_kategori_id").prop('disabled',1);
	}else{
		$("#ib_kategori_id").removeAttr('disabled');
	}
});

function genKode(){
	var n = $("#inama").val().toUpperCase().replace(/[^\w\s]/gi, '');
	var ns = n.split(" ");
	if(ns.length>=2){
		n = ns[0].charAt(0)+ns[1].charAt(0);
	}else{
		n = n.slice(0,2);
	}
	var u = $("#iutype option:selected").attr('data-kode').toUpperCase();
	var p = '';
	if($("#ib_kategori_id option:selected").attr('data-kode') !== undefined){
		p = $("#ib_kategori_id option:selected").attr('data-kode').toUpperCase().slice(0,2);
	}
	$("#ikode").val(p+n+u);
}
$("#inama").on("blur",function(e){e.preventDefault(); genKode()});
$("#iutype").on("blur",function(e){e.preventDefault(); genKode()});
$("#ib_kategori_id").on("blur",function(e){e.preventDefault(); genKode()});

function genKodeEdit(){
	var n = $("#ienama").val().toUpperCase().replace(/[^\w\s]/gi, '');
	var ns = n.split(" ");
	if(ns.length>=2){
		n = ns[0].charAt(0)+ns[1].charAt(0);
	}else{
		n = n.slice(0,2);
	}

	var u = $("#ieutype option:selected").attr('data-kode').toUpperCase();
	var p = $("#ieb_kategori_id option:selected").attr('data-kode').toUpperCase().slice(0,2);
	$("#iekode").val(p+n+u+ieid);
}
$("#ienama").on("blur",function(e){e.preventDefault(); genKodeEdit()});
$("#ieutype").on("blur",function(e){e.preventDefault(); genKodeEdit()});
$("#ieb_kategori_id").on("blur",function(e){e.preventDefault(); genKodeEdit()});

$("#ficon_change").on("submit",function(e){
	e.preventDefault();
	NProgress.start();
	var url = '<?=base_url("api_admin/band/group/change_icon/")?>'+ieid;
	$.ajax({
		type: $(this).attr('method'),
		url: url,
		data: new FormData(this),
		processData: false,
		contentType: false,
		success: function(respon){
			if(respon.status==200){
				gritter('<h4>Success</h4><p>Icon changed successfully</p>','success');
				setTimeout(function(){
					NProgress.done();
					drTable.ajax.reload();
					$("#modal_icon_change").modal("hide");
				},1000);
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
})