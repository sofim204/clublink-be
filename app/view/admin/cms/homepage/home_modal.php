<style>
	.text-left {
		text-align: left !important;
	}
</style>

<!-- modal option -->
<div id="modal_option" class="modal fade " role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">

			<!-- Modal Header -->
			<div class="modal-header text-center">
				<h2 class="modal-title">Pilihan</h2>
			</div>
			<!-- END Modal Header -->

			<!-- Modal Body -->
			<div class="modal-body">
				<div class="row">
					<div class="col-xs-12 btn-group-vertical " style="text-align: left;">
						<a id="adetail" href="#" class="btn btn-info text-left" style="display:none;"><i class="fa fa-info-circle"></i> Detail</a>
						<a id="apindah_ke" href="#" class="btn btn-info text-left" style="display:none;"><i class="fa fa-users"></i> Pindah ke</a>
						<a id="aduplikat_ke" href="#" class="btn btn-info text-left" style="display:none;"><i class="fa fa-briefcase"></i> Duplikat Ke</a>
						<a id="aedit" href="#" class="btn btn-info text-left"><i class="fa fa-pencil"></i> Edit</a>
						<button id="bhapus" type="button" class="btn btn-info text-left"><i class="fa fa-trash-o"></i> Hapus</button>
					</div>
				</div>
				<div class="row" style="margin-top: 1em; ">
					<div class="col-md-12" style="border-top: 1px #afafaf dashed;">&nbsp;</div>
					<div class="col-xs-12 btn-group-vertical">
						<button type="button" class="btn btn-default btn-block text-left" data-dismiss="modal"><i class="fa fa-times"></i> Tutup</button>
					</div>
				</div>
				<!-- END Modal Body -->
			</div>
		</div>
	</div>
</div>
<!-- end modal option -->

<!-- modal tambah -->
<div id="modal_tambah" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<!-- Modal Header -->
			<div class="modal-header text-center">
				<h2 class="modal-title">Tambah</h2>
			</div>
			<!-- END Modal Header -->

			<!-- Modal Body -->
			<div class="modal-body">
				<form id="ftambah" action="<?php echo base_url_admin(); ?>" method="post" enctype="multipart/form-data" class="form-horizontal form-bordered" onsubmit="return false;">
					<div class="form-group">
						<div class="col-md-4">
							<label for="ipromo_jenis">Jenis Promo</label>
							<select id="ipromo_jenis" name="promo_jenis" class="form-control">
								<option value="harga">Diskon Harga</option>
								<option value="persen">Persentase</option>
							</select>
						</div>
						<div class="col-md-8">
							<label for="ipromo_nilai">Nilai Promo *</label>
							<div class="input-group">
                <span id="ipromo_nilai_rp" class="input-group-addon">Rp</span>
                <input id="ipromo_nilai" name="promo_nilai" type="number" class="form-control" placeholder="Nilai promosi" required />
								<span id="ipromo_nilai_ps"  class="input-group-addon" style="display:none;">%</span>
							</div>
						</div>
					</div>
					<div class="form-group">
						<div class="col-md-4">
							<label for="iutype">Jenis Target</label>
							<select id="iutype" name="utype" class="form-control">
								<option value="produk">ID Produk</option>
								<option value="tag">ID Tag</option>
								<option value="kategori">ID Kategori</option>
							</select>
						</div>
						<div class="col-md-8">
							<br />
							<a id="aid_target_utype_btn" href="#" class="btn btn-default"><i class="fa fa-search"></i> Cari target</a>
						</div>
					</div>
					<fieldset><legend>Target</legend>
						<div class="form-group">
							<div class="col-md-2">
								<label class="" for="iid_target_utype">ID</label>
								<input id="iid_target_utype" name="id_target_utype" type="text" class="form-control" minlength="1" placeholder="ID Target" disabled required />
							</div>
							<div class="col-md-4">
								<label class="" for="inama_target_utype">slug</label>
								<input id="inama_target_utype" name="nama_target_utype" type="text" class="form-control" minlength="1" placeholder="Slug Target" disabled required />
							</div>
							<div class="col-md-6">
								<label class="" for="islug_target_utype">Nama</label>
								<input id="islug_target_utype" name="slug_target_utype" type="text" class="form-control" minlength="1" placeholder="Nama Target" disabled required />
							</div>
						</div>
					</fieldset>
					<fieldset><legend>Qty</legend>
						<div class="form-group">
							<div class="col-md-6">
								<label for="iqty_limit">Limit QTY</label>
								<input id="iqty_limit" name="qty_limit" type="number" class="form-control" value="0" />
							</div>
							<div class="col-md-6">
								<label for="iqty_sisa">Qty Sisa</label>
								<input id="iqty_sisa" name="qty_sisa" type="number" class="form-control" value="0" />
							</div>
						</div>
					</fieldset>
					<fieldset><legend>Penting</legend>
						<div class="form-group">
							<div class="col-md-4">
								<label for="iprioritas">Prioritas</label>
								<input id="iprioritas" name="prioritas" type="number" class="form-control" value="1" />
							</div>
							<div class="col-md-4">
								<label for="iis_habis">Habis?</label>
								<select id="iis_habis" name="is_habis" class="form-control">
									<option value="0">Tidak</option>
									<option value="1">Iya</option>
								</select>
							</div>
							<div class="col-md-4">
								<label for="iis_active">Status</label>
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
<!-- end modal tambah -->

