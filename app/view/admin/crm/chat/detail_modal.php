<style>
	.form-row{
		display:flex; 
		flex-direction:column; 
		padding-top:1em
	}
	.select2-container{
		width: 100%!important;
	}
	.select2-search--dropdown .select2-search__field {
		width: 98%;
	}
</style>
<div id="__message_attachment" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<!-- Modal Header -->
			<div class="modal-header modal-header-title text-center">
				<a href="javascript:void(0)" data-dismiss="modal" class="pull-right modal-dismiss"><strong><i class="fa fa-times"></i></strong></a>
				<h2 class="modal-title"><strong>Send Message with Attachment</strong></h2>
			</div>
			<!-- END Modal Header -->

			<!-- Modal Body -->
			<div class="modal-body">
				<form 
					id="send_chat_admin" 
					action="<?=base_url_admin(); ?>" 
					method="post" 
					enctype="multipart/form-data" 
					class="form-horizontal 
					form-bordered" 
					onsubmit="return false;"
				>
					<fieldset>
		            	<div class="form-group">
							<div class="col-md-12 form-row">
		                		<label class="" for="product_attachment" > Product Attachment </label>
								<select id="product_attachment" class="form-control mandatory-form" name="product_id">
									<option value="" selected="selected">-Product Name - Price -</option>
								</select>
		              		</div>
							<div class="col-md-12 form-row">
		                		<label class="" for="buyer_invoice" > Invoice Buyer </label>
								<select id="buyer_invoice" class="form-control mandatory-form" name="buyer_invoice">
                    				<option value="" selected="selected">-Invoice - Product name-</option>
								</select>
							</div>
							<div class="col-md-12 form-row">
		                		<label class="" for="seller_invoice" > Invoice Seller </label>
								<select id="seller_invoice" class="form-control mandatory-form" name="seller_invoice">
                    				<option value="" selected="selected">-Invoice - Product name-</option>
								</select>
							</div>
							<div class="col-md-12 form-row">
		                		<label class="" for="image_attachment" > Image </label>
								<input id="image_attachment" type="file" name="jenis" class="form-control mandatory-form" minlength="1" placeholder="" />
							</div>
							<div class="col-md-12 form-row">
								<label class="" for="message_attachment" >Message</label>
								<textarea
									id="message_attachment"
									class="message" 
									name="message" 
									rows="4" 
									required
								></textarea>
							</div>
		            	</div>
					</fieldset>
					<ul style="font-size:11px">
						<li class="" for="note" >Message text is required</li>
						<li class="" for="note" >
							Please only select one of the following option (Product Attachment, Invoice Buyer, Invoice Seller, Image) 
						</li>
					</ul>
					<div class="form-group form-actions">
						<div class="col-xs-12 text-right">
                  			<button id="reset_attach" class="btn btn-sm">Reset Attach</button>
							<button type="submit" class="btn btn-sm">Send</button>
						</div>
					</div>
				</form>
			</div>
			<!-- END Modal Body -->
		</div>
	</div>
</div>