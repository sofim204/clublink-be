var media_target_div = 'dgaleri_items';
var media_single = 0;
var media_name = 'image[]';
var media_caption = 0;
var media_id = '';
var folder_id = '';
var galeri_item_count = 0;

function gritter(pesan,jenis="info"){
	$.bootstrapGrowl(pesan, {
		type: jenis,
		delay: 3500,
		allow_dismiss: true
	});
}

function updateCkEditor(){
	for (instance in CKEDITOR.instances) {
		CKEDITOR.instances[instance].updateElement();
		//$("#"+instance).val(CKEDITOR.instances[instance].getData());
	}
}
//form control

$("#iutype").on("change",function(e){
	e.preventDefault();
	var v = $(this).val();
	if(v.toLowerCase() == 'kategori' || v.toLowerCase() == 'tag'){
		$("#ib_kategori_id").val('null');
		$("#ib_kategori_id").prop('disabled',1);
	}else{
		$("#ib_kategori_id").removeAttr('disabled');
	}
});

function genKode(){
	var n = $("#inama").val().toUpperCase().replace(/[^\w\s]/gi, '');
	var ns = n.split(" ");
	if(ns.length>=2){
		n = ns[0].charAt(0)+ns[1].charAt(0);
	}else{
		n = n.slice(0,2);
	}
	var u = $("#iutype option:selected").attr('data-kode').toUpperCase();
	var p = '';
	if($("#ib_kategori_id option:selected").attr('data-kode') !== undefined){
		p = $("#ib_kategori_id option:selected").attr('data-kode').toUpperCase().slice(0,2);
	}
	$("#ikode").val(p+n+u);
}
$("#inama").on("blur",function(e){e.preventDefault(); genKode()});
$("#iutype").on("blur",function(e){e.preventDefault(); genKode()});
$("#ib_kategori_id").on("blur",function(e){e.preventDefault(); genKode()});

//end form control


//seo Start

function convertToSlug(Text){
	return Text
		.toString().toLowerCase()
		.replace(/\s+/g, '-')           // Replace spaces with -
		.replace(/[^\w\-]+/g, '')       // Remove all non-word chars
		.replace(/\-\-+/g, '-')         // Replace multiple - with single -
		.replace(/^-+/, '')             // Trim - from start of text
		.replace(/-+$/, '');
}
function convertToKeyword(Text){
	return Text
		.toString().toLowerCase()
		.replace(/\s+/g, ',')           // Replace spaces with -
		//.replace(/[^\w\-]+/g, '')       // Remove all non-word chars
		.replace(/\-\-+/g, ',')         // Replace multiple - with single -
		.replace(/^-+/, '')             // Trim - from start of text
		.replace(/-+$/, '');
}
function convertToCode(Text){
	return Text
		.toString().toLowerCase()
		.replace(/\s+/g, ''+makeid())           // Replace spaces with -
		.replace(/[^\w\-]+/g, '')       // Remove all non-word chars
		.replace(/\-\-+/g, ''+makeid())         // Replace multiple - with single -
		.replace(/^-+/, '')             // Trim - from start of text
		.replace(/-+$/, '');
}
function makeid(){
	var i=0
	var text = "";
	var possible = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
	for(i=0;1>i;i++){
		text += possible.charAt(Math.floor(Math.random() * possible.length));
		return text;
	}
}

$("#iutype").trigger('change');

//seo end

//submit form
$("#ftambah").on("submit",function(e){
	e.preventDefault();
	NProgress.start();
	//if using ckeditor
	updateCkEditor();
	//get al value from form as fd formdata object
	var fd = new FormData($(this)[0]);
	var url = '<?=base_url("api_admin/community/category/tambah/"); ?>';

	$.ajax({
		type: $(this).attr('method'),
		url: url,
		data: fd,
		processData: false,
		contentType: false,
		success: function(respon){
			NProgress.done();
			if(respon.status==200){
				gritter('<h4>Success</h4><p>New community category successfully added</p>','success');
				setTimeout(function(){
					window.location = '<?=base_url_admin('community/category/')?>';
				},500); <!-- by Muhammad Sofi 21 January 2022 22:03 | reduce loading time after save and edit data -->
			}else{
				gritter('<h4>Failed</h4><p>'+respon.message+'</p>','danger');
			}
			//setTimeout(function(){
			//	NProgress.done();
			//}, 666);
		},
		error:function(){
			NProgress.done();
			setTimeout(function(){
				gritter('<h4>Error</h4><p>Cant create data right now, please try again later</p>','warning');
			}, 666);
			return false;
		}
	});

});


