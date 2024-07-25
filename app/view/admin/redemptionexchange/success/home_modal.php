<style>
	.text-left {
		text-align: left !important;
	}
	.purple-color-menu{
		background: #bb6bd9;
		color: #ffffff;
		border: 1px solid #ab43d2;
	}
	.purple-color-menu:hover{
		background: #ab43d2;
		color: #ffffff;
	}
	.yellow-color-menu{
		background: #f2c94c;
		color: #ffffff;
		border: 1px solid #eab615;
	}
	.yellow-color-menu:hover{
		background: #eab615;
		color: #ffffff;
	}
	.row .btn-group-vertical > button {
		margin-bottom: 5px;
	}
</style>
<!-- modal notification -->
<div id="modal_notification" class="modal fade " tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-xl">
		<div class="modal-content">

			<!-- Modal Header -->
			<div class="modal-header modal-header-title text-center">
				<h2 class="modal-title"><strong>History Info</strong></h2>
			</div>
			<!-- END Modal Header -->

			<!-- Modal Body -->
			<div class="modal-body">
				<div class="row">				
					<form id="fnotification" action="<?=base_url_admin(); ?>" method="post" enctype="multipart/form-data" class="form-horizontal" onsubmit="return false;">
					<fieldset>
                            <div class="form-group">
                                <div class="col-md-12">
									<center><h4><b>Data Redeem</b></h4></center>
                                    <center>
                                        <table>
                                            <tr>
                                                <td>Name</td>
                                                <td>&nbsp; : &nbsp;</td>
                                                <td id="ivnama"><b></b></td>
                                            </tr>
                                            <tr>
                                                <td>Type</td>
                                                <td>&nbsp; : &nbsp;</td>
                                                <td id="ivtype"><b></b></td>
                                            </tr>
                                            <tr>
                                                <td>Redemption Exchange</td>
                                                <td>&nbsp; : &nbsp;</td>
                                                <td id="ivre_name"><b></b></td>
                                            </tr>
                                            <tr>
                                                <td>Telp</td>
                                                <td>&nbsp; : &nbsp;</td>
                                                <td id="ivtelp"><b></b></td>
                                            </tr>
                                            <tr>
                                                <td>Cost SPT</td>
                                                <td>&nbsp; : &nbsp;</td>
                                                <td id="ivcost_spt"><b></b></td>
                                            </tr>
                                            <tr>
                                                <td>Amount Get</td>
                                                <td>&nbsp; : &nbsp;</td>
                                                <td id="ivamount_get"><b></b></td>
                                            </tr>
                                            <tr>
                                                <td>Request Date</td>
                                                <td>&nbsp; : &nbsp;</td>
                                                <td id="ivcdate"><b></b></td>
                                            </tr>
                                        </table>
                                    </center>
                                </div>								
								<div class="col-md-12" style="margin-top: 1em; ">
								<div class="col-md-12" style="border-top: 1px #afafaf dashed;">&nbsp;</div>
								<center><h4><b>Data User</b></h4></center>
                                    <center>
                                        <table>
                                            <tr>
                                                <td>Name</td>
                                                <td>&nbsp; : &nbsp;</td>
                                                <td id="ivunama"><b></b></td>
                                            </tr>
                                            <tr>
                                                <td>Email</td>
                                                <td>&nbsp; : &nbsp;</td>
                                                <td id="ivuemail"><b></b></td>
                                            </tr>
                                            <tr>
                                                <td>Reg Date</td>
                                                <td>&nbsp; : &nbsp;</td>
                                                <td id="ivureg_date"><b></b></td>
                                            </tr>
                                            <tr>
                                                <td>Telp</td>
                                                <td>&nbsp; : &nbsp;</td>
                                                <td id="ivutelp"><b></b></td>
                                            </tr>
                                            <tr>
                                                <td>Total Recommendation</td>
                                                <td>&nbsp; : &nbsp;</td>
                                                <td id="ivutotal_recruited"><b></b></td>
                                            </tr>
                                            <tr>
                                                <td>Is Influencer</td>
                                                <td>&nbsp; : &nbsp;</td>
                                                <td id="ivuis_influencer"><b></b></td>
                                            </tr>
                                            <tr>
                                                <td>SPT Balance</td>
                                                <td>&nbsp; : &nbsp;</td>
                                                <td id="ivuwallet_balance"><b></b></td>
                                            </tr>
                                            <tr>
                                                <td>IP Address</td>
                                                <td>&nbsp; : &nbsp;</td>
                                                <td id="ivuip_address"><b></b></td>
                                            </tr>
                                            <tr>
                                                <td>Permanent Inactive</td>
                                                <td>&nbsp; : &nbsp;</td>
                                                <td id="ivupermanent_inactive"><b></b></td>
                                            </tr>
                                            <tr>
                                                <td>Recommender</td>
                                                <td>&nbsp; : &nbsp;</td>
                                                <td id="ivurecommender"><b></b></td>
                                            </tr>
                                            <tr>
                                                <td>Device ID</td>
                                                <td>&nbsp; : &nbsp;</td>
                                                <td id="ivudevice_id"><b></b></td>
                                            </tr>
                                            <tr>
                                                <td>Address</td>
                                                <td>&nbsp; : &nbsp;</td>
                                                <td id="ivuaddress"><b></b></td>
                                            </tr>
                                            <tr>
                                                <td>Signup Method</td>
                                                <td>&nbsp; : &nbsp;</td>
                                                <td id="ivusignup_method"><b></b></td>
                                            </tr>
										</table>
									</center>
								</div>
                            </div>
                        </fieldset>
					</form>
				</div>
				<div class="row" style="margin-top: 1em; ">
					<div class="col-md-12" style="border-top: 1px #afafaf dashed;">&nbsp;</div>
					<div class="col-xs-12 btn-group-vertical">
						<button type="button" class="btn btn-default btn-block text-left" data-dismiss="modal"><i class="fa fa-times"></i> Close</button>
					</div>
				</div>
			</div>
			<!-- END Modal Body -->
		</div>
	</div>
</div>







