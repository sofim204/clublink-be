var growlPesan = '<h4>Error</h4><p>Tidak dapat diproses, silakan coba beberapa saat lagi!</p>';
var growlType = 'danger';
var drTable = {};
var ieid = '';
var modal = 'add';
var bank_for = 'from';
var bank_data = [];
App.datatables();

if(jQuery('#drTable').length>0){
	drTable = jQuery('#drTable')
	.on('preXhr.dt', function ( e, settings, data ){
		NProgress.start();
	}).DataTable({
			"order"					: [[ 0, "desc" ]],
			"responsive"	  : true,
			"bProcessing"		: true,
			"bServerSide"		: true,
			"sAjaxSource"		: "<?=base_url("api_admin/misc/trfcost/")?>",
			"fnServerParams": function ( aoData ) {
				aoData.push(
					{ "name": "is_active", "value": $("#fl_is_active").val() }
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
					NProgress.done();
					$('#drTable > tbody').off('click', 'tr');
					$('#drTable > tbody').on('click', 'tr', function (e) {
						e.preventDefault();
						var bank_from = $($(this).find("td")[1]).html();
						var bank_to = $($(this).find("td")[2]).html();
						ieid = $(this).find("td").html();
						var url = '<?=base_url()?>api_admin/misc/trfcost/detail/'+ieid;
						$.get(url).done(function(response){
							if(response.status == 200){
								var dta = response.data;
                $("#iea_bank_id_to").val(dta.a_bank_id_to);
                $("#iea_bank_id_from").val(dta.a_bank_id_from);
                $("#iea_bank_nama_to").val(dta.a_bank_nama_to);
                $("#iea_bank_nama_from").val(dta.a_bank_nama_from);
                $("#ieutype").val(dta.utype);
                $("#iecost").val(dta.cost);
								$("#ieis_active").val(dta.is_active);
								$("#modal_option").modal("show");
							}else{
								growlType = 'danger';
								growlPesan = '<h4>Error</h4><p>Cannot fetch data from server</p>';
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
					$("#modal-preloader").modal("hide");
					growlType = 'warning';
					growlPesan = '<h4>Error</h4><p>Cannot fetch data from server</p>';
					$.bootstrapGrowl(growlPesan, {
						type: growlType,
						delay: 2500,
						allow_dismiss: true
					});
				});
			},
	});
	$('.dataTables_filter input').attr('placeholder', 'Search Bank name');
	$("#fl_reset").on("click",function(e){
		e.preventDefault();
		$("#fl_is_active").val("");
		drTable.ajax.reload();
	});
	$("#fl_button").on("click",function(e){
		e.preventDefault();
		if($("#fl_is_active").val().length>0){
			drTable.order([4, 'asc']).ajax.reload();
		}else{
			drTable.ajax.reload();
		}
	});
}

//tambah
$("#atambah").on("click",function(e){
	e.preventDefault();
	$("#modal_tambah").modal("show");
	$("#modal_tambah").find("form").trigger("reset");
	modal = 'add';
});
$("#modal_tambah").on("shown.bs.modal",function(e){

});
$("#modal_tambah").on("hidden.bs.modal",function(e){
	//$("#modal_tambah").find("form").trigger("reset");
});


$("#ftambah").on("submit",function(e){
	e.preventDefault();
	var fd = new FormData($(this)[0]);
	var url = '<?=base_url("api_admin/misc/trfcost/tambah/")?>';
	$.ajax({
		type: $(this).attr('method'),
		url: url,
		data: fd,
		processData: false,
		contentType: false,
		success: function(respon){
			if(respon.status == 200){
				growlPesan = '<h4>Success</h4><p>Data successfully inserted</p>';
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
	modal = 'edit';
});
$("#modal_edit").on("hidden.bs.modal",function(e){
	//$("#modal_edit").find("form").trigger("reset");
});

$("#fedit").on("submit",function(e){
	e.preventDefault();
	var fd = new FormData($(this)[0]);
	var url = '<?=base_url("api_admin/misc/trfcost/edit/")?>'+ieid;
	$.ajax({
		type: $(this).attr('method'),
		url: url,
		data: fd,
		processData: false,
		contentType: false,
		success: function(respon){
			if(respon.status == 200){
				growlType = 'success';
				growlPesan = '<h4>Success</h4><p>Data edit successfully</p>';
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
$("#ahapus").on("click",function(e){
	e.preventDefault();
	var id = ieid;
	if(id){
		var c = confirm('Are you sure?');
		if(c){
			var url = '<?=base_url('api_admin/misc/trfcost/hapus/')?>'+id;
			$.get(url).done(function(response){
				if(response.status == 200){
					growlType = 'success';
					growlPesan = '<h4>Success</h4><p>Data successfully deleted</p>';
				}else{
					growlType = 'danger';
					growlPesan = '<h4>Failed</h4><p>'+response.message+'</p>';
				}
				drTable.ajax.reload();
				$("#modal_option").modal("hide");
				$.bootstrapGrowl(growlPesan,{
					type: growlType,
					delay: 2500,
					allow_dismiss: true
				});
			}).fail(function() {
				growlPesan = '<h4>Error</h4><p>Cannot delete data right now, please try again later</p>';
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

//bank search
$(".btn-cari-bank").on("click",function(e){
	e.preventDefault();
	bank_for = $(this).attr("data-for");
	if(modal == 'edit'){
		$("#modal_edit").modal("hide");
	}else{
		$("#modal_tambah").modal("hide");
	}
	setTimeout(function(){
		$("#bank_cari_modal").modal("show");
	},333);
});
$(".btn-cari-bank-do").on("click",function(e){
	e.preventDefault();
	$("#bank_cari_modal_form").trigger("submit");
});
$("#bank_cari_modal_form").on("submit",function(e){
	bank_data = [];
	e.preventDefault();
	NProgress.start();
	var fd = {};
	fd.is_active = "1";
	fd.sSearch = $("#bank_cari_modal_input").val();
	$.post("<?=base_url('api_admin/misc/bank')?>",fd).done(function(dt){
		NProgress.done();
		$("#bank_cari_modal_table tbody").empty();
		if(dt.data.length){
			var i = 0;
			$.each(dt.data,function(k,v){
				var h = '<tr>';
				h += '<td>'+v[0]+'</td>';
				h += '<td>'+v[1]+'</td>';
				h += '<td>'+v[2]+'</td>';
				h += '<td><a href="#" class="btn btn-default btn-pilih-bank" data-id="'+i+'"><i class="fa fa-check"></i></a></td>';
				h += '</tr>';
				$("#bank_cari_modal_table tbody").append(h);
				bank_data[i] = v;
				i++;
			});
			$("#bank_cari_modal_table tbody").off("click",".btn-pilih-bank");
			$("#bank_cari_modal_table tbody").on("click",".btn-pilih-bank",function(e){
				e.preventDefault();
				var e = '';
				var did = $(this).attr("data-id");
				if(modal == 'edit') e = 'e';
				if(bank_for == "from"){
					$("#i"+e+"a_bank_nama_from").val(bank_data[did][2]);
					$("#i"+e+"a_bank_id_from").val(bank_data[did][0]);
					console.log("#i"+e+"a_bank_id_from");
				}else{
					$("#i"+e+"a_bank_nama_to").val(bank_data[did][2]);
					$("#i"+e+"a_bank_id_to").val(bank_data[did][0]);
					console.log("#i"+e+"a_bank_id_to");
				}
				$("#bank_cari_modal").modal("hide");
			});
		}else{
			var h = '<tr><td colspan="4">empty result</a></tr>';
			$("#bank_cari_modal_table tbody").append(h);
		}
	}).error(function(){
		NProgress.done();
		growlType = 'danger';
		growlPesan = '<h4>Error</h4><p>Cannot fetch data from server</p>';
		$.bootstrapGrowl(growlPesan, {
			type: growlType,
			delay: 2500,
			allow_dismiss: true
		});
	});
});

$("#bank_cari_modal").on("hidden.bs.modal",function(e){
	setTimeout(function(){
		if(modal == 'edit'){
			$("#modal_edit").modal("show");
		}else{
			$("#modal_tambah").modal("show");
		}
	},333);
});
