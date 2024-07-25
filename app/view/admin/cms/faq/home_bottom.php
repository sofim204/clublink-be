var ieid = '';
var faq_section_count = 1;
var faq_form_jenis = 'tambah';
function gritter(pesan,jenis='info'){
	$.bootstrapGrowl(pesan, {
		type: jenis,
		delay: 2500,
		allow_dismiss: true
	});
}

$("#afaq_new").on("click",function(e){
	e.preventDefault();
	$("#faq_modal_add").modal("show");
});

$("#faq_modal_add").on("shown.bs.modal",function(e){
});

$("#faq_modal_add").on("hidden.bs.modal",function(e){
	$("#faq_modal_add").find("form").trigger("reset");
});

$("#ftambahfaq").on("submit",function(e){
	e.preventDefault();
	<!-- by Muhammad Sofi 21 January 2022 20:53 | add insert priority and sort by priority -->
	<!-- add checking for priority -->
	var priority = $("#ipriority").val();
	if(priority == "0") {
		alert("Priority cannot be 0");
		$("#ipriority").focus();
	} else if($("#ilanguage").attr("selectedIndex") == 0) {
		alert("You haven't selected anything!");
	} else {
		NProgress.start();
		<!-- by Muhammad Sofi 17 March 2022 17:44 | bug fix when add data, save data twice -->
		for ( instance in CKEDITOR.instances ) CKEDITOR.instances[instance].updateElement();
		var fd = new FormData($(this)[0]);
		var url = '<?=base_url("api_admin/cms/faq/tambah/")?>';
		$.ajax({
			type: $(this).attr('method'),
			url: url,
			data: fd,
			processData: false,
			contentType: false,
			success: function(respon){
				if(respon.status==200){
					gritter('<h4>Success</h4><p>Data successfully added!</p>','success');
					setTimeout(function(){
						window.location = '<?php echo base_url_admin('cms/faq'); ?>';
					}, 600);
				}else{
					gritter('<h4>Failed</h4><p>'+respon.message+'</p>','danger');
				}
			},
			error:function(){
				NProgress.done();
				gritter('<h4>Error</h4><p>Cant update data right now, please try again later</p>','warning');
				return false;
			}
		});
	}
});

var EditData = function (rowId) {
	ieid = rowId;
	var url = '<?=base_url()?>api_admin/cms/faq/detail/' + rowId;
	$.get(url).done(function(response){
		if(response.status==200){
			var dta = response.data;
			//$("#ietitle").val(dta.title);
			//$("#iecontent").val(dta.content);
			$("#iepriority").val(dta.priority); <!-- by Muhammad Sofi 21 January 2022 20:53 | add insert priority and sort by priority -->
			$("#ielanguage").val(dta.language_id); <!-- by Muhammad Sofi 21 January 2022 20:53 | add insert priority and sort by priority -->
			CKEDITOR.instances['ietitle'].setData(dta.title);
			CKEDITOR.instances['iecontent'].setData(dta.content);
			$("#modal_edit").modal("show");
		}else{
			gritter('<h4>Failed</h4><p>'+response.message+'</p>','danger');
		}
	});
}

$("#modal_edit").on("shown.bs.modal",function(e){
});

$("#modal_edit").on("hidden.bs.modal",function(e){
	$("#modal_edit").find("form").trigger("reset");
});

$("#feditfaq").on("submit",function(e){
	e.preventDefault();
	NProgress.start();
	<!-- by Muhammad Sofi 17 March 2022 17:44 | bug fix when edit data, save data twice -->
	for ( instance in CKEDITOR.instances ) CKEDITOR.instances[instance].updateElement();
	var fd = new FormData($(this)[0]);
	var url = '<?=base_url("api_admin/cms/faq/edit/")?>' + ieid;
	$.ajax({
		type: $(this).attr('method'),
		url: url,
		data: fd,
		processData: false,
		contentType: false,
		success: function(respon){
			if(respon.status==200){
				gritter('<h4>Success</h4><p>Data changed successfully!</p>','success');
				setTimeout(function(){
					window.location = '<?php echo base_url_admin('cms/faq'); ?>';
				}, 600);
			}else{
				gritter('<h4>Failed</h4><p>'+respon.message+'</p>','danger');
			}
			$("#modal_edit").modal("hide");
		},
		error:function(){
			NProgress.done();
			gritter('<h4>Error</h4><p>Cant change data right now, please try again later</p>','warning');
			return false;
		}
	});
});

var DeleteData = function (rowId) {
	var confirmation = confirm("Are you sure to delete?");
	if(confirmation){
		var url = '<?=base_url("api_admin/cms/faq/hapus/")?>' + rowId;
		$.get(url).done(function(response){
			if(response.status==200){
				gritter('<h4>Success</h4><p>Data removed successfully</p>','success');
				setTimeout(function(){
					window.location = '<?php echo base_url_admin('cms/faq'); ?>';
				}, 600);
			}else{
				gritter('<h4>Failed</h4><p>'+response.message+'</p>','danger');
			}
		}).fail(function() {
			NProgress.done();
			gritter('<h4>Error</h4><p>Cant remove data right now, please try again later</p>','warning');
		});
	}
}