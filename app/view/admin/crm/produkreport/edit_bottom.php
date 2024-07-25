//media

var media_id = '';
var folder_id = '';
var media_name = '';
var galeri_item_count = 0;
var image_list_count = 0;

window.toPlainFloat = function(mny){
	return mny.replace( /^\D+/g, '').split('.').join("");
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
$("#fedit").on("submit",function(e){
	e.preventDefault();
	NProgress.start();
	gritter('<h4>Processing</h4><p>Please wait...</p>','info');
	for ( instance in CKEDITOR.instances ) CKEDITOR.instances[instance].updateElement();
	var fd = new FormData($(this)[0]);

	$.ajax({
		url: '<?=base_url('api_admin/crm/produkreport/edit/'.$produk->id); ?>',
		type: "POST",
		data: fd,
		contentType: false,
		cache: false,
		processData:false,
		success: function(data){
			NProgress.done();
			if(data.status == '100' || data.status == 100){
				growlShow('<h4>Success</h4><p>Product has changed..</p>','success');
				setTimeout(function(){
					window.location = '<?=base_url_admin('crm/produkreport/'); ?>';
				},3000);
			}else{
				growlShow('<h4>Failed</h4><p>'+data.message+'</p>','danger');
			}
		},
		error: function(data){
			NProgress.done();
			growlShow('<h4>Error</h4><p>Cant edit product right now, please try again later</p>','warning');
		}
	});
});

//add list of product image
function getProductImage(){
	var base_url = '<?=base_url()?>';
	var base_url_def = '<?=base_url($this->media_produk.'/default.png')?>';
	var url_produk = '<?=base_url()?>api_admin/crm/produkreport/image/<?=$produk->id?>';
	$.get(url_produk).done(function(dt){
		console.log(dt);
		if(dt.status == 200 || dt.status == '200'){
			var media_id = 0;
			var j = '';
			$.each(dt.data.images,function(k,v){
				media_id++;
  			url_img = v.url;
				url_thb = v.url_thumb;

				var j = '';
				j += '<div id="kartu-'+v.id+'" class="col-md-3">';
				j += '	<div class="kartu">';
				j += '		<div class="gambar" style="background-image:url('+base_url_def+'); min-width: 100px;min-height: 60px;">';
				j += '			<img src="'+base_url+url_thb+'" class="img-responsive" alt="" />';
				j += '		</div>';
				j += '    <div class="teks">'
				j += '		  <div class="btn-group">';
				j += '		  	<a href="#" class="btn btn-info btn-cover-set" data-id="'+v.id+'"><i class="fa fa-file-image-o"></i> Set Cover</a>';
				j += '        <a href="#" class="btn btn-danger btn-remove-gambar" data-id="'+v.id+'"><i class="fa fa-trash"></i> Remove</a>';
				j += '	    </div>';
				j += '	  </div>';
				j += '	</div>';
				j += '</div>';
				$("#dimage_list").append(j);
				galeri_item_count++;
			});

			//gambar set cover
			$("#dimage_list").off("click",'.btn-cover-set');
			$("#dimage_list").on("click",'.btn-cover-set',function(e){
				e.preventDefault();
				var dtid=$(this).attr("data-id");
				var c = confirm('Are you sure?');
				if(c){
					NProgress.start();
					$.get('<?=base_url("api_admin/crm/produkreport/gambar_cover/".$produk->id.'/')?>'+encodeURIComponent(dtid)).done(function(res){
						NProgress.done();
						if(res.status == 200){
							gritter("<h4>Success</h4><p>Product main image has changed</p>","success");
						}else{
							gritter('<h4>Failed</h4><p>'+res.message+'</p>',"danger");
						}
					}).fail(function(){
						NProgress.done();
						gritter("<h4>Error</h4><p>Cant change main image right now, please try again later</p>","warning");
					});
				}
			});

			//gambar hapus
			$("#dimage_list").off("click",'.btn-remove-gambar');
			$("#dimage_list").on("click",'.btn-remove-gambar',function(e){
				e.preventDefault();
				var cpfid=$(this).attr("data-id");
				var c = confirm('Are you sure?');
				if(c){
					NProgress.start();
					$.get('<?=base_url("api_admin/crm/produkreport/gambar_hapus/".$produk->id)?>/'+encodeURIComponent(cpfid)).done(function(res){
						NProgress.done();
						if(res.status == 200){
							gritter("<h4>Success</h4><p>The image has been deleted</p>","success");
							$("#kartu-"+cpfid).remove();
						}else{
							gritter('<h4>Failed</h4><p>'+res.message+'</p>',"danger");
						}
					}).fail(function(){
						NProgress.done();
						gritter("<h4>Error</h4><p>Cant delete image right now, please try again later</p>","warning");
					});
				}
			});
		}else{
			NProgress.done();
			gritter('<h4>Failed</h4><p>'+dt.message+'</p>',"danger");
		}
	}).fail(function(){
		NProgress.done();
		gritter("<h4>Failed</h4><p>Cant fetch images data right now, please try again later</p>","danger");
	});
}
getProductImage();


$("#ieharga_jual").priceFormat({
	prefix: '$',
	centsSeparator: ',',
	thousandsSeparator: '.',
	centsLimit: 0
});
$("#ieharga_jual").on("blur",function(e){
	e.preventDefault();
	$("#iehharga_jual").val(toPlainFloat($(this).val()));
});

$("#ieharga_beli").priceFormat({
	prefix: '$',
	centsSeparator: ',',
	thousandsSeparator: '.',
	centsLimit: 0
});
$("#ieharga_beli").on("blur",function(e){
	e.preventDefault();
	$("#iehharga_beli").val(toPlainFloat($(this).val()));
});

$("#ieharga_retail").priceFormat({
	prefix: '$',
	centsSeparator: ',',
	thousandsSeparator: '.',
	centsLimit: 0
});
$("#ieharga_retail").on("blur",function(e){
	e.preventDefault();
	$("#iehharga_retail").val(toPlainFloat($(this).val()));
});

$("a.btn-hidden-block").on("click",function(e){
	e.preventDefault();
	$(this).parent().parent().next().slideToggle('slow');
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

				$("#ieb_user_fnama").val($(this).attr("data-fnama"));
				$("#ieb_user_id").val($(this).attr("data-id"));
				$("#iealamat").val($(this).attr("data-alamat"));
				$("#iekecamatan").val($(this).attr("data-kecamatan"));
				$("#iekabkota").val($(this).attr("data-kabkota"));
				$("#ieprovinsi").val($(this).attr("data-provinsi"));
				$("#ienegara").val($(this).attr("data-negara"));
				$("#ielatitude").val($(this).attr("data-latitude"));
				$("#ielongitude").val($(this).attr("data-longitude"));
				$("#iekodepos").val($(this).attr("data-kodepos"));
				$("#user_search_modal").modal("hide");
				userAlamat();
			});
		},333);
	}).fail(function(){
		NProgress.done();
		gritter("<h4>Error</h4><p>Cant search customer right now, please try again later</p>");
	})
});
$("#bimage_upload").on("click",function(e){
	e.preventDefault();
	$("#image_upload_modal_form").trigger("reset");
	$("#image_upload_modal").modal("show");
});
$("#image_upload_modal_form").on("submit",function(e){
	$("#image_upload_modal").modal("hide");
	e.preventDefault();
	NProgress.start();
	$.ajax({
		url: '<?=base_url('api_admin/crm/produkreport/gambar_upload/'.$produk->id); ?>', // Url to which the request is send
		type: "POST",
		data: new FormData(this),
		contentType: false,
		cache: false,
		processData:false,
		success: function(data){
			NProgress.done();
			$("#image_upload_modal").modal("hide");
			$("#dimage_list").empty();
			getProductImage();
			if(data.status == "200" || data.status == 200){
				setTimeout(function(){
					gritter('<h4>Success</h4><p>Image uploaded successfuly</p>','success');
				},1333);
			}else{
				NProgress.done();
				gritter('<h4>Failed</h4><p>'+data.message+'</p>','warning');
				return false;
			}
		},
		error: function(d){
			gritter('<h4>Error</h4><p>Cant uploading image right now, please try again later</p>','danger');
			NProgress.done();
		}
	});
});
function userAlamat(){
	console.log("Test");
	setTimeout(function(){
		var b_user_alamat_id = $("#ieb_user_id").val();
		var url = '<?=base_url("api_admin/ecommerce/pelanggan_alamat/list/")?>'+b_user_alamat_id;
		NProgress.start();
		$.get(url).done(function(raw){
			NProgress.done();
			$("#ieb_user_alamat_id").html('<option value="-">-</option>');
			$.each(raw.data,function(k,v){
				$("#ieb_user_alamat_id").append('<option value="'+v.id+'">'+v.judul+' '+v.alamat+'</option>');
			});
		}).fail(function(){
			NProgress.done();
		});
	},1000);
}




//add existing value to current form
<?php foreach($produk as $key=>$val){ if(!is_string($val)) continue; ?>
	<?php
		$val = trim($val);
		if($key == 'deskripsi' || $key =='deskripsi_singkat'){
			$val = str_replace("\n","",$val);
			$val = str_replace("\r","",$val);
		}
		if($key == 'courier_services' || $key == 'services_duration' || $key == 'vehicle_types'){
			$val = strtolower($val);
		}
	?>
$("#ie<?=$key?>").val('<?=$val?>');
<?php if($key == 'harga_jual' || $key == 'harga_retail' || $key == 'harga_beli'){ ?>
$("#ieh<?=$key?>").val('<?=$val?>');
<?php } ?>
<?php } ?>
