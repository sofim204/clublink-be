var growlPesan = '<h4>Error</h4><p>Cannot be proceed. Please try again later!</p>';
var growlType = 'danger';
var drTable = {};
var ieid = '';
var url_def = '';
var type_ads = ''; <!-- by Muhammad Sofi 17 January 2022 11:06 | add logic if banner is video, hide input description -->
var type_eventbanner = ''; // variable for type event(original, shop, product)

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
		"columnDefs"		:[{
								"targets": [1], <!-- hide column --> 
								"visible": false,
								"searchable": false
							}],
		//"scrollX"			: true, <!-- by Muhammad Sofi 7 January 2022 21:06 | remark unused code-->
		"order"				: [[ 0, "asc" ]],
		"responsive"	  	: true,
		"bProcessing"		: true,
		"bServerSide"		: true,
		"searching"			: false, // hide input search
		"sAjaxSource"		: "<?=base_url("api_admin/sellon_ads/"); ?>",
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
				//console.log(response);
				NProgress.done();
				$('#drTable > tbody').off('click', 'tr');
				$('#drTable > tbody').on('click', 'tr', function (e) {
					e.preventDefault();
					var currentRow = $(this).closest("tr");
					var id = $('#drTable').DataTable().row(currentRow).data()[1]; <!-- to get data from specific column, change this "data()[id_column]" -->
					ieid = id
					var url = '<?=base_url("api_admin/sellon_ads/detail/")?>' + ieid;
					$.get(url).done(function(response){
						if(response.status==200){
							var dta = response.data;
							$("#ieid").val(dta.id);
							$("#iepriority").val(dta.priority);
							$("#ieurl").val(dta.url);
							url_def = dta.url;
							$("#iecdate").val(dta.start_date);
							$("#iecdate").datepicker('setDate', dta.start_date).val();
							$("#ieedate").datepicker('setDate', dta.end_date).val();
							$("#ieis_active").val(dta.is_active);

							$("#edit_url_webview").val(dta.url_webview);
							
							$("#ieejudul").val(dta.judul);
							$("#ieeteks").html(dta.teks);
							jqueryA('#ieeteks').Editor('setText',$("#ieeteks").val());
							
							if(dta.url_type == "image") {
								$("#modal_options").modal("show");
								$("#video_id, #video_loader").hide(); <!-- by Muhammad Sofi 7 January 2022 14:26 | hide preview video if there is no video -->
								$("#imageDisplay").attr("src","<?=base_url()?>"+dta.url);
							}

							<!-- check if event banner is video or photo before show edit modal -->
							var url_type = dta.url_type; 
							type_ads = url_type; <!-- by Muhammad Sofi 17 January 2022 11:06 | add logic if banner is video, hide input description -->

							$("#ieselect_seller_shop_value_edit").val(dta.seller_id);
							$("#ieselect_product_detail_value_edit").val(dta.product_id);
							$("#ieselect_community_value_edit").val(dta.community_id);
							$("#ieitype_ads").val(dta.type_ads);
							
							var type = dta.type_ads;
							type_eventbanner = type;
							
							//if(dta.teks) {
							//	$("#edit_webview_teks").show();
							//	$("#edit_url_webview").val(dta.url_webview);
							//} else {
							//	$("#edit_webview_teks").hide();
							//}

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
	$('.dataTables_filter input').attr('placeholder', 'Search event banner title').css({'width':'250px', 'display':'inline-block'});
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

$("#modal_options").on('hidden.bs.modal', function (e) {
});

$("#aEditBanner").on("click",function(e){
	e.preventDefault();
	//$("#modal_options").modal("hide");
	//setTimeout(function(){
	//	$("#modal_edit").modal("show");
	//}, 300);

	$("#modal_options").modal("hide");
	setTimeout(function(){
		<!-- by Muhammad Sofi 17 January 2022 11:06 | add logic if banner is video, hide input description -->
		<!-- if type event banner is video, hide textarea -->
		if(type_ads == "video") {
			$("#textarea_editbanner").hide();
			<!-- START by Muhammad Sofi 23 January 2022 23:00 | improvement and bug fixing on edit event banner -->
			$("#editFilePhoto").hide();
			$("#editFileVideo").show();
			$("#videopreviewedit, #textvideopreviewedit").show();

			$("#iefile").prop('disabled', true);
			$("#iefileVideo").prop('disabled', false);

		} else if(type_ads == "image") {
			if(type_eventbanner == "shop" || type_eventbanner == "product" || type_eventbanner == "community") {
				$("#textarea_editbanner").hide();
			} else {
				$("#textarea_editbanner").show();
			}

			$("#editFilePhoto").show();
			$("#editFileVideo").hide();
			$("#videopreviewedit").hide();

			$("#iefile").prop('disabled', false);
			$("#iefileVideo").prop('disabled', true);
			<!-- END by Muhammad Sofi 23 January 2022 23:00 | improvement and bug fixing on edit event banner -->
		} else {}
		$("#modal_edit").modal("show");
	}, 200);
});

$("#aEditBannerThumbnail").on("click",function(e){
	e.preventDefault();
	$("#modal_options").modal("hide");
	setTimeout(function(){
		$("#modal_edit_thumbnail").modal("show");
	}, 300);	
});

$("#atestURL").on("click",function(e){
	e.preventDefault();
	$("#modal_options").modal("hide");
	window.open('<?=base_url_admin('sellon_ads/getDetailAds/')?>'+ieid, '_blank');
});

$("#atambah").on("click",function(e){
	e.preventDefault();
	$("#modal_tambah").modal("show");
});

$("#modal_tambah").on("shown.bs.modal",function(e){
	$("#ifile, #ifilevideo").prop("readonly", false);
	$("#original-File").attr("src","");
	$("#upload-Preview").attr("src","");
});

$("#modal_tambah").on("hidden.bs.modal",function(e){
	$("#modal_tambah").find("form").trigger("reset");
	$("#webview_teks, #banner_ads_detail").hide();
	$("#ieteks").val("");
});

$("#modal_edit_thumbnail").on("hidden.bs.modal",function(e){
	$("#modal_edit_thumbnail").find("form").trigger("reset");
	$("#ieimageicon").val("");
});

$("#iitype_ads").on("change", () => {
	if($("#iitype_ads").val() === "webview" || $("#iitype_ads").val() === "webview_wallet_ads") {
		$("#webview_teks, #banner_ads_detail").show();
	} else {
		$("#webview_teks, #banner_ads_detail").hide();
	}
});

$("#ftambah").on("submit",function(e){
	$('#ieteks').val(jqueryA('#ieteks').Editor('getText')); <!-- by Muhammad Sofi 3 January 2022 17:18 | add description for event banner -->
	e.preventDefault();
	NProgress.start();
	var fd = new FormData($("#ftambah")[0]);
	console.log(fd);
	var url = '<?=base_url("api_admin/sellon_ads/tambah/")?>';
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
			Swal.fire({
				title: 'Advertisement has been added successfuly',
				icon: 'success',
				showConfirmButton: false,
				timer: 1500
			});
			<!-- Start by Muhammad Sofi 7 January 2022 21:43 | after save data, clear value form data in modal -->
			drTable.ajax.reload();
			$('#ftambah')[0].reset();
			$(".add_date").datepicker('setDate', 'today').val("");
			$("#modal_tambah").modal("hide");
			
		} else {
			//gritter( '<h4>Failed</h4><p>'+respon.message+'</p>','danger');
			Swal.fire({
				icon: 'error',
				title: 'Failed to edit data',
				confirmButtonText: 'Okay',
			});
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
	$("#row_community_edit").hide();
	$("#select_seller_shop_edit").val("").trigger("change");
	$("#select_seller_shop_product_edit").val("").trigger("change");
	$("#select_product_detail_edit").val("").trigger("change");
	$("#select_community_edit").val("").trigger("change");
	$("#videopreviewedit")[0].pause();
});

$("#ieitype_ads").on("change", () => {
	if($("#ieitype_ads").val() === "webview" || $("#ieitype_ads").val() === "webview_wallet_ads") {
		$("#edit_webview_teks, #edit_banner_ads_detail").show();
	} else {
		$("#edit_webview_teks, #edit_banner_ads_detail").hide();
	}
});

$("#ftedit").on("submit",function(e){
	e.preventDefault();
	NProgress.start();
	//gritter( '<h4>Processing</h4><p>Please wait while uploading image...</p>','info');
	var fd = new FormData($("#ftedit")[0]);
	var url = '<?=base_url("api_admin/sellon_ads/edit/")?>'+ieid;
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
			//gritter('<h4>Success</h4><p>Data edited successfuly</p>','success');
			Swal.fire({
				title: 'Event Banner edited successfuly',
				icon: 'success',
				showConfirmButton: false,
				timer: 1500
			});
			$('#ftedit')[0].reset();
			$("#imageDisplay").attr("src", "");
			drTable.ajax.reload();
			//setTimeout(function(){
			//	window.location = '<?php echo base_url_admin('sellon_ads'); ?>';
			//}, 600);
		}else{
			//gritter('<h4>Failed</h4><p>'+respon.message+'</p>','danger');
			Swal.fire({
				icon: 'error',
				title: 'Failed to edit data',
				confirmButtonText: 'Okay',
			});
			drTable.ajax.reload();
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
$("#bhapus").on("click", (e) => {
	e.preventDefault();
	if(ieid){
		Swal.fire({
			title: 'Are you sure to Delete?',
			icon: 'error',
			showDenyButton: true,
			confirmButtonText: 'DELETE',
			denyButtonText: 'CANCEL',
			reverseButtons: false
		}).then((result) => {
			if (result.isConfirmed) {
				var url = '<?=base_url('api_admin/sellon_ads/hapus/')?>'+ieid;
				$.get(url).done(function(response){
					$("#modal_edit").modal("hide");
					if(response.status==200){
						//gritter('<h4>Success</h4><p>Data successfuly deleted</p>','success');
						//Swal.fire('Successfully Deleted!', '', 'success');
						Swal.fire({
							title: 'Successfully Deleted!',
							icon: 'success',
							//background: '#272424',
							//color: '#FAFAFA',
							showConfirmButton: false,
							timer: 1500
						});
						drTable.ajax.reload();
						$("#modal_options").modal("hide");
					}else{
						gritter('<h4>Failed</h4><p>'+response.message+'</p>','warning');
					}
				}).fail(function() {
					gritter('<h4>Error</h4><p>Cant delete data right now, please try again later</p>','warning');
				});
			} else if (result.isDenied) { }
		});
	}
});

$(".due-date").datepicker({
	startDate: new Date(),
	format: "yyyy-mm-dd",
});

<!-- initialize datepicker -->
$('#icdate, #iedate').datepicker();
$('#icdate, #iedate').datepicker('setDate', 'today').val("");

$("#icdate, #iedate, #iecdate, #ieedate").change(function() {
	$('.datepicker').hide();
});

<!-- validation for date (modal add)-->
$("#iedate").change(function(){
	var startDate = $("#icdate").val();
	var endDate = $("#iedate").val();
	if(endDate < startDate) {
		Swal.fire({
			icon: 'warning',
			title: 'Sorry, End Date cannot less than Start Date',
			confirmButtonText: 'Okay',
		});
		$("#iedate").val("");
	}
});

<!-- validation for date (modal edit)-->
$("#ieedate").change(function(){
	var startDate = $("#iecdate").val();
	var endDate = $("#ieedate").val();
	if(endDate < startDate) {
		Swal.fire({
			icon: 'warning',
			title: 'Sorry, End Date cannot less than Start Date',
			confirmButtonText: 'Okay',
		});
		$("#iedate").val("");
	}
});


//begin resize upload image
var fileReaderImage = new FileReader();
var fileReaderImageEdit = new FileReader(); <!-- by Muhammad Sofi 23 January 2022 23:00 | improvemnt and bug fixing on edit data event banner -->

var filterType = /^(?:image\/bmp|image\/cis\-cod|image\/gif|image\/ief|image\/jpeg|image\/jpeg|image\/jpeg|image\/pipeg|image\/png|image\/svg\+xml|image\/tiff|image\/x\-cmu\-raster|image\/x\-cmx|image\/x\-icon|image\/x\-portable\-anymap|image\/x\-portable\-bitmap|image\/x\-portable\-graymap|image\/x\-portable\-pixmap|image\/x\-rgb|image\/x\-xbitmap|image\/x\-xpixmap|image\/x\-xwindowdump|video\/mp4|video\/mkv)$/i;

fileReaderImage.onload = function (event) {
  var image = new Image();
  image.onload=function(){
    document.getElementById("original-File").src=image.src;
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

<!-- by Muhammad Sofi 23 January 2022 23:00 | improvemnt and bug fixing on edit data event banner -->
fileReaderImageEdit.onload = function (event) {
  var image = new Image();
  image.onload=function(){
    var canvas=document.createElement("canvas");
    var context=canvas.getContext("2d");
    canvas.width = 800;
    canvas.height = 320;
    context.drawImage(image, 0, 0, image.width, image.height, 0, 0, canvas.width, canvas.height);
    document.getElementById("imageDisplay").src = canvas.toDataURL();
  }
  image.src=event.target.result;
};

<!-- by Muhammad Sofi 23 January 2022 23:00 | improvemnt and bug fixing on edit data event banner -->

$("#isuploadfile").change(function(){
	if($("#isuploadfile").val() === "image") {
		$("#chooseFile").show();
		$("#chooseFileVideo").hide();
		$("#ifileVideo").prop('disabled', true);
		$("#ifile").prop('disabled', false);
		$('#ifile').val('');
		$("#textonly_video, #textvideopreview, #video-Preview, #divPreview").hide();
		$('#ifileVideo, #textimagebase64').val('');
		$("#input_description").show(); <!-- by Muhammad Sofi 10 January 2022 18:53 | change position input description, hide if input event banner video -->
	} 

	if($("#isuploadfile").val() === "video") {
		$("#chooseFileVideo").show();
		$("#chooseFile").hide();
		$("#ifile").prop('disabled', true);
		$("#ifileVideo").prop('disabled', false);

		$("#textonly_video, #textvideopreview, #divPreview").show();
		$("#upload-Preview").hide();
		$('#ifile').val('');
		$("#upload-Preview").attr("src", "");

		var canvas = document.getElementById('myCanvas');
		var context = canvas.getContext('2d');
		context.clearRect(0, 0, canvas.width, canvas.height);
		$("#input_description").hide(); <!-- by Muhammad Sofi 10 January 2022 18:53 | change position input description, hide if input event banner video -->
	}

	if($("#isuploadfile").val() === "") {
		<!-- by Muhammad Sofi 17 January 2022 11:06 | reset image and video & snapshot file upload -->
		$("#chooseFile, #chooseFileVideo").hide();
		$("#textonly_video, #textvideopreview, #video-Preview, #divPreview").hide();
		$("#input_description").hide();
	}
});

// by Muhammad Sofi 5 January 2022 20:22 | can input file image/video, and add validation when upload file
$("#ifile").change(function () {
	<!-- verification -->

	<!-- check extension of uploaded file -->
	var inputFile = $("#ifile").val();
	var fileExtensions = inputFile.split('.').pop();
	var listExtensionImage = ["jpg", "jpeg", "png", "ico", "tiff", "tif", "bmp", "gif"]; <!-- by Muhammad Sofi 6 January 2022 11:09 | add extension -->
	
	if(listExtensionImage.indexOf(fileExtensions) > -1) {
		//alert("this is image extension"); <!-- by Muhammad Sofi 5 January 2022 20:22 | comment code -->
		var numb = $('#ifile')[0].files[0].size/1024/1024;
		var size = numb.toFixed(3);
		var maxImageUpload = 5.120;
		if(size > maxImageUpload){
			Swal.fire({
				icon: 'warning',
				title: 'File is too big, maximum is 5MB or less',
				confirmButtonText: 'Okay',
			});
			$('#ifile').val('');
			$("#upload-Preview").attr("src", "");
		} else {
			var uploadFile = document.getElementById("ifile").files[0];
			fileReaderImage.readAsDataURL(uploadFile);
		}
	} else if(!listExtensionImage.indexOf(fileExtensions) > -1 && inputFile != "") { 
		Swal.fire({
			icon: 'warning',
			title: 'Not valid image extension',
			confirmButtonText: 'Okay',
		});
		$('#ifile').val('');
		$("#upload-Preview").attr("src", "");
		return;
	} else {}
});

$("#ifileVideo").change(function () {
	$("#video-Preview").show(); <!-- after upload video, show video preview canvas -->
	$("#textonly_video").hide();
	<!-- verification -->

	<!-- check extension of uploaded file -->
	var inputFile = $("#ifileVideo").val();
	var fileExtensions = inputFile.split('.').pop();
	var listExtensionVideo = ["mp4", "mkv", "mov", "3gp"]; <!-- by Muhammad Sofi 6 January 2022 11:09 | add extension -->
	
	if(listExtensionVideo.indexOf(fileExtensions) > -1) {
		//alert("this is video extension"); <!-- by Muhammad Sofi 5 January 2022 20:22 | comment code -->
		var numb = $('#ifileVideo')[0].files[0].size/1024/1024;
		var size = numb.toFixed(3);
		var maxVideoUpload = 40.960; <!-- by Muhammad Sofi 20 January 2022 11:55 | request from mr jackie, change max file size upload to 40MB -->
		if(size > maxVideoUpload){
			Swal.fire({
				icon: 'warning',
				title: 'File is too big, maximum is 40MB or less',
				confirmButtonText: 'Okay',
			});
			$('#ifileVideo').val('');
			$('#video-Preview').hide();
			$("#textonly_video").show();
			//$("#upload-Preview").attr("src", "");
		} else {
			let fileVideo = document.getElementById("ifileVideo").files[0];
			let blobURL = URL.createObjectURL(fileVideo);
			document.getElementById("video-Preview").style.display = 'block';
			document.getElementById("video-Preview").src = blobURL;
		}
	} else if(!listExtensionVideo.indexOf(fileExtensions) > -1 && inputFile != "") { 
		Swal.fire({
			icon: 'warning',
			title: 'Not valid video extension',
			confirmButtonText: 'Okay',
		});
		$('#ifileVideo').val('');
		$('#video-Preview').hide();
		$("#textonly_video").show();
		return;
	} else {}
});

<!-- START by Muhammad Sofi 23 January 2022 23:00 | improvemnt and bug fixing on edit data event banner -->
$("#iefile").change(function () {
	<!-- verification -->

	<!-- check extension of uploaded file -->
	var inputFile = $("#iefile").val();
	var fileExtensions = inputFile.split('.').pop();
	var listExtensionImage = ["jpg", "jpeg", "png", "ico", "tiff", "tif", "bmp", "gif"]; <!-- by Muhammad Sofi 6 January 2022 11:09 | add extension -->
	
	if(listExtensionImage.indexOf(fileExtensions) > -1) {
		var numb = $('#iefile')[0].files[0].size/1024/1024;
		var size = numb.toFixed(3);
		var maxImageUpload = 5.120;
		if(size > maxImageUpload){
			Swal.fire({
				icon: 'warning',
				title: 'File is too big, maximum is 5MB or less',
				confirmButtonText: 'Okay',
			});
			$('#iefile').val('');
			$("#imageDisplay").attr("src", "");
		} else {
			var uploadFile = document.getElementById("iefile").files[0];
			fileReaderImageEdit.readAsDataURL(uploadFile);
		}
	} else if(!listExtensionImage.indexOf(fileExtensions) > -1 && !listExtensionVideo.indexOf(fileExtensions) > -1) { 
		Swal.fire({
			icon: 'warning',
			title: 'Not valid extension',
			confirmButtonText: 'Okay',
		});
		$('#iefile').val('');
		$("#imageDisplay").attr("src", "");
		return;
	} else {}
});

$("#iefileVideo").change(function () {
	//$("#video-Preview").show(); <!-- after upload video, show video preview canvas -->
	//$("#textonly_video").hide();
	<!-- verification -->

	<!-- check extension of uploaded file -->
	var inputFile = $("#iefileVideo").val();
	var fileExtensions = inputFile.split('.').pop();
	var listExtensionVideo = ["mp4", "mkv", "mov", "3gp"]; <!-- by Muhammad Sofi 6 January 2022 11:09 | add extension -->
	
	if(listExtensionVideo.indexOf(fileExtensions) > -1) {
		//alert("this is video extension"); <!-- by Muhammad Sofi 5 January 2022 20:22 | comment code -->
		var numb = $('#iefileVideo')[0].files[0].size/1024/1024;
		var size = numb.toFixed(3);
		var maxVideoUpload = 40.960; <!-- by Muhammad Sofi 20 January 2022 11:55 | request from mr jackie, change max file size upload to 40MB -->
		if(size > maxVideoUpload){
			Swal.fire({
				icon: 'warning',
				title: 'File is too big, maximum is 40MB or less',
				confirmButtonText: 'Okay',
			});
			$('#iefileVideo').val('');
			$('#videopreviewedit').hide();
			//$("#textonly_video").show();
			//$("#upload-Preview").attr("src", "");
		} else {
			let fileVideo = document.getElementById("iefileVideo").files[0];
			let blobURL = URL.createObjectURL(fileVideo);
			document.getElementById("videopreviewedit").style.display = 'block';
			document.getElementById("videopreviewedit").src = blobURL;
			$("#divPreviewEdit").show();
		}
	} else if(!listExtensionVideo.indexOf(fileExtensions) > -1 && inputFile != "") { 
		Swal.fire({
			icon: 'warning',
			title: 'Not valid video extension',
			confirmButtonText: 'Okay',
		});
		$('#iefileVideo').val('');
		$('#videopreviewedit').hide();
		//$("#textonly_video").show();
		return;
	} else {}
});
<!-- END by Muhammad Sofi 23 January 2022 23:00 | improvemnt and bug fixing on edit data event banner -->

<!-- by Muhammad Sofi 17 January 2022 11:06 | checking file upload size -->
$("#ieimage_icon").change(function () {
	var numb = $('#ieimage_icon')[0].files[0].size/1024/1024;
	var size = numb.toFixed(3);
	var maxImageUpload = 5.120;
	if(size > maxImageUpload){
		Swal.fire({
			icon: 'warning',
			title: 'File is too big, maximum is 5MB or less',
			confirmButtonText: 'Okay',
		});
		$('#ieimage_icon').val('');
	} else {}
});

$("#fthumbnail_change").on("submit",function(e){
	e.preventDefault();
	NProgress.start();
	var url = '<?=base_url("api_admin/sellon_ads/change_thumbnail/")?>'+ieid;
	$.ajax({
		type: $(this).attr('method'),
		url: url,
		data: new FormData(this),
		processData: false,
		contentType: false,
		success: function(respon){
			if(respon.status==200){
				//gritter('<h4>Success</h4><p>Thumbnail changed successfully</p>','success');
				//setTimeout(function(){
				//	window.location = '<?php echo base_url_admin('sellon_ads'); ?>';
				//},500);
				Swal.fire({
					title: 'Thumbnail changed successfully',
					icon: 'success',
					showConfirmButton: false,
					timer: 1500
				});
				$('#ftambah')[0].reset();
				$("#modal_edit_thumbnail").modal("hide");
				drTable.ajax.reload();
			}else{
				NProgress.done();
				gritter('<h4>Failed</h4><p>'+respon.message+'</p>','danger');
				$("#modal_edit_thumbnail").modal("hide");
			}
		},
		error:function(){
			setTimeout(function(){
				$("#modal_edit_thumbnail").modal("hide");
				NProgress.done();
				gritter('<h4>Error</h4><p>Cant change icon right now, please try again later</p>','warning');
			}, 666);
			return false;
		}
	});
});

<!-- add data -->

$("#itype_event_banner").change(function(){
	var type_ads = $("#itype_event_banner").val();
	if(type_ads == "original" || type_ads == "polling") {
		$("#row_url").hide();
		$("#row_seller_shop").hide();
		$("#row_seller_shop_product").hide();
		$("#row_product_detail").hide();
		$("#row_community").hide();
		$("#select_seller_shop").val("").trigger("change");
		$("#select_seller_shop_product").val("").trigger("change");
		$("#select_product_detail").val("").trigger("change");
		$("#select_community").val("").trigger("change");
		$("#input_description").show();
	} else if(type_ads == "shop") {
		$("#row_url").hide();
		$("#input_url_webview").val("");
		$("#row_seller_shop").show();
		$("#row_seller_shop_product").hide();
		$("#row_product_detail").hide();
		$("#row_community").hide();
		$("#select_seller_shop").val("").trigger("change");
		$("#select_seller_shop_product").val("").trigger("change");
		$("#select_product_detail").val("").trigger("change");
		$("#select_community").val("").trigger("change");
		$("#input_description").hide();
	} else if(type_ads == "product") {
		$("#row_url").hide();
		$("#input_url_webview").val("");
		$("#row_seller_shop_product").show();
		$("#row_product_detail").show();
		$("#row_seller_shop").hide();
		$("#row_community").hide();
		$("#select_seller_shop").val("").trigger("change");
		$("#select_seller_shop_product").val("").trigger("change");
		$("#select_product_detail").val("").trigger("change");
		$("#select_community").val("").trigger("change");
		$("#input_description").hide();
	} else if(type_ads == "community") {
		$("#row_url").hide();
		$("#input_url_webview").val("");
		$("#row_seller_shop_product").hide();
		$("#row_product_detail").hide();
		$("#row_seller_shop").hide();
		$("#row_community").show();
		$("#select_seller_shop").val("").trigger("change");
		$("#select_seller_shop_product").val("").trigger("change");
		$("#select_product_detail").val("").trigger("change");
		$("#select_community").val("").trigger("change");
		$("#input_description").hide();
	} else if(type_ads == "webview_wallet") {
		$("#row_url").show();
		$("#row_seller_shop_product").hide();
		$("#row_product_detail").hide();
		$("#row_seller_shop").hide();
		$("#row_community").hide();
		$("#select_seller_shop").val("").trigger("change");
		$("#select_seller_shop_product").val("").trigger("change");
		$("#select_product_detail").val("").trigger("change");
		$("#select_community").val("").trigger("change");
		$("#input_description").hide();
	} else if(type_ads == "guide") {
		$("#row_url").hide();
		$("#input_url_webview").val("");
		$("#row_seller_shop_product").hide();
		$("#row_product_detail").hide();
		$("#row_seller_shop").hide();
		$("#row_community").hide();
		$("#select_seller_shop").val("").trigger("change");
		$("#select_seller_shop_product").val("").trigger("change");
		$("#select_product_detail").val("").trigger("change");
		$("#select_community").val("").trigger("change");
		$("#input_description").hide();
	} else if(type_ads == "activity_dashboard") {
		$("#row_url").hide();
		$("#input_url_webview").val("");
		$("#row_seller_shop_product").hide();
		$("#row_product_detail").hide();
		$("#row_seller_shop").hide();
		$("#row_community").hide();
		$("#select_seller_shop").val("").trigger("change");
		$("#select_seller_shop_product").val("").trigger("change");
		$("#select_product_detail").val("").trigger("change");
		$("#select_community").val("").trigger("change");
		$("#input_description").hide();
	} else if(type_ads == "invitation_page") {
		$("#row_url").show();
		$("#input_url_webview").val("");
		$("#row_seller_shop_product").hide();
		$("#row_product_detail").hide();
		$("#row_seller_shop").hide();
		$("#row_community").hide();
		$("#select_seller_shop").val("").trigger("change");
		$("#select_seller_shop_product").val("").trigger("change");
		$("#select_product_detail").val("").trigger("change");
		$("#select_community").val("").trigger("change");
		$("#input_description").hide();
	} else {}
});

$("#select_seller_shop").select2({
	placeholder: "--Select Seller--",
	width: "100%",
	allowClear: true, <!-- add x button to clear value -->
	ajax: { 
		url: "<?= base_url('api_admin/sellon_ads/getCustomer') ?>",
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
		url: "<?= base_url('api_admin/sellon_ads/getCustomer') ?>",
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
		url: "<?= base_url('api_admin/sellon_ads/getProductDetail') ?>",
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

$("#select_community").select2({
	placeholder: "--Select Post--",
	width: "150%",
	allowClear: true, <!-- add x button to clear value -->
	ajax: { 
		url: "<?= base_url('api_admin/sellon_ads/getCommunity') ?>",
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
	var value = $("#select_community option:selected").val();
	$("#select_community_value").val(value);
});

<!-- edit data -->

$("#itype_event_banner_edit").change(function(){
	var type_event_banner_edit = $("#itype_event_banner_edit").val();
	if(type_event_banner_edit == "original" || type_event_banner_edit == "polling") {
		$("#row_seller_shop_edit").hide();
		$("#row_seller_shop_product_edit").hide();
		$("#row_product_detail_edit").hide();
		$("#row_community_edit").hide();
		$("#select_seller_shop_edit").val("").trigger("change");
		$("#select_seller_shop_product_edit").val("").trigger("change");
		$("#select_product_detail_edit").val("").trigger("change");
		$("#select_community_edit").val("").trigger("change");
		$("#ietype_value_edit").val(type_event_banner_edit);
	} else if(type_event_banner_edit == "shop") {
		$("#row_seller_shop_edit").show();
		$("#row_seller_shop_product_edit").hide();
		$("#row_product_detail_edit").hide();
		$("#row_community_edit").hide();
		$("#select_seller_shop_edit").val("").trigger("change");
		$("#select_seller_shop_product_edit").val("").trigger("change");
		$("#select_product_detail_edit").val("").trigger("change");
		$("#select_community_edit").val("").trigger("change");
		$("#ietype_value_edit").val(type_event_banner_edit);
	} else if(type_event_banner_edit == "product") {
		$("#row_seller_shop_product_edit").show();
		$("#row_product_detail_edit").show();
		$("#row_seller_shop_edit").hide();
		$("#row_community_edit").hide();
		$("#select_seller_shop_edit").val("").trigger("change");
		$("#select_seller_shop_product_edit").val("").trigger("change");
		$("#select_product_detail_edit").val("").trigger("change");
		$("#select_community_edit").val("").trigger("change");
		$("#ietype_value_edit").val(type_event_banner_edit);
	} else if(type_event_banner_edit == "community") {
		$("#row_seller_shop_product_edit").hide();
		$("#row_product_detail_edit").hide();
		$("#row_seller_shop_edit").hide();
		$("#row_community_edit").show();
		$("#select_seller_shop_edit").val("").trigger("change");
		$("#select_seller_shop_product_edit").val("").trigger("change");
		$("#select_product_detail_edit").val("").trigger("change");
		$("#select_community_edit").val("").trigger("change");
		$("#ietype_value_edit").val(type_event_banner_edit);
	} else {}
});

$("#select_seller_shop_edit").select2({
	placeholder: "--Select Seller--",
	width: "100%",
	allowClear: true, <!-- add x button to clear value -->
	ajax: { 
		url: "<?= base_url('api_admin/sellon_ads/getCustomer') ?>",
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
		url: "<?= base_url('api_admin/sellon_ads/getCustomer') ?>",
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
		url: "<?= base_url('api_admin/sellon_ads/getProductDetail') ?>",
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

$("#select_community_edit").select2({
	placeholder: "--Select Post--",
	width: "100%",
	allowClear: true, <!-- add x button to clear value -->
	ajax: { 
		url: "<?= base_url('api_admin/sellon_ads/getCommunity') ?>",
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
	var value = $("#select_community_edit option:selected").val();
	$("#select_community_value_edit").val(value);
	$("#ieselect_community_value_edit").val(value);
});

//$("#iimg_event_banner").change(function () {
//	var numb = $('#iimg_event_banner')[0].files[0].size/1024/1024;
//	var size = numb.toFixed(3);
//	var maxImageUpload = 5.120;
//	if(size > maxImageUpload){
//		alert('File too big, maximum is 5MB or less');
//		$('#iimg_event_banner').val('');
//	} else {}
//});

var inputs = document.querySelectorAll('.file-input');

for (var i = 0, len = inputs.length; i < len; i++) {
  customInput(inputs[i]);
}

function customInput (el) {
  const fileInput = el.querySelector('[type="file"]');
  const label = el.querySelector('[data-js-label]');
  
  fileInput.onchange =
  fileInput.onmouseout = function () {
    if (!fileInput.value) return
    
    var value = fileInput.value.replace(/^.*[\\\/]/, '')
    el.className += ' -chosen'
    label.innerText = value
  }
}

$(document).ready(function() {
   	<!-- by Muhammad Sofi 8 January 2022 17:48 | get thumbnail photo -->
	<!-- Get handles on the video and canvas elements -->
	var video = document.getElementById("video-Preview");
	var canvas = document.getElementById("myCanvas");

	<!-- Get a handle on the 2d context of the canvas element -->
	var context = canvas.getContext('2d');
	<!-- Define some vars required later -->
	var w, h, ratio;
	
	<!-- Add a listener to wait for the 'loadedmetadata' state so the video(s) dimensions can be read -->
	video.addEventListener('loadedmetadata', function() {
		<!-- Calculate the ratio of the video's width to height -->
		ratio = video.videoWidth / video.videoHeight;
		<!-- Define the required width as 100 pixels smaller than the actual video's width -->
		w = video.videoWidth - 100;
		<!-- Calculate the height based on the video's width and the ratio -->
		h = parseInt(w / ratio, 10);
		<!-- Set the canvas width and height to the values just calculated -->
		canvas.width = w;
		canvas.height = h;			
	}, false);

	<!-- button to take snapshot of video -->
	$("#snap").click(function() {
		<!-- check if file video is not upload yet -->
		var valueInputFile = $("#ifileVideo").val();
		if(valueInputFile != "") {
			<!-- Define the size of the rectangle that will be filled (basically the entire element) -->
			context.fillRect(0, 0, w, h);
			<!-- Grab the image from the video -->
			context.drawImage(video, 0, 0, w, h);
			var dataImagetoPNG = canvas.toDataURL("image/png");
			<!-- show image (testing only) -->
			//document.getElementById("fotone").src = dataImagetoPNG;
			<!-- get base64 encode string -->
			$("#textimagebase64").val(dataImagetoPNG);

			<!-- after capture/snapshot video, pause video player -->
			$("#video-Preview")[0].pause();
		} else {
			Swal.fire({
				icon: 'warning',
				title: 'Please, upload the file first',
				confirmButtonText: 'Okay',
			});
			return;
		}
	});

	<!-- START by Muhammad Sofi 23 January 2022 23:00 | improvemnt and bug fixing on edit data event banner -->
	var video_edit = document.getElementById("videopreviewedit");
	var canvas_edit = document.getElementById("myCanvasEdit");

	var context_edit = canvas_edit.getContext('2d');
	var w_edit, h_edit, ratio_edit;
	
	video_edit.addEventListener('loadedmetadata', function() {
		ratio_edit = video_edit.videoWidth / video_edit.videoHeight;
		w_edit = video_edit.videoWidth - 100;
		h_edit = parseInt(w_edit / ratio_edit, 10);
		canvas_edit.width = w_edit;
		canvas_edit.height = h_edit;			
	}, false);

	$("#snap_edit").click(function() {
		var valueInputFile = $("#iefileVideo").val();
		if(valueInputFile != "") {
			context_edit.fillRect(0, 0, w_edit, h_edit);
			context_edit.drawImage(video_edit, 0, 0, w_edit, h_edit);
			var dataImagetoPNG = canvas_edit.toDataURL("image/png");
			//document.getElementById("fotone").src = dataImagetoPNG;
			$("#textimagebase64edit").val(dataImagetoPNG);

			$("#videopreviewedit")[0].pause();
		} else {
			Swal.fire({
				icon: 'warning',
				title: 'Please, upload the file first',
				confirmButtonText: 'Okay',
			});
			return;
		}
	});
	<!-- END by Muhammad Sofi 23 January 2022 23:00 | improvemnt and bug fixing on edit data event banner -->
});