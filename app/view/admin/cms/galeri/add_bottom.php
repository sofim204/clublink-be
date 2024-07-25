
var media_id = '';
var folder_id = '';
var media_name = '';
var galeri_item_count = 0;


$(document).on('show.bs.modal', '.modal', function () {
    var zIndex = 1040 + (10 * $('.modal:visible').length);
    $(this).css('z-index', zIndex);
    setTimeout(function() {
        $('.modal-backdrop').not('.modal-stack').css('z-index', zIndex - 1).addClass('modal-stack');
    }, 0);
});


function row_media_manager(){
  	var base_url_img = '<?php echo base_url(); ?>';
  	var base_url_def = '<?php echo base_url($this->site_config->cms_blog.'/default.jpg'); ?>';
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

            j += '<div id="galeri_item_'+galeri_item_count+'" class="col-xs-6 col-sm-4 col-md-4 document galeri_item_item">';
  					j += '	<div class="thmb">';
  					j += '		<div class="thmb-prev" style="background-image:url('+base_url_def+'); min-width: 100px;min-height: 60px;">';
  					j += '			<img src="'+url_thb+'" class="img-responsive" alt="">';
            j += '		</div>';
            j += '		<input type="hidden" name="image[]" value="'+url_img+'" />';
            j += '    <div class="input-group">'
            j += '		  <input type="text" id="galeri_item_caption_'+galeri_item_count+'" name="caption[]" value="" class="form-control " placeholder="Caption"  />';
            j += '		  <span class="input-group-btn">';
            j += '        <button id="bgaleri_item_del" type="button" class="btn btn-danger" data-id="'+galeri_item_count+'"><i class="fa fa-trash-o"></i></button>';
            j += '	    </span>';
  					j += '	  </div>';
  					j += '	</div>';
  					j += '</div>';
            galeri_item_count++;

            $("#dgaleri_items").append(j);
            $("#dgaleri_items").off("click",'#bgaleri_item_del');
            $("#dgaleri_items").on("click",'#bgaleri_item_del',function(e){
              e.preventDefault();
              var id=$(this).attr("data-id");
              var cap = $('#galeri_item_caption_'+id).val();
              if(cap.length>0){
                var c = confirm('Apakah anda yakin?');
                if(c){
                  $("#galeri_item_"+id).remove();
                }
              }else{
                $("#galeri_item_"+id).remove();
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
            j += '		<input type="hidden" name="image[]" value="'+url_img+'" />';
            j += '    <div class="input-group">'
            j += '		  <input type="text" id="galeri_item_caption_'+galeri_item_count+'" name="caption[]" value="" class="form-control " placeholder="Caption"  />';
            j += '		  <span class="input-group-btn">';
            j += '        <button id="bgaleri_item_del" type="button" class="btn btn-danger" data-id="'+galeri_item_count+'"><i class="fa fa-trash-o"></i></button>';
            j += '	    </span>';
  					j += '	  </div>';
  					j += '	</div>';
  					j += '</div>';
            galeri_item_count++;

            $("#dgaleri_items").append(j);
            $("#dgaleri_items").off("click",'#bgaleri_item_del');
            $("#dgaleri_items").on("click",'#bgaleri_item_del',function(e){
              e.preventDefault();
              var id=$(this).attr("data-id");
              var cap = $('#galeri_item_caption_'+id).val();
              if(cap.length>0){
                var c = confirm('Apakah anda yakin?');
                if(c){
                  $("#galeri_item_"+id).remove();
                }
              }else{
                $("#galeri_item_"+id).remove();
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

  $("#mfadd").on("hidden.bs.modal",function(e){
  	$("#mfaddform").trigger("reset");
  	$("#mfaddloading").show();
  	$("#mfaddform").hide('slow');
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
    $("#modal_media").modal('show');
    row_media_manager();
    $("#buploadshow").off("click");
    $("#buploadshow").on("click",function(e){
      e.preventDefault();
      uploadFormShow();
    });
  });


  function uploadFormShow(){
  	$("#mfadd").modal('show');
  	var url = '<?php echo base_url('api_admin/cms/media/'); ?>';
  	$.get(url).done(function(dt){
  		var h = '';
  		$.each(dt.result.folders,function(key,val){
  			h += '<option value="'+val.folder+'">'+val.folder+'</option>';
  		});
  		$("#ifolder").html(h).trigger('change');
  		$("#mfaddloading").hide();
  		$("#mfaddform").slideDown('slow');

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

  $("#mfaddform").on("submit",function(e){
  	e.preventDefault();
  	jQuery.gritter.add({
  			title: 'Memuat...',
  			text: 'Sedang upload gambar, silakan tunggu!',
  			image: '<?php echo base_url('skin/admin/images/comment.png'); ?>',
  			sticky: false,
  			time: ''
  		});
  	//var fd = new FormData();
  	$("#mfadd").modal("hide");

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
  					gritter('Selesai','Media berhasil diupload');
  				},1333);
  			}else{
  				gritter('Gagal',data.message);
  				return false;
  			}
  			setTimeout(function(){
  				row_media_manager();
  			},3000);
  		},
  		error: function(d){
  			jQuery.gritter.add({
  				title: 'Error',
  				text: 'Maaf, sementara ini belum bisa upload media',
  				image: '<?php echo base_url('skin/admin/images/comment.png'); ?>',
  				sticky: false,
  				time: ''
  			});
  		}
  	});

  });



  $("#afaddsubmit").on("click",function(e){
    e.preventDefault();
    $("#fadd").submit();
  });

  $("#fadd").on("submit",function(e){
  	e.preventDefault();
  	tinymce.triggerSave();
  	var frm = $(this);
  	$.ajax({
  		type: frm.attr('method'),
  		url: frm.attr('action'),
  		data: frm.serialize(),
  		success: function(d){
  			console.log(d);
  			if(d.status==1 || d.status=="1"){
					jQuery.gritter.add({
						title: 'Berhasil',
						text: 'Galeri berhasil ditambahkan',
						//class_name: 'growl-info',
						image: '<?php echo base_url('skin/admin/'); ?>images/comment.png',
						sticky: false,
						time: ''
					});
  				setTimeout(function(){
            window.location = '<?php echo base_url_admin('cms/galeri'); ?>';
  				}, 1666);

  			}else{
  				$("#ploading").html(d.result);
  				$("#ploading").show();
  			}

  		}
  	});

  });
