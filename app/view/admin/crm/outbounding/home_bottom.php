var growlPesan = '<h4>Error</h4><p>Cannot be proceed. Please try again later!</p>';
var growlType = 'danger';
var drTable = {};
var ieid = '';
var url_def = '';

App.datatables();
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
		"columnDefs"		: [{
								"targets": [1], <!-- hide column -->
								"visible": false,
								"searchable": false
							}],	
		"order"				: [[ 0, "asc" ]],
		"responsive"	  	: true,
		"bProcessing"		: true,
		"bServerSide"		: true,
		"sAjaxSource"		: "<?=base_url("api_admin/crm/outbounding"); ?>",
		"fnServerParams": function ( aoData ) {
			aoData.push(
				{ "name": "is_active", "value": $("#fl_is_active").val() }
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
					var currentRow = $(this).closest("tr");
					var id = $('#drTable').DataTable().row(currentRow).data()[1]; <!-- to get data from specific column, change this "data()[id_column]" -->
					ieid = id;
					var url = '<?=base_url("api_admin/crm/outbounding/detail/")?>'+ieid;
					$.get(url).done(function(response){
						if(response.status==200){
							var dta = response.data;
							//input nilai awal
							$("#ieid").val(dta.id);
							$("#iejudul").val(dta.judul);
							$("#ieteks").val(dta.teks);
							$("#ieis_active").val(dta.active);
							$("#ieis_notif").val(dta.notif);

							//tampilkan modal
							$("#modal_option").modal("show");
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
	$('.dataTables_filter input').attr('placeholder', 'Search marketing outbound title').css({'width':'250px', 'display':'inline-block'});
	$("#fl_reset").on("click",function(e){
		e.preventDefault();
		$("#fl_is_active").val("");
		drTable.ajax.reload();
	});
}
$("#atambah").on("click",function(e){
	e.preventDefault();
	$("#modal_tambah").modal("show");
});
$("#modal_tambah").on("shown.bs.modal",function(e){
	//
	$("#iutype").trigger("change");
	$("#original-Img").attr("src","");
	$("#upload-Preview").attr("src","");
});
$("#modal_tambah").on("hidden.bs.modal",function(e){
	$("#modal_tambah").find("form").trigger("reset");
	<!-- on close modal, remove/clear the appended html -->
	$("#newFieldProduct").empty();
	$("#newFieldShop").empty();
});
$("#ftambah").on("submit",function(e){
	e.preventDefault();
	NProgress.start();
	var fd = new FormData($("#ftambah")[0]);
	console.log(fd);
	var url = '<?=base_url("api_admin/crm/outbounding/tambah/")?>';
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
			drTable.ajax.reload();
			$("#modal_tambah").modal("hide");
			gritter( '<h4>Success</h4><p>Marketing Outbounding has been added successfuly</p>','success');
		}else{
			gritter( '<h4>Failed</h4><p>'+respon.message+'</p>','danger');
		}
	}).fail(function() {
		NProgress.done();
		gritter('<h4>Error</h4><p>Cannot add data right now, please try again later</p>','warning');
		return false;
	});
});

//edit
$("#modal_edit").on("shown.bs.modal",function(e){
	//
});
$("#modal_edit").on("hidden.bs.modal",function(e){
	$("#modal_edit").find("form").trigger("reset");
});
//option
$("#aedit").on("click",function(e){
  e.preventDefault();
  $("#modal_option").modal("hide");
  setTimeout(function(){
    $("#modal_edit").modal("show");
  },333);
});

$("#fedit").on("submit",function(e){
  e.preventDefault();
  var fd = new FormData($(this)[0]);
  
  var url = '<?=base_url("api_admin/crm/outbounding/edit/"); ?>'+ieid;
  $.ajax({
    type: $(this).attr('method'),
    url: url,
    data: fd,
    processData: false,
    contentType: false,
    success: function(respon){
      if(respon.status == 200){
        gritter('<h4>Success</h4><p>Data changed successfully</p>','success');
        drTable.ajax.reload();
        $("#modal_edit").modal("hide");
      }
      else{
        gritter('<h4>Failed</h4><p>'+respon.message+'</p>','danger');
        $("#modal_edit").modal("hide");
      }
    },
    error:function(){
      gritter('<h4>Error</h4><p>Cannot edit data right now, please try again later</p>','warning');
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
			var url = '<?=base_url('api_admin/crm/outbounding/hapus/')?>'+ieid;
			$.get(url).done(function(response){
				NProgress.done();
				$("#modal_edit").modal("hide");
				if(response.status==200){
					gritter('<h4>Success</h4><p>Data successfuly deleted</p>','success');
					drTable.ajax.reload();
					$("#modal_option").modal("hide");
				}else{
					gritter('<h4>Failed</h4><p>'+response.message+'</p>','warning');
					$("#modal_option").modal("hide");
				}
			}).fail(function() {
				NProgress.done();
				gritter('<h4>Error</h4><p>Cant delete data right now, please try again later</p>','warning');
			});
		}
	}
});

//detail
$("#adetail").on("click",function(e){
	e.preventDefault();
	$("#modal_option").modal("hide");
	setTimeout(function(){
		window.location='<?=base_url_admin('crm/outbounding/default/')?>'+ieid;
	},333);
});

$("#iteks").keyup(function() {
  var maxLength = $(this).attr("maxlength");
  if(maxLength == $(this).val().length) {
    alert("You can't write more than " + maxLength +" characters");
  }
});

$("#btnAddFieldProduct").click(function () {
	var product = '';
	product += '<div id="areaFieldProduct">';
	product += '<div class="input-group mb-3" style="display: flex; align-items: flex-end">';
	product += '<input type="text" name="product[]" class="form-control" placeholder="Product Name" value="" autocomplete="off">';
	product += '&nbsp;&nbsp;&nbsp;';
	product += '<input type="text" name="urlp[]" class="form-control" placeholder="Product Link" value="" autocomplete="off">';
	product += '<div class="input-group-append">';
	product += '<button id="btnRemoveFieldProduct" type="button" class="btn btn-danger">Remove</button>';
	product += '</div>';
	product += '</div>';
	product += '<div style="margin-bottom: 10px;"></div>';

	$('#newFieldProduct').append(product);
});

// remove row
$(document).on('click', '#btnRemoveFieldProduct', function () {
	$(this).closest('#areaFieldProduct').remove();
});

$("#btnAddFieldShop").click(function () {
	var shop = '';
	shop += '<div id="areaFieldShop">';
	shop += '<div class="input-group mb-3" style="display: flex; align-items: flex-end">';
	shop += '<input type="text" name="shop[]" class="form-control" placeholder="Shop Name" value="" autocomplete="off">';
	shop += '&nbsp;&nbsp;&nbsp;';
	shop += '<input type="text" name="urls[]" class="form-control" placeholder="Shop Link" value="" autocomplete="off">';
	shop += '<div class="input-group-append">';
	shop += '<button id="btnRemoveFieldShop" type="button" class="btn btn-danger" style="margin-top: -6px;">Remove</button>';
	shop += '</div>';
	shop += '</div>';
	shop += '<div style="margin-bottom: 10px;"></div>';

	$('#newFieldShop').append(shop);
});

// remove row
$(document).on('click', '#btnRemoveFieldShop', function () {
	$(this).closest('#areaFieldShop').remove();
});
