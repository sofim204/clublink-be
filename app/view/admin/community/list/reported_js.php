var growlPesan = '<h4>Error</h4><p>Tidak dapat diproses, silakan coba beberapa saat lagi!</p>';
var growlType = 'danger';
var drTable = {};
var ieid = '';
var community_id = '';
var buser_id = '';
var flag_takedown = '';
App.datatables();

function updateCkEditor(){
	for (instance in CKEDITOR.instances) {
		CKEDITOR.instances[instance].updateElement();
		//$("#"+instance).val(CKEDITOR.instances[instance].getData());
	}
}
function convertToSlug(Text){
	return Text
		.toString().toLowerCase()
		.replace(/\s+/g, '-')           // Replace spaces with -
		.replace(/[^\w\-]+/g, '')       // Remove all non-word chars
		.replace(/\-\-+/g, '-')         // Replace multiple - with single -
		.replace(/^-+/, '')             // Trim - from start of text
		.replace(/-+$/, '');
}
function convertToKeyword(Text){
	return Text
		.toString().toLowerCase()
		.replace(/\s+/g, ',')           // Replace spaces with -
		//.replace(/[^\w\-]+/g, '')       // Remove all non-word chars
		.replace(/\-\-+/g, ',')         // Replace multiple - with single -
		.replace(/^-+/, '')             // Trim - from start of text
		.replace(/-+$/, '');
}
function convertToCode(Text){
	return Text
		.toString().toLowerCase()
		.replace(/\s+/g, ''+makeid())           // Replace spaces with -
		.replace(/[^\w\-]+/g, '')       // Remove all non-word chars
		.replace(/\-\-+/g, ''+makeid())         // Replace multiple - with single -
		.replace(/^-+/, '')             // Trim - from start of text
		.replace(/-+$/, '');
}
function makeid(){
	var i=0
	var text = "";
	var possible = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
	for(i=0;1>i;i++){
		text += possible.charAt(Math.floor(Math.random() * possible.length));
		return text;
	}
}
function gritter(pesan,jenis="info"){
	$.bootstrapGrowl(pesan, {
		type: jenis,
		delay: 2500,
		allow_dismiss: true
	});
}


if(jQuery('#drTable').length>0){
	drTable = jQuery('#drTable')
	.on('preXhr.dt', function ( e, settings, data ){
		NProgress.start();
	}).DataTable({
			"columnDefs"	: [{
								"targets": [1], <!-- hide column -->
								"visible": false,
								"searchable": false
							},{
								"targets": [2], <!-- hide column -->
								"visible": false,
								"searchable": false
							},{
								"targets": [3], <!-- hide column -->
								"visible": false,
								"searchable": false
							}],
			"order"				: [[ 0, "asc" ]],
			"responsive"	  	: true,
			"bProcessing"		: true,
			"bServerSide"		: true,
			"sAjaxSource"		: "<?=base_url("api_admin/community/listing/reported"); ?>",
			"fnServerParams": function ( aoData ) {
				aoData.push(
					{ "name": "from_date", "value": $("#from_date").val() },
					{ "name": "to_date", "value": $("#to_date").val() },
					{ "name": "user_id", "value": $("#select_admin").val() },
				);
			},
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
						//var id = $(this).find("td").html();
						var currentRow = $(this).closest("tr");
						var id = $('#drTable').DataTable().row(currentRow).data()[0]; // no
						var com_id = $('#drTable').DataTable().row(currentRow).data()[1];
						var bu_id = $('#drTable').DataTable().row(currentRow).data()[2];
						var check_status = $('#drTable').DataTable().row(currentRow).data()[3];
						flag_takedown = check_status;
						ieid = id;
						community_id = com_id;
						buser_id = bu_id;

						if(flag_takedown == "takedown") {
							//$("#atakedown").hide();
							$("#modal_option").modal("hide");
							alert("Sorry, this post already taken down");
						} else if(flag_takedown == "reported"){
							//$("#atakedown").show();
							$("#modal_option").modal("show");
						}

						//$("#modal_option").modal("show");
					});
					fnCallback(response);
				}).error(function (response, status, headers, config) {
					NProgress.done();
					gritter("<h4>Error</h4><p>Cant fetch data right now, please try again later</p>",'warning');
				});
			},
	});
	$('.dataTables_filter input').attr('placeholder', 'Search reported post');
}

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

	//if using ckeditor
	updateCkEditor();
	//get al value from form as fd formdata object
	var fd = new FormData($(this)[0]);
	var url = '<?=base_url("api_admin/produk/listing/tambah/"); ?>';

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

$(".select2").select2();

//-- start  by ali - 18 january 2023 14:42 add export excel-->
$("#download").on("click", function(e) {
	e.preventDefault()
	if($("#from_date").val() == null || $("#from_date").val() == "") {
        alert("From date cannot be null");
	} else if($("#to_date").val() == null || $("#to_date").val() == "") {
        alert("To date cannot be null");
	} else {
        $.ajax({
            url: '<?=base_url("a/community/listing/export")?>',
            type: "POST",
            dataType: "json",
            data: {
                export: true,
                userId: $("#select_admin").val(),
                formDate: $("#from_date").val(),
                toDate: $("#to_date").val(),
            },
            success: function(data){
				var $a = $("<a>");

				$a.attr("href",data.file);
				$("body").append($a);
				$a.attr("download", data.filename);
				$a[0].click();
				$a.remove();
            },	
            error: function(data){
				console.log(data);
            }
        });
    }
});

$("#filter").on("click", function() {
	drTable.ajax.reload();
})

