var ieid = <?=$jasa->id?>;
//media
var media_id = '';
var folder_id = '';
var media_name = '';
var galeri_item_count = 0;
var produk_terpilih = <?php echo json_encode($jasa->komposisi); ?>;


window.toPlainFloat = function(mny){
	return mny.replace( /^\D+/g, '').split('.').join("");
}

function growlShow(pesan,type='danger'){
	$.bootstrapGrowl(pesan, {
		type: type,
		delay: 2500,
		allow_dismiss: true
	});
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
					j += '        <button type="button" class="btn btn-danger bgaleri_item_del" data-id="'+galeri_item_count+'"><i class="fa fa-trash-o"></i></button>';
					j += '	    </span>';
					j += '	  </div>';
					j += '	</div>';
					j += '</div>';
					galeri_item_count++;

					$("#dgaleri_items").append(j);
					$("#dgaleri_items").off("click",'.bgaleri_item_del');
					$("#dgaleri_items").on("click",'.bgaleri_item_del',function(e){
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
					j += '        <button type="button" class="btn btn-danger bgaleri_item_del" data-id="'+galeri_item_count+'"><i class="fa fa-trash-o"></i></button>';
					j += '	    </span>';
					j += '	  </div>';
					j += '	</div>';
					j += '</div>';
					galeri_item_count++;

					$("#dgaleri_items").append(j);
					$("#dgaleri_items").off("click",'.bgaleri_item_del');
					$("#dgaleri_items").on("click",'.bgaleri_item_del',function(e){
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

$("#modal_media_add_form").on("submit",function(e){
	e.preventDefault();
	growlShow('Sedang upload gambar, silakan tunggu!','info');
	$("#modal_media_add").modal("hide");
	$("#modal_media").modal("hide");

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
					growlShow('Media berhasil diupload','success');
				},1333);
			}else{
				growlShow(data.message,'danger');
				return false;
			}
			setTimeout(function(){
				row_media_manager();
				$("#modal_media").modal("show");
			},3000);
		},
		error: function(d){
			growlShow('Maaf, sementara ini belum bisa upload media','danger');
		}
	});

});
//end media


//update ck editor
function updateCkEditor(){
	for (instance in CKEDITOR.instances) {
		CKEDITOR.instances[instance].updateElement();
		//$("#"+instance).val(CKEDITOR.instances[instance].getData());
	}
}

function gritter(pesan,jenis="info"){
	$.bootstrapGrowl(pesan, {
		type: jenis,
		delay: 2500,
		allow_dismiss: true
	});
}


