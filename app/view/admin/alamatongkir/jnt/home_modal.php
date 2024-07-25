<style>
	.text-left {
		text-align: left !important;
	}
</style>
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
					<fieldset>
            <div class="form-group">
              <div class="col-md-6">
                <label class="" for="iorigin" >Origin</label>
								<select id="iorigin" name="origin" class="form-control" >
                  <?php foreach($kabkota2 as $k2) { ?>
                  <option value="<?=$k2->id?>"><?=$k2->nama_kabkota?></option>
                  <?php }?>
                </select>
							</div>
            </div>
            <div class="form-group">
              <div class="col-md-3">
                <select id="iprovinsi" name="provinsi" class="form-control" >
									<option value=""></option>
                  <?php foreach($provinsi as $p) { ?>
                  <option value="<?=$p->id?>"><?=$p->nama_provinsi?></option>
                  <?php }?>
                </select></div>
              <div class="col-md-3">
                <select id="ikabkota" name="kabkota" class="form-control" >
									<option value=""></option>
                  <?php foreach($kabkota as $k) { ?>
                  <option value="<?=$k->id?>"><?=$k->nama_kabkota?></option>
                  <?php }?>
                </select>
              </div>
              <div class="col-md-3">
                <select id="ikecamatan" name="kecamatan" class="form-control" >
									<option value=""></option>
                  <?php foreach($kecamatan as $kc) { ?>
                  <option value="<?=$kc->id?>"><?=$kc->nama_kecamatan?></option>
                  <?php }?>
                </select>
              </div>
            </div>
            <div class="form-group">
              <div class="col-md-6">
                <label class="" for="ireg_tarif" >reg_tarif</label>
                <input id="ireg_tarif" type="text" name="reg_tarif" class="form-control" minlength="1" placeholder="" />
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
				<form id="fedit" action="<?php echo base_url_admin(); ?>" method="post" enctype="multipart/form-data" class="form-horizontal form-bordered" onsubmit="return false;">
					<fieldset>
            <div class="form-group">
              <div class="col-md-6">
                <label class="" for="ieorigin" >Origin</label>
								<select id="ieorigin" name="origin" class="form-control" >
                  <?php foreach($kabkota2 as $k2) { ?>
                  <option value="<?=$k2->id?>"><?=$k2->nama_kabkota?></option>
                  <?php }?>
                </select>
							</div>
            </div>
            <div class="form-group">
              <div class="col-md-3">
                <select id="ieprovinsi" name="provinsi" class="form-control" >
									<option value=""></option>
                  <?php foreach($provinsi as $p) { ?>
                  <option value="<?=$p->id?>"><?=$p->nama_provinsi?></option>
                  <?php }?>
                </select></div>
              <div class="col-md-3">
                <select id="iekabkota" name="kabkota" class="form-control" >
									<option value=""></option>
                  <?php foreach($kabkota as $k) { ?>
                  <option value="<?=$k->id?>"><?=$k->nama_kabkota?></option>
                  <?php }?>
                </select>
              </div>
              <div class="col-md-3">
                <select id="iekecamatan" name="kecamatan" class="form-control" >
									<option value=""></option>
                  <?php foreach($kecamatan as $kc) { ?>
                  <option value="<?=$kc->id?>"><?=$kc->nama_kecamatan?></option>
                  <?php }?>
                </select>
              </div>
            </div>
            <div class="form-group">
              <div class="col-md-6">
                <label class="" for="iereg_tarif" >reg_tarif</label>
                <input id="iereg_tarif" type="text" name="reg_tarif" class="form-control" minlength="1" placeholder="" />
              </div>
            </div>
					</fieldset>
					<div class="form-group form-actions">
						<div class="col-xs-12 text-right">
							<button type="button" class="btn btn-sm btn-default" data-dismiss="modal">Close</button>
							<button id="bhapus" type="button" class="btn btn-sm btn-warning">Hapus</button>
							<button type="submit" class="btn btn-sm btn-primary">Simpan</button>
						</div>
					</div>
				</form>
			</div>
			<!-- END Modal Body -->
		</div>
	</div>
</div>

<!-- modal option -->
<div id="modal_option" class="modal fade " tabindex="-1" role="dialog" aria-hidden="true">
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
						<a id="aedit" href="#" class="btn btn-info text-left"><i class="fa fa-pencil"></i> Edit</a>
						<button id="ahapus" type="button" class="btn btn-info text-left"><i class="fa fa-trash-o"></i> Hapus</button>
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