<!-- modal edit -->
<div id="modal_edit" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<!-- Modal Header -->
			<div class="modal-header text-center">
				<h2 class="modal-title">Edit</h2>
			</div>
			<!-- END Modal Header -->

			<!-- Modal Body -->
			<div class="modal-body">
				<form id="fedit" action="<?php echo base_url_admin(); ?>" method="post" enctype="multipart/form-data" class="form-horizontal form-bordered" onsubmit="return false;">
					<div class="form-group">
						<div class="col-md-4">
							<label for="iepromo_jenis">Jenis Promo</label>
							<select id="iepromo_jenis" name="promo_jenis" class="form-control">
								<option value="harga">Diskon Harga</option>
								<option value="persen">Persentase</option>
							</select>
						</div>
						<div class="col-md-8">
							<label for="iepromo_nilai">Nilai Promo *</label>
							<div class="input-group">
                <span id="iepromo_nilai_rp" class="input-group-addon">Rp</span>
                <input id="iepromo_nilai" name="promo_nilai" type="number" class="form-control" placeholder="Nilai promosi" required />
								<span id="iepromo_nilai_ps"  class="input-group-addon" style="display:none;">%</span>
							</div>
						</div>
					</div>
					<div class="form-group">
						<div class="col-md-4">
							<label for="ieutype">Jenis Target</label>
							<select id="ieutype" name="utype" class="form-control">
								<option value="produk">ID Produk</option>
								<option value="tag">ID Tag</option>
								<option value="kategori">ID Kategori</option>
							</select>
						</div>
						<div class="col-md-8">
							<br />
							<a id="aeid_target_utype_btn" href="#" class="btn btn-default"><i class="fa fa-search"></i> Cari target</a>
						</div>
					</div>
					<fieldset><legend>Target</legend>
						<div class="form-group">
							<div class="col-md-2">
								<label class="" for="ieid_target_utype">ID</label>
								<input id="ieid_target_utype" name="id_target_utype" type="text" class="form-control" minlength="1" placeholder="ID Target" disabled required />
							</div>
							<div class="col-md-4">
								<label class="" for="ieslug_target_utype">slug</label>
								<input id="ieslug_target_utype" name="slug_target_utype" type="text" class="form-control" minlength="1" placeholder="Slug Target" disabled required />
							</div>
							<div class="col-md-6">
								<label class="" for="ienama_target_utype">Nama</label>
								<input id="ienama_target_utype" name="nama_target_utype" type="text" class="form-control" minlength="1" placeholder="Nama Target" disabled required />
							</div>
						</div>
					</fieldset>
					<fieldset><legend>Qty</legend>
						<div class="form-group">
							<div class="col-md-6">
								<label for="ieqty_limit">Limit QTY</label>
								<input id="ieqty_limit" name="qty_limit" type="number" class="form-control" value="0" />
							</div>
							<div class="col-md-6">
								<label for="ieqty_sisa">Qty Sisa</label>
								<input id="ieqty_sisa" name="qty_sisa" type="number" class="form-control" value="0" />
							</div>
						</div>
					</fieldset>
					<fieldset><legend>Penting</legend>
						<div class="form-group">
							<div class="col-md-4">
								<label for="ieprioritas">Prioritas</label>
								<input id="ieprioritas" name="prioritas" type="number" class="form-control" value="1" />
							</div>
							<div class="col-md-4">
								<label for="ieis_habis">Habis?</label>
								<select id="ieis_habis" name="is_habis" class="form-control">
									<option value="0">Tidak</option>
									<option value="1">Iya</option>
								</select>
							</div>
							<div class="col-md-4">
								<label for="ieis_active">Status</label>
								<select id="ieis_active" name="is_active" class="form-control">
									<option value="1">Iya</option>
									<option value="0">Tidak</option>
								</select>
							</div>
						</div>
					</fieldset>
					<div class="form-group form-actions">
						<div class="col-xs-12 text-right">
							<button type="button" class="btn btn-sm btn-default" data-dismiss="modal">Close</button>
							<button id="bhapus2" type="button" class="btn btn-sm btn-warning">Hapus</button>
							<button type="submit" class="btn btn-sm btn-primary">Simpan</button>
						</div>
					</div>
				</form>
			</div>
			<!-- END Modal Body -->
		</div>
	</div>
