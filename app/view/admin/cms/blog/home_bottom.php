media_id = '';
folder_id = '';
media_name = '';
//
$(document).on('show.bs.modal', '.modal', function () {
    var zIndex = 1040 + (10 * $('.modal:visible').length);
    $(this).css('z-index', zIndex);
    setTimeout(function() {
        $('.modal-backdrop').not('.modal-stack').css('z-index', zIndex - 1).addClass('modal-stack');
    }, 0);
});

function gritter(judul,isi){
	jQuery.gritter.add({
		title: judul,
		text: isi,
		image: '<?php echo base_url('assets/img/ji-char/smile.png'); ?>',
		sticky: false,
		time: ''
	});
}

tinymce.init({
	selector: '.mswrd',
	height: 300,
	plugins: [
      'advlist autolink link image imagetools lists charmap print preview hr anchor pagebreak spellchecker colorpicker 	media',
      'searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking',
      'save table contextmenu directionality emoticons template paste textcolor textcolor'
    ],
	toolbar:  [
    'undo redo | styleselect | bold italic underline | link image | bullist  numlist | alignleft aligncenter alignright | indent outdent',
		'blockquote anchor | code | table toc | emoticons'
  ],
	relative_urls : false,
	remove_script_host : false,
	convert_urls : true
});
var json_upgrade = {};
var shTable =  {};
//var jq = jQuery.noConflict();
jQuery(document).ready(function(){

shTable = jQuery('#shTable').DataTable({
	dom: 'Bfrtip',
	"lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
	'buttons': [
	],
	"order"					: [[ 0, "desc" ],[ 0, "desc" ]],
	"responsive"	  : true,
	"bProcessing"		: true,
	"bServerSide"		: true,
	"sAjaxSource"		: "<?php echo base_url("api_admin/cms/blog/index/"); ?>",
	"fnServerParams": function ( aoData ) {
		aoData.push(
				{ "name": "tgl_max", "value": $("#max").val() },
				{ "name": "tgl_min", "value": $("#min").val() },
				{ "name": "supplier", "value": $("#suppplier_selector").val() }
			);
	},
	"fnServerData"	: function (sSource, aoData, fnCallback, oSettings) {
		$('body').removeClass('loaded');

		oSettings.jqXHR = $.ajax({
			dataType 	: 'json',
			method 		: 'POST',
			url 		: sSource,
			data 		: aoData,
		}).success(function (response, status, headers, config) {

			//$('body').addClass('loaded');
			$('#shTable > tbody').off('click', 'tr');
				$('#shTable > tbody').on('click', 'tr', function (e) {
					$("#ploadingx").html("Loading....");
					$("#ploadingx").show();
					$("#fpengumumanedit").hide();
					x = $(this).find("td");
					id = x[0].innerHTML;
					var url = "<?php echo base_url("api_admin/cms/blog/detail/"); ?>"+id;
					$.get(url).done(function(d){
						console.log(d);

						var blog_url = '<?php echo base_url('cerita/'); ?>';

            $("#iefeatured_image").val(d.result.featured_image);
            $("#fieimage").attr('src','<?php echo base_url(); ?>'+d.result.featured_image);
						$("#ietitle").val(d.result.title);
						$("#ieslug").val(d.result.slug);
						$("#ieexcerpt").val(d.result.excerpt);
						$("#sestatus").val(d.result.status);
						$("#taecontent").html(d.result.content);
						$("#aepreview").attr("href",blog_url+d.result.slug);
						tinymce.activeEditor.setContent(d.result.content);

						$("#fpengumumanedit").show("slow");
						$("#apengumumanubah").off("click");
						$("#fpengumumanedit").off("submit");
						$("#apengumumanubah").on("click",function(e){
							e.preventDefault();
							$("#fpengumumanedit").submit();
						})

						$("#fpengumumanedit").on("submit",function(e){
							e.preventDefault();
							tinymce.triggerSave();
							var frm = $(this);
							var url = "<?php echo base_url("api_admin/cms/blog/update/"); ?>"+id;
							$.ajax({
								type: frm.attr('method'),
								url: url,
								data: frm.serialize(),
								success: function(da){
									//alert(da.result);
									if(da.result=="Berhasil"){
										shTable.ajax.reload();
										//$("#list_modal").modal('hide');
										setTimeout(function(){
											gritter('Berhasil','Tulisan blog berhasil diubah');
										}, 666);
                    $("#list_modal").modal('hide');
									}else{
										$("#ploadingx").html(da.result);
										$("#ploadingx").show();
									}
								}
							});
						});
						$("#ploadingx").hide();
					});
					$("#apengumumanhapus").off("click");
					$("#apengumumanhapus").on("click",function(e){
						e.preventDefault();
						var c = confirm('Apakah anda yakin?');
						if(c){
							var url = '<?php echo base_url("api_admin/cms/blog/del/"); ?>'+id;
							$.get(url).done(function(d){
								if(d.status==1 || d.status=="1"){
									$("#list_modal").modal('hide');

									shTable.ajax.reload();

									setTimeout(function(){
										jQuery.gritter.add({
											title: 'Berhasil',
											text: 'Tulisan blog berhasil dihapus',
											//class_name: 'growl-info',
											image: '<?php echo base_url('skin/admin/'); ?>images/comment.png',
											sticky: false,
											time: ''
										});
									}, 666);
								}else{
									$("#ploadingx").html(d.result);
									$("#ploadingx").show();
								}
							});
						}
					});
					$("#fpengumumanedit").hide();
					$("#list_modal").modal('show');
				});
			fnCallback(response);
		}).error(function (response, status, headers, config) {
			//console.log(response, response.responseText);
			//$('body').addClass('loaded');
			alert("Error");
		});
	},
});




$("#apengumumansimpan").on("click",function(e){
	e.preventDefault();
	$("#fpengumumanadd").submit();
});
$("#apengumumanadd").on("click",function(e){
	e.preventDefault();
	$("#mpengumumanadd").modal("show");
	$("#fpengumumanadd").trigger("reset");
	$("#fpengumumanadd").show("slow");
  $("#fiimage").attr(src,'');
	$("#ploading").hide("fast");
});
$("#fpengumumanadd").on("submit",function(e){
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
				shTable.ajax.reload();
				$("#mpengumumanadd").modal("hide");
				setTimeout(function(){
					jQuery.gritter.add({
						title: 'Berhasil',
						text: 'Tulisan blog berhasil ditambahkan',
						//class_name: 'growl-info',
						image: '<?php echo base_url('skin/admin/'); ?>images/comment.png',
						sticky: false,
						time: ''
					});
				}, 666);

			}else{
				$("#ploading").html(d.result);
				$("#ploading").show();
			}

		}
	});

});

