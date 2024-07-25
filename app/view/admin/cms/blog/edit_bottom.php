//media
var media_id = '';
var folder_id = '';
var media_name = '';
var galeri_item_count = 0;

function gritter(pesan,jenis="info"){
	$.bootstrapGrowl(pesan, {
		type: jenis,
		delay: 2500,
		allow_dismiss: true
	});
}

window.toPlainFloat = function(mny){
	return mny.replace( /^\D+/g, '').split('.').join("");
}

function uploadFormShow(){
	$("#modal_media_add").modal('show');
	var url = '<?php echo base_url('api_admin/cms/media/'); ?>';
	$.get(url).done(function(dt){
		var h = '';
		$.each(dt.result.folders,function(key,val){
			h += '<option value="'+val.folder+'">'+val.folder+'</option>';
		});
		$("#ifolder").html(h).trigger('change');
		$("#modal_media_add_loading").hide();
		$("#modal_media_add_form").slideDown('slow');

		$("#ifoldertambah").off("click")
		$("#ifoldertambah").on("click",function(e){
			e.preventDefault();
			var f = prompt('Masukan nama folder baru');
			if(f != null){
				h = '<option value="'+f+'">'+f+'</option>';
				$("#ifolder").prepend(h).val(f).trigger('change');
			}
		});
	});
}
function row_media_manager(){
	//console.log('row_media_manager');
	var base_url_img = '<?php echo base_url(); ?>';
	var base_url_def = '<?php echo base_url('media/uploads/'.'/default.jpg'); ?>';
	var url = '<?php echo base_url('api_admin/cms/media/'); ?>';

	url += '?folder='+folder_id;

	var h = '';
	$("#rwm").html('<div class="col-md-12"><h2>Loading....</h2></div>');
	$.get(url).done(function(dt){
		if(dt.status == 100 || dt.status == '100'){
			if(dt.result.files.length > 0){

				var h = '';
				$.each(dt.result.files,function(key,val){
					h += '<div class="col-xs-6 col-sm-4 col-md-3 document">';
					h += '	<div class="thmb">';
					h += '		<div class="thmb-prev" data-id="'+val.id+'" data-nama="'+val.nama+'" data-thumb="'+val.thumb+'" style="background-image:url('+base_url_def+');min-width: 100px;min-height: 60px;">';
					h += '			<img src="'+base_url_img+'/'+val.thumb+'" class="img-responsive" alt="">';
					h += '		</div>';
					h += '		<h5 class="fm-title"><a id="athmbopt" href="#" data-id="'+val.id+'" data-thumb="'+val.thumb+'" data-nama="'+val.nama+'">'+val.filename+'</a></h5>';
					h += '		<small class="text-muted">'+val.tgl+'</small>';
					h += '	</div>';
					h += '</div>';
				});

				var base_url_media = '<?php echo base_url(); ?>';

				$("#rwm").html(h);
				$("#rwm").off("click",".thmb-prev");
				$("#rwm").on("click",".thmb-prev",function(e){
					e.preventDefault();
					media_id = $(this).attr("data-id");
					url_img = $(this).attr("data-nama");
					url_thb = base_url_media+$(this).attr("data-thumb");
					$("#img_featured_image").attr("src",url_thb);
					$("#iefeatured_image").val(url_img);
					$("#modal_media").modal("hide");
				});

				//folders
				var h = '';
				$("#folder_list").empty();
				$.each(dt.result.folders,function(key,val){
					h +='<li><a href="#" class="folder_selector" data-folder="'+val.folder+'"><i class="fa fa-folder-o"></i> '+val.folder+'</a></li>';
				});
				$("#folder_list").html(h);

				$("#folder_list").off("click");
				$("#folder_list").on("click",".folder_selector",function(e){
					e.preventDefault();
					folder_id = $(this).attr("data-folder");
					row_media_manager();
				});
			}else{
				var h ='<div class="col-md-12"><h2>Folder Media masih kosong</h2></div>';
				$("#rwm").html(h);
			}
		}

	});
}

$("#modal_media_add").on("hidden.bs.modal",function(e){
	$("#modal_media_add_form").trigger("reset");
	$("#modal_media_add_loading").show();
	$("#modal_media_add_form").hide('slow');
});
$("#aiimgsel").on("click",function(e){
  e.preventDefault();
  $("#modal_media").modal('show');
  row_media_manager();
  $("#buploadshow").off("click");
  $("#buploadshow").on("click",function(e){
    e.preventDefault();
    uploadFormShow();
  });
});
$("#aieimgsel").on("click",function(e){
  e.preventDefault();
  $("#modal_media").modal('show');
  row_media_manager();
  $("#buploadshow").off("click");
  $("#buploadshow").on("click",function(e){
    e.preventDefault();
    uploadFormShow();
  });
});

