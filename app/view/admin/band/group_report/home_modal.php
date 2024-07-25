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

<!-- modal icon change -->
<div id="modal_icon_change" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<!-- Modal Header -->
			<div class="modal-header modal-header-title text-center">
				<h2 class="modal-title"><strong>Change Icon</strong></h2>
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
						<a id="adetail" href="#" class="btn btn-info text-left mb-8px"><i class="fa fa-info-circle"></i> Detail</a>
						<a id="bconfirmation" href="#" class="btn btn-primary text-left mb-8px"><i class="fa fa-phone"></i> Already Confirmation?</a>
						<!-- <a id="aedit" href="#" class="btn btn-info text-left"><i class="fa fa-pencil"></i> Edit</a> -->
						<a id="brestore" href="#" class="btn btn-warning text-left mb-8px"><i class="fa fa-undo"></i> Restore Report</a>
						<a id="btakedown" href="#" class="btn btn-danger text-left"><i class="fa fa-times"></i> Takedown</a>
						<!-- <button id="bhapus" type="button" class="btn btn-danger text-left"><i class="fa fa-trash-o"></i> Delete</button> -->
						
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