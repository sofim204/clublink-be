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
			<div class="modal-header modal-header-title text-center">
				<a href="javascript:void(0)" data-dismiss="modal" class="pull-right modal-dismiss"><strong><i class="fa fa-times"></i></strong></a>
				<h2 class="modal-title"><strong>Add</strong></h2>
			</div>
			<!-- END Modal Header -->

			<!-- Modal Body -->
			<div class="modal-body">
				<form id="ftambah" action="<?=base_url_admin(); ?>" method="post" enctype="multipart/form-data" class="form-horizontal form-bordered" onsubmit="return false;">
					<fieldset>
		            	<div class="form-group">
		              		<div class="col-md-12">
		                		<label class="" for="icdate" >Cdate *</label>
		                		<input id="icdate" type="number" step="0.01" name="cdate" class="form-control" minlength="1" placeholder="" required />
		              		</div>
							<div class="col-md-12">
								<label class="" for="ijenis" >Jenis*</label>
								<input id="ijenis" type="text" name="jenis" class="form-control" minlength="1" placeholder="" required />
							</div>
							<div class="col-md-12">
								<label class="" for="imessage" >message*</label>
								<input id="imessage" type="number" step="0.01" name="message" class="form-control" minlength="1" placeholder="" required />
							</div>
		            	</div>
					</fieldset>
					<div class="form-group form-actions">
						<div class="col-xs-12 text-right">
							<button type="button" class="btn btn-sm btn-default" data-dismiss="modal">Close</button>
							<button type="submit" class="btn btn-sm btn-primary">Save</button>
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
			<div class="modal-header modal-header-title text-center">
				<a href="javascript:void(0)" data-dismiss="modal" class="pull-right modal-dismiss"><strong><i class="fa fa-times"></i></strong></a>
				<h2 class="modal-title"><strong>Edit</strong></h2>
			</div>
			<!-- END Modal Header -->

			<!-- Modal Body -->
			<div class="modal-body">
				<form id="fedit" action="<?=base_url_admin(); ?>" method="post" enctype="multipart/form-data" class="form-horizontal form-bordered" onsubmit="return false;">
					<fieldset>
						<div class="form-group">
							<div class="col-md-6">
								<label class="" for="ienation_code">Nation Code</label>
								<input id="ienation_code" type="number" name="nation_code" class="form-control" disabled>
							</div>
							<div class="col-md-6">
								<label class="" for="ieid">ID</label>
								<input id="ieid" type="number" name="id" class="form-control" disabled>
							</div>
						</div>
						<div class="form-group">
			              	<div class="col-md-12">
			                	<label class="" for="iecdate" >cdate *</label>
			                	<input id="iecdate" type="number" step="0.01" name="cdate" class="form-control" minlength="1" placeholder="" required />
			              	</div>
							<div class="col-md-12">
								<label class="" for="iejenis" >Jenis*</label>
								<input id="iejenis" type="text" name="jenis" class="form-control" minlength="1" placeholder="" required />
							</div>
							<div class="col-md-12">
								<label class="" for="iemessage" >message*</label>
								<input id="iemessage" type="number" step="0.01" name="message" class="form-control" minlength="1" placeholder="" required />
							</div>
            			</div>
					</fieldset>
					<div class="form-group form-actions">
						<div class="col-xs-12 text-right">
							<button type="button" class="btn btn-sm btn-default" data-dismiss="modal">Close</button>
							<button id="bhapus" type="button" class="btn btn-sm btn-warning">Delete</button>
							<button type="submit" class="btn btn-sm btn-primary">Save</button>
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
			<div class="modal-header modal-header-title text-center">
				<h2 class="modal-title"><strong>Options</strong></h2>
			</div>
			<!-- END Modal Header -->

			<!-- Modal Body -->
			<div class="modal-body">
				<div class="row">
					<div class="col-xs-12 btn-group-vertical " style="text-align: left;">
						<!-- <a id="aedit" href="#" class="btn btn-info text-left"><i class="fa fa-pencil"></i> Edit</a>
						<button id="ahapus" type="button" class="btn btn-danger text-left"><i class="fa fa-trash-o"></i> Delete</button> -->
						<a id="adetail" href="#" class="btn btn-info text-left">
							<i class="fa fa-info-circle"></i> Detail
						</a>
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