$("#bgaleritambah").on("click",function(e){
  e.preventDefault();
	//console.log('click');
  $("#modal_media").modal('show');
  row_media_manager();
  $("#buploadshow").off("click");
  $("#buploadshow").on("click",function(e){
    e.preventDefault();
    uploadFormShow();
  });
});
$("#featured_image_pilih").on("click",function(e){
	e.preventDefault();
$("#bgaleritambah").trigger("click");
});

$("#modal_media_add_form").on("submit",function(e){
	e.preventDefault();
	growlShow('<h4>Loading...</h4><p>Sedang upload gambar, silakan tunggu!</p>','info');
	$("#modal_media_add").modal("hide");
	$("#modal_media").modal("hide");
	NProgress.start();
	$.ajax({
		url: '<?php echo base_url('api_admin/cms/media/add'); ?>', // Url to which the request is send
		type: "POST",
		data: new FormData(this),
		contentType: false,
		cache: false,
		processData:false,
		success: function(data){
			if(data.status == "100" || data.status == 100){
				setTimeout(function(){
					gritter('<h4>Berhasil</h4><p>File media berhasil diupload</p>','success');
				},1333);
			}else{
				gritter('<h4>Gagal</h4><p>'+data.message+'</p>','warning');
				return false;
			}
			setTimeout(function(){
				row_media_manager();
				$("#modal_media").modal("show");
				NProgress.done();
			},3000);
		},
		error: function(d){
			gritter('<h4>Error</h4><p>Maaf, sementara ini belum bisa upload media</p>','danger');
		}
	});

});

//end media
function growlShow(pesan,type='danger'){
	$.bootstrapGrowl(pesan, {
		type: type,
		delay: 2500,
		allow_dismiss: true
	});
}

//SEO
function convertToSlug(title){
	return title
		.toString().toLowerCase()
		.replace(/\s+/g, '-')           // Replace spaces with -
		.replace(/[^\w\-]+/g, '')       // Remove all non-word chars
		.replace(/\-\-+/g, '-')         // Replace multiple - with single -
		.replace(/^-+/, '')             // Trim - from start of title
		.replace(/-+$/, '');
}

//form
$("#bsubmit2").on("click",function(e){
	e.preventDefault();
	$("#fedit").trigger("submit");
});
$("#fedit").on("submit",function(e){
	e.preventDefault();
	NProgress.start();
	growlShow('<h4>Loading</h4><p>Silakan tunggu...</p>','info');
	for ( instance in CKEDITOR.instances ) CKEDITOR.instances[instance].updateElement();
	var fd = new FormData($(this)[0]);

	$.ajax({
		url: '<?php echo base_url('api_admin/cms/blog/edit/'.$blog->id); ?>',
		type: "POST",
		data: fd,
		contentType: false,
		cache: false,
		processData:false,
		success: function(data){
			NProgress.done();
			if(data.status == '100' || data.status == 100){
				growlShow('<h4>Berhasil</h4><p>Blog berhasil diubah</p>','success');
				setTimeout(function(){
					window.location = '<?php echo base_url_admin('cms/blog/'); ?>';
				},3000);
			}else{
				growlShow('<h4>Gagal</h4><p>'+data.message+'</p>','danger');
			}
		},
		error: function(data){
			NProgress.done();
			growlShow('<h4>Error</h4><p>Untuk saat ini tidak dapat menambahkan blog, cobalah beberapa saat lagi</p>');
		}
	});
});

//add existing value to current form
<?php foreach($blog as $key=>$val){?>
	<?php if(is_string($val)){ ?>
	<?php
		$val = trim($val);
		if($key == 'content' || $key =='deskripsi_singkat'){
			$val = str_replace("\n","",$val);
			$val = str_replace("\r","",$val);
		}
	?>
$("#ie<?=$key?>").val('<?=$val?>');
<?php if($key == 'harga_jual' || $key == 'harga_retail' || $key == 'harga_beli'){ ?>
$("#ieh<?=$key?>").val('<?=$val?>');
<?php } ?>
<?php } ?>
<?php } ?>
$("#img_featured_image").attr("src",'<?=base_url($blog->featured_image)?>');


$("#iekategori_tambah").on("click",function(e){
	e.preventDefault();
	var p = prompt('Nama Kategori');
	if(p){
		$("#iekategori").prepend("<option value='"+p+"'>"+p+"</option>");
		$("#iekategori").val(p);
	}
});
