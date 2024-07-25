<?php if(!isset($api_url)) $api_url = 'https://bandros.id/ongkir/'; ?>
var api_url = '<?php echo $api_url; ?>';
var negara = '<?php echo $alamat->negara; ?>';
var provinsi = '<?php echo $alamat->provinsi; ?>';
var kabkota = '<?php echo $alamat->kabkota; ?>';
var kecamatan = '<?php echo $alamat->kecamatan; ?>';

function gritter(title,text){
  jQuery.gritter.add({
		title: title,
		text: text,
		image: '<?php echo base_url(); ?>assets/img/ji-char/smile.png',
		sticky: false,
		time: ''
	});
}

function getProvinsi(){
  var url = api_url+'provinsi';
  $("#sprovinsi").html('-- Loading --');
  $.get(url).done(function(dt){
    if(dt.result.length>0){
      var h = '';
      $.each(dt.result,function(key,val){
        h += '<option value="'+val.id+'"';
        if(val.nama_provinsi == provinsi){
          h += ' selected="selected"';
        }
        h += '>'+val.nama_provinsi+'</option>';
      });
      $("#sprovinsi").html(h);
      $("#sprovinsi").trigger("change");
    }
  }).fail(function(){
    $("#sprovinsi").html('-- Error --');
  });
}
function getKabkota(provinsi_id){
  var url = api_url+'kabkota/?provinsi_id='+encodeURIComponent(provinsi_id);
  $("#skabkota").html('-- Loading --');
  $.get(url).done(function(dt){
    if(dt.result.length>0){
      var h = '';
      $.each(dt.result,function(key,val){
        h += '<option value="'+val.id+'"';
        if(val.nama_kabkota == kabkota){
          h += ' selected="selected"';
        }
        h += '>'+val.nama_kabkota+'</option>';
      });
      $("#skabkota").html(h);
      $("#skabkota").trigger("change");
    }
  }).fail(function(){
    $("#skabkota").html('-- Error --');
  });
}
function getKecamatan(kabkota_id){
  var url = api_url+'kecamatan/?kabkota_id='+encodeURIComponent(kabkota_id);
  $("#skecamatan").html('-- Loading --');
  $.get(url).done(function(dt){
    if(dt.result.length>0){
      var h = '';
      $.each(dt.result,function(key,val){
        h += '<option value="'+val.id+'"';
        if(val.nama_kecamatan == kecamatan){
          h += ' selected="selected"';
        }
        h += '>'+val.nama_kecamatan+'</option>';
      });
      $("#skecamatan").html(h);
      $("#skecamatan").trigger('change');
      //$("#skelurahan").trigger("change");
    }
  }).fail(function(){
    $("#skecamatan").html('-- Error --');
  });
}
function getKelurahan(kecamatan_id){
  var url = api_url+'kecamatan/?kecamatan_id='+encodeURIComponent(kecamatan_id);
  $("#skelurahan").html('-- Loading --');
  $.get(url).done(function(dt){
    if(dt.result.length>0){
      var h = '';
      $.each(dt.result,function(key,val){
        h += '<option value="'+val.id+'"';
        if(val.nama_kelurahan == kelurahan){
          h += ' selected="selected"';
        }
        h += '>'+val.nama_kelurahan+'</option>';
      });
      $("#skelurahan").html(h);
      //getOngkir();
    }
  }).fail(function(){
    $("#skelurahan").html('-- Error --');
  });
}
function getOngkir(kelurahan_id){
  var url = api_url+'by_kelurahan_id/?kelurahan_id='+encodeURIComponent(kelurahan_id);
  $("#skelurahan").html('-- Loading --');
  $.get(url).done(function(dt){
    if(dt.result.length>0){
      var h = '';
      $.each(dt.result,function(key,val){
        h += '<option value="'+val.id+'">'+val.nama_kelurahan+'</option>';
      });
      $("#skelurahan").html(h);
    }
  }).fail(function(){
    $("#skelurahan").html('-- Error --');
  });
}
function drinit(){
  var negara_nama = $("#snegara").val();
  console.log(negara_nama);
  if(negara_nama.toLowerCase() == 'indonesia'){
    console.log('Show');
    $("#sprovinsi").show();
    $("#skabkota").show();
    $("#skecamatan").show();
    $("#skelurahan").show();

    $("#iprovinsi").hide();
    $("#ikabkota").hide();
    $("#ikecamatan").hide();
    $("#ikelurahan").hide();

    getProvinsi();
  }else{
    console.log('Hide');
    $("#sprovinsi").hide();
    $("#skabkota").hide();
    $("#skecamatan").hide();
    $("#skelurahan").hide();

    $("#iprovinsi").show();
    $("#ikabkota").show();
    $("#ikecamatan").show();
    $("#ikelurahan").show();
  }
}

$("#snegara").on("change",function(){
  drinit();
});
$("#sprovinsi").on("change",function(e){
  e.preventDefault();
  var val = $(this).find("option:selected").text();
  $("#iprovinsi").val(val);
  getKabkota($(this).val());
});
$("#skabkota").on("change",function(e){
  e.preventDefault();
  var val = $(this).find("option:selected").text();
  $("#ikabkota").val(val);
  getKecamatan($(this).val());
});
$("#skecamatan").on("change",function(e){
  e.preventDefault();
  var val = $(this).find("option:selected").text();
  $("#ikecamatan").val(val);
  getKelurahan($(this).val());
});


drinit();


$("#f_address_edit").on("submit",function(e){
  e.preventDefault();
  var url = '<?php echo base_url('api_web/address/edit/'.$alamat->id); ?>';
  var fd = $(this).serialize();
	$.ajax({
		url : url,
		type: "POST",
		data : fd,
		success: function(data, textStatus, jqXHR){
      var judul = '';
      var isi = '';
			if(data.status=="100" || data.status==100){
        judul = 'Berhasil';
        isi = 'Perubahan alamat berhasil disimpan';
        setTimeout(function(){
          window.location = '<?php echo base_url('account/address/'); ?>';
        },3000);
			}else{
        judul = 'Error';
        isi = data.message;
			}
      gritter(judul,isi);

		},
		error: function (jqXHR, textStatus, errorThrown){
      var judul = 'Error';
      var isi = 'Cobalah beberapa saat lagi';
      gritter(judul,isi);
			return false;
		}
	});

});
