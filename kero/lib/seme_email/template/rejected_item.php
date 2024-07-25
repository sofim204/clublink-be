<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta name="viewport" content="width=device-width" />
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<title>Rejected Item</title>
		<link rel="stylesheet" type="text/css" href="//cdn.thecloudalert.com/assets/css/email.min.css" />
	</head>

	<body bgcolor="#FFFFFF">
		<table class="head-wrap" bgcolor="#ededed" width="100%" summary="header content">
			<tr>
				<th><h4>Rejected Item</h4></th>
			</tr>
		</table>
		<table class="body-wrap" summary="body content">
			<tr>
				<td>&nbsp;</td>
				<td class="container" bgcolor="#FFFFFF">
					<div class="content">
						<table width="100%">
							<tr>
								<td>
                  <h3>Dear {{site_name}} Administrator,</h3>

                 	 				<!-- by Donny Dennison - 11 Augustus 2020 - 15:06
               						change from invoice_code to order_id -->
									<!-- <p><b>{{c_produk_nama}}</b> {{invoice_code}} has been rejected by the buyer ({{buyer_fnama}}).</p> -->
									<p><b>{{c_produk_nama}}</b> {{order_id}} has been rejected by the buyer ({{buyer_fnama}}).</p>

									<!-- by Donny Dennison - 11 Augustus 2020 - 15:06
               						change from invoice_code to order_id -->
									<p>Please contact him/her to know the complaints.</p>

									<!-- by Donny Dennison - 11 Augustus 2020 - 15:06
               						change from invoice_code to order_id -->
									<!-- <p>Please make a quick decision on a rejected item and change a status here:</p> -->
                  <p>And make a quick decision on a rejected item and change a status here:</p>
                  
									<p><a href="{{admincms_link}}">{{admincms_link}}</a></p>
									<p>&nbsp;</p>
									<p>&nbsp;</p>
									<p>Best wishes</p>
									<p>The {{site_name}} team</p>
								</td>
							</tr>
						</table>
						<p style="font-size: small; color: #ededed; font-style: italic;">Copyright © {{site_name}}, All rights reserved.</p>
					</div>
				</td>
				<td>&nbsp;</td>
			</tr>
		</table>

	</body>
</html>