$("#reset").on("click", function() {
	$("#from_date").val("")
	$("#to_date").val("")
	$("#select_admin").val("")

	drTable.ajax.reload();
});

$('#from_date, #to_date').datepicker('setDate', 'today').val("");


//end

//edit
$("#modal_edit").on("shown.bs.modal",function(e){
	//
	var url = '<?=base_url(); ?>api_admin/produk/listing/detail/'+ieid;
	$.get(url).done(function(response){
		if(response.status==200){
			var dta = response.data;
			$("#ieid").val(dta.id);
			$("#ieutype").val(dta.utype);
			$("#iekode").val(dta.kode);
			$("#ieb_kategori_id").val(dta.b_kategori_id);
			$("#ienama").val(dta.nama);
			$("#ieslug").val(dta.slug);
			$("#iemtitle").val(dta.mtitle);
			$("#iemkeyword").val(dta.mkeyword);
			$("#iemdescription").val(dta.mdescription);

			$("#ieprioritas").val(dta.prioritas);
			$("#ieis_visible").val(dta.is_visible);
			$("#ieis_active").val(dta.is_active);
			setTimeout(function(){
				CKEDITOR.instances.iedeskripsi.setData(dta.deskripsi);
			},2000);
		}else{
			gritter('<h4>Error</h4><p>Tidak dapat mengambil detail data</p>','danger');
		}
	});
});
$("#modal_edit").on("hidden.bs.modal",function(e){
	$("#modal_edit").find("form").trigger("reset");
});

$("#fedit").on("submit",function(e){
	e.preventDefault();
	var fd = new FormData($(this)[0]);
	var url = '<?=base_url("api_admin/produk/listing/edit/"); ?>'+ieid;
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

//hapus
$("#bhapus").on("click",function(e){
	e.preventDefault();
	if(ieid){
		var c = confirm('Are you sure?');
		if(c){
			NProgress.start();
			var url = '<?=base_url('api_admin/community/listing/hapus/'); ?>'+ieid;
			$.get(url).done(function(response){
				NProgress.done();
				if(response.status==200){
					gritter('<h4>Success</h4><p>Data successfully deleted</p>','success');
				}else{
					gritter('<h4>Failed</h4><p>'+response.message+'</p>','danger');
				}
				drTable.ajax.reload();
				$("#modal_option").modal("hide");
				$("#modal_edit").modal("hide");
			}).fail(function() {
				NProgress.done();
				gritter('<h4>Error</h4><p>Cant deteled data right now, please try again later</p>','danger');
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
		window.location = '<?=base_url_admin('community/listing/edit/');?>'+ieid;
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
	var url = '<?=base_url("api_admin/community/listing/change_icon/")?>'+ieid;
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

// ===========================================================================
// ||                                                                       ||
// ||                            D E T A I L                                ||
// ||                                                                       ||
// ===========================================================================
//$("#adetail").on("click",function(e){
//	e.preventDefault();
//	$("#modal_option").modal("hide");
//	setTimeout(function(){
//		$("#modal_edit").modal("show");
//		//alert('masih dalam pengembangan');
//	},333);
//});

$("#adetail").on("click",function(e){
	e.preventDefault();
	$("#modal_option").modal("hide");
	setTimeout(function(){
		//$("#modal_edit").modal("show");
		//alert('masih dalam pengembangan');
		window.location ='<?=base_url_admin('community/listing/detail/')?>'+ieid;
	},333);
});

<!-- START by Muhammad Sofi 14 January 2022 18:09 | change query to get reported community post data -->
$("#aignore").on('click',function(e){
	e.preventDefault();
	NProgress.start();
	//$.get("<?=base_url("api_admin/community/listing/report/0/")?>"+ieid).done(function(dt){
	<!-- is_report set to 0 -->
	var url = '<?=base_url() ?>api_admin/community/listing/report/0/?c_community_id='+community_id+'&b_user_id='+buser_id;
	$.get(url).done(function(dt){
		NProgress.done();
		$("#modal_option").modal("hide");
		drTable.ajax.reload(null,false);
		if(dt.status == "200"){
			gritter("<h4>Success</h4><p>Post Ignored.</p>",'success');
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

$("#atakedown").on('click',function(e){
	e.preventDefault();
	NProgress.start();
	//$.get("<?=base_url("api_admin/community/listing/report/1/")?>"+ieid).done(function(dt){
	<!-- is_takedown set to 1 -->

    let admin_name = $("#admin_name").text();

	<!-- add confirmation popup -->
	var message_popup = confirm("Are you sure you want to takedown this post, your action can't be undone");
	if(message_popup){ 
		var url = '<?=base_url() ?>api_admin/community/listing/report/1/?c_community_id='+community_id+'&b_user_id='+buser_id+'&admin_name='+admin_name;
		$.get(url).done(function(dt){
			NProgress.done();
			$("#modal_option").modal("hide");
			drTable.ajax.reload(null,false);
			if(dt.status == "200"){
				gritter("<h4>Success</h4><p>Post Taken down.</p>",'success');
			}else if(dt.status == 999){
				gritter("<h4>Warning</h4><p>"+dt.message+"</p>",'warning');
			}else{
				gritter("<h4>Failed</h4><p>"+dt.message+"</p>",'danger');
			}
		}).fail(function(e){
			NProgress.done();
			gritter("<h4>Error</h4><p>Cant change user activation right now, please try again.</p>",'warning');
		})
	} else {
		$("#modal_option").modal("hide");
		NProgress.done();
	}

	
});
<!-- END by Muhammad Sofi 14 January 2022 18:09 | change query to get reported community post data -->