$(".datepicker").datepicker({ dateFormat: 'yy-mm-dd' });

$("#min").on("change",function(e){
	e.preventDefault();
	var m = $("#max").val();
	if(m.length==0){
		$("#max").val($("#min").val());
	}
});
$("#max").on("change",function(e){
	e.preventDefault();
	var m = $("#min").val();
	if(m.length==0){
		$("#min").val($("#max").val());
	}
});

$("#a_filter_data").on("click",function(e){
	e.preventDefault();
	shTable.ajax.reload();
});

$("#aswitching").on("click",function(e){
	e.preventDefault();
	$("#switching_modal").modal("show");
	var m = $("#switching_modal");
	$(m).find("#pswitching").hide("slow");
	$(m).find("#fswitching").show("slow");

});

$("#ibeli").on("change",function(e){
	e.preventDefault();
	var t = $("#itotal").val();
	var k = $("#ikosong").val();
	var b = eval(t) - eval(k);
	$("#ibeli").val(b);
});
$("#ikosong").on("change",function(e){
	$("#ibeli").trigger("change");
});

$("#iebeli").on("change",function(e){
	e.preventDefault();
	var t = $("#ietotal").val();
	var k = $("#iekosong").val();
	var b = eval(t) - eval(k);
	$("#ietotal").val(b);
});
$("#iekosong").on("change",function(e){
	$("#iebeli").trigger("change");
});

$("#pagesize").on("change",function(e){
	e.preventDefault();
	shTable.page.len($(this).val()).draw();
});

//$('.mswrd').ckeditor();

$(document).on('focusin', function(e) {
  if ($(e.target).closest(".mce-window").length) {
    e.stopImmediatePropagation();
  }
});

});//end ready

	$("#switching_modal").on("hidden.bs.modal",function(e){
		$("#fswitching").trigger("reset");
	});
	$("#list_modal").on("hidden.bs.modal",function(e){
		$("#fpengumumanedit").trigger("reset");
		//tinymce.remove('.mswrd');
	});
	$("#mpengumumanadd").on("hidden.bs.modal",function(e){
		$("#fpengumumanadd").trigger("reset");
		//tinymce.remove('.mswrd');
	});

function row_media_manager(){
  	var base_url_img = '<?php echo base_url(); ?>';
  	var base_url_def = '<?php echo base_url($this->cms_blog.'/default.jpg'); ?>';
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
  					h += '		<div class="thmb-prev" data-id="'+val.id+'" data-nama="'+val.nama+'" data-thumb="'+val.thumb+'" style="background-image:url('+base_url_def+');min-width: 100px;min-height: 100px;">';
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

            $("#ifeatured_image").val(url_img);
            $("#iefeatured_image").val(url_img);

  					$("#fiimage").attr("src",url_thb);
  					$("#fieimage").attr("src",url_thb);

            $("#modal_media").modal('hide');
  				});
  				$("#rwm").off("click","#athmbopt");
  				$("#rwm").on("click","#athmbopt",function(e){
  					e.preventDefault();
  					media_id = $(this).attr("data-id");
  					url_img = $(this).attr("data-nama");
  					url_thb = base_url_media+$(this).attr("data-thumb");

            $("#ifeatured_image").val(url_img);
            $("#iefeatured_image").val(url_img);

  					$("#fiimage").attr("src",url_thb);
  					$("#fieimage").attr("src",url_thb);

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
  			image: '<?php echo base_url('assets/img/ji-char/smile.png'); ?>',
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
        gritter('Error','Maaf, sementara ini belum bisa upload media')
  		}
  	});

  });
