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
		"order"				: [[ 1, "asc" ]],
		"responsive"	  	: true,
		"bProcessing"		: true,
		"bServerSide"		: true,
		"searching"			: false, // hide input search
		"sAjaxSource"		: "<?=base_url("api_admin/misc/firstlogin/"); ?>",
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
				NProgress.done();
				$('#drTable > tbody').off('click', 'tr');
				$('#drTable > tbody').on('click', 'tr', function (e) {
					e.preventDefault();
					//var id = $(this).find("td").html();
					var currentRow = $(this).closest("tr");
					var id = $('#drTable').DataTable().row(currentRow).data()[1]; <!-- to get data from specific column, change this "data()[id_column]" -->
					ieid = id;
					var url = '<?=base_url("api_admin/misc/firstlogin/detail/")?>'+ieid;
					$.get(url).done(function(response){
						if(response.status==200){
							var dta = response.data;
							//input nilai awal
							$("#ieid").val(dta.id);
							$("#ieurl").val(dta.url);
							$("#iepriority").val(dta.priority);
							$("#ieis_active").val(dta.is_active);
							$("#imageDisplay").attr("src","<?=base_url()?>"+dta.url);

							//tampilkan modal
							//$("#modal_edit").modal("show");
							$("#modal_options").modal("show");
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

	$("#fl_reset").on("click",function(e){
		e.preventDefault();
		$("#fl_is_active").val("");
		drTable.search('').columns().search('').draw();
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

$("#aEditImage").on("click",function(e){
	e.preventDefault();
	$("#modal_options").modal("hide");
	$("#modal_edit").modal("show");
});

$("#atambah").on("click",function(e){
	e.preventDefault();
	$("#modal_tambah").modal("show");
});
$("#modal_tambah").on("shown.bs.modal",function(e){
	$("#original-Img").attr("src","");
	$("#upload-Preview").attr("src","");
});
$("#modal_tambah").on("hidden.bs.modal",function(e){
	$("#modal_tambah").find("form").trigger("reset");
	$("#iedate").datepicker('setDate', 'today').val("");
    $("#placeholder_image").show();
});
$("#ftambah").on("submit",function(e){
	e.preventDefault();
	NProgress.start();
	var fd = new FormData($("#ftambah")[0]);
	var url = '<?=base_url("api_admin/misc/firstlogin/tambah/")?>';
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
			gritter( '<h4>Success</h4><p>Image has been added successfuly</p>','success');
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

$("#ftedit").on("submit",function(e){
	e.preventDefault();
	NProgress.start();
	var fd = new FormData($("#ftedit")[0]);
	var url = '<?=base_url("api_admin/misc/firstlogin/edit/")?>'+ieid;
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
			drTable.ajax.reload();
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

//hapus
$("#bhapus").on("click",function(e){
	e.preventDefault();
	if(ieid){
		var c = confirm('Are you sure?');
		if(c){
			NProgress.start();
			var url = '<?=base_url('api_admin/misc/firstlogin/hapus/')?>'+ieid;
			$.get(url).done(function(response){
				NProgress.done();
				$("#modal_edit").modal("hide");
				if(response.status==200){
					gritter('<h4>Success</h4><p>Data successfuly deleted</p>','success');
					drTable.ajax.reload();
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

$("#bhapus_modal").on("click",function(e){
	e.preventDefault();
	if(ieid){
		var c = confirm('Are you sure?');
		if(c){
			NProgress.start();
			var url = '<?=base_url('api_admin/misc/firstlogin/hapus/')?>'+ieid;
			$.get(url).done(function(response){
				NProgress.done();
				$("#modal_options").modal("hide");
				if(response.status==200){
					gritter('<h4>Success</h4><p>Data successfuly deleted</p>','success');
					drTable.ajax.reload();
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

var fileReader = new FileReader();
var fileReaderEdit = new FileReader();

fileReader.onload = function (event) {
   	var image = new Image();
   	image.onload=function() {
		var canvas=document.createElement("canvas");
		var context=canvas.getContext("2d");
		canvas.width = window.innerWidth * 0.25;
		canvas.height = 400;
		context.drawImage(image, 0, 0, image.width, image.height, 0, 0, canvas.width, canvas.height);
		document.getElementById("upload-Preview").src = canvas.toDataURL();
   	}
   	image.src=event.target.result;
};

fileReaderEdit.onload = function (event) {
   	var image = new Image();
   	image.onload=function() {
		var canvas=document.createElement("canvas");
		var context=canvas.getContext("2d");
		canvas.width = window.innerWidth * 0.25;
		canvas.height = 500;
		context.drawImage(image, 0, 0, image.width, image.height, 0, 0, canvas.width, canvas.height);
		document.getElementById("imageDisplay").src = canvas.toDataURL();
   	}
   	image.src=event.target.result;
};

<!-- end comment code -->

<!-- initialize datepicker -->
$('.createdate').datepicker();
$('.createdate').datepicker('setDate', 'today').val("");

<!-- hide datepicker after select date -->
$('.createdate').change(function(){
	$('.datepicker').hide();
});

<!-- upload file add sponsored -->
$("#ifile").change(function () {
	<!-- verification -->
	<!-- check extension of uploaded file -->
	var inputFile = $("#ifile").val();
	var fileExtensions = inputFile.split('.').pop();
	var listExtensionImage = ["jpg", "jpeg", "png", "gif"]; <!-- by Muhammad Sofi 6 January 2022 11:09 | add extension -->
	
	if(listExtensionImage.indexOf(fileExtensions) > -1) {
		var numb = $("#ifile")[0].files[0].size/1024/1024;
		var size = numb.toFixed(3);
		var maxImageUpload = 5.120;
		if(size > maxImageUpload){
			alert("File too big, maximum is 5MB or less");
			$("#ifile").val("");
			$("#upload-Preview").attr("src", "");
		} else {
			var uploadFile = document.getElementById("ifile").files[0];
			fileReader.readAsDataURL(uploadFile);
            $("#placeholder_image").hide();
		}
	} else if(!listExtensionImage.indexOf(fileExtensions) > -1 ) { 
		alert("not valid extension");
		$("#ifile").val("");
		$("#upload-Preview").attr("src", "");
		return;
	} else {}
});

<!-- upload file edit sponsored -->
$("#iefile").change(function () {
	<!-- verification -->
	<!-- check extension of uploaded file -->
	var inputFile = $("#iefile").val();
	var fileExtensions = inputFile.split('.').pop();
	var listExtensionImage = ["jpg", "jpeg", "png", "gif"]; <!-- by Muhammad Sofi 6 January 2022 11:09 | add extension -->
	
	if(listExtensionImage.indexOf(fileExtensions) > -1) {
		var numb = $("#iefile")[0].files[0].size/1024/1024;
		var size = numb.toFixed(3);
		var maxImageUpload = 5.120;
		if(size > maxImageUpload){
			alert("File too big, maximum is 5MB or less");
			$("#iefile").val("");
			$("#imageDisplay").attr("src", "");
		} else {
			var uploadFile = document.getElementById("iefile").files[0];
			fileReaderEdit.readAsDataURL(uploadFile);
		}
	} else if(!listExtensionImage.indexOf(fileExtensions) > -1 ) { 
		alert("not valid extension");
		$("#iefile").val("");
		$("#imageDisplay").attr("src", "");
		return;
	} else {}
});