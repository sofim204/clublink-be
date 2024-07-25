<style>
	.text-left {
		text-align: left !important;
	}
	.google_mtitle {
    color: rgb(26, 13, 171);
    font-family: arial, sans-serif;
    font-size: 18px;
    font-weight: normal;
    height: auto;
    line-height: 21.6px;
    list-style-type: decimal;
    text-align: left;
    text-decoration: none;
    visibility: visible;
    white-space: nowrap;
    width: auto;
    padding: 0px;
    margin: 0px;
}
.google_slug {
    color: rgb(0, 102, 33);
    font-family: arial, sans-serif;
    font-size: 14px;
    font-style: normal;
    font-weight: normal;
    height: auto;
    line-height: 16px;
    list-style-type: decimal;
    text-align: left;
    visibility: visible;
    white-space: nowrap;
    width: auto;
    padding: 0px;
    margin: 0px;
}
.google_mdescription {
    color: rgb(84, 84, 84);
    font-family: arial, sans-serif;
    font-size: 13px;
    font-weight: normal;
    height: auto;
    line-height: 18.2px;
    list-style-type: decimal;
    text-align: left;
    visibility: visible;
    width: auto;
    word-wrap: break-word;
    padding: 0px;
    margin: 0px;
    bottom: 4px;
}
.google_mkeyword {
    color: rgb(84, 84, 84);
    font-family: arial, sans-serif;
    font-size: 13px;
    font-weight: normal;
    height: auto;
    line-height: 18.2px;
    list-style-type: decimal;
    text-align: left;
    visibility: visible;
    width: auto;
    word-wrap: break-word;
    padding: 0px;
    margin: 0px;
}
</style>

<!-- ================================================================
||                                                                 ||
||                        OPTION CLICKED                           ||
||                                                                 ||
================================================================= -->
<div id="modal_option" class="modal fade " tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">

			<!-- Modal Header -->
			<div class="modal-header text-center">
				<h2 class="modal-title">Options</h2>
			</div>
			<!-- END Modal Header -->

			<!-- Modal Body -->
			<div class="modal-body">
				<div class="row">
					<div class="col-xs-12 btn-group-vertical " style="text-align: left;">
						<!-- <a id="adetail" href="#" class="btn btn-info text-left"><i class="fa fa-info-circle"></i> Detail</a> -->
						<a id="aignore" href="#" class="btn btn-danger text-left"><i class="fa fa-pencil"></i> Ignore</a>
						<a id="atakedown" href="#" class="btn btn-danger text-left"><i class="fa fa-pencil"></i> Takedown</a>
					</div>
				</div>
				<div class="row" style="margin-top: 1em; ">
					<div class="col-md-12" style="border-top: 1px #afafaf dashed;">&nbsp;</div>
					<div class="col-xs-12 btn-group-vertical">
						<button type="button" class="btn btn-default btn-block text-left" data-dismiss="modal"><i class="fa fa-times"></i> Close</button>
					</div>
				</div>
				<!-- END Modal Body -->
			</div>
		</div>
	</div>
</div>

