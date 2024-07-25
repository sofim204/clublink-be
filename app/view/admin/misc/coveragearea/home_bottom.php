<!-- by Muhammad Sofi 13 January 2022 16:11 | remodel on sponsored menu -->
var growlPesan = '<h4>Error</h4><p>Cannot be proceed. Please try again later!</p>';
var growlType = 'danger';
var drTable = {};
var ieid = '';

App.datatables();
function gritter(pesan,jenis="info"){
	$.bootstrapGrowl(pesan, {
		type: jenis,
		delay: 2500,
		allow_dismiss: true
	});
}

$(document).ready(function(){
	
	$("#ilatitude, #ielatitude").inputmask({
		mask: "*{1,3}.*{1,6}",
		greedy: false, <!-- initial mask, set true to full shown -->
		onBeforePaste: function (pastedValue, opts) {
			pastedValue = pastedValue.toLowerCase();
			return pastedValue.replace("mailto:", "");
		},
		definitions: {
			'*': {
				//validator: "[0-9A-Za-z!#$%&'*+/=?^_`{|}~\-]", <!-- full validator -->
				validator: "[0-9-]",
				casing: "lower"
			}
		}
  	});

	$("#ilongitude, #ielongitude").inputmask({
		mask: "*{1,4}.*{1,6}",
		greedy: false,
		onBeforePaste: function (pastedValue, opts) {
			pastedValue = pastedValue.toLowerCase();
			return pastedValue.replace("mailto:", "");
		},
		definitions: {
			'*': {
				validator: "[0-9-]",
				casing: "lower"
			}
		}
  	});

	$("#iradius, #ieradius").inputmask("9999999 m", {"placeholder": "", removeMaskOnSubmit: true});
	
	if(jQuery('#drTable').length>0){
		drTable = jQuery('#drTable')
		.on('preXhr.dt', function ( e, settings, data ){
			NProgress.start();
		}).DataTable({
			"columnDefs"		: [{
									"targets": [1], <!-- hide column -->
									"visible": false,
									"searchable": false
								}],
			"order"				: [[ 0, "asc" ]],
			"responsive"	  	: true,
			"bProcessing"		: true,
			"bServerSide"		: true,
			"bFilter"			: true,
			"sAjaxSource"		: "<?=base_url("api_admin/misc/coveragearea/"); ?>",
			"fnServerParams": function ( aoData ) {
				aoData.push(
					<!-- { "name": "fltype_coverage", "value": $("#fltype_coverage").val() }, -->
					{ "name": "select_provinsi", "value": $("#iprovinsi").val() },
					{ "name": "select_kabkota", "value": $("#ikabkota").val() },
					{ "name": "select_kecamatan", "value": $("#ikecamatan").val() },
					{ "name": "select_kelurahan", "value": $("#ikelurahan").val() }
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
						$(this).toggleClass('selected');				
					});
					$('#drTable > tbody').off('click', 'button');
					$('#drTable > tbody').on('click', 'button', function (e) {
						e.preventDefault();
						var id = $(this).attr("data-id");
						ieid = id;
						var url = '<?=base_url("api_admin/misc/coveragearea/detail/")?>'+ieid;
						$.get(url).done(function(response){
							if(response.status==200){
								var dta = response.data;
								$("#ieid").val(dta.id);
								$("#ietype").val(dta.type);
								$("#ieprovinsi").val(dta.provinsi);
								$("#iekabkota").val(dta.kabkota);
								$("#iekecamatan").val(dta.kecamatan);
								$("#iekelurahan").val(dta.kelurahan);
								$("#iejalan").val(dta.jalan);
								$("#ielatitude").val(dta.latitude);
								$("#ielongitude").val(dta.longitude);
								$("#ieradius").val(dta.radius);
								$("#iestatus").val(dta.is_active);

								$("#modal_edit").modal("show");
							}else{
								gritter('<h4>Failed</h4><p>Cannot fetch data, try again later</p>','info');
							}
						});
					});
					fnCallback(response);
				}).error(function (response, status, headers, config) {
					NProgress.start();
					gritter('<h4>Error</h4><p>Cannot fetch data, try again later</p>','warning');
					return false;
				});
			},
		});
		$('.dataTables_filter input').attr('placeholder', 'Search......').css({'width':'250px', 'display':'inline-block'});
	}

	<!-- START by Muhammad Sofi 27 January 2022 16:42 | adding form add data -->

	$("#form_add_data").on("submit",function(e){
		e.preventDefault();
		NProgress.start();
		var fd = new FormData($(this)[0]);
		var url = '<?=base_url("api_admin/misc/coveragearea/add/")?>';
		$.ajax({
			type: $(this).attr('method'),
			url: url,
			data: fd,
			processData: false,
			contentType: false,
			success: function(respon){
				NProgress.done();
				if(respon.status=="200"){
					gritter('<h4>Success</h4><p>Data Added successfuly</p>','success');
					setTimeout(function(){
						window.location = '<?php echo base_url_admin('misc/coveragearea'); ?>';
					}, 600);
				} else {
					gritter('<h4>Failed</h4><p>'+respon.message+'</p>','danger');
				}
			},
			error:function(){
				NProgress.done();
				gritter('<h4>Error</h4><p>Cant Add data right now, please try again later</p>','warning');
				return false;
			}
		});
	});
	<!-- END by Muhammad Sofi 27 January 2022 16:42 | adding form add data -->

	$("#modal_edit").on("hidden.bs.modal",function(e){
		drTable.ajax.reload(null, false);
		$("#select_ieprovinsi").val('').trigger("change");
		$("#select_iekabkota").val('').trigger("change");
		$("#select_iekecamatan").val('').trigger("change");
		$("#select_iekelurahan").val('').trigger("change");
	});

	$("#form_edit_data").on("submit",function(e){
		e.preventDefault();
		NProgress.start();
		var fd = new FormData($("#form_edit_data")[0]);
		var url = '<?=base_url("api_admin/misc/coveragearea/edit/")?>' +ieid;
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
				$("#modal_edit").modal("hide");
			}else{
				gritter('<h4>Failed</h4><p>'+respon.message+'</p>','danger');
			}
		}).fail(function(){
			NProgress.done();
			setTimeout(function(){
				gritter('<h4>Error</h4><p>Cannot edit data right now, please try again later</p>','warning');
			}, 666);
			return false;
		});
	});

	$('#fltype_coverage').prop('selectedIndex', 0);

	function ResetEachOptions() {
		$("#iprovinsi").val('').trigger("change");
		$("#ikabkota").val('').trigger("change");
		$("#ikecamatan").val('').trigger("change");
		$("#ikelurahan").val('').trigger("change");
	}

	function ResetSelection() {
		$("#toggle_provinsi").hide();
		$("#toggle_kabkota").hide();
		$("#toggle_kecamatan").hide();
		$("#toggle_kelurahan").hide();
		$("#toggle_jalan").hide();
		ResetEachOptions();
		$("#ijalan").val('');
		$("#ilatitude").val('');
		$("#ilongitude").val('');
		$("#iradius").val('');
	}

	$("#reset-filter").on("click",function(e){
		e.preventDefault();
		drTable.search('').columns().search('').draw();
		drTable.ajax.reload();
		$("#fltype_coverage").val("");
		ResetSelection();
	});

	$("#fltype_coverage").change(function() {
		var type = $("#fltype_coverage").val();
		if(type == "jalan") {
			$("#toggle_provinsi").show();
			$("#toggle_kabkota").show();
			$("#toggle_kecamatan").show();
			$("#toggle_kelurahan").show();
			$("#toggle_jalan").show();
			ResetEachOptions();
		} else if(type == "kelurahan") {
			$("#toggle_provinsi").show();
			$("#toggle_kabkota").show();
			$("#toggle_kecamatan").show();
			$("#toggle_kelurahan").show();
			$("#toggle_jalan").hide();
			ResetEachOptions();
		} else if(type == "kecamatan") {
			$("#toggle_provinsi").show();
			$("#toggle_kabkota").show();
			$("#toggle_kecamatan").show();
			$("#toggle_kelurahan").hide();
			$("#toggle_jalan").hide();
			ResetEachOptions();
		} else if(type == "kabkota") {
			$("#toggle_provinsi").show();
			$("#toggle_kabkota").show();
			$("#toggle_kecamatan").hide();
			$("#toggle_kelurahan").hide();
			$("#toggle_jalan").hide();
			ResetEachOptions();
		} else if(type == "provinsi") {
			$("#toggle_provinsi").show();
			$("#toggle_kabkota").hide();
			$("#toggle_kecamatan").hide();
			$("#toggle_kelurahan").hide();
			$("#toggle_jalan").hide();
			ResetEachOptions();
		} else {
			ResetSelection();
		}
	});

	<!-- add modal -->

	$("#iprovinsi").select2({
		placeholder: "--Select Provinsi--",
		width: "100%",
		allowClear: true, <!-- add x button to clear value -->
		ajax: { 
			url: "<?= base_url('api_admin/misc/coveragearea/getProvinsi') ?>",
			type: "post",
			dataType: 'json',
			delay: 250,
			data: function (params) {
				return {
					search: params.term, // search term
				};
			},
			processResults: function (response) {
				return {
					results: response
				};
			}
		},
		sorter: function(data) {
			return data.sort(function(a, b) {
				return a.text < b.text ? -1 : a.text > b.text ? 1 : 0;
			});
		}
	}).change(function(){
		var value = $("#iprovinsi option:selected").val();
		drTable.ajax.reload();
	});

	$("#iprovinsi").on("select2:unselect", function(e){
        $("#iprovinsi").val('').trigger("change");
        $("#ikabkota").val('').trigger("change");
        $("#ikecamatan").val('').trigger("change");
        $("#ikelurahan").val('').trigger("change");
		drTable.ajax.reload();
    }).trigger('change');

	$("#ikabkota").select2({
		placeholder: "--Select Kabupaten--",
		width: "100%",
		allowClear: true, <!-- add x button to clear value -->
		"language": {
			"noResults": function(){
				return "Choose Provinsi first";
			}
		},
		ajax: { 
			url: "<?= base_url('api_admin/misc/coveragearea/getKabupatenkota') ?>",
			type: "post",
			dataType: 'json',
			delay: 250,
			data: function (params) {
				return {
					provinsi_id: $("#iprovinsi").val(),
					search: params.term, // search term
				};
			},
			processResults: function (response) {
				return {
					results: response
				};
			}
		},
		sorter: function(data) {
			return data.sort(function(a, b) {
				return a.text < b.text ? -1 : a.text > b.text ? 1 : 0;
			});
		}
	}).change(function(){
		drTable.ajax.reload();
	});

	$("#ikabkota").on("select2:unselect", function(e){
        $("#ikabkota").val('').trigger("change");
        $("#ikecamatan").val('').trigger("change");
        $("#ikelurahan").val('').trigger("change");
		drTable.ajax.reload();
    }).trigger('change');

	$("#ikecamatan").select2({
		placeholder: "--Select Kecamatan--",
		width: "100%",
		allowClear: true, <!-- add x button to clear value -->
		"language": {
			"noResults": function(){
				return "Choose Kabupaten / Kota first";
			}
		},
		ajax: { 
			url: "<?= base_url('api_admin/misc/coveragearea/getKecamatan') ?>",
			type: "post",
			dataType: 'json',
			delay: 250,
			data: function (params) {
				return {
					provinsi_id: $("#iprovinsi").val(),
					kabkota_id: $("#ikabkota").val(),
					search: params.term, // search term
				};
			},
			processResults: function (response) {
				return {
					results: response
				};
			}
		},
		sorter: function(data) {
			return data.sort(function(a, b) {
				return a.text < b.text ? -1 : a.text > b.text ? 1 : 0;
			});
		}
	}).change(function(){
		drTable.ajax.reload();
	});

	$("#ikecamatan").on("select2:unselect", function(e){
        $("#ikecamatan").val('').trigger("change");
        $("#ikelurahan").val('').trigger("change");
		drTable.ajax.reload();
    }).trigger('change');

	$("#ikelurahan").select2({
		placeholder: "--Select Kelurahan--",
		width: "100%",
		allowClear: true, <!-- add x button to clear value -->
		"language": {
			"noResults": function(){
				return "Choose Kecamatan first";
			}
		},
		ajax: { 
			url: "<?= base_url('api_admin/misc/coveragearea/getKelurahan') ?>",
			type: "post",
			dataType: 'json',
			delay: 250,
			data: function (params) {
				return {
					provinsi_id: $("#iprovinsi").val(),
					kabkota_id: $("#ikabkota").val(),
					kecamatan_id: $("#ikecamatan").val(),
					search: params.term, // search term
				};
			},
			processResults: function (response) {
				return {
					results: response
				};
			}
		},
		sorter: function(data) {
			return data.sort(function(a, b) {
				return a.text < b.text ? -1 : a.text > b.text ? 1 : 0;
			});
		}
	}).change(function(){
		drTable.ajax.reload();
	});

	$("#ikelurahan").on("select2:unselect", function(e){
		drTable.ajax.reload();
    }).trigger('change');

	<!-- end add modal -->

	<!-- edit modal -->

	$("#select_ieprovinsi").select2({
		placeholder: "--Select Provinsi--",
		width: "100%",
		allowClear: true, <!-- add x button to clear value -->
		ajax: { 
			url: "<?= base_url('api_admin/misc/coveragearea/getProvinsi') ?>",
			type: "post",
			dataType: 'json',
			delay: 250,
			data: function (params) {
				return {
					search: params.term, // search term
				};
			},
			processResults: function (response) {
				return {
					results: response
				};
			}
		},
		sorter: function(data) {
			return data.sort(function(a, b) {
				return a.text < b.text ? -1 : a.text > b.text ? 1 : 0;
			});
		}
	}).change(function(){
		var value = $("#select_ieprovinsi option:selected").val();
		$("#ieprovinsi").val(value);
		$("#select_iekabkota").val('').trigger("change");
        $("#select_iekecamatan").val('').trigger("change");
        $("#select_iekelurahan").val('').trigger("change");
		$("#iekabkota").val("");
		$("#iekecamatan").val("");
		$("#iekelurahan").val("");
	});

	$("#select_iekabkota").select2({
		placeholder: "--Select Kabupaten--",
		width: "100%",
		allowClear: true, <!-- add x button to clear value -->
		"language": {
			"noResults": function(){
				return "Choose Provinsi first";
			}
		},
		ajax: { 
			url: "<?= base_url('api_admin/misc/coveragearea/getKabupatenkota') ?>",
			type: "post",
			dataType: 'json',
			delay: 250,
			data: function (params) {
				return {
					provinsi_id: $("#select_ieprovinsi").val(),
					search: params.term, // search term
				};
			},
			processResults: function (response) {
				return {
					results: response
				};
			}
		},
		sorter: function(data) {
			return data.sort(function(a, b) {
				return a.text < b.text ? -1 : a.text > b.text ? 1 : 0;
			});
		}
	}).change(function(){
		var value = $("#select_iekabkota option:selected").val();
		$("#iekabkota").val(value);
		$("#select_iekecamatan").val('').trigger("change");
        $("#select_iekelurahan").val('').trigger("change");
		$("#iekecamatan").val("");
		$("#iekelurahan").val("");
	});

	$("#select_iekecamatan").select2({
		placeholder: "--Select Kecamatan--",
		width: "100%",
		allowClear: true, <!-- add x button to clear value -->
		"language": {
			"noResults": function(){
				return "Choose Kabupaten / Kota first";
			}
		},
		ajax: { 
			url: "<?= base_url('api_admin/misc/coveragearea/getKecamatan') ?>",
			type: "post",
			dataType: 'json',
			delay: 250,
			data: function (params) {
				return {
					provinsi_id: $("#select_ieprovinsi").val(),
					kabkota_id: $("#select_iekabkota").val(),
					search: params.term, // search term
				};
			},
			processResults: function (response) {
				return {
					results: response
				};
			}
		},
		sorter: function(data) {
			return data.sort(function(a, b) {
				return a.text < b.text ? -1 : a.text > b.text ? 1 : 0;
			});
		}
	}).change(function(){
		var value = $("#select_iekecamatan option:selected").val();
		$("#iekecamatan").val(value);
		$("#select_iekelurahan").val('').trigger("change");
		$("#iekelurahan").val("");
	});

	$("#select_iekelurahan").select2({
		placeholder: "--Select Kelurahan--",
		width: "100%",
		allowClear: true, <!-- add x button to clear value -->
		"language": {
			"noResults": function(){
				return "Choose Kecamatan first";
			}
		},
		ajax: { 
			url: "<?= base_url('api_admin/misc/coveragearea/getKelurahan') ?>",
			type: "post",
			dataType: 'json',
			delay: 250,
			data: function (params) {
				return {
					provinsi_id: $("#select_ieprovinsi").val(),
					kabkota_id: $("#select_iekabkota").val(),
					kecamatan_id: $("#select_iekecamatan").val(),
					search: params.term, // search term
				};
			},
			processResults: function (response) {
				return {
					results: response
				};
			}
		},
		sorter: function(data) {
			return data.sort(function(a, b) {
				return a.text < b.text ? -1 : a.text > b.text ? 1 : 0;
			});
		}
	}).change(function(){
		var value = $("#select_iekelurahan option:selected").val();
		$("#iekelurahan").val(value);
	});

	<!-- end edit modal -->

	$('#btn-coverage-inactive').click(function () {
		var row_length = drTable.rows('.selected').data().length;

		if(!row_length > 0) {
			alert("please, select the data first");
		} else {
			for (var i = 0; i < row_length; i++) {
				var idcom = $.map(drTable.rows('.selected').data(), function (item) {
					return item[1]; <!--item[column_data] -->
				});
				$.post("<?=base_url(); ?>api_admin/misc/coveragearea/setInactive/"+ idcom[i], { }, 
				function(data) {
					if(data.status == 200) {
						//setTimeout(function(){
						//	window.location = '<?=base_url_admin('community/coveragearea')?>';
						//},1200);
						drTable.ajax.reload(null, false);
					} else {
						gritter("<h4>Error</h4><p>Cannot fetch data right now, please try again later</p>", "warning");
					}	
				}, "json");
			};
			gritter('<h4>Success</h4><p>Data Successfully changed!</p>', 'success');
		}		
	});

	$('#btn-coverage-active').click(function () {
		var row_length = drTable.rows('.selected').data().length;

		if(!row_length > 0) {
			alert("please, select the data first");
		} else {
			for (var i = 0; i < row_length; i++) {
				var idcom = $.map(drTable.rows('.selected').data(), function (item) {
					return item[1];
				});
				$.post("<?=base_url(); ?>api_admin/misc/coveragearea/setActive/"+ idcom[i], { }, 
				function(data) {
					if(data.status == 200) {
						//setTimeout(function(){
						//	window.location = '<?=base_url_admin('community/coveragearea')?>';
						//},1200);
						drTable.ajax.reload(null, false);
					} else {
						gritter("<h4>Error</h4><p>Cannot fetch data right now, please try again later</p>", "warning");
					}	
				}, "json");
			};
			gritter('<h4>Success</h4><p>Data Successfully changed!</p>', 'success');
		}
	});

	$('#btn-coverage-delete').click(function () {
		var row_length = drTable.rows('.selected').data().length;

		if(!row_length > 0) {
			alert("please, select the data first");
		} else {
			var c = confirm("Are you sure to delete?");
			if(c) {
				for (var i = 0; i < row_length; i++) {
					var idcom = $.map(drTable.rows('.selected').data(), function (item) {
						return item[1];
					});
					$.post("<?=base_url(); ?>api_admin/misc/coveragearea/setDelete/"+ idcom[i], { }, 
					function(data) {
						if(data.status == 200) {
							//setTimeout(function(){
							//	window.location = '<?=base_url_admin('community/coveragearea')?>';
							//},1200);
							$("#modal_edit").modal("hide");
							drTable.ajax.reload(null, false);
						} else {
							gritter("<h4>Error</h4><p>Cannot fetch data right now, please try again later</p>", "warning");
						}	
					}, "json");
				};
				gritter('<h4>Success</h4><p>Data Successfully deleted!</p>', 'success');
			}
		}
	});
});