var growlPesan = '<h4>Error</h4><p>Tidak dapat diproses, silakan coba beberapa saat lagi!</p>';
var growlType = 'danger';
var api_url = '<?=base_url('api_admin/misc/bank')?>';
var drTable = {};
var ieid = '';
App.datatables();

if(jQuery('#drTable').length>0){
	drTable = jQuery('#drTable')
	.on('preXhr.dt', function ( e, settings, data ){
		$("#modal-preloader").modal("hide");
		//$("#modal-preloader").modal("show");
	}).DataTable({
			"order"					: [[ 0, "asc" ]],
			"responsive"	  : true,
			"bProcessing"		: true,
			"bServerSide"		: true,
			"sAjaxSource"		: "<?=base_url("api_admin/misc/bank/")?>",
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
					$("#modal-preloader").modal("hide");
					//$('body').addClass('loaded');
					$('#drTable > tbody').off('click', 'tr');
					$('#drTable > tbody').on('click', 'tr', function (e) {
						e.preventDefault();
						var id = $(this).find("td").html();
						var url = '<?=base_url()?>api_admin/misc/bank/detail/'+id;
						$.get(url).done(function(response){
							if(response.status==200){
								var dta = response.data;
								ieid = dta.id;
								$("#ienation_code").val(dta.nation_code);
								$("#ieid").val(dta.id);
                $("#ienama").val(dta.nama);
								$("#ieis_active").val(dta.is_active);


								//$("#modal_edit").modal("show");
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
					$("#modal-preloader").modal("hide");
					//console.log(response, response.responseText);
					//$('body').addClass('loaded');
					alert("Error");
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
			drTable.order([3, 'asc']).ajax.reload();
		}else{
			drTable.ajax.reload();
		}
	});
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
	//var fd = $(this).serialize();
	//var fd = {};
	//fd.origin = $("#iorigin").val();
	//fd.propinsi = $("#ipropinsi option:select").text();

	var url = '<?=base_url("api_admin/misc/bank/tambah/")?>';
	$.ajax({
		type: $(this).attr('method'),
		url: url,
		data: fd,
		processData: false,
		contentType: false,
		success: function(respon){
			if(respon.status==200){
				growlPesan = '<h4>Success</h4><p>Data inserted successfully</p>';
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
			growlPesan = '<h4>Error</h4><p>Cannot add data right now, please try again later</p>';
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
	var url = '<?=base_url("api_admin/misc/bank/edit/")?>'+ieid;
	$.ajax({
		type: $(this).attr('method'),
		url: url,
		data: fd,
		processData: false,
		contentType: false,
		success: function(respon){
			if(respon.status==200){
				growlType = 'success';
				growlPesan = '<h4>Success</h4><p>Data successfully changed</p>';
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
			growlPesan = '<h4>Error</h4><p>Cannot edit data right now, please try again later</p>';
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
			var url = '<?=base_url('api_admin/misc/bank/hapus/')?>'+id;
			$.get(url).done(function(response){
				if(response.status==200){
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