Number.prototype.formatMoney = function(c, d, t){
var n = this,
    c = isNaN(c = Math.abs(c)) ? 2 : c,
    d = d == undefined ? "," : d,
    t = t == undefined ? "." : t,
    s = n < 0 ? "-" : "",
    i = String(parseInt(n = Math.abs(Number(n) || 0).toFixed(c))),
    j = (j = i.length) > 3 ? j % 3 : 0;
   return s + 'Rp '+ (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
};

$("#isatuan_before").on("click",function(e){
	e.preventDefault();
	$("#modal_satuan").modal("show");
});
$("#modal_satuan").on("shown.bs.modal",function(e){
	//e.preventDefault();
	$("#modal_satuan_keyword").focus();
	$("#modal_satuan_keyword").off("keyup");
	$("#modal_satuan_keyword").on("keyup",function(e){
		e.preventDefault();
		var keyword = $(this).val();
		var url = '<?php echo base_url(); ?>api_admin/produk/satuan/?iDisplayLength=100&sSearch='+encodeURIComponent(keyword);
		$.get(url).done(function(res){
			console.log(res.data.length);
			var h = '<tr><td colspan="3">Data tidak ditemukan</td></tr>';
			if(res.data.length>0){
				h = '';
				$.each(res.data,function(key,val){
					h += '<tr data-id="'+val[0]+'" data-satuan="'+val[1]+'">';
					h += '<td>'+val[1]+'</td>';
					h += '<td>'+val[2]+'</td>';
					h += '<td><a href="#" class="btn btn-info btn-xs">Pilih</a></td>';
					h += '</tr>';
				});
			}
			$("#modal_grupjenis_table tbody").html(h);
			$("#modal_grupjenis_table tbody").off("click","tr");
			$("#modal_grupjenis_table tbody").on("click","tr",function(e){
				var satuan = $(this).attr('data-satuan');
				$("#modal_satuan").modal("hide");
				$("#isatuan_qty").val(satuan).focus();
			});
		});
	});
});
$("#modal_satuan").on("hidden.bs.modal",function(e){
	//e.preventDefault();
	$("#modal_satuan_keyword").val('');
	var h = '<tr><td colspan="3">---</td></tr>';
	$("#modal_satuan_table tbody").html(h);
});

function genSku(){
	var n = $("#ienama").val().toUpperCase().trim().replace(/[^\w\s]/gi, '');
	var k = $("#ieb_kategori_id option:selected").attr('data-kode').trim().toUpperCase();
	var t = $("#ietindakan_oleh option:selected").html().trim().slice(0,1).toUpperCase();
	var a = $("#ieis_asistensi").val();
	var s = $("#iescope option:selected").attr("data-kode").toUpperCase();
	var c = $("#iea_company_id option:selected").attr("data-kode").toUpperCase();

	var ns = n.split(" ");
	if(ns.length>=2){
		n = ns[0].charAt(0)+ns[1].charAt(0);
	}else{
		n = n.slice(0,2);
	}
	if(t == '-') t = 'S';
	if(a == "1" || a == 1){
		a = '1';
	}else{
		a = '0';
	}
	$("#iesku").val(c+s+'.'+t+a+'.'+k+n+ieid);
}
$("#iea_company_id").on("blur",function(e){
	e.preventDefault();
	$("#iescope").val('all');
	if($(this).val().toLowerCase() != 'null'){
		$("#iescope").val('current_only');
	}
	genSku();
});
$("#iescope").on("blur",function(e){ e.preventDefault(); genSku(); });
$("#ienama").on("blur",function(e){ e.preventDefault(); genSku(); });
$("#ieb_kategori_id").on("blur",function(e){ e.preventDefault(); genSku(); });
$("#ietindakan_oleh").on("blur",function(e){ e.preventDefault(); genSku(); });
$("#ieis_asistensi").on("blur",function(e){ e.preventDefault(); genSku(); });
$("#iesku").on("blur",function(e){ e.preventDefault(); genSku(); });


$("#fedit").on("submit",function(e){
	e.preventDefault();

	var scp = $("#iescope").val();
	if(scp.toLowerCase() != 'all'){
		var cbg = $("#iea_company_id").val();
		if(cbg.toLowerCase() == 'null'){
			alert('Silakan pilih cabang penempatan!');
			$("#iea_company_id").focus();
			return false;
		}
	}

	NProgress.start();
	updateCkEditor();
	$("#ieproduk_komposisi").val(JSON.stringify(produk_terpilih));
	var fd = new FormData($(this)[0]);
	var url = '<?php echo base_url('api_admin/ecommerce/flashsale/edit/'.$jasa->id); ?>';
	$.ajax({
		type: $(this).attr('method'),
		url: url,
		data: fd,
		processData: false,
		contentType: false,
		success: function(respon){
			if(respon.status=="100" || respon.status == 100){
				gritter('<h4>Berhasil</h4><p>Proses edit data telah berhasil!</p>','success');
				setTimeout(function(){
					NProgress.done();
					window.location = '<?php echo base_url_admin('ecommerce/flashsale/'); ?>';
					//window.location.reload();
				},2000);
			}else{
				NProgress.done();
				gritter('<h4>Gagal</h4><p>'+respon.message+'</p>','danger');
			}
		},
		error:function(){
			NProgress.done();
			gritter('<h4>Error</h4><p>Proses edit data tidak bisa dilakukan, coba beberapa saat lagi</p>','warning');
			return false;
		}
	});
});

//komposisi produk
var tableProduk = {};
if($("#tableProduk").length>0){
	function produkTerpilihAdd(id,qty){
		console.log(produk_terpilih);
		var pt = {};
		pt.id = id;
		pt.qty = qty;
		for(var i = produk_terpilih.length - 1; i >= 0; i--) {
			if(produk_terpilih[i].id !== undefined){
				if(produk_terpilih[i].id === id) {
					produk_terpilih.splice(i, 1);
				}
			}
		}
		produk_terpilih.push(pt);
		console.log(produk_terpilih);
	}
	function produkTerpilihRemove(id){
		console.log(produk_terpilih);
		for(var i = produk_terpilih.length - 1; i >= 0; i--) {
			if(produk_terpilih[i].id !== undefined){
			if(produk_terpilih[i].id === id) {
					produk_terpilih.splice(i, 1);
				}
			}
		}
		console.log(produk_terpilih);
	}
	function updateTableProduk(){
		for(var i = produk_terpilih.length - 1; i >= 0; i--) {
			if(produk_terpilih[i].id !== undefined){
				console.log("#tableProduk #input-komposisi-"+produk_terpilih[i].id);
				$('#input-komposisi-'+produk_terpilih[i].id).val(produk_terpilih[i].qty);
			}
		}
	}

	App.datatables();
	tableProduk = jQuery('#tableProduk')
	.on('preXhr.dt', function ( e, settings, data ){
		$("#modal-preloader").modal("hide");
		//$("#modal-preloader").modal("show");
	}).DataTable({
			"order"					: [[ 3, "asc" ]],
			"responsive"	  : true,
			"bProcessing"		: true,
			"bServerSide"		: true,
			"sAjaxSource"		: "<?php  echo base_url("api_admin/ecommerce/flashsale/pilihan/"); ?>",
			"fnServerParams": function ( aoData ) {
				aoData.push(
					{ "name": "utype", "value": $('input[name=cb_utype]:checkbox:checked').map(function(){ return this.value; }).get().join(",") },
					{ "name": "b_kategori_id", "value": $("#filter_b_kategori_id").val() },
					{ "name": "tglmax", "value": $("#max").val() }
				);
			},
			"fnServerData"	: function (sSource, aoData, fnCallback, oSettings) {
				oSettings.jqXHR = $.ajax({
					dataType 	: 'json',
					method 		: 'POST',
					url 		: sSource,
					data 		: aoData
				}).success(function (response, status, headers, config) {
					console.log('Refresh produk');
					setTimeout(function(){
						console.log('Refresh produk timeout');
						updateTableProduk();
					},333);
					$("#tableProduk tbody").off("click",'.btn-angka-turun');
					$("#tableProduk tbody").on("click",'.btn-angka-turun',function(e){
						e.preventDefault();
						var did = $(this).attr('data-id');
						var v = eval($("#input-komposisi-"+did).val());
						if(v>0){
							v = v-1;
							$("#input-komposisi-"+did).val(v);
							produkTerpilihAdd(did,v);
						}else{
							$("#input-komposisi-"+did).val(0);
							produkTerpilihRemove(did);
						}
					});

					$("#tableProduk tbody").off("click",'.btn-angka-naik');
					$("#tableProduk tbody").on("click",'.btn-angka-naik',function(e){
						e.preventDefault();
						var did = $(this).attr('data-id');
						var v = eval($("#input-komposisi-"+did).val());
						v = v+1;
						$("#input-komposisi-"+did).val(v);
						produkTerpilihAdd(did,v);
					});

					fnCallback(response);
				}).error(function (response, status, headers, config) {
					gritter('<h4>Error</h4><p>Tidak dapat mengambil data produk</p>');
				});
			},
	});
	$('.dataTables_filter input').attr('placeholder', 'Cari');
	$("#bkomposisi_produk_reset").on("click",function(e){
		e.preventDefault();
		if(produk_terpilih.length>0){
			var c = confirm('Apakah anda yakin?');
			if(c){
				produk_terpilih = <?php echo json_encode($jasa->komposisi); ?>;
			}
		}
		tableProduk.ajax.reload();
	});
	$("#filter_proses").on("click",function(e){
		e.preventDefault();
		tableProduk.ajax.reload();
	});
}


//add list of product image
function getProductImage(){
	var base_url = '<?=base_url()?>';
	var base_url_def = '<?php echo base_url('media/uploads/'.'/default.jpg'); ?>';
	var url_produk = '<?=base_url()?>api_admin/ecommerce/produk/image/<?=$jasa->id?>';
	$.get(url_produk).done(function(dt){
		console.log(dt);
		if(dt.status == 100 || dt.status == '100'){
			var media_id = 0;
			var j = '';
			$.each(dt.result.images,function(k,v){
				media_id++;
  			url_img = v.url;
				url_thb = v.url_thumb;

				j += '<div id="galeri_item_'+galeri_item_count+'" class="col-xs-6 col-sm-4 col-md-4 document galeri_item_item">';
				j += '	<div class="thmb">';
				j += '		<div class="thmb-prev" style="background-image:url('+base_url_def+'); min-width: 100px;min-height: 60px;">';
				j += '			<img src="'+base_url+url_thb+'" class="img-responsive" alt="">';
				j += '		</div>';
				j += '		<input type="hidden" name="image[]" value="'+url_img+'" />';
				j += '    <div class="input-group">'
				j += '		  <input type="text" id="galeri_item_caption_'+galeri_item_count+'" name="caption[]" value="" class="form-control " placeholder="Caption"  />';
				j += '		  <span class="input-group-btn">';
				j += '        <button type="button" class="btn btn-danger bgaleri_item_del" data-id="'+galeri_item_count+'"><i class="fa fa-trash-o"></i></button>';
				j += '	    </span>';
				j += '	  </div>';
				j += '	</div>';
				j += '</div>';
				galeri_item_count++;
			});


			$("#dgaleri_items").append(j);
			$("#dgaleri_items").off("click",'.bgaleri_item_del');
			$("#dgaleri_items").on("click",'.bgaleri_item_del',function(e){
				e.preventDefault();
				var id=$(this).attr("data-id");
				var c = confirm('Apakah anda yakin?');
				if(c){
					$("#galeri_item_"+id).remove();
				}
			});
		}else{

		}
	});
}
getProductImage();


$("#ieharga_jual").priceFormat({
	prefix: 'Rp',
	centsSeparator: ',',
	thousandsSeparator: '.',
	centsLimit: 0
});
$("#ieharga_jual").on("blur",function(e){
	e.preventDefault();
	$("#iehharga_jual").val(toPlainFloat($(this).val()));
});


$("a.btn-hidden-block").on("click",function(e){
	e.preventDefault();
	$(this).parent().parent().next().slideToggle('slow');
});
