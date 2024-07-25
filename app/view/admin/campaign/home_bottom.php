<!-- by Muhammad Sofi 13 January 2022 16:11 | remodel on sponsored menu -->
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
//Fungsi Upload Image
function parseFiles(id){
	var formData = new FormData();
	formData.append('image', $(id)[0].files[0]);
	return formData;
}
if(jQuery('#drTable').length>0){
	drTable = jQuery('#drTable')
	.on('preXhr.dt', function ( e, settings, data ){
		NProgress.start();
	}).DataTable({
			//"scrollX"				: true,
		"columnDefs"		: [{
								"targets": [1], <!-- hide column -->
								"visible": false,
								"searchable": false
							}],	
		"order"				: [[ 1, "asc" ]],
		"responsive"	  	: true,
		"bProcessing"		: true,
		"bServerSide"		: true,
		"sAjaxSource"		: "<?=base_url("api_admin/campaign/"); ?>",
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
					//var id = $(this).find("td").html();
					var currentRow = $(this).closest("tr");
					var id = $('#drTable').DataTable().row(currentRow).data()[1]; <!-- to get data from specific column, change this "data()[id_column]" -->
					ieid = id;
					var url = '<?=base_url("api_admin/campaign/detail/")?>'+id;
					$.get(url).done(function(response){
						if(response.status==200){
							var dta = response.data;
							//input nilai awal
							$("#ieid").val(dta.id);
							$("#iejudul").val(dta.judul);
							<!-- $("#ieteks").html(dta.teks); -->
							//$("#ieteks").html(dta.teks);
							//jqueryA('#ieteks').Editor('setText',$("#ieteks").val());
							$("#ieurl").val(dta.url);
							url_def = dta.url;
							$("#ieedate").datepicker('setDate', dta.edate).val();
							//$("#ietopbar").val(dta.top_bar);
							//$("#ieutype").val(dta.utype);
							$("#iepriority").val(dta.priority);
							$("#ieis_active").val(dta.is_active);
							$("#imageDisplay").attr("src","<?=base_url()?>"+dta.gambar);

							//tampilkan modal
							//$("#modal_edit").modal("show");
							$("#modal_options").modal("show");

							if(dta.gambar_sponsored !== null && dta.gambar_sponsored !== '') {
								$("#aEditSponsoredPicture").hide();
								$("#aChangeSponsoredPicture").show();
							} else {
								$("#aChangeSponsoredPicture").hide();
								$("#aEditSponsoredPicture").show();
							}

							$("#ieselect_seller_shop_value_edit").val(dta.seller_id);
							$("#ieselect_product_detail_value_edit").val(dta.product_id);
							$("#itype_sponsored_edit").val(dta.type_sponsored);
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
	$('.dataTables_filter input').attr('placeholder', 'Search sponsored title');
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

$("#aEditSponsored").on("click",function(e){
	e.preventDefault();
	$("#modal_options").modal("hide");
	$("#modal_edit").modal("show");
});

$("#aEditSponsoredPicture").on("click",function(e){
	e.preventDefault();
	$("#modal_options").modal("hide");
	$("#modal_add_sponsored_picture").modal("show");
});

$("#fadd_sponsored_picture").on("submit",function(e){
	e.preventDefault();
	NProgress.start();
	var url = '<?=base_url("api_admin/campaign/addsponsoredpicture/")?>'+ieid;
	$.ajax({
		type: $(this).attr('method'),
		url: url,
		data: new FormData(this),
		processData: false,
		contentType: false,
		success: function(respon){
			if(respon.status==200){
				gritter('<h4>Success</h4><p>Sponsored Picture added successfully</p>','success');
				setTimeout(function(){
					window.location = '<?php echo base_url_admin('campaign'); ?>';
				},500);
			}else{
				NProgress.done();
				gritter('<h4>Failed</h4><p>'+respon.message+'</p>','danger');
				$("#modal_add_sponsored_picture").modal("hide");
			}
		},
		error:function(){
			setTimeout(function(){
				$("#modal_add_sponsored_picture").modal("hide");
				NProgress.done();
				gritter('<h4>Error</h4><p>Cant add picture right now, please try again later</p>','warning');
			}, 666);
			return false;
		}
	});
});

$("#aChangeSponsoredPicture").on("click",function(e){
	e.preventDefault();
	$("#modal_options").modal("hide");
	$("#modal_add_sponsored_picture").modal("show");
});

$("#igambar_sponsored").change(function () {
	var numb = $('#igambar_sponsored')[0].files[0].size/1024/1024;
	var size = numb.toFixed(3);
	var maxImageUpload = 5.120;
	if(size > maxImageUpload){
		alert('File too big, maximum is 5MB or less');
		$('#igambar_sponsored').val('');
	} else {}
});

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
	$("#iedate").datepicker('setDate', 'today').val("");
	$("#row_seller_shop").hide();
	$("#row_seller_shop_product").hide();
	$("#row_product_detail").hide();
	$("#select_seller_shop").val("").trigger("change");
	$("#select_seller_shop_product").val("").trigger("change");
	$("#select_product_detail").val("").trigger("change");
});
$("#ftambah").on("submit",function(e){
	//$('#iteks').val(jqueryA('#iteks').Editor('getText'));
	e.preventDefault();
	NProgress.start();
	//gritter( '<h4>Processing</h4><p>Please wait while uploading image...</p>','info');
	var fd = new FormData($("#ftambah")[0]);
	console.log(fd);
	var url = '<?=base_url("api_admin/campaign/tambah/")?>';
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
			gritter( '<h4>Success</h4><p>Promotion has been added successfuly</p>','success');
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
	$("#row_seller_shop_edit").hide();
	$("#row_seller_shop_product_edit").hide();
	$("#row_product_detail_edit").hide();
	$("#select_seller_shop_edit").val("").trigger("change");
	$("#select_seller_shop_product_edit").val("").trigger("change");
	$("#select_product_detail_edit").val("").trigger("change");
});
$("#ftedit").on("submit",function(e){
	//$('#ieteks').val(jqueryA('#ieteks').Editor('getText'));
	e.preventDefault();
	NProgress.start();
	//gritter( '<h4>Processing</h4><p>Please wait while uploading image...</p>','info');
	var fd = new FormData($("#ftedit")[0]);
	var url = '<?=base_url("api_admin/campaign/edit/")?>'+ieid;
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
			var url = '<?=base_url('api_admin/campaign/hapus/')?>'+ieid;
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
			var url = '<?=base_url('api_admin/campaign/hapus/')?>'+ieid;
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

$("#iutype").on("change",function(e){
	var pre = $("#iutype option:selected").attr('data-pre');
	$("#iurl").val(pre);
});

$("#ieutype").on("change",function(e){
	var pre = $("#ieutype option:selected").attr('data-pre');
	$("#ieurl").val(pre);
});
$(".due-date").datepicker({
	startDate: new Date(),
	format: "yyyy-mm-dd",
});

function justNumber(evt) {
	var charCode = (evt.which) ? evt.which : event.keyCode
	if (charCode > 31 && (charCode < 48 || charCode > 57))

	return false;
	return true;
}

$('#ipriority').keyup(function(){
	if ($(this).val() > 5){
		alert("Maksimal 5");
		$(this).val('5');
	}
});

$('#iepriority').keyup(function(){
	if ($(this).val() > 5){
		alert("Maksimal 5");
		$(this).val('5');
	}
});

<!-- start comment code -->

// //check image allowed
// (function($) {
// 	$.fn.checkFileType = function(options) {
// 		var defaults = {
// 			allowedExtensions: [],
// 			success: function() {},
// 			error: function() {}
// 		};
// 		options = $.extend(defaults, options);

// 		return this.each(function() {

// 			$(this).on('change', function() {
// 				var value = $(this).val(),
// 				file = value.toLowerCase(),
// 				extension = file.substring(file.lastIndexOf('.') + 1);

// 				if ($.inArray(extension, options.allowedExtensions) == -1) {
// 					options.error();
// 					$(this).focus();
// 				} else {
// 					options.success();

// 				}

// 			});

// 		});
// 	};

// })(jQuery);

// $(function() {
// 	$('#ifile').checkFileType({
// 		allowedExtensions: ['jpg', 'jpeg', 'ico', 'png', 'bmp'],
// 		success: function() {
// 			// alert('Allowed extension icon!');
// 			console.log($('#ifile')[0].files[0]);
// 			var numb = $('#ifile')[0].files[0].size/1024/1024;
// 			numb = numb.toFixed(2);
// 			if(numb > 2){
// 				alert('File too big, maximum is 3MB');
// 				$('#ifile').val('');
// 			}
// 		},
// 		error: function() {
// 			alert('Invalid file icon, please change your icon!');
// 		}
// 	});

// });

// $(function() {
// 	$('#iefile').checkFileType({
// 		allowedExtensions: ['jpg', 'jpeg', 'ico', 'png', 'bmp'],
// 		success: function() {
// 			// alert('Allowed extension icon!');
// 			console.log($('#iefile')[0].files[0]);
// 			var numb = $('#iefile')[0].files[0].size/1024/1024;
// 			numb = numb.toFixed(2);
// 			if(numb > 2){
// 				alert('File edit too big, maximum is 3MB');
// 				$('#iefile').val('');
// 				$("#imageDisplay").attr("src", "");
// 				return;
// 			}
// 		},
// 		error: function() {
// 			alert('Invalid file icon, please change your icon!');
// 		}
// 	});
// });


var fileReader = new FileReader();
var fileReaderThumb = new FileReader();
var fileReaderEdit = new FileReader();

// var filterType = /^(?:image\/bmp|image\/cis\-cod|image\/ief|image\/jpeg|image\/jpeg|image\/jpeg|image\/pipeg|image\/png|image\/svg\+xml|image\/tiff|image\/x\-cmu\-raster|image\/x\-cmx|image\/x\-icon|image\/x\-portable\-anymap|image\/x\-portable\-bitmap|image\/x\-portable\-graymap|image\/x\-portable\-pixmap|image\/x\-rgb|image\/x\-xbitmap|image\/x\-xpixmap|image\/x\-xwindowdump)$/i;
fileReader.onload = function (event) {
   	var image = new Image();
   	image.onload=function() {
		document.getElementById("original-Img").src=image.src;
		var canvas=document.createElement("canvas");
		var context=canvas.getContext("2d");
		//canvas.width=image.width/4;
		//canvas.height=image.height/4;
		canvas.width = 800;
		canvas.height = 320;
		context.drawImage(image, 0, 0, image.width, image.height, 0, 0, canvas.width, canvas.height);
		document.getElementById("upload-Preview").src = canvas.toDataURL();
   	}
   	image.src=event.target.result;
};

fileReaderThumb.onload = function (event) {
   	var image = new Image();
   	image.onload=function() {
		document.getElementById("original-Img_thumb").src=image.src;
		var canvas=document.createElement("canvas");
		var context=canvas.getContext("2d");
		//canvas.width=image.width/4;
		//canvas.height=image.height/4;
		canvas.width = 800;
		canvas.height = 320;
		context.drawImage(image, 0, 0, image.width, image.height, 0, 0, canvas.width, canvas.height);
		document.getElementById("upload-Preview_thumb").src = canvas.toDataURL();
   	}
   	image.src=event.target.result;
};

fileReaderEdit.onload = function (event) {
   	var image = new Image();
   	image.onload=function() {
		//document.getElementById("original-Img").src=image.src;
		var canvas=document.createElement("canvas");
		var context=canvas.getContext("2d");
		//canvas.width=image.width/4;
		//canvas.height=image.height/4;
		canvas.width = 800;
		canvas.height = 320;
		context.drawImage(image, 0, 0, image.width, image.height, 0, 0, canvas.width, canvas.height);
		document.getElementById("imageDisplay").src = canvas.toDataURL();
   	}
   	image.src=event.target.result;
};

// var loadImageFile = function () {
//   var uploadImage = document.getElementById("ifile");
//   //check and retuns the length of uploded file.
//   if (uploadImage.files.length === 0) {
//     return;
//   }
//   //Is Used for validate a valid file.
//   var uploadFile = document.getElementById("ifile").files[0];
//   if (!filterType.test(uploadFile.type)) {
//     alert("Please select a valid image.");
//     return;
//   }
//   fileReader.readAsDataURL(uploadFile);
// }

// var loadImageFileEdit = function () {
//   var uploadImage = document.getElementById("iefile");
//   //check and retuns the length of uploded file.
//   if (uploadImage.files.length === 0) {
//     return;
//   }
//   //Is Used for validate a valid file.
//   var uploadFile = document.getElementById("iefile").files[0];
//   if (!filterType.test(uploadFile.type)) {
//     alert("Please select a valid image.");
//     return;
//   }
//   fileReaderEdit.readAsDataURL(uploadFile);
// }

// //end resize upload image

<!-- end comment code -->

<!-- initialize datepicker -->
$('.duedate').datepicker();
$('.duedate').datepicker('setDate', 'today').val("");

<!-- hide datepicker after select date -->
$('.duedate').change(function(){
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
		}
	} else if(!listExtensionImage.indexOf(fileExtensions) > -1 ) { 
		alert("not valid extension");
		$("#ifile").val("");
		$("#upload-Preview").attr("src", "");
		return;
	} else {}
});

$("#ifilethumb").change(function () {
	<!-- verification -->
	<!-- check extension of uploaded file -->
	var inputFileThumb = $("#ifilethumb").val();
	var fileExtensions = inputFileThumb.split('.').pop();
	var listExtensionImage = ["jpg", "jpeg", "png", "gif"]; <!-- by Muhammad Sofi 6 January 2022 11:09 | add extension -->
	
	if(listExtensionImage.indexOf(fileExtensions) > -1) {
		var numb = $("#ifilethumb")[0].files[0].size/1024/1024;
		var size = numb.toFixed(3);
		var maxImageUpload = 5.120;
		if(size > maxImageUpload){
			alert("File too big, maximum is 5MB or less");
			$("#ifilethumb").val("");
			$("#upload-Preview_thumb").attr("src", "");
		} else {
			var uploadFile = document.getElementById("ifilethumb").files[0];
			fileReaderThumb.readAsDataURL(uploadFile);
		}
	} else if(!listExtensionImage.indexOf(fileExtensions) > -1 ) { 
		alert("not valid extension");
		$("#ifilethumb").val("");
		$("#upload-Preview_thumb").attr("src", "");
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

<!-- add data -->

$("#itype_sponsored").change(function(){
	var type_sponsored = $("#itype_sponsored").val();
	if(type_sponsored == "original" || type_sponsored == "") {
		$("#row_seller_shop").hide();
		$("#row_seller_shop_product").hide();
		$("#row_product_detail").hide();
		$("#select_seller_shop").val("").trigger("change");
		$("#select_seller_shop_product").val("").trigger("change");
		$("#select_product_detail").val("").trigger("change");
	} else if(type_sponsored == "shop") {
		$("#row_seller_shop").show();
		$("#row_seller_shop_product").hide();
		$("#row_product_detail").hide();
		$("#select_seller_shop").val("").trigger("change");
		$("#select_seller_shop_product").val("").trigger("change");
		$("#select_product_detail").val("").trigger("change");
	} else if(type_sponsored == "product") {
		$("#row_seller_shop_product").show();
		$("#row_product_detail").show();
		$("#row_seller_shop").hide();
		$("#select_seller_shop").val("").trigger("change");
		$("#select_seller_shop_product").val("").trigger("change");
		$("#select_product_detail").val("").trigger("change");
	} else {}
});

$("#select_seller_shop").select2({
	placeholder: "--Select Seller--",
	width: "100%",
	allowClear: true, <!-- add x button to clear value -->
	ajax: { 
		url: "<?= base_url('api_admin/campaign/getCustomer') ?>",
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
	var value = $("#select_seller_shop option:selected").val();
	$("#select_seller_shop_value").val(value);
});

$("#select_seller_shop_product").select2({
	placeholder: "--Select Seller--",
	width: "100%",
	allowClear: true, <!-- add x button to clear value -->
	ajax: { 
		url: "<?= base_url('api_admin/campaign/getCustomer') ?>",
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
	var value = $("#select_seller_shop_product option:selected").val();
	$("#select_seller_shop_product_value").val(value);
});

$("#select_product_detail").select2({
	placeholder: "--Select Product--",
	width: "100%",
	allowClear: true, <!-- add x button to clear value -->
	ajax: { 
		url: "<?= base_url('api_admin/campaign/getProductDetail') ?>",
		type: "post",
		dataType: 'json',
		delay: 250,
		data: function (params) {
			return {
				seller_id: $("#select_seller_shop_product_value").val(),
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
	var value = $("#select_product_detail option:selected").val();
	$("#select_product_detail_value").val(value);
});

<!-- edit data -->

$("#itype_sponsored_edit").change(function(){
	var type_sponsored_edit = $("#itype_sponsored_edit").val();
	if(type_sponsored_edit == "original" || type_sponsored_edit == "") {
		$("#row_seller_shop_edit").hide();
		$("#row_seller_shop_product_edit").hide();
		$("#row_product_detail_edit").hide();
		$("#select_seller_shop_edit").val("").trigger("change");
		$("#select_seller_shop_product_edit").val("").trigger("change");
		$("#select_product_detail_edit").val("").trigger("change");
	} else if(type_sponsored_edit == "shop") {
		$("#row_seller_shop_edit").show();
		$("#row_seller_shop_product_edit").hide();
		$("#row_product_detail_edit").hide();
		$("#select_seller_shop_edit").val("").trigger("change");
		$("#select_seller_shop_product_edit").val("").trigger("change");
		$("#select_product_detail_edit").val("").trigger("change");
	} else if(type_sponsored_edit == "product") {
		$("#row_seller_shop_product_edit").show();
		$("#row_product_detail_edit").show();
		$("#row_seller_shop_edit").hide();
		$("#select_seller_shop_edit").val("").trigger("change");
		$("#select_seller_shop_product_edit").val("").trigger("change");
		$("#select_product_detail_edit").val("").trigger("change");
	} else {}
});

$("#select_seller_shop_edit").select2({
	placeholder: "--Select Seller--",
	width: "100%",
	allowClear: true, <!-- add x button to clear value -->
	ajax: { 
		url: "<?= base_url('api_admin/campaign/getCustomer') ?>",
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
	var value = $("#select_seller_shop_edit option:selected").val();
	$("#select_seller_shop_value_edit").val(value);
	$("#ieselect_seller_shop_value_edit").val(value);
});


$("#select_seller_shop_product_edit").select2({
	placeholder: "--Select Seller--",
	width: "100%",
	allowClear: true, <!-- add x button to clear value -->
	ajax: { 
		url: "<?= base_url('api_admin/campaign/getCustomer') ?>",
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
	var value = $("#select_seller_shop_product_edit option:selected").val();
	$("#select_seller_shop_product_value_edit").val(value);
});

$("#select_product_detail_edit").select2({
	placeholder: "--Select Product--",
	width: "100%",
	allowClear: true, <!-- add x button to clear value -->
	ajax: { 
		url: "<?= base_url('api_admin/campaign/getProductDetail') ?>",
		type: "post",
		dataType: 'json',
		delay: 250,
		data: function (params) {
			return {
				seller_id: $("#select_seller_shop_product_value_edit").val(),
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
	var value = $("#select_product_detail_edit option:selected").val();
	$("#select_product_detail_value_edit").val(value);
	$("#ieselect_product_detail_value_edit").val(value);
});