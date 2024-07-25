<style>
	/* refer to https://stackoverflow.com/questions/60149994/how-to-add-a-x-to-clear-input-field 
		by Muhammad Sofi 27 December 2021 18:00 | Add x button to clear search box 
	*/
	.dataTables_wrapper .dataTables_filter input::-webkit-search-cancel-button {
		-webkit-appearance: button !important;
		padding: 2px;
		margin-right: 5px;
	}

	table#drTable tr:hover {
		background-color: #EFBF65;
	}

	.swal2-popup {
		font-size: 1.2rem !important;
	}

	.btn-back {
        width: 85px;
        cursor: pointer;
        background: transparent;
        border: 1px solid #999;
        outline: none;
        transition: .5s ease;
    }

    .btn-back.full {
        width: 100%;
    }

    .btn-back:hover {
        transition: .3s ease;
        background: #DD8A0D;
        border: 1px solid transparent;
        color:#FFF;
    }

    .btn-back:hover svg {
        stroke-dashoffset: -480;
    }

    .btn-back span {
        color: white;
        font-size: 18px;
        font-weight: 100;
    }

	#custom_from_date_container, 
	#custom_to_date_container {
    	position: absolute;
		width: 185px;
		height: auto;
		background-color: #fafafa;
		border: 1px solid #d3d6d8;
		border-radius: 5px;  
		padding: 30px;
		margin: 20px;
		z-index: 199;
		display: none;
	}
</style>

<div id="page-content">
	<!-- Static Layout Header -->
	<div class="content-header">
		<div class="row" style="padding: 0.5em 2em;">
			<div class="col-md-6">
				<div class="btn-group">
					<a id="aback" href="<?=base_url_admin(''); ?>" class="btn btn-default btn-back"><i class="fa fa-chevron-left"></i> Back</a>
				</div>
			</div>
		</div>
	</div>
	<ul class="breadcrumb breadcrumb-top">
		<li>Admin</li>
		<li>Misc</li>
		<li>Offer Detail Seller</li>
	</ul>

	<div class="block full">
	<div class="block-title">
			<h2><strong>Offer Detail Seller</strong></h2>
		</div>
		<div class="block-section">
			<div class="row" style="display:flex; align-items:flex-end">
			<input type="hidden" id="reset_year_month" value="<?= $today_year_month; ?>">
				<div class="col-md-2">
					<label for="flcdate_start">From Date</label>
					<div class="input-group">
						<div class="input-group-addon">
							<i class="fa fa-calendar"></i>
						</div>
						<input id="flcdate_start" type="text" class="form-control" placeholder="From date" value="<?=$from_date_detail?>" readonly />
					</div>
				</div>
				<div class="col-md-2">
					<label for="flcdate_end">To Date</label>
					<div class="input-group">
						<div class="input-group-addon">
							<i class="fa fa-calendar"></i>
						</div>
						<input id="flcdate_end" type="text" class="form-control" placeholder="To date" value="<?=$to_date_detail?>" readonly />
					</div>
				</div>
				<div class="col-md-1">
					<button id="reset-filter" class="btn btn-block btn-danger">Reset</button>
				</div>
			</div>
			<div class="row" style="margin-top: 10px;">
				<div class="col-md-2">
					<label for="seller_name_offer">Name </label>
					<div class="input-group">
						<div class="input-group-addon">
							<i class="fa fa-user"></i>
						</div>
						<input id="seller_name_offer" type="text" class="form-control" value="<?=$seller_name_offer->fnama?>" readonly />
					</div>
				</div>
				<div class="col-md-2">
					<label for="seller_type_offer">Type </label>
					<div class="input-group">
						<div class="input-group-addon">
							<i class="fa fa-user"></i>
						</div>
						<input id="seller_type_offer" type="text" class="form-control" placeholder="buyer/seller" value="<?=$toggle_seller?>" readonly />
					</div>
				</div>
				<div class="col-md-2" style="display: none;">
					<label for="seller_type_offer">Seller ID </label>
					<div class="input-group">
						<div class="input-group-addon">
							<i class="fa fa-user"></i>
						</div>
						<input id="id_seller_type_offer" type="text" class="form-control" value="<?=$id_seller?>" readonly />
					</div>
				</div>
			</div>
		</div>
		<div class="table-responsive">
			<table id="drTable" class="table table-vcenter table-condensed table-bordered" width="100%">
				<thead>
					<tr style="background-color: #FFFFFF;">
						<th width="10px">No.</th>
						<th width="10px">Chat Room ID.</th>
						<!-- <th width="20px">b_user_id</th> -->
						<th>Product</th>
						<th>Type</th>
						<th>Price</th>
						<!-- <th>Message</th> -->
						<th>Buyer (email)</th>
						<th>Review Date</th>
					</tr>
				</thead>
				<tbody>
				</tbody>
			</table>
		</div>
	</div>