<!-- ================================================================
||                                                                 ||
||                          D E T A I L                            ||
||                                                                 ||
================================================================= -->
<div id="modal_edit" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<!-- Modal Header -->
			<div class="modal-header text-center">
				<h2 class="modal-title">Details</h2>
			</div>
			<!-- END Modal Header -->

			<!-- Modal Body -->
			<div class="modal-body">
				<form id="fedit" action="<?=base_url_admin(); ?>" method="post" enctype="multipart/form-data" class="form-horizontal form-bordered" onsubmit="return false;">
					<fieldset>
						<div class="form-group" style="display:none;">
							<div class="col-md-4">
								<label for="ieutype">Tingkat Kategori*</label>
								<select id="ieutype" name="utype" class="form-control">
									<option value="kategori" data-kode="1">Kategori Utama</option>
									<option value="kategori_sub" data-kode="2">Sub Kategori</option>
									<option value="kategori_sub_sub" data-kode="3">Sub Kategori dari Sub Kategori</option>
								</select>
							</div>
							<div class="col-md-4">
								<label for="ieb_kategori_id">Kategori Induk</label>
								<select id="ieb_kategori_id" name="b_kategori_id" class="form-control">
									<option value="null" data-kode=""> - </option>
									<?php if(isset($kategori)){ foreach($kategori as $kat){ ?>
									<?php if(isset($kat->id)){ ?>
									<option value="<?php echo $kat->id; ?>" data-kode="<?=$kat->kode;?>"><?php echo $kat->nama; ?></option>
									<?php if(count($kat->childs)){ foreach($kat->childs as $kc){ ?>
									<option value="<?php echo $kc->id; ?>" data-kode="<?=$kat->kode;?>">--&nbsp;<?php echo $kc->nama; ?></option>
									<?php }}}}} ?>
								</select>
							</div>
							<div class="col-md-4">
								<label for="iekode">Kode*</label>
								<input id="iekode" name="kode" class="form-control" placeholder="Kode" />
							</div>
						</div>
						<div class="form-group">
							<div class="col-md-12">
								<label class="" for="ienama">Nama Post</label>
								<input id="ienama" type="text" name="nama" class="form-control" minlength="1" placeholder="Nama Produk" required />
							</div>
						</div>
						<div class="form-group" style="display:none;">
							<div class="col-md-12">
								<label class="" for="ieslug">Slug</label>
								<input id="ieslug" type="text" name="slug" class="form-control" minlength="2" required />
							</div>
						</div>
					</fieldset>
					<fieldset><legend>Deskripsi</legend>
						<div class="form-group">
							<div class="col-md-12">
								<label class="control-label" for="iedeskripsi">Deskripsi Lengkap</label>
								<textarea id="iedeskripsi" name="deskripsi" class="ckeditor" rows="5"></textarea>
							</div>
						</div>
					</fieldset>
					<fieldset style="display:none;"><legend>SEO</legend>
						<div class="form-group">
							<div class="col-md-12">
								<label for="iemtitle">Meta Title</label>
								<input id="iemtitle" name="mtitle" type="text" class="form-control" value="" />
							</div>
							<div class="col-md-12">
								<label for="iemdescription">Meta Description</label>
								<textarea id="iemdescription" name="mdescription" type="text" class="form-control" rows="5"></textarea>
							</div>
							<div class="col-md-12">
								<label for="iemkeyword">Meta Keyword</label>
								<input id="iemkeyword" name="mkeyword" type="text" class="form-control" placeholder="kata kunci utama, contoh kerupuk cireng" value="" />
							</div>
						</div>
					</fieldset>
					<fieldset style="display:none;"><legend>SEO Preview</legend>
						<div class="form-group">
							<div class="col-md-4">
								<img src="<?=base_url(); ?>assets/img/seo-preview.png" class="img-responsive" />
							</div>
							<div class="col-md-8">
								<h4 class="google_mtitle" id="iegmtitle">Meta Title</h4>
								<p class="google_slug" id="iegslug">Slug</p>
								<p class="google_mdescription" id="iegmdescription">Meta Description for search engine</p>
								<p class="google_mkeyword" id="iegmkeyword">Keywords</p>
							</div>
						</div>
					</fieldset>
					<fieldset><legend>Penting</legend>
						<div class="form-group">
							<div class="col-md-4">
								<label for="ieprioritas">Prioritas* <small>(0 high-99 low)</small></label>
								<input id="ieprioritas" name="prioritas" type="text" class="form-control" value="0" />
							</div>
							<div class="col-md-4">
								<label for="ieis_visible">Dapat Dilihat</label>
								<select id="ieis_visible" name="is_visible" class="form-control">
									<option value="1">Iya</option>
									<option value="0">Tidak</option>
								</select>
							</div>
							<div class="col-md-4">
								<label for="ieis_active">Status</label>
								<select id="ieis_active" name="is_active" class="form-control">
									<option value="1">Aktif</option>
									<option value="0">Tidak</option>
								</select>
							</div>
						</div>
					</fieldset>
					<div class="form-group form-actions">
						<div class="col-xs-12 text-right">
							<button type="button" class="btn btn-sm btn-default" data-dismiss="modal">Close</button>
							<button type="submit" class="btn btn-sm btn-primary" style="display:none">Simpan</button>
						</div>
					</div>
				</form>
			</div>
			<!-- END Modal Body -->
		</div>
	</div>
</div>

<!-- ================================================================
||                                                                 ||
||                          ICON CHANGE                            ||
||                                                                 ||
================================================================= -->
<div id="modal_icon_change" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<!-- Modal Header -->
			<div class="modal-header text-center">
				<h2 class="modal-title">Change Icon</h2>
			</div>
			<!-- END Modal Header -->

			<!-- Modal Body -->
			<div class="modal-body">
				<form id="ficon_change" action="<?=base_url_admin(); ?>" method="post" enctype="multipart/form-data" class="form-horizontal form-bordered" onsubmit="return false;">
					<fieldset>
						<div class="form-group">
							<div class="col-md-12">
								<label class="" for="ieimage_icon">Choose Icon File * <small>128px x 128px</small></label>
								<input id="ieimage_icon" type="file" name="image_icon" class="form-control" accept="image/x-png,image/jpeg,image/jpg"  required />
							</div>
						</div>
					</fieldset>
					<div class="form-group form-actions">
						<div class="col-xs-12 text-right">
							<button type="button" class="btn btn-sm btn-default" data-dismiss="modal">Close</button>
							<button type="submit" class="btn btn-sm btn-primary"><i class="fa fa-upload"></i> Upload</button>
						</div>
					</div>
				</form>
			</div>
			<!-- END Modal Body -->
		</div>
	</div>
</div>

