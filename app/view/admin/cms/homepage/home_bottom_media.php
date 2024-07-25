//media start

//global var
var media_target_div = 'dgaleri_items';
var media_single = 0;
var media_name = 'image[]';
var media_caption = 0;
var media_url = 0;
var media_id = '';
var folder_id = '';
var media_item_count = 0;

//notification check
if (typeof growlShow !== "undefined") {
	function growlShow(pesan,type='danger'){
		$.bootstrapGrowl(pesan, {
			type: type,
			delay: 2500,
			allow_dismiss: true
		});
	}
}
if (typeof gritter !== "undefined") {
  function gritter(pesan,type='danger'){
  	growlShow(pesan,type);
  }
}

//media upload form
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

//media manager
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

					var j = '';
					j += '<div id="'+media_target_div+'_'+media_item_count+'" class="col-xs-6 col-sm-4 col-md-4 document galeri_item_item">';
					j += '	<div class="thmb">';
					j += '		<div class="thmb-prev" style="background-image:url('+base_url_def+'); min-width: 100px;min-height: 60px;">';
					j += '			<img src="'+url_thb+'" class="img-responsive" alt="">';
					j += '		</div>';
					j += '		<input type="hidden" name="'+media_name+'" value="'+url_img+'" />';
					if(media_url == 1 || media_url == "1"){
						j += '		<input type="text" id="'+media_target_div+'_url_'+media_item_count+'" name="targeturl[]" value="" class="form-control " placeholder="Target Url"  />';
					}
					j += '    <div class="input-group">';
					if(media_caption == 1 || media_caption == "1"){
						j += '		  <input type="text" id="'+media_target_div+'_caption_'+media_item_count+'" name="caption[]" value="" class="form-control " placeholder="Caption"  />';
					}
					j += '		  <span class="input-group-btn">';
					j += '        <button type="button" class="btn btn-danger btn-media-del" data-media-div="'+media_target_div+'" data-id="'+media_item_count+'"><i class="fa fa-trash-o"></i></button>';
					j += '	    </span>';
					j += '	  </div>';
					j += '	</div>';
					j += '</div>';
					media_item_count++;

					if(media_single == "1" || media_single == 1){
						$("#"+media_target_div).html(j);
					}else{
						$("#"+media_target_div).append(j);
					}

					$("#"+media_target_div).off("click",'.btn-media-del');
					$("#"+media_target_div).on("click",'.btn-media-del',function(e){
						e.preventDefault();
						var dnm = $(this).attr("data-media-name");
						var dmd = $(this).attr("data-media-div");
						var id=$(this).attr("data-id");
						if(media_caption == 1 || media_caption == "1"){
							var cap = $('#'+media_target_div+'_caption_'+id).val();
							if(cap.length>0){
								var c = confirm('Apakah anda yakin?');
								if(c){
									$('input[name="'+dnm+'"]').val('');
									$("#"+dmd+" #"+dmd+"_"+id).remove();
								}
							}else{
								$('input[name="'+dnm+'"]').val('');
								$("#"+dmd+" #"+dmd+"_"+id).remove();
							}
						}else{
							var c = confirm('Apakah anda yakin?');
							if(c){
								$('input[name="'+dnm+'"]').val('');
								$("#"+dmd+" #"+dmd+"_"+id).remove();
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
					j += '<div id="'+media_target_div+'_'+media_item_count+'" class="col-xs-6 col-sm-4 col-md-4 document galeri_item_item">';
					j += '	<div class="thmb">';
					j += '		<div class="thmb-prev" style="background-image:url('+base_url_def+'); min-width: 100px;min-height: 60px;">';
					j += '			<img src="'+url_thb+'" class="img-responsive" alt="">';
					j += '		</div>';
					j += '		<input type="hidden" name="'+media_name+'" value="'+url_img+'" />';
					if(media_url == 1 || media_url == "1"){
						j += '		<input type="text" id="'+media_target_div+'_url_'+media_item_count+'" name="targeturl[]" value="" class="form-control " placeholder="Target URl"  />';
					}
					j += '    <div class="input-group">';
					if(media_caption == 1 || media_caption == "1"){
						j += '		  <input type="text" id="'+media_target_div+'_caption_'+media_item_count+'" name="caption[]" value="" class="form-control " placeholder="Caption"  />';
					}
					j += '		  <span class="input-group-btn">';
					j += '        <button type="button" class="btn btn-danger btn-media-del" data-media-div="'+media_target_div+'" data-id="'+media_item_count+'"><i class="fa fa-trash-o"></i></button>';
					j += '	    </span>';
					j += '	  </div>';
					j += '	</div>';
					j += '</div>';
					media_item_count++;

					if(media_single == "1" || media_single == 1){
						 $("#"+media_target_div).html(j);
					}else{
						 $("#"+media_target_div).append(j);
					}

					$("#"+media_target_div).off("click",'.btn-media-del');
					$("#"+media_target_div).on("click",'.btn-media-del',function(e){
						e.preventDefault();
						var dmd = $(this).attr("data-media-div");
						var id = $(this).attr("data-id");
						var cap = $('#'+media_target_div+'_caption_'+id).val();
						if(media_caption == 1 || media_caption == "1"){
							if(cap.length>0){
								var c = confirm('Apakah anda yakin?');
								if(c){
									$("#"+dmd+"_"+id).remove();
								}
							}else{
								$("#"+dmd+"_"+id).remove();
							}
						}else{
							var c = confirm('Apakah anda yakin?');
							if(c){
								$("#+dmd+_"+id).remove();
							}
						}
					});
					$("#modal_media").modal('hide');
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
				})
			}else{
				var h ='<div class="col-md-12"><h2>Folder Media masih kosong</h2></div>';
				$("#rwm").html(h);
			}
		}

	});
}

//media add listener
$("#modal_media_add").on("hidden.bs.modal",function(e){
	$("#modal_media_add_form").trigger("reset");
	$("#modal_media_add_loading").show();
	$("#modal_media_add_form").hide('slow');
});

//media submit form
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

//media add listener
$(".btn-media-tambah").on("click",function(e){
	e.preventDefault();
	//console.log('click');
	media_target_div = $(this).attr('data-media-div');
	if(typeof media_target_div === 'undefined') media_target_div = 'dgaleri_items';

	media_single = $(this).attr('data-media-single');
	if(typeof media_single === 'undefined') media_single = 0;

	media_name = $(this).attr('data-media-name');
	if(typeof media_name === 'undefined') media_name = 'image[]';

	media_caption = $(this).attr('data-media-caption');
	if(typeof media_caption === 'undefined') media_caption = 0;

	media_url = $(this).attr('data-media-url');
	if(typeof media_url === 'undefined') media_url = 0;

	media_item_count = $("#"+media_target_div).children().length;

	$("#modal_media").modal('show');
	row_media_manager();
	$("#buploadshow").off("click");
	$("#buploadshow").on("click",function(e){
		e.preventDefault();
		uploadFormShow();
	});
});

//media delete
$(".btn-media-del").on("click",function(e){
	e.preventDefault();
	media_target_div = $(this).attr('data-media-div');
	if(typeof media_target_div === 'undefined') media_target_div = 'dgaleri_items';
	media_item_count = $("#"+media_target_div).children().length;
	var iid = $(this).attr("data-id");
	$("#"+media_target_div+"_"+iid).fadeOut("slow",function(){
		$("#"+media_target_div+"_"+iid).remove();
	});
});
//end media