</div>

<div id="custom_from_date_container">
	<div class="row" style="text-align: right;">
		<select name="" id="year_from_date" class="offer_summary_date" style="width: 70px;">
			<option value="">-year-</option>
			<option value="2011">2011</option>
			<option value="2012">2012</option>
			<option value="2013">2013</option>
			<option value="2014">2014</option>
			<option value="2015">2015</option>
			<option value="2016">2016</option>
			<option value="2017">2017</option>
			<option value="2018">2018</option>
			<option value="2019">2019</option>
			<option value="2020">2020</option>
			<option value="2021">2021</option>
			<option value="2022">2022</option>
			<option value="2023">2023</option>
			<option value="2024">2024</option>
			<option value="2025">2025</option>
			<option value="2026">2026</option>
			<option value="2027">2027</option>
			<option value="2028">2028</option>
			<option value="2029">2029</option>
			<option value="2030">2030</option>
			<option value="2031">2031</option>
			<option value="2032">2032</option>
			<option value="2033">2033</option>
			<option value="2034">2034</option>
			<option value="2035">2035</option>
			<option value="2036">2036</option>
			<option value="2037">2037</option>
			<option value="2038">2038</option>
			<option value="2039">2039</option>
			<option value="2040">2040</option>
		</select>
		<select name="" id="month_from_date" class="offer_summary_date" style="width: 70px;">
			<option value="">-month-</option>
			<option value="01">Jan</option>
			<option value="02">Feb</option>
			<option value="03">Mar</option>
			<option value="04">Apr</option>
			<option value="05">May</option>
			<option value="06">Jun</option>
			<option value="07">Jul</option>
			<option value="08">Aug</option>
			<option value="09">Sep</option>
			<option value="10">Oct</option>
			<option value="11">Nov</option>
			<option value="12">Des</option>
		</select>
	</div>
	<div class="row" style="margin-top: 25px;">
		<div class="" style="text-align: right;">
			<button type="button" class="btn btn-primary" id="btn_done_from_date">Done</button>
		</div>
	</div>          
</div>

<div id="custom_to_date_container">
	<div class="row" style="text-align: right;">
		<select name="" id="year_to_date" class="offer_summary_date" style="width: 70px;">
			<option value="">-year-</option>
			<option value="2011">2011</option>
			<option value="2012">2012</option>
			<option value="2013">2013</option>
			<option value="2014">2014</option>
			<option value="2015">2015</option>
			<option value="2016">2016</option>
			<option value="2017">2017</option>
			<option value="2018">2018</option>
			<option value="2019">2019</option>
			<option value="2020">2020</option>
			<option value="2021">2021</option>
			<option value="2022">2022</option>
			<option value="2023">2023</option>
			<option value="2024">2024</option>
			<option value="2025">2025</option>
			<option value="2026">2026</option>
			<option value="2027">2027</option>
			<option value="2028">2028</option>
			<option value="2029">2029</option>
			<option value="2030">2030</option>
			<option value="2031">2031</option>
			<option value="2032">2032</option>
			<option value="2033">2033</option>
			<option value="2034">2034</option>
			<option value="2035">2035</option>
			<option value="2036">2036</option>
			<option value="2037">2037</option>
			<option value="2038">2038</option>
			<option value="2039">2039</option>
			<option value="2040">2040</option>
		</select>
		<select name="" id="month_to_date" class="offer_summary_date" style="width: 70px;">
			<option value="">-month-</option>
			<option value="01">Jan</option>
			<option value="02">Feb</option>
			<option value="03">Mar</option>
			<option value="04">Apr</option>
			<option value="05">May</option>
			<option value="06">Jun</option>
			<option value="07">Jul</option>
			<option value="08">Aug</option>
			<option value="09">Sep</option>
			<option value="10">Oct</option>
			<option value="11">Nov</option>
			<option value="12">Des</option>
		</select>
	</div>
	<div class="row" style="margin-top: 25px;">
		<div class="" style="text-align: right;">
			<button type="button" class="btn btn-primary" id="btn_done_to_date">Done</button>
		</div>
	</div>          
</div>