<!-- ================================================================
||                                                                 ||
||                             A D D                               ||
||                                                                 ||
================================================================= -->
<div id="modal_tambah" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<!-- Modal Header -->
			<div class="modal-header text-center">
				<h2 class="modal-title">Tambah</h2>
			</div>
			<!-- END Modal Header -->

			<!-- Modal Body -->
			<div class="modal-body">
				<form id="ftambah" action="<?=base_url_admin(); ?>" method="post" enctype="multipart/form-data" class="form-horizontal form-bordered" onsubmit="return false;">
					<fieldset>
						<div class="form-group">
							<div class="col-md-4">
								<label for="iutype">Tingkat Kategori*</label>
								<select id="iutype" name="utype" class="form-control">
									<option value="kategori" data-kode="1">Kategori Utama</option>
									<option value="kategori_sub" data-kode="2">Sub Kategori</option>
									<option value="kategori_sub_sub" data-kode="3">Sub Kategori dari Sub Kategori</option>
								</select>
							</div>
							<div class="col-md-4">
								<label for="ib_kategori_id">Kategori Induk</label>
								<select id="ib_kategori_id" name="b_kategori_id" class="form-control">
									<option value="null"> - </option>
									<?php if(isset($kategori)){ foreach($kategori as $kat){ ?>
									<?php if(isset($kat->id)){ ?>
									<option value="<?php echo $kat->id; ?>" data-kode="<?=$kat->kode?>"><?php echo $kat->nama; ?></option>
									<?php if(count($kat->childs)){ foreach($kat->childs as $kc){ ?>
									<option value="<?php echo $kc->id; ?>" data-kode="<?=$kc->kode?>">--&nbsp;<?php echo $kc->nama; ?></option>
									<?php }}}}} ?>
								</select>
							</div>
							<div class="col-md-4">
								<label class="" for="ikode">Kode</label>
								<input id="ikode" type="text" name="kode" class="form-control" minlength="1" placeholder="Kode" required />
							</div>
						</div>
						<div class="form-group">
							<div class="col-md-12">
								<label class="" for="inama">Nama Kategori</label>
								<input id="inama" type="text" name="nama" class="form-control" minlength="1" placeholder="Nama Produk" required />
							</div>
						</div>
						<div class="form-group">
							<div class="col-md-12">
								<label class="" for="islug">Slug</label>
								<input id="islug" type="text" name="slug" class="form-control" minlength="2" required />
							</div>
						</div>
					</fieldset>
					<fieldset><legend>Deskripsi</legend>
						<div class="form-group">
							<div class="col-md-12">
								<label class="control-label" for="ideskripsi">Deskripsi Lengkap</label>
								<textarea id="ideskripsi" name="deskripsi" class="ckeditor" rows="5"></textarea>
							</div>
						</div>
					</fieldset>
					<fieldset><legend>SEO</legend>
						<div class="form-group">
							<div class="col-md-12">
								<label for="imtitle">Meta Title</label>
								<input id="imtitle" name="mtitle" type="text" class="form-control" value="" />
							</div>
							<div class="col-md-12">
								<label for="imdescription">Meta Description</label>
								<textarea id="imdescription" name="mdescription" type="text" class="form-control" rows="5"></textarea>
							</div>
							<div class="col-md-12">
								<label for="imkeyword">Meta Keyword</label>
								<input id="imkeyword" name="mkeyword" type="text" class="form-control" placeholder="kata kunci utama, contoh kerupuk cireng" value="" />
							</div>
						</div>
					</fieldset>
					<fieldset><legend>SEO Preview</legend>
						<div class="form-group">
							<div class="col-md-4">
								<img src="<?=base_url(); ?>assets/img/seo-preview.png" class="img-responsive" />
							</div>
							<div class="col-md-8">
								<h4 class="google_mtitle" id="igmtitle">Meta Title</h4>
								<p class="google_slug" id="igslug">Slug</p>
								<p class="google_mdescription" id="igmdescription">Meta Description for search engine</p>
								<p class="google_mkeyword" id="igmkeyword">Keywords</p>
							</div>
						</div>
					</fieldset>
					<fieldset><legend>Penting</legend>
						<div class="form-group">
							<div class="col-md-4">
								<label for="iprioritas">Prioritas* <small>(0 high-99 low)</small></label>
								<input id="iprioritas" name="prioritas" type="text" class="form-control" value="0" />
							</div>
							<div class="col-md-4">
								<label for="iis_visible">Dapat Dilihat</label>
								<select id="iis_visible" name="is_visible" class="form-control">
									<option value="1">Iya</option>
									<option value="0">Tidak</option>
								</select>
							</div>
							<div class="col-md-4">
								<label for="iis_active">Active</label>
								<select id="iis_active" name="is_active" class="form-control">
									<option value="1">Iya</option>
									<option value="0">Tidak</option>
								</select>
							</div>
						</div>
					</fieldset>
					<div class="form-group form-actions">
						<div class="col-xs-12 text-right">
							<button type="button" class="btn btn-sm btn-default" data-dismiss="modal">Close</button>
							<button type="submit" class="btn btn-sm btn-primary">Simpan</button>
						</div>
					</div>
				</form>
			</div>
			<!-- END Modal Body -->
		</div>
	</div>
</div>
