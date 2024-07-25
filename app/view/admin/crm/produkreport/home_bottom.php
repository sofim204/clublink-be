var growlPesan = '<h4>Error</h4><p>Tidak dapat diproses, silakan coba beberapa saat lagi!</p>';
var growlType = 'danger';
var drTable = {};
var ieid = '';
var buser_id = ''; // user_id_reporter
App.datatables();
function gritter(pesan,jenis='info'){
	$.bootstrapGrowl(pesan, {
		type: jenis,
		delay: 500,
		allow_dismiss: true
	});
}

if(jQuery('#drTable').length>0){
	drTable = jQuery('#drTable')
	.on('preXhr.dt', function ( e, settings, data ){
		NProgress.start();
	}).DataTable({
			"columnDefs"		: [{
									"targets": [1], <!-- hide column -->
									"visible": false,
									"searchable": false
								},{
									"targets": [2], <!-- hide column -->
									"visible": false,
									"searchable": false
								}],	
			"order"				: [[ 0, "asc" ]],//changed table sort to created date by Rendi Fajrianto - 26 october 2020 17:22
			"responsive"	  	: true,
			"bProcessing"		: true,
			"bServerSide"		: true,
			"sAjaxSource"		: "<?=base_url("api_admin/crm/produkreport/")?>",
			"fnServerParams": function ( aoData ) {
				aoData.push(
					{ "name": "b_kategori_id", "value": $("#ifb_kategori_id").val() },
					{ "name": "price_min", "value": $("#ifprice_min").val() },
					{ "name": "price_max", "value": $("#ifprice_max").val() },
					{ "name": "b_kondisi_id", "value": $("#ifb_kondisi_id").val() },
					{ "name": "courier_service", "value": $("#if_courier_service").val() },
					{ "name": "free_ship", "value": $("#iffree_ship").val() },
					{ "name": "reported_status", "value": $("#if_reported_status").val() },
                    { "name": "p_admin_name", "value": $("#input_admin_name").val() },
					{ "name": "from_date", "value": $("#from_date").val() },
					{ "name": "end_date", "value": $("#to_date").val() },
				);
			},
			"fnServerData"	: function (sSource, aoData, fnCallback, oSettings) {
				oSettings.jqXHR = $.ajax({
					dataType 	: 'json',
					method 		: 'POST',
					url 		: sSource,
					data 		: aoData
				}).success(function (response, status, headers, config) {
					console.log(response);
					NProgress.done();
					$('#drTable > tbody').off('click', 'tr');
					$('#drTable > tbody').on('click', 'tr', function (e) {
						e.preventDefault();
						//var id = $(this).find("td").html();
						var currentRow = $(this).closest("tr");
						var id = $('#drTable').DataTable().row(currentRow).data()[1]; <!-- to get data from specific column, change this "data()[id_column]" -->
						var bu_id = $('#drTable').DataTable().row(currentRow).data()[2];
						var check_status = $('#drTable').DataTable().row(currentRow).data()[11];
						ieid = id;
						buser_id = bu_id;
						flag_takedown = check_status;

						if(flag_takedown == "takedown") {
							$("#modal_option").modal("hide");
							alert("Sorry, this post already taken down");
						} else if(flag_takedown == "reported"){
							$("#modal_option").modal("show");
						}

						//$("#modal_option").modal("show");
					});
					fnCallback(response);
				}).error(function (response, status, headers, config) {
					NProgress.done();
					gritter('<h4>Error</h4><p>Cant fetch product data right now, please try again later</p>','warning');
				});
			},
	});
	$('.dataTables_filter input').attr('placeholder', 'Search product name');
	$("#bfilter").on("click",function(e){
		e.preventDefault();
		if($("#ifprice_min").val().length > 0 && $("#ifprice_max").val().length > 0){
			drTable.order([4, 'asc']).ajax.reload();
		}else if($("#ifprice_min").val().length > 0 && $("#ifprice_max").val().length == 0){
			drTable.order([4, 'asc']).ajax.reload();
		}else if($("#ifprice_min").val().length == 0 && $("#ifprice_max").val().length > 0){
			drTable.order([4, 'desc']).ajax.reload();
		}else{
			drTable.ajax.reload();
		}
	});
	$("#fl_reset").on("click",function(e){
		e.preventDefault();
		$("#ifb_kategori_id").val("");
		$("#ifprice_min").val("");
		$("#ifprice_max").val("");
		$("#ifb_kondisi_id").val("");
		$("#if_courier_service").val("");
		$("#if_free_ship").val("");
		$("#ifb_kondisi_id").val("");
		$("#ifproduk_status").val("");
		$("#if_reported_status").val("");
		$("#input_admin_name").val("");
		$("#from_date").val("");
		$("#to_date").val("");
		drTable.ajax.reload();
	})
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
	var url = '<?=base_url("api_admin/crm/produkreport/tambah/")?>';
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
	var url = '<?=base_url("api_admin/crm/produkreport/edit/")?>';
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
		var c = confirm('Are you sure?');
		if(c){
			var url = '<?=base_url('api_admin/crm/produkreport/hapus/')?>'+ieid;
			$.get(url).done(function(response){
				if(response.status==200){
					growlType = 'success';
					growlPesan = '<h4>Success</h4><p>Data has deleted</p>';
					$("#modal_option").modal('hide');
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
				growlPesan = '<h4>Error</h4><p>Delete process not success, please try later</p>';
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

//option
$("#aedit").on("click",function(e){
	e.preventDefault();
	$("#modal_option").modal("hide");
	setTimeout(function(){
		//quick edit
		//$("#modal_edit").modal("show");
		window.location = '<?=base_url_admin('crm/produkreport/edit/')?>'+ieid;
	},333);
});

//detail
$("#adetail").on("click",function(e){
	e.preventDefault();
	$("#modal_option").modal("hide");
	setTimeout(function(){
		//$("#modal_edit").modal("show");
		//alert('masih dalam pengembangan');
		window.location ='<?=base_url_admin('crm/produkreport/detail/')?>'+ieid;
	},333);
});

$("#aset_showhp").on("click",function(e){
	e.preventDefault();
	NProgress.start();
	$.get("<?=base_url("api_admin/crm/produkreport/homepage_show/")?>"+ieid).done(function(dt){
		NProgress.done();
		if(dt.status == 200){
			gritter("<h4>Success</h4><p>Product now visible in homepage</p>",'success');
		}else{
			gritter("<h4>Failed</h4><p>"+dt.message+"</p>",'danger');
		}
		$("#modal_option").modal("hide");
		drTable.ajax.reload(null,false);
	}).fail(function(){
		NProgress.done();
		gritter("<h4>Failed</h4><p>Cant change product status right now, please try again later</p>",'danger');
	});
});

$("#aset_hidehp").on("click",function(e){
	e.preventDefault();
	NProgress.start();
	$.get("<?=base_url("api_admin/crm/produkreport/homepage_hide/")?>"+ieid).done(function(dt){
		NProgress.done();
		if(dt.status == 200){
			gritter("<h4>Success</h4><p>Product has removed from homepage</p>",'success');
		}else{
			gritter("<h4>Failed</h4><p>"+dt.message+"</p>",'danger');
		}
		$("#modal_option").modal("hide");
		drTable.ajax.reload(null,false);
	}).fail(function(){
		NProgress.done();
		gritter("<h4>Failed</h4><p>Cant change product status right now, please try again later</p>",'danger');
	});
});

$("#aset_publish").on("click",function(e){
	e.preventDefault();
	NProgress.start();
	$.get("<?=base_url("api_admin/crm/produkreport/publish/")?>"+ieid).done(function(dt){
		NProgress.done();
		if(dt.status == 200){
			gritter("<h4>Success</h4><p>Product published</p>",'success');
		}else{
			gritter("<h4>Failed</h4><p>"+dt.message+"</p>",'danger');
		}
		$("#modal_option").modal("hide");
		drTable.ajax.reload(null,false);
	}).fail(function(){
		NProgress.done();
		gritter("<h4>Failed</h4><p>Cant change product status right now, please try again later</p>",'danger');
	});
});

$("#aset_draft").on("click",function(e){
	e.preventDefault();
	NProgress.start();
	$.get("<?=base_url("api_admin/crm/produkreport/draft/")?>"+ieid).done(function(dt){
		NProgress.done();
		if(dt.status == 200){
			gritter("<h4>Success</h4><p>Product set as draft</p>",'success');
		}else{
			gritter("<h4>Failed</h4><p>"+dt.message+"</p>",'danger');
		}
		$("#modal_option").modal("hide");
		drTable.ajax.reload(null,false);
	}).fail(function(){
		NProgress.done();
		gritter("<h4>Failed</h4><p>Cant change product status right now, please try again later</p>",'danger');
	});
});

$("#aset_active").on("click",function(e){
	e.preventDefault();
	NProgress.start();
	$.get("<?=base_url("api_admin/crm/produkreport/active/")?>"+ieid).done(function(dt){
		NProgress.done();
		if(dt.status == 200){
			gritter("<h4>Success</h4><p>Product activated</p>",'success');
		}else{
			gritter("<h4>Failed</h4><p>"+dt.message+"</p>",'danger');
		}
		$("#modal_option").modal("hide");
		drTable.ajax.reload(null,false);
	}).fail(function(){
		NProgress.done();
		gritter("<h4>Failed</h4><p>Cant change product status right now, please try again later</p>",'danger');
	});
});


$("#aset_inactive").on("click",function(e){
	e.preventDefault();
	NProgress.start();
	$.get("<?=base_url("api_admin/crm/produkreport/inactive/")?>"+ieid).done(function(dt){
		NProgress.done();
		if(dt.status == 200){
			gritter("<h4>Success</h4><p>Product inactivated</p>",'success');
		}else{
			gritter("<h4>Failed</h4><p>"+dt.message+"</p>",'danger');
		}
		$("#modal_option").modal("hide");
		drTable.ajax.reload(null,false);
	}).fail(function(){
		NProgress.done();
		gritter("<h4>Failed</h4><p>Cant change product status right now, please try again later</p>",'danger');
	});
});

<!-- start  by ali - 18 january 2023 14:42 add export excel-->
$("#fl_download").on("click", function(e) {
    e.preventDefault();
    if ($("#input_admin_name").val() == null || $("#input_admin_name").val() == "") {
        alert("Name admin cannot be null");
    } else if($("#from_date").val() == null || $("#from_date").val() == "") {
        alert("From date cannot be null");
	} else if($("#to_date").val() == null || $("#to_date").val() == "") {
        alert("To date cannot be null");
	} else {
        $.ajax({
            url: '<?=base_url("a/crm/produkreport/exportExcel")?>',
            type: "POST",
            dataType: "json",
            data: {
                export: true,
                admin_name: $("#input_admin_name").val()
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

$('#from_date, #to_date').datepicker('setDate', 'today').val("");

<!-- end  by ali - 18 january 2023 14:42 add export excel-->

//takedown Action
$("#btakedown").on("click",function(e){
	e.preventDefault();

	let admin_name = $("#admin_name").text();

	<!-- add confirmation popup -->
	var message_popup = confirm("Are you sure you want to takedown this post, your action can't be undone");
	if(message_popup){ 
		//var url = '<?=base_url("api_admin/crm/produkreport/takedown/")?>'+ieid;
		var url = '<?=base_url() ?>api_admin/crm/produkreport/takedown/?c_product_id='+ieid+'&b_user_id='+buser_id+'&admin_name='+admin_name;
		$.get(url).done(function(response){
			if(response.status==200){
				growlType = 'success';
				growlPesan = '<h4>Success</h4><p>Product has been takedown</p>';
				drTable.ajax.reload(null,false);
			}else{
				growlType = 'danger';
				growlPesan = '<h4>Failed</h4><p>'+response.message+'</p>';
			}
			$.bootstrapGrowl(growlPesan,{
				type: growlType,
				delay: 500,
				allow_dismiss: true
			});

			//setTimeout(function(){
			//	window.location.reload();
			//},1000)
			$("#modal_option").modal("hide");
		}).fail(function(){
			growlPesan = '<h4>Error</h4><p>Failed, please try later</p>';
			growlType = 'danger';
			$.bootstrapGrowl(growlPesan,{
				type: growlType,
				delay: 2500,
				allow_dismiss: true
			});
		})
	}
});

//ignore action
$("#bignore").on("click", function(e){
	e.preventDefault();
	var url = '<?=base_url("api_admin/crm/produkreport/ignore/")?>'+ieid;
	$.get(url).done(function(response){
		if(response.status==200){
			growlType = 'success';
			growlPesan = '<h4>Success</h4><p>Product has been ignore</p>';
		}else{
			growlType = 'danger';
			growlPesan = '<h4>Failed</h4><p>'+response.message+'</p>';
		}
		$.bootstrapGrowl(growlPesan,{
			type: growlType,
			delay: 2500,
			allow_dismiss: true
		});
		setTimeout(function(){
			window.location.reload();
		},1000)
	}).fail(function(){
		growlPesan = '<h4>Error</h4><p>Failed, please try later</p>';
		growlType = 'danger';
		$.bootstrapGrowl(growlPesan,{
			type: growlType,
			delay: 2500,
			allow_dismiss: true
		});
	})
});
