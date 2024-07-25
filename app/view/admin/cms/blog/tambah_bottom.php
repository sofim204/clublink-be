var variasi = [];
var variasi_last_id = 0;
function variasiAdd(jenis,nilai){
	var vr = {'jenis':jenis,'nilai':nilai};
	variasi.push(vr);
}
function variasiRemove(jenis,nilai){
	var variasi_temp = [];
	$.each(variasi,function(k,v){
		if(v.jenis == jenis && v.nilai == nilai){
			delete variasi[k];
		}else{
			variasi_temp.push(v);
		}
	});
	variasi = variasi_temp;
}
function variasiRemoveByKey(k){
	//delete variasi[k];
	variasi.splice(k,1);
}
function variasiGet(){
	var h = '';
	if(variasi.length>0){
		var sty = '';
		$.each(variasi,function(k,v){
			//if(k == variasi_last_id) sty = 'display:none;';
			h += '<tr id="tr_var_id_'+k+'" style="'+sty+'">';
			h += '<td>'+v.jenis+'</td>';
			h += '<td>'+v.nilai+'</td>';
			h += '<td><a id="tr_var_del_'+k+'" href="#" data-id="'+k+'" data-jenis="'+v.jenis+'" data-nilai="'+v.nilai+'" class="btn btn-danger btn-sm btn-hapus-var"><i class="fa fa-times"></i></a></td>';
			h += '</tr>';
		});
	}
	$("#tabel_variasi_list tbody").html(h);
	$("#tabel_variasi_list").on("click",".btn-hapus-var",function(e){
		e.preventDefault();
		var c = confirm('Apakah anda yakin?');
		if(c){
			var k = $(this).attr("data-id");
			$("#tr_var_id_"+k).hide("slow",function(e){
				variasiRemoveByKey(k);
				variasiGet();
			});
		}
	});
}

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

$("#ititle").on("change",function(e){
	e.preventDefault();
	$("#islug").val(convertToSlug($('#ititle').val()));
});

function growlShow(pesan,type='danger'){
	$.bootstrapGrowl(pesan, {
		type: type,
		delay: 2500,
		allow_dismiss: true
	});
}

//media

var media_id = '';
var folder_id = '';
var media_name = '';
var galeri_item_count = 0;

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
					$("#ifeatured_image").val(url_img);
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
})

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


//form
$("#bsubmit2").on("click",function(e){
	e.preventDefault();
	$("#ftambah").trigger("submit");
});
$("#ftambah").on("submit",function(e){
	e.preventDefault();

	growlShow('<h4>Loading</h4><p>Silakan tunggu...</p>','info');

	for ( instance in CKEDITOR.instances ) CKEDITOR.instances[instance].updateElement();
	var fd = new FormData($(this)[0]);
	$.ajax({
		url: '<?php echo base_url('api_admin/cms/blog/tambah/'); ?>',
		type: "POST",
		data: fd,
		contentType: false,
		cache: false,
		processData:false,
		success: function(data){
			if(data.status == '100' || data.status == 100){
				growlShow('<h4>Berhasil</h4><p>Blog berhasil ditambahkan</p>','success');
				setTimeout(function(){
					window.location = '<?php echo base_url_admin('cms/blog/'); ?>';
				},3000);
			}else{
				growlShow('<h4>Galat</h4><p>'+data.message+'</p>','danger');
			}
		},
		error: function(data){
			growlShow('<h4>Error</h4><p>Untuk saat ini tidak dapat menambahkan blog, silakan coba beberapa saat lagi</p>','warning');
		}
	});
});

$("a.btn-hidden-block").on("click",function(e){
	e.preventDefault();
	$(this).parent().parent().next().slideToggle('slow');
});

$("#ikategori_tambah").on("click",function(e){
	e.preventDefault();
	var p = prompt('Nama Kategori');
	if(p){
		$("#ikategori").prepend("<option value='"+p+"'>"+p+"</option>");
		$("#ikategori").val(p);
	}
});
