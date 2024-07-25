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
				<form id="ftambah" action="<?=base_url_admin(); ?>" method="post" enctype="multipart/form-data" class="form-horizontal form-bordered" onsubmit="return false;">
					<fieldset>
						<div class="form-group">
							<div class="col-md-4">
								<label class="control-label" for="ilevel">Menu Level *</label>
								<select id="ilevel" name="level" class="form-control" required>
									<option value="0">Level 0</option>
									<option value="1">Level 1</option>
									<option value="2">Level 2</option>
								</select>
							</div>
							<div class="col-md-8">
								<label class="control-label" for="ichildren_identifier">Parent</label>
								<select id="ichildren_identifier" name="children_identifier" class="form-control" required>
									<option value="null">-</option>
									<?php foreach($modules as $momod){ ?>
									<option value="<?=$momod->identifier?>"><?=$momod->identifier?></option>
									<?php } ?>
								</select>
							</div>
						</div>
						<div class="form-group">
							<div class="col-md-6">
								<label class=" control-label" for="iidentifier">Identifier *</label>
								<input id="iidentifier" type="text" name="identifier" class="form-control" minlength="1"  placeholder="Identifier" required />
							</div>
							<div class="col-md-6">
								<label class="control-label" for="iname">Module Name *</label>
								<input id="iname" type="text" name="name" class="form-control" minlength="1"  placeholder="Module Name" required />
							</div>
						</div>
						<div class="form-group">
							<div class="col-md-4">
								<label class="control-label" for="iutype">Path Link Type</label>
								<select id="iutype" name="utype" class="form-control" required>
									<option value="internal">Internal Link Path</option>
									<option value="external">External Link Path</option>
								</select>
							</div>
							<div class="col-md-8">
								<label class="control-label" for="ipath">Path *</label>
								<input id="ipath" type="text" name="path" class="form-control" minlength="1"  placeholder="Path" required />
							</div>
						</div>
						<div class="form-group">
							<div class="col-md-4">
								<label class="control-label" for="ihas_submenu">Punya Sub Menu *</label>
								<select id="ihas_submenu" name="has_submenu" class="form-control" required>
									<option value="0">Tidak</option>
									<option value="1">Iya</option>
								</select>
							</div>
						</div>
						<div class="form-group">
							<div class="col-md-3">
								<label class="control-label" for="ifa_icon">Menu Icon *</label>
								<input id="ifa_icon" type="text" name="fa_icon" class="form-control" minlength="1" value="fa fa-home"  placeholder="contoh: fa fa-home" required />
							</div>
							<div class="col-md-3">
								<label class="control-label" for="ipriority">Prioritas * 0=awal</label>
								<input id="ipriority" type="number" name="priority" class="form-control" value="0" required />
							</div>
							<div class="col-md-3">
								<label class="control-label" for="iis_visible">Tampil *</label>
								<select id="iis_visible" name="is_visible" class="form-control" required>
									<option value="1">Iya</option>
									<option value="0">Tidak</option>
								</select>
							</div>
							<div class="col-md-3">
								<label class="control-label" for="iis_active">Aktif *</label>
								<select id="iis_active" name="is_active" class="form-control" required>
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
				<form id="fedit" action="<?=base_url_admin(); ?>" method="post" enctype="multipart/form-data" class="form-horizontal form-bordered" onsubmit="return false;">
					
					<fieldset>
						<div class="form-group">
							<div class="col-md-4">
								<label class="control-label" for="ielevel">Menu Level *</label>
								<select id="ielevel" name="level" class="form-control" required>
									<option value="0">Level 0</option>
									<option value="1">Level 1</option>
									<option value="2">Level 2</option>
								</select>
							</div>
							<div class="col-md-8">
								<label class="control-label" for="iechildren_identifier">Parent Identifier Module</label>
								<select id="iechildren_identifier" name="children_identifier" class="form-control" required>
									<option value="null">-</option>
									<?php foreach($modules as $momod){ ?>
									<option value="<?=$momod->identifier?>"><?=$momod->identifier?></option>
									<?php } ?>
								</select>
							</div>
						</div>
						<div class="form-group">
							<div class="col-md-6">
								<label class=" control-label" for="ieidentifier">Identifier *</label>
								<input id="ieidentifier" type="text" name="identifier" class="form-control" minlength="1"  placeholder="Identifier" required />
							</div>
							<div class="col-md-6">
								<label class="control-label" for="iename">Module Name *</label>
								<input id="iename" type="text" name="name" class="form-control" minlength="1"  placeholder="Module Name" required />
							</div>
						</div>
						<div class="form-group">
							<div class="col-md-4">
								<label class="control-label" for="ieutype">Path Link Type</label>
								<select id="ieutype" name="utype" class="form-control" required>
									<option value="internal">Internal Link Path</option>
									<option value="external">External Link Path</option>
								</select>
							</div>
							<div class="col-md-8">
								<label class="control-label" for="iepath">Path *</label>
								<input id="iepath" type="text" name="path" class="form-control" minlength="1"  placeholder="Path" required />
							</div>
						</div>
						<div class="form-group">
							<div class="col-md-4">
								<label class="control-label" for="iehas_submenu">Punya Sub Menu *</label>
								<select id="iehas_submenu" name="has_submenu" class="form-control" required>
									<option value="0">Tidak</option>
									<option value="1">Iya</option>
								</select>
							</div>
						</div>
						<div class="form-group">
							<div class="col-md-3">
								<label class="control-label" for="iefa_icon">Menu Icon *</label>
								<input id="iefa_icon" type="text" name="fa_icon" class="form-control" minlength="1" value="fa fa-home"  placeholder="contoh: fa fa-home" required />
							</div>
							<div class="col-md-3">
								<label class="control-label" for="iepriority">Prioritas * 0=awal</label>
								<input id="iepriority" type="number" name="priority" class="form-control" value="0" required />
							</div>
							<div class="col-md-3">
								<label class="control-label" for="ieis_visible">Tampil *</label>
								<select id="ieis_visible" name="is_visible" class="form-control" required>
									<option value="1">Iya</option>
									<option value="0">Tidak</option>
								</select>
							</div>
							<div class="col-md-3">
								<label class="control-label" for="ieis_active">Aktif *</label>
								<select id="ieis_active" name="is_active" class="form-control" required>
									<option value="1">Iya</option>
									<option value="0">Tidak</option>
								</select>
							</div>
						</div>
					</fieldset>
					<div class="form-group form-actions">
						<div class="col-xs-12 text-right">
							<button type="button" class="btn btn-sm btn-default" data-dismiss="modal">Close</button>
							<button id="bhapus" type="button" class="btn btn-sm btn-warning">Hapus</button>
							<button type="submit" class="btn btn-sm btn-primary">Simpan Perubahan</button>
						</div>
					</div>
				</form>
			</div>
			<!-- END Modal Body -->
		</div>
	</div>
</div>