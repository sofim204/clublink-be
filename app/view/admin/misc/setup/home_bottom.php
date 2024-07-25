$("#page-content").on("submit",".form-setup",function(e){
	e.preventDefault();
	var c = confirm("Are you sure?");
	if(c){
		NProgress.start();
		var fd = new FormData($(this)[0]);
		var url = $(this).attr("action");
		$.ajax({
			type: $(this).attr('method'),
			url: url,
			data: fd,
			processData: false,
			contentType: false,
			success: function(respon){
				NProgress.done();
				if(respon.status == 200){
					growlType = 'success';
					growlPesan = '<h4>Success</h4><p>'+respon.message+'</p>';
					setTimeout(function(){
						$.bootstrapGrowl(growlPesan, {
							type: growlType,
							delay: 2500,
							allow_dismiss: true
						});
						location.reload();
					}, 800);
				}else{
					growlType = 'danger';
					growlPesan = '<h4>Failed</h4><p>'+respon.message+'</p>';
					setTimeout(function(){
						$.bootstrapGrowl(growlPesan, {
							type: growlType,
							delay: 2500,
							allow_dismiss: true
						});
					}, 666);
				}
			},
			error: function(){
				NProgress.done();
				growlPesan = '<h4>Error</h4><p>Cannot process data right now, please try again later</p>';
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
	}
});

//begin resize upload image
var fileReaderImage = new FileReader();

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

$("#fs_app_config_remark_C9").change(function () {
	<!-- verification -->

	<!-- check extension of uploaded file -->
	var inputFile = $("#fs_app_config_remark_C9").val();
	var fileExtensions = inputFile.split('.').pop();
	var listExtensionImage = ["jpg", "jpeg", "png", "ico", "tiff", "tif", "bmp", "gif"];
	
	if(listExtensionImage.indexOf(fileExtensions) > -1) {
		var numb = $('#fs_app_config_remark_C9')[0].files[0].size/1024/1024;
		var size = numb.toFixed(3);
		var maxImageUpload = 5.120;
		if(size > maxImageUpload){
			Swal.fire({
				icon: 'warning',
				title: 'File is too big, maximum is 5MB or less',
				confirmButtonText: 'Okay',
			});
			$('#fs_app_config_remark_C9').val('');
			$("#upload-Preview").attr("src", "");
		} else {
			var uploadFile = document.getElementById("fs_app_config_remark_C9").files[0];
			fileReaderImage.readAsDataURL(uploadFile);
		}
	} else if(!listExtensionImage.indexOf(fileExtensions) > -1 && inputFile != "") { 
		Swal.fire({
			icon: 'warning',
			title: 'Not valid image extension',
			confirmButtonText: 'Okay',
		});
		$('#fs_app_config_remark_C9').val('');
		$("#upload-Preview").attr("src", "");
		return;
	} else {}
});

//fill value
var cc = <?=json_encode($product_fee)?>;
$.each(cc,function(k,v){
	$("#fs_"+k).val(v);
});

<!-- by Muhammad Sofi 2 February 2022 09:24 | add Maintenance App configuration -->
var cc = <?=json_encode($app_config)?>;
//let sellon_image_config = cc.app_config_remark_C9;
//var base_url = window.location.origin;

$.each(cc,function(k,v){
	console.log(k)
	if(k == "app_config_remark_C9") {
		//$("#fs_"+k).val(base_url+"/backend-sellon/"+v);
		$("#fs_"+k).val("<?= base_url(); ?>"+v);
	} else {
		$("#fs_"+k).val(v);
	}
});
