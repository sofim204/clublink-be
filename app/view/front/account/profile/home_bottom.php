function gritter(isi,judul="info",emoji='smile'){
	jQuery.gritter.add({
		title: judul,
		text: isi,
		image: '<?=base_url('favicon.png')?>',
		sticky: false,
		time: ''
	});
}
$("#aprofil_edit").on("click",function(e){
  e.preventDefault();
  $("#modal_profil").modal("show");
});

$("#fprofil").on("submit",function(e){
  e.preventDefault();
	var fd = new FormData(this);
	gritter('Silakan tunggu..','Memproses');
  $.ajax({
    method: 'post',
    contentType: false,
		cache: false,
		processData:false,
    url: '<?php echo base_url('api_web/account/edit/'); ?>',
    data: fd,
    success: function(data){
			if(data.status == "100" || data.status == 100){
				setTimeout(function(){
					gritter('Profil berhasil di ubah','Berhasil');
				},1333);
			}else{
				gritter(data.message,'Error');
				return false;
			}
      $("#modal_profil").modal("hide");
      setTimeout(function(){
        location.reload();
      },2000);
		},
		error: function(d){
			gritter('Maaf, sementara ini belum bisa ubah profil','Error');
		}
  });
});

$("#aprofil_password").on("click",function(e){
  e.preventDefault();
  $("#modal_password").modal("show");
});

$("#fpassword").on("submit",function(e){
  e.preventDefault();
  var p = $("#password").val();
  var r = $("#repassword").val();
  if(p != r){
    gritter('Password konfirmasi tidak sama');
    $("#password").focus();
    return false;
  }
  $.ajax({
    method: 'post',
    contentType: false,
		cache: false,
		processData:false,
    url: '<?php echo base_url('api_web/account/edit_password/'); ?>',
    data: new FormData(this),
    success: function(data){
			if(data.status == "100" || data.status == 100){
				setTimeout(function(){
					gritter('Password berhasil diubah','Berhasil');
				},1333);
			}else{
				gritter(data.message,'Error');
				return false;
			}
		},
		error: function(d){
			gritter('Maaf, sementara ini belum bisa ubah password','Error');
		}
  });
});
$("#aconfirm_email_re").on("click",function(e){
	e.preventDefault();
	$.get('<?php echo base_url('api_web/account/resend_email/'); ?>').done(function(){
		gritter('Email konfirmasi berhasil dikirim ulang',judul="Berhasil",emoji='smile');
		<?php
			$email_provider = explode('@',$sess->user->email);
			$email_provider = end($email_provider);
		?>
		window.open('//<?php echo $email_provider; ?>');
		//$("#aemail_buka").attr('href','<?php echo base_url('account/dashboard/'); ?>');
		//$("#aemail_buka").trigger("click");
		setTimeout(function(){
			window.location = '//<?php echo $email_provider; ?>';
		},2000);
	}).fail(function(){
		gritter('Tidak dapat mengirim email konfirmasi, coba beberapa saat lagi',judul="Error",emoji='smile');
	});
});
$("#aprofil_foto").on("click",function(e){
	e.preventDefault();
	$("#profil_foto_modal").modal("show");
	$("img#preview").attr("src",$("#display_picture").attr("src"));
	var m = $("#fprofil_foto");
	$(m).find("#ifoto").off("change");
	$(m).find("#ifoto").on("change",function(e){
		e.preventDefault();
		m.trigger("submit");
	});
});
$("#fprofil_foto").on("submit",function(e){
	e.preventDefault();
	var m = $("#profil_foto_modal");
	$(m).find("#pprofil_foto").html("Uploading...");
	$(m).find("#pprofil_foto").show("slow");
	gritter('Sedang upload gambar...','Memproses');
	$.ajax({
		url: $(this).attr("action"), // Url to which the request is send
		type: "POST",
		data: new FormData(this),
		contentType: false,
		cache: false,
		processData:false,
		success: function(data){
			if(data.status == "100" || data.status == 100){
				var m = $("#profil_foto_modal");
				$(m).find('#pprofil_foto').hide();
				$(m).find('#pprofil_foto').html("Berhasil, mengeset foto profil mohon tunggu...");
				$(m).find('#pprofil_foto').show("slow");

				$('#fprofil_foto').trigger("reset");
				console.log(data);
				$("#display_picture").attr("src",data.result.image);
				$("#display_picture_mobile").attr("src",data.result.image);
				setTimeout(function(){
					gritter('Foto profil berhasil diubah','Berhasil');
					$("#profil_foto_modal").modal("hide");
				},3333);
			}else{
				gritter(data.message,'Gagal');
				return false;
			}
		},
		error: function(){
			gritter('Untuk saat ini tidak dapat mengganti foto profil, coba beberapa saat lagi','Error');
		}
	})
});
