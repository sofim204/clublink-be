var pakaian = {};
pakaian.ukuran = ['XXS','XS','S','M','L','XL','XXL','XXXL'];
pakaian.warna = ['Hitam'];

var image_list_count = 0;

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

function gritter(pesan,jenis='info'){
  $.bootstrapGrowl(pesan, {
    type: jenis,
    delay: 2500,
    allow_dismiss: true
  });
}

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
            h += '  <div class="thmb">';
            h += '    <div class="thmb-prev" data-id="'+val.id+'" data-nama="'+val.nama+'" data-thumb="'+val.thumb+'" style="background-image:url('+base_url_def+');min-width: 100px;min-height: 60px;">';
            h += '      <img src="'+base_url_img+'/'+val.thumb+'" class="img-responsive" alt="">';
            h += '    </div>';
            h += '    <h5 class="fm-title"><a id="athmbopt" href="#" data-id="'+val.id+'" data-thumb="'+val.thumb+'" data-nama="'+val.nama+'">'+val.filename+'</a></h5>';
            h += '    <small class="text-muted">'+val.tgl+'</small>';
            h += '  </div>';
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
            j += '  <div class="thmb">';
            j += '    <div class="thmb-prev" style="background-image:url('+base_url_def+'); min-width: 100px;min-height: 60px;">';
            j += '      <img src="'+url_thb+'" class="img-responsive" alt="">';
            j += '    </div>';
            j += '    <input type="hidden" name="image[]" value="'+url_img+'" />';
            j += '    <div class="input-group">'
            j += '      <input type="text" id="galeri_item_caption_'+galeri_item_count+'" name="caption[]" value="" class="form-control " placeholder="Caption"  />';
            j += '      <span class="input-group-btn">';
            j += '        <button id="bgaleri_item_del" type="button" class="btn btn-danger" data-id="'+galeri_item_count+'"><i class="fa fa-trash-o"></i></button>';
            j += '      </span>';
            j += '    </div>';
            j += '  </div>';
            j += '</div>';
            galeri_item_count++;

            $("#dgaleri_items").append(j);
            $("#dgaleri_items").off("click",'#bgaleri_item_del');
            $("#dgaleri_items").on("click",'#bgaleri_item_del',function(e){
              e.preventDefault();
              var id=$(this).attr("data-id");
              var cap = $('#galeri_item_caption_'+id).val();
              if(cap.length>0){
                var c = confirm('Are you sure?');
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
            j += '  <div class="thmb">';
            j += '    <div class="thmb-prev" style="background-image:url('+base_url_def+'); min-width: 100px;min-height: 60px;">';
            j += '      <img src="'+url_thb+'" class="img-responsive" alt="">';
            j += '    </div>';
            j += '    <input type="hidden" name="image[]" value="'+url_img+'" />';
            j += '    <div class="input-group">'
            j += '      <input type="text" id="galeri_item_caption_'+galeri_item_count+'" name="caption[]" value="" class="form-control " placeholder="Caption"  />';
            j += '      <span class="input-group-btn">';
            j += '        <button id="bgaleri_item_del" type="button" class="btn btn-danger" data-id="'+galeri_item_count+'"><i class="fa fa-trash-o"></i></button>';
            j += '      </span>';
            j += '    </div>';
            j += '  </div>';
            j += '</div>';
            galeri_item_count++;

            $("#dgaleri_items").append(j);
            $("#dgaleri_items").off("click",'#bgaleri_item_del');
            $("#dgaleri_items").on("click",'#bgaleri_item_del',function(e){
              e.preventDefault();
              var id=$(this).attr("data-id");
              var cap = $('#galeri_item_caption_'+id).val();
              if(cap.length>0){
                var c = confirm('Are you sure?');
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
    gritter('<h4>Memproses</h4><p>Silakan tunggu sedang upload gambar</p>','info');
    $("#modal_media_add").modal("hide");
    $("#modal_media").modal("hide");

    $.ajax({
      url: '<?=base_url('api_admin/cms/media/add/'); ?>', // Url to which the request is send
      type: "POST",
      data: new FormData(this),
      contentType: false,
      cache: false,
      processData:false,
      success: function(data){
        if(data.status == "100" || data.status == 100){
          setTimeout(function(){
            NProgress.done();
            gritter('<h4>Success</h4><p>Media file has been uploaded</p>','success');
          },1333);
        }else{
          gritter('<h4>Error</h4><p>'+data.message+'</p>','warning');
          NProgress.done();
          return false;
        }
        setTimeout(function(){
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

$("#inama").on("blur",function(e){
  e.preventDefault();
  $("#islug").val(convertToSlug($('#inama').val()));
});
$("#isku").on("blur",function(e){
  e.preventDefault();
  $("#isku").parent().parent().removeClass('has-error');
  var fd = {};
  fd.kolom = 'sku';
  fd.nilai = $(this).val();
  var url = '<?=base_url('api_admin/crm/produkreport/check/'); ?>';
  $.post(url,fd).done(function(dt){
    if(dt.status == 443 || dt.status == '443'){
      $("#isku").parent().parent().addClass('has-error');
    }
  });
});
$("#iutype").on("change",function(e){
  e.preventDefault();
  var v = $(this).val();
  if(v =='paket' || v == 'variasi'){
    $("#paket_wrapper").slideDown('slow');
  }else{
    $("#paket_wrapper").hide();
  }
});
$("#iutype").trigger('change');

$("#ipoin_dokter").on("blur",function(e){
  e.preventDefault();
  var v = parseFloat($(this).val()).toFixed(1);
  $(this).val(v);
});
$("#ipoin_terapis").on("blur",function(e){
  e.preventDefault();
  var v = parseFloat($(this).val()).toFixed(1);
  $(this).val(v);
});
$("#ipoin_pelanggan").on("blur",function(e){
  e.preventDefault();
  var v = parseFloat($(this).val()).toFixed(1);
  $(this).val(v);
});

$("#bpaket_item_tambah").on("click",function(e){
  e.preventDefault();
  alert('underconstruction');
});

//form
$("#ftambah").on("submit",function(e){
  e.preventDefault();
  NProgress.start();
  gritter('<h4>Loading</h4><p>Silakan tunggu...</p>','info');

  for ( instance in CKEDITOR.instances ) CKEDITOR.instances[instance].updateElement();
  var fd = new FormData($(this)[0]);
  $.ajax({
    url: '<?=base_url('api_admin/crm/produkreport/tambah/'); ?>',
    type: "POST",
    data: fd,
    contentType: false,
    cache: false,
    processData:false,
    success: function(data){
      NProgress.done();
      if(data.status == '100' || data.status == 100){
        growlShow('<h4>Success</h4><p>Product has been added</p>','success');
        setTimeout(function(){
          window.location = '<?=base_url_admin('crm/produkreport/'); ?>';
        },3000);
      }else{
        growlShow('<h4>Galat</h4><p>'+data.message+'</p>','danger');
      }
    },
    error: function(data){
      NProgress.done();
      growlShow('<h4>Error</h4><p>Untuk saat ini tidak dapat menambahkan produk, silakan coba beberapa saat lagi</p>','warning');
    }
  });
});


$("#iharga_jual").priceFormat({
  prefix: '$',
  centsSeparator: ',',
  thousandsSeparator: '.',
  centsLimit: 0
});
$("#iharga_jual").on("blur",function(e){
  e.preventDefault();
  $("#ihharga_jual").val(toPlainFloat($(this).val()));
});

$("#iharga_beli").priceFormat({
  prefix: '$',
  centsSeparator: ',',
  thousandsSeparator: '.',
  centsLimit: 0
});
$("#iharga_beli").on("blur",function(e){
  e.preventDefault();
  $("#ihharga_beli").val(toPlainFloat($(this).val()));
});

$("#iharga_retail").priceFormat({
  prefix: '$',
  centsSeparator: ',',
  thousandsSeparator: '.',
  centsLimit: 0
});
$("#iharga_retail").on("blur",function(e){
  e.preventDefault();
  $("#ihharga_retail").val(toPlainFloat($(this).val()));
});

$("a.btn-hidden-block").on("click",function(e){
  e.preventDefault();
  $(this).parent().parent().next().slideToggle('slow');
});

$("#bimageadd").on("click",function(e){
  e.preventDefault();
  var h = '<div id="fg_'+image_list_count+'" class="form-group" style="display:none;">';
  h += '<label for="image_list_'+image_list_count+'" class="">Image</label>';
  h += '<div class="input-group">';
  h += '<input id="image_list_'+image_list_count+'" type="file" name="foto[]" class="form-control" required />';
  h += '<span class="input-group-btn">';
  h += '<button type="button" class="btn btn-danger btn-image-list-remove" data-id="'+image_list_count+'"><i class="fa fa-trash"></i> remove</button>';
  h += '</span>';
  h += '</div>'; //input-group
  h += '</div>';
  $("#dimage_list").append(h);
  $("#fg_"+image_list_count).slideDown("slow");
  image_list_count++;

  $("#dimage_list").off("click",".btn-image-list-remove");
  $("#dimage_list").on("click",".btn-image-list-remove",function(ev){
    ev.preventDefault();
    console.log("click remove");
    var did = $(this).attr("data-id");
    $("#fg_"+did).slideUp('slow',function(){
      $("#fg_"+did).remove();
    });
  });

});

$("#buser_search").on("click",function(e){
  e.preventDefault();
  $("#user_search_modal").modal("show");
});
$("#user_search_modal_form").on("submit",function(e){
  e.preventDefault();
  NProgress.start();
  var tb = $("#user_search_table");
  $(tb).find("tbody").empty();
  var keyword = $("#user_search_modal_input").val();
  $.get("<?=base_url("api_admin/ecommerce/pelanggan/cari/")?>?keyword="+encodeURIComponent(keyword)).done(function(respon){
    NProgress.done();
    $.each(respon.data.pelanggan,function(kdt,vdt){
      var h = '<tr id="trid_'+vdt.id+'" class="user_search_table_tr">';
      h += '<td>'+vdt.id+'</td>';
      h += '<td>'+vdt.fnama+'</td>';
      h += '<td>'+vdt.email+'</td>';
      h += '<td><button type="button" class="btn btn-default btn-customer-choose"  data-id="'+vdt.id+'" data-fnama="'+vdt.fnama+'" data-alamat="'+vdt.alamat+'" data-kecamatan="'+vdt.kecamatan+'" data-kabkota="'+vdt.kabkota+'" data-provinsi="'+vdt.provinsi+'" data-negara="'+vdt.negara+'" data-latitude="'+vdt.latitude+'" data-longitude="'+vdt.longitude+'">Choose</button></td>';
      h += '</tr>';
      $(tb).find("tbody").append(h);
    });
    setTimeout(function(){
      var tbb = $("#user_search_table tbody");
      $(tbb).off("click",".btn-customer-choose");
      $(tbb).on("click",".btn-customer-choose",function(ev){
        ev.preventDefault();
        var did = $(this).attr("data-id");

        $("#ib_user_fnama").val($(this).attr("data-fnama"));
        $("#ib_user_id").val($(this).attr("data-id"));
        $("#ialamat").val($(this).attr("data-alamat"));
        $("#ikecamatan").val($(this).attr("data-kecamatan"));
        $("#ikabkota").val($(this).attr("data-kabkota"));
        $("#iprovinsi").val($(this).attr("data-provinsi"));
        $("#inegara").val($(this).attr("data-negara"));
        $("#ilatitude").val($(this).attr("data-latitude"));
        $("#ilongitude").val($(this).attr("data-longitude"));
        $("#ikodepos").val($(this).attr("data-kodepos"));

        $("#user_search_modal").modal("hide");
      });
    },333);
  }).fail(function(){
    NProgress.done();
    gritter("<h4>Error</h4><p>Cant search customer right now, please try again later</p>");
  })
});