//media start
function uploadFormShow(){
	$("#modal_media_add").modal('show');
	var url = '<?=base_url('api_admin/cms/media/'); ?>';
	$.get(url).done(function(dt){
		var h = '';
		$.each(dt.data.folders,function(key,val){
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
	var base_url_img = '<?=base_url(); ?>';
	var base_url_def = '<?=base_url('media/uploads/'.'/default.jpg'); ?>';
	var url = '<?=base_url('api_admin/cms/media/'); ?>';

	url += '?folder='+folder_id;

	var h = '';
	$("#rwm").html('<div class="col-md-12"><h2>Loading....</h2></div>');
	$.get(url).done(function(dt){
		if(dt.status == 200){
			if(dt.data.files.length > 0){

				var h = '';
				$.each(dt.data.files,function(key,val){
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

				var base_url_media = '<?=base_url(); ?>';

				$("#rwm").html(h);
				$("#rwm").off("click",".thmb-prev");
				$("#rwm").on("click",".thmb-prev",function(e){
					e.preventDefault();

					media_id = $(this).attr("data-id");
					url_img = $(this).attr("data-nama");
					url_thb = base_url_media+$(this).attr("data-thumb");

					var j = '';

					j += '<div id="galeri_item_'+galeri_item_count+'" class="col-xs-6 col-sm-4 col-md-4 document galeri_item_item">';
					j += '	<div class="thmb">';
					j += '		<div class="thmb-prev" style="background-image:url('+base_url_def+'); min-width: 100px;min-height: 60px;">';
					j += '			<img src="'+url_thb+'" class="img-responsive" alt="">';
					j += '		</div>';
					j += '		<input type="hidden" name="'+media_name+'" value="'+url_img+'" />';
					j += '    <div class="input-group">';
					if(media_caption == 1 || media_caption == "1"){
					j += '		  <input type="text" id="galeri_item_caption_'+galeri_item_count+'" name="caption[]" value="" class="form-control " placeholder="Caption"  />';
					}
					j += '		  <span class="input-group-btn">';
					j += '        <button id="bgaleri_item_del" type="button" class="btn btn-danger" data-id="'+galeri_item_count+'"><i class="fa fa-trash-o"></i></button>';
					j += '	    </span>';
					j += '	  </div>';
					j += '	</div>';
					j += '</div>';
					galeri_item_count++;

					if(media_single == "1" || media_single == 1){
						$("#"+media_target_div).html(j);
					}else{
						$("#"+media_target_div).append(j);
					}

					$("#"+media_target_div).off("click",'#bgaleri_item_del');
					$("#"+media_target_div).on("click",'#bgaleri_item_del',function(e){
						e.preventDefault();
						var id=$(this).attr("data-id");
						if(media_caption == 1 || media_caption == "1"){
							var cap = $('#galeri_item_caption_'+id).val();
							if(cap.length>0){
								var c = confirm('Are you sure?');
								if(c){
									$("#galeri_item_"+id).remove();
								}
							}else{
								$("#galeri_item_"+id).remove();
							}
						}else{
							var c = confirm('Are you sure?');
							if(c){
								$("#galeri_item_"+id).remove();
							}
						}
					});

					$("#modal_media").modal('hide');
				});
				$("#rwm").off("click","#athmbopt");
				$("#rwm").on("click","#athmbopt",function(e){
					e.preventDefault();

					media_id = $(this).attr("data-id");
					url_img = $(this).attr("data-nama");
					url_thb = base_url_media+$(this).attr("data-thumb");

					var j = '';

					j += '<div id="galeri_item_'+galeri_item_count+'" class="col-xs-6 col-sm-4 col-md-4 document galeri_item_item">';
					j += '	<div class="thmb">';
					j += '		<div class="thmb-prev" style="background-image:url('+base_url_def+'); min-width: 100px;min-height: 60px;">';
					j += '			<img src="'+url_thb+'" class="img-responsive" alt="">';
					j += '		</div>';
					j += '		<input type="hidden" name="'+media_name+'" value="'+url_img+'" />';
					j += '    <div class="input-group">';
					if(media_caption == 1 || media_caption == "1"){
					j += '		  <input type="text" id="galeri_item_caption_'+galeri_item_count+'" name="caption[]" value="" class="form-control " placeholder="Caption"  />';
					}
					j += '		  <span class="input-group-btn">';
					j += '        <button id="bgaleri_item_del" type="button" class="btn btn-danger" data-id="'+galeri_item_count+'"><i class="fa fa-trash-o"></i></button>';
					j += '	    </span>';
					j += '	  </div>';
					j += '	</div>';
					j += '</div>';
					galeri_item_count++;



					if(media_single == "1" || media_single == 1){
						 $("#"+media_target_div).html(j);
					}else{
						 $("#"+media_target_div).append(j);
					}

					$("#"+media_target_div).off("click",'#bgaleri_item_del');
					$("#"+media_target_div).on("click",'#bgaleri_item_del',function(e){
						e.preventDefault();
						var id=$(this).attr("data-id");
						var cap = $('#galeri_item_caption_'+id).val();
						if(media_caption == 1 || media_caption == "1"){
							if(cap.length>0){
								var c = confirm('Are you sure?');
								if(c){
									$("#galeri_item_"+id).remove();
								}
							}else{
								$("#galeri_item_"+id).remove();
							}
						}else{
							var c = confirm('Are you sure?');
							if(c){
								$("#galeri_item_"+id).remove();
							}
						}
					});

					$("#modal_media").modal('hide');
				});


				//folders
				var h = '';
				$("#folder_list").empty();
				$.each(dt.data.folders,function(key,val){
					h +='<li><a href="#" class="folder_selector" data-folder="'+val.folder+'"><i class="fa fa-folder-o"></i> '+val.folder+'</a></li>';
				});
				$("#folder_list").html(h);

				$("#folder_list").off("click");
				$("#folder_list").on("click",".folder_selector",function(e){
					e.preventDefault();
					folder_id = $(this).attr("data-folder");
					row_media_manager();
				})
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
	media_target_div = $(this).attr('data-media-div');
	if(typeof media_target_div === 'undefined') media_target_div = 'dgaleri_items';

	media_single = $(this).attr('data-media-single');
	if(typeof media_single === 'undefined') media_single = 0;

	media_name = $(this).attr('data-media-name');
	if(typeof media_name === 'undefined') media_name = 'image[]';

	media_caption = $(this).attr('data-media-caption');
	if(typeof media_caption === 'undefined') media_caption = 1;

	$("#modal_media").modal('show');
	row_media_manager();
	$("#buploadshow").off("click");
	$("#buploadshow").on("click",function(e){
		e.preventDefault();
		uploadFormShow();
	});
});

$("#bgaleritambah2").on("click",function(e){
	e.preventDefault();
	//console.log('click');
	media_target_div = $(this).attr('data-media-div');
	if(typeof media_target_div === 'undefined') media_target_div = 'dgaleri_items';

	media_single = $(this).attr('data-media-single');
	if(typeof media_single === 'undefined') media_single = 0;

	media_name = $(this).attr('data-media-name');
	if(typeof media_name === 'undefined') media_name = 'image[]';

	media_caption = $(this).attr('data-media-caption');
	if(typeof media_caption === 'undefined') media_caption = 1;

	$("#modal_media").modal('show');
	row_media_manager();
	$("#buploadshow").off("click");
	$("#buploadshow").on("click",function(e){
		e.preventDefault();
		uploadFormShow();
	});
});

$("#modal_media_add_form").on("submit",function(e){
	e.preventDefault();
	NProgress.start();
	gritter('<h4>Loading..</h4><p>Sedang upload gambar, silakan tunggu!</p>','info');
	$("#modal_media_add").modal("hide");
	$("#modal_media").modal("hide");

	$.ajax({
		url: '<?=base_url('api_admin/cms/media/add'); ?>', // Url to which the request is send
		type: "POST",
		data: new FormData(this),
		contentType: false,
		cache: false,
		processData:false,
		success: function(data){
			if(data.status == "100" || data.status == 100){
				setTimeout(function(){
					gritter('<h4>Success</h4><p>File media berhasil diupload!</p>','success');
				},1333);
			}else{
				gritter(data.message,'danger');
				gritter('<h4>Failed</h4><p>'+data.message+'</p>','danger');
				return false;
			}
			setTimeout(function(){
				NProgress.done();
				row_media_manager();
				$("#modal_media").modal("show");
			},3000);
		},
		error: function(d){
			NProgress.done();
			gritter('<h4>Error</h4><p>Maaf, sementara ini belum bisa upload media</p>','danger');
		}
	});

});
//media end