</div>


<!-- modal produk cari -->
<div id="modal_cari_produk" class="modal fade " role="dialog" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header text-center">
				<h2 class="modal-title">Cari Produk</h2>
			</div>
			<!-- modal produk cari body -->
			<div class="modal-body">
				<div class="row">
					<div class="col-md-12">
						<form id="modal_cari_produk_input_form">
							<input id="modal_cari_produk_input" type="text" name="keyword" class="form-control" placeholder="Masukan kata kunci pencarian" required autocomplete="off" />
						</form>
					</div>
					<div class="col-md-12">
						<table id="modal_cari_produk_tabel" class="table table-bordered">
							<thead>
								<tr>
									<th>ID</th>
									<th>Nama</th>
									<th>Slug</th>
									<th>Aksi</th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td colspan="4">Cari</td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
				<div class="row" style="margin-top: 1em; ">
					<div class="col-md-12" style="border-top: 1px #afafaf dashed;">&nbsp;</div>
					<div class="col-xs-12 btn-group-vertical">
						<button type="button" class="btn btn-default btn-block text-left" data-dismiss="modal"><i class="fa fa-times"></i> Tutup</button>
					</div>
				</div>
			</div>
			<!-- modal produk cari body -->
		</div>
	</div>
</div>
<!-- end modal produk cari -->

<!-- modal tag cari -->
<div id="modal_cari_tag" class="modal fade " tabindex="1" role="dialog" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header text-center">
				<h2 class="modal-title">Cari Tags</h2>
			</div>
			<!-- modal tag cari body -->
			<div class="modal-body">
				<div class="row">
					<div class="col-md-12">
						<form id="modal_cari_tag_input_form">
							<input id="modal_cari_tag_input" type="text" name="keyword" class="form-control" placeholder="Masukan kata kunci pencarian" required />
						</form>
					</div>
					<div class="col-md-12">
						<table id="modal_cari_tag_tabel" class="table table-bordered">
							<thead>
								<tr>
									<th>ID</th>
									<th>Nama</th>
									<th>Slug</th>
									<th>Aksi</th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td colspan="4">Cari</td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
				<div class="row" style="margin-top: 1em; ">
					<div class="col-md-12" style="border-top: 1px #afafaf dashed;">&nbsp;</div>
					<div class="col-xs-12 btn-group-vertical">
						<button type="button" class="btn btn-default btn-block text-left" data-dismiss="modal"><i class="fa fa-times"></i> Tutup</button>
					</div>
				</div>
			</div>
			<!-- modal tag cari body -->
		</div>
	</div>
</div>
<!-- end modal tag cari -->

<!-- modal kategori cari -->
<div id="modal_cari_kategori" class="modal fade " role="dialog" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header text-center">
				<h2 class="modal-title">Cari Kategori</h2>
			</div>
			<!-- modal kategori cari body -->
			<div class="modal-body">
				<div class="row">
					<div class="col-md-12">
						<form id="modal_cari_kategori_input_form">
							<input id="modal_cari_kategori_input" type="text" name="keyword" class="form-control" placeholder="Masukan kata kunci pencarian" required />
						</form>
					</div>
					<div class="col-md-12">
						<table id="modal_cari_kategori_tabel" class="table table-bordered">
							<thead>
								<tr>
									<th>ID</th>
									<th>Nama</th>
									<th>Slug</th>
									<th>Aksi</th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td colspan="4">Cari</td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
				<div class="row" style="margin-top: 1em; ">
					<div class="col-md-12" style="border-top: 1px #afafaf dashed;">&nbsp;</div>
					<div class="col-xs-12 btn-group-vertical">
						<button type="button" class="btn btn-default btn-block text-left" data-dismiss="modal"><i class="fa fa-times"></i> Tutup</button>
					</div>
				</div>
			</div>
			<!-- modal kategori cari body -->
		</div>
	</div>
</div>
<!-- end modal kategori cari -->
