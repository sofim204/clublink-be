<!-- cekstok modal -->
<div id="cekstok_modal" class="modal fade " tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<!-- Modal Header -->
			<div class="modal-header text-center">
				<h2 class="modal-title">Check Stock</h2>
			</div>
			<!-- END Modal Header -->

			<!-- Modal Body -->
			<div class="modal-body">
				<div class="row">
          <div class="col-md-12">
            <div class="table-responsive">
              <table id="cekstok_modal_tabel" class="table table-bordered">
                <thead>
                  <tr>
                    <th>Products</th>
                    <th>Order</th>
                    <th>Stock</th>
                    <th>Result</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td colspan="4">Loading...</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
          <div class="col-md-12">
            <div class="btn btn-group">
              <a id="cekstok_modal_batal_btn" href="#" class="btn btn-warning"><i class="fa fa-times"></i> Cancel</a>
              <a id="cekstok_modal_po_btn" href="#" class="btn btn-info">Make PO <i class="fa fa-bank"></i></a>
              <a id="cekstok_modal_qc_btn" href="#" class="btn btn-info">Continue to QC <i class="fa fa-chevron-right"></i></a>
            </div>
          </div>
        </div>
			</div>
		</div>
	</div>
</div>


<!-- cekstok modal -->
<div id="modal_order_proses_kirim" class="modal fade " tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-md">
		<div class="modal-content">
			<!-- Modal Header -->
			<div class="modal-header text-center">
				<h2 class="modal-title">Shipping and Settlement Purchasing</h2>
			</div>
			<!-- END Modal Header -->

			<!-- Modal Body -->
			<div class="modal-body">
				<div class="row">
					<div class="col-md-12">
						<label for="input_order_noresi">Airwaybill Number</label>
						<input id="input_order_noresi" type="text" value="" class="form-control" />
						<br>
					</div>
					<div class="col-md-12">
						<a id="a_order_proses_kirim" href="#" class="btn btn-success">Complete the order</a>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>



<!-- modal submit rating -->
<div id="modal_rating" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<!-- Modal Header -->
			<div class="modal-header text-center">
				<h2 class="modal-title">Assessment</h2>
			</div>
			<!-- END Modal Header -->

			<!-- Modal Body -->
			<div class="modal-body">
		    <div class="lead">
					<h3>Rating</h3>
	        <div id="stars" class="starrr star-color"></div>
				</div>
				<h3>Satisfaction Level</h3>
				<div class="text-left" id="dbtn_rating">
					<button type="button" class="btn btn-primary btn-pilih btn-rating-teks" name="button" data-teks="Tidak Puas">Not Satisfied</button>
					<button type="button" class="btn btn-primary btn-pilih btn-rating-teks" name="button" data-teks="Puas">Satisfied</button>
					<button type="button" class="btn btn-primary btn-pilih btn-rating-teks" name="button" data-teks="Sangat Puas">very Satisfied</button>
				</div>
				<div class="text-right">
					<button type="button" class="btn btn-sm btn-default" data-dismiss="modal"><i class="fa fa-times" aria-hidden="true"></i> Close</button>
					<a id="arating_submit" href="#" class="btn btn-sm btn-primary"><i class="fa fa-check" aria-hidden="true"></i> Save</a>
				</div>
			</div>
			<!-- END Modal Body -->
		</div>
	</div>
</div>
<!-- End modal submit rating -->
