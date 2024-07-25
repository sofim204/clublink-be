var growlPesan = '<h4>Error</h4><p>Tidak dapat diproses, silakan coba beberapa saat lagi!</p>';
var growlType = 'danger';
var drTable = {};
var ieid = '';
var b_user_id = '';
var email_creator = '';
var name_creator = '';
App.datatables();

let user_role = $("#user_role").val();

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

// credit : https://stackoverflow.com/questions/28899298/extract-the-text-out-of-html-string-using-javascript
function extractContent(s) {
  var span = document.createElement('span');
  span.innerHTML = s;
  return span.textContent || span.innerText;
};

// credit : https://stackoverflow.com/questions/573145/get-everything-after-the-dash-in-a-string-in-javascript
function getSecondPart(str) {
    return str.split('Reported')[0];
}

if(jQuery('#drTable').length>0){
	drTable = jQuery('#drTable')
	.on('preXhr.dt', function ( e, settings, data ){
		NProgress.start();
	}).DataTable({
			"columnDefs"		: 
                                [{
									"targets": [0], <!-- hide column -->
									"visible": false,
									"searchable": false
								},
								{
									"targets": [1], <!-- hide column -->
									"visible": false,
									"searchable": false
								},
								{
									"targets": [11], <!-- hide column -->
									"visible": false,
									"searchable": false
								}
                                ],
			"order"				: [[ 2, "desc" ]],
			"responsive"	  	: true,
			"bProcessing"		: true,
			"bServerSide"		: true,
			"sAjaxSource"		: "<?=base_url("api_admin/redemptionexchange/validity/"); ?>",
				"fnServerParams": function ( aoData ) {
					aoData.push(
						{ "name": "from_date", "value": $("#ifcdate_start").val() },
						{ "name": "to_date", "value": $("#ifcdate_end").val() },
						{ "name": "status", "value": $("#ifstatus").val() },
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
					<!-- console.log(response); -->
					$('#drTable > tbody').off('click', 'tr');
					$('#drTable > tbody').on('click', 'tr', function (e) {
						e.preventDefault();
						//var id = $(this).find("td").html();
						//ieid = id;
						var currentRow = $(this).closest("tr");
						var id = $('#drTable').DataTable().row(currentRow).data()[1]; <!-- to get data from specific column, change this "data()[id_column]" -->
						ieid = id;
						if(user_role != "marketing") {
							$("#modal_validation").modal("show");
						}
						$("#b_approve").attr("style", "display:none");
						$("#b_reject").attr("style", "display:none");
					});
					<!-- highlighting row -->
					//$('#drTable tbody').on('mouseenter', 'tr', function (e) {
					//	e.preventDefault();
					//	if ($(this).hasClass('highlight')) {
					//		$(this).removeClass('highlight');
					//	} else {
					//		drTable.$('tr.highlight').removeClass('highlight');
					//		$(this).addClass('highlight');
					//	}
					//}).on('mouseleave', 'tr',  function(){
					//	if ($(this).hasClass('highlight')) {
					//		$(this).removeClass('highlight');
					//	} else {
					//		drTable.$('tr.highlight').removeClass('highlight');
					//		$(this).addClass('highlight');
					//	}
					//});
					fnCallback(response);
				}).error(function (response, status, headers, config) {
					NProgress.done();
					gritter("<h4>Error</h4><p>Cant fetch data right now, please try again later</p>",'warning');
				});
			},
	});
	$('.dataTables_filter input').attr('placeholder', 'Search by name, email or telp').css({'width':'250px', 'display':'inline-block'}); <!-- show searchbox + add styling -->
	$("#apply-filter").on("click",function(e){
		e.preventDefault();
		drTable.ajax.reload();
	});
}

//tambah
$("#atambah").on("click",function(e){
	e.preventDefault();
	$("#modal_tambah").modal("show");
});
//change icon
$("#aicon_change").on("click",function(e){
	e.preventDefault();
	$("#modal_validation").modal("hide");
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
$("#modal_change_status_permanent_inactive").on("hidden.bs.modal",function(e){
	$("#modal_change_status_permanent_inactive").find("form").trigger("reset");
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
			var url = '<?=base_url('api_admin/redemptionexchange/validity/hapus/'); ?>'+ieid;
			$.get(url).done(function(response){
				NProgress.done();
				if(response.status==200){
					gritter('<h4>Success</h4><p>Data successfully deleted</p>','success');
				}else{
					gritter('<h4>Failed</h4><p>'+response.message+'</p>','danger');
				}
				drTable.ajax.reload();
				$("#modal_validation").modal("hide");
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
	$("#modal_validation").modal("hide");
	setTimeout(function(){
		//$("#modal_edit").modal("show");
		window.location = '<?=base_url_admin('redemptionexchange/validity/edit/');?>'+ieid;
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
	var url = '<?=base_url("api_admin/redemptionexchange/validity/change_icon/")?>'+ieid;
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
//	$("#modal_validation").modal("hide");
//	setTimeout(function(){
//		$("#modal_edit").modal("show");
//		//alert('masih dalam pengembangan');
//	},333);
//});

$("#adetail").on("click",function(e){
	e.preventDefault();
	$("#modal_validation").modal("hide");
	setTimeout(function(){
		//$("#modal_edit").modal("show");
		//alert('masih dalam pengembangan');
		//window.location ='<?=base_url_admin('redemptionexchange/validity/detail/')?>'+ieid;
		window.open("<?=base_url_admin('redemptionexchange/validity/detail/')?>"+ieid, "_blank");
	},333);
});

$("#reported_post_list").on("click",function(e){
	e.preventDefault();
	$("#modal_validation").modal("hide");
	setTimeout(function(){
		window.open("<?=base_url_admin('redemptionexchange/validity/reported/')?>", "_blank");
	},333);
});

$("#areport").on('click',function(e){
	e.preventDefault();
	NProgress.start();
	$.get("<?=base_url("api_admin/redemptionexchange/validity/report")?>"+ieid).done(function(dt){
		NProgress.done();
		$("#modal_validation").modal("hide");
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

<!-- by Muhammad Sofi 27 December 2021 14:43 | Fix issue button clear cannot reset filter and reload table -->
// clear filter 
$("#reset-filter").on("click",function(e){
	e.preventDefault();
	$("#ifcdate_start").val("");
	$("#ifcdate_end").val("");
	<!-- $("#select_customer").val('').trigger("change"); -->
	$("#ifstatus").val("");
	drTable.search('').columns().search('').draw();
	drTable.ajax.reload();
});

$("#refresh-table").on("click",function(e){
	e.preventDefault();
	drTable.ajax.reload(null,false);
});

$("#b_change_status_permanent_inactive").on('click', function() {
	$("#modal_validation").modal("hide");
	setTimeout(function(){
		$("#modal_change_status_permanent_inactive").modal("show");
	}, 300);
});

$("#b_delete_post").on("click", (e) => {
    e.preventDefault();
    let admin_name = $("#admin_name").text();
    let confirm_message = confirm("Are you sure to delete this post?"); // same like takedown
    if(confirm_message) {
        NProgress.start();
        var url = '<?=base_url() ?>api_admin/redemptionexchange/validity/delete_from_admin/?community_id=' +ieid+'&admin_name='+admin_name;
        $.get(url).done(function(response){
            NProgress.done();
            if(response.status=="200"){
                gritter('<h4>Success</h4><p>Post Successfully Deleted</p>','success');
                setTimeout(function(){
                    drTable.ajax.reload(null, false);
					$("#modal_validation").modal("hide");
                }, 400);
            }else{
                gritter('<h4>Failed</h4><p>'+response.message+'</p>','danger');
            }
        }).fail(function() {
            NProgress.done();
            gritter('<h4>Error</h4><p>Error, Please try again</p>','danger');
        });
    } 
});

$("#b_restore_post").on("click", (e) => {
    e.preventDefault();
    let admin_name = $("#admin_name").text();
    let confirm_message = confirm("Are you sure to restore this post?");
    if(confirm_message) {
        NProgress.start();
        var url = '<?=base_url() ?>api_admin/redemptionexchange/validity/restore_from_admin/?community_id=' +ieid+'&admin_name='+admin_name;
        $.get(url).done(function(response){
            NProgress.done();
            if(response.status=="200"){
                gritter('<h4>Success</h4><p>Post Successfully Restored</p>','success');
                setTimeout(function(){
                    drTable.ajax.reload(null, false);
					$("#modal_validation").modal("hide");
                }, 400);
            }else{
                gritter('<h4>Failed</h4><p>'+response.message+'</p>','danger');
            }
        }).fail(function() {
            NProgress.done();
            gritter('<h4>Error</h4><p>Error, Please try again</p>','danger');
        });
    } 
});

$("#form_change_status_permanent_inactive").on("submit",function(e){
	e.preventDefault();
	NProgress.start();
	var fd = new FormData($("#form_change_status_permanent_inactive")[0]);
	var url = '<?=base_url("api_admin/ecommerce/pelanggan/edit_status_permanent_inactive/")?>' +b_user_id;
	let confirm_message = confirm("are you sure to permanent inactive this account?");
	if(confirm_message) {
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
	}
});

$(document).ready(function() {
	$("#select_customer").select2({
		<!-- START by Muhammad Sofi 26 January 2022 13:37 | get data user(b_user_id) from table c_community -->
		//placeholder: "--Select User--",
		ajax: { 
			<!-- url: "<?= base_url('api_admin/ecommerce/pelanggan/getcustomerajax') ?>", -->
			url: "<?= base_url('api_admin/redemptionexchange/validity/getCustomer') ?>",
			type: "post",
			dataType: 'json',
			delay: 250,
			data: function (params) {
				return {
					search: params.term, // search term
				};
			},
			processResults: function (response) {
				response.unshift({id: '', text: '===== Cancel your selection ====='})
				return {
					results: response
				};
			}
		}
		<!-- END by Muhammad Sofi 26 January 2022 13:37 | get data user(b_user_id) from table c_community -->
	});

	<!-- by Muhammad Sofi 11 January 2022 19:22 | change datepicker css and set input datepicker readonly -->
	<!-- initialize datepicker -->
	$('#ifcdate_start, #ifcdate_end').datepicker();
    $('#ifcdate_start, #ifcdate_end').datepicker('setDate', 'today').val("");

	$("#ifcdate_start, #ifcdate_end").change(function(){
		$('.datepicker').hide(); <!-- hide datepicker after select a date -->
	});
});




<!-- MODAL VALIDATION -->
//edit
$("#modal_validation").on("shown.bs.modal",function(e){
	//
	var url = '<?=base_url(); ?>api_admin/redemptionexchange/validity/detail/'+ieid;
	NProgress.start();
	$("#ivnama").html("<i>Loading . . .</i>");
    $("#ivtype").html("<i>Loading . . .</i>");
    $("#ivre_name").html("<i>Loading . . .</i>");
    $("#ivtelp").html("<i>Loading . . .</i>");
    $("#ivcost_spt").html("<i>Loading . . .</i>");
    $("#ivamount_get").html("<i>Loading . . .</i>");
    $("#ivcdate").html("<i>Loading . . .</i>");

	$("#ivunama").html("<i>Loading . . .</i>");
	$("#ivuemail").html("<i>Loading . . .</i>");
	$("#ivureg_date").html("<i>Loading . . .</i>");
	$("#ivutelp").html("<i>Loading . . .</i>");
	$("#ivutotal_recruited").html("<i>Loading . . .</i>");
	$("#ivuis_influencer").html("<i>Loading . . .</i>");
	$("#ivuwallet_balance").html("<i>Loading . . .</i>");
	$("#ivuip_address").html("<i>Loading . . .</i>");
	$("#ivupermanent_inactive").html("<i>Loading . . .</i>");
	$("#ivurecommender").html("<i>Loading . . .</i>");
	$("#ivudevice_id").html("<i>Loading . . .</i>");
	$("#ivuaddress").html("<i>Loading . . .</i>");
	$("#ivusignup_method").html("<i>Loading . . .</i>");
	$.get(url).done(function(response){
		NProgress.done();
		if(response.status==200){
			var dta = response.data;
			$("#b_approve").attr("style", "display:none");
			$("#b_reject").attr("style", "display:none");
			if(dta.status_no == 1) {
				$("#b_approve").removeAttr("style");
				$("#b_reject").removeAttr("style");
			} else {
				$("#b_approve").attr("style", "display:none");
				$("#b_reject").attr("style", "display:none");
			}
			$("#ivid").text(dta.id);
			$("#ivnama").text(dta.user);
			$("#ivtype").text(dta.type);
			$("#ivre_name").text(dta.redemption_exchange_name);
			$("#ivtelp").text(dta.telp);
			$("#ivcost_spt").text(dta.cost_spt);
			$("#ivamount_get").text(dta.amount_get);
			$("#ivcdate").text(dta.cdate);
			$("#ivreason_rejected").html(dta.note_rejected);

			$("#ivunama").html("<b>"+dta.user_name+"</b>");
			$("#ivuemail").html("<b>"+dta.user_email+"</b>");
			$("#ivureg_date").html("<b>"+dta.user_reg_date+"</b>");
			$("#ivutelp").text(dta.user_telp);
			$("#ivutotal_recruited").text(dta.user_total_recruited);
			$("#ivuis_influencer").text(dta.user_is_influencer);
			$("#ivuwallet_balance").html("<b>"+dta.user_wallet_balance+"</b>");
			$("#ivuip_address").text(dta.user_ip_address);
			$("#ivupermanent_inactive").text(dta.user_permanent_inactive);
			$("#ivurecommender").text(dta.user_recommender);
			$("#ivudevice_id").text(dta.user_device_id);
			$("#ivuaddress").text(dta.user_address);
			$("#ivusignup_method").text(dta.user_signup_method);
		}else{
			gritter('<h4>Error</h4><p>Invalid ID or ID has been deleted</p>','danger');
			setTimeout(function(){
				drTable.ajax.reload(null, false);
				$("#modal_validation").modal("hide");
				$("#modal_validation").find("form").trigger("reset");
			}, 1000);
		}
	});
});
$("#modal_validation").on("hidden.bs.modal",function(e){
    $("#ivnama").text("");
    $("#ivtype").text("");
    $("#ivre_name").text("");
    $("#ivtelp").text("");
    $("#ivcost_spt").text("");
    $("#ivamount_get").text("");
    $("#ivcdate").text("");
    $("#ivreason_rejected").html("");

	$("#ivunama").text("");
	$("#ivuemail").text("");
	$("#ivureg_date").text("");
	$("#ivutelp").text("");
	$("#ivutotal_recruited").text("");
	$("#ivuis_influencer").text("");
	$("#ivuwallet_balance").text("");
	$("#ivuip_address").text("");
	$("#ivupermanent_inactive").text("");
	$("#ivurecommender").text("");
	$("#ivudevice_id").text("");
	$("#ivuaddress").text("");
	$("#ivusignup_method").text("");

	$("#modal_validation").find("form").trigger("reset");
});

$("#fvalidation").on("submit",function(e){
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
				$("#modal_validation").modal("hide");
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

<!-- APPROVE -->
$("#b_approve").on("click", (e) => {
    e.preventDefault();
    let confirm_message = confirm("Are you sure to approve this data?"); // same like takedown
    if(confirm_message) {
        NProgress.start();
        var url = '<?=base_url() ?>api_admin/redemptionexchange/validity/approve/?id=' +ieid;
        $.get(url).done(function(response){
            NProgress.done();
            if(response.status=="200"){
                gritter('<h4>Success</h4><p>Post Successfully Approved</p>','success');
                setTimeout(function(){
                    drTable.ajax.reload(null, false);
					$("#modal_validation").modal("hide");
                }, 400);
            }else if(response.status=="201"){
				alert('Post Rejected, because the SPT not enough.');
                gritter('<h4>Rejected</h4><p>Post Rejected, because the SPT not enough.</p>','warning');
                setTimeout(function(){
                    drTable.ajax.reload(null, false);
					$("#modal_validation").modal("hide");
                }, 400);
            }else{
                gritter('<h4>Failed</h4><p>'+response.message+'</p>','danger');
				setTimeout(function(){
					drTable.ajax.reload(null, false);
					$("#modal_validation").modal("hide");
					$("#modal_validation").find("form").trigger("reset");
				}, 1000);
            }
        }).fail(function() {
            NProgress.done();
            gritter('<h4>Error</h4><p>Error, Please try again</p>','danger');
			setTimeout(function(){
				drTable.ajax.reload(null, false);
				$("#modal_validation").modal("hide");
				$("#modal_validation").find("form").trigger("reset");
			}, 1000);
        });
    } 
});

<!-- REJECT -->
$("#b_reject").on("click", (e) => {
    e.preventDefault();
	$("#modal_reject_option").modal("show");
	$("#modal_validation").modal("hide");
});

<!-- REJECT -->
$("#b_reject_option_1").on("click", (e) => {
    e.preventDefault();
    let confirm_message = confirm("Are you sure to reject data with this reason?");
    if(confirm_message) {
        NProgress.start();
		var reason = 1;
        var url = '<?=base_url() ?>api_admin/redemptionexchange/validity/reject/?id=' +ieid +'&reason=' +reason;
        $.get(url).done(function(response){
            NProgress.done();
            if(response.status=="200"){
                gritter('<h4>Success</h4><p>Data Successfully Rejected</p>','success');
                setTimeout(function(){
                    drTable.ajax.reload(null, false);
					$("#modal_reject_option").modal("hide");
                }, 400);
            }else{
                gritter('<h4>Failed</h4><p>'+response.message+'</p>','danger');
				setTimeout(function(){
					drTable.ajax.reload(null, false);
					$("#modal_validation").modal("hide");
					$("#modal_validation").find("form").trigger("reset");
					$("#modal_reject_option").modal("hide");
				}, 1000);
            }
        }).fail(function() {
            NProgress.done();
            gritter('<h4>Error</h4><p>Error, Please try again</p>','danger');
			setTimeout(function(){
				drTable.ajax.reload(null, false);
				$("#modal_validation").modal("hide");
				$("#modal_validation").find("form").trigger("reset");
				$("#modal_reject_option").modal("hide");
			}, 1000);
        });
    } 
});

<!-- REJECT -->
$("#b_reject_option_2").on("click", (e) => {
    e.preventDefault();
    let confirm_message = confirm("Are you sure to reject data with this reason?");
    if(confirm_message) {
        NProgress.start();
		var reason = 2;
        var url = '<?=base_url() ?>api_admin/redemptionexchange/validity/reject/?id=' +ieid +'&reason=' +reason;
        $.get(url).done(function(response){
            NProgress.done();
            if(response.status=="200"){
                gritter('<h4>Success</h4><p>Data Successfully Rejected</p>','success');
                setTimeout(function(){
                    drTable.ajax.reload(null, false);
					$("#modal_reject_option").modal("hide");
                }, 400);
            }else{
                gritter('<h4>Failed</h4><p>'+response.message+'</p>','danger');
				setTimeout(function(){
					drTable.ajax.reload(null, false);
					$("#modal_validation").modal("hide");
					$("#modal_validation").find("form").trigger("reset");
					$("#modal_reject_option").modal("hide");
				}, 1000);
            }
        }).fail(function() {
            NProgress.done();
            gritter('<h4>Error</h4><p>Error, Please try again</p>','danger');
			setTimeout(function(){
				drTable.ajax.reload(null, false);
				$("#modal_validation").modal("hide");
				$("#modal_validation").find("form").trigger("reset");
				$("#modal_reject_option").modal("hide");
			}, 1000);
        });
    } 
});

<!-- REJECT -->
$("#b_reject_option_3").on("click", (e) => {
    e.preventDefault();
    let confirm_message = confirm("Are you sure to reject data with this reason?");
    if(confirm_message) {
        NProgress.start();
		var reason = 3;
        var url = '<?=base_url() ?>api_admin/redemptionexchange/validity/reject/?id=' +ieid +'&reason=' +reason;
        $.get(url).done(function(response){
            NProgress.done();
            if(response.status=="200"){
                gritter('<h4>Success</h4><p>Data Successfully Rejected</p>','success');
                setTimeout(function(){
                    drTable.ajax.reload(null, false);
					$("#modal_reject_option").modal("hide");
                }, 400);
            }else{
                gritter('<h4>Failed</h4><p>'+response.message+'</p>','danger');
				setTimeout(function(){
					drTable.ajax.reload(null, false);
					$("#modal_validation").modal("hide");
					$("#modal_validation").find("form").trigger("reset");
					$("#modal_reject_option").modal("hide");
				}, 1000);
            }
        }).fail(function() {
            NProgress.done();
            gritter('<h4>Error</h4><p>Error, Please try again</p>','danger');
			setTimeout(function(){
				drTable.ajax.reload(null, false);
				$("#modal_validation").modal("hide");
				$("#modal_validation").find("form").trigger("reset");
				$("#modal_reject_option").modal("hide");
			}, 1000);
        });
    } 
});