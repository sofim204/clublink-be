<?php
  
	
	//Merchant's account information
	$merchantID = "JT01";		//Get MerchantID when opening account with 2C2P
	$secretKey = "7jYcp4FxFdf0";	//Get SecretKey from 2C2P PGW Dashboard

	//Request Information 
	/* 
	Process Type:
		I = transaction inquiry
		V = transaction void
		R = transaction Refund
		S = transaction Settlement 
	*/
	$processType = "I";		
	$invoiceNo = "Invoice1401872337";
	$version = "3.4";
	
	//Construct signature string
	$stringToHash = $version . $merchantID . $processType . $invoiceNo ; 
	$hash = strtoupper(hash_hmac('sha1', $stringToHash ,$secretKey, false));	//Compute hash value

	//Construct request message
	$xml = "<PaymentProcessRequest>
			<version>$version</version> 
			<merchantID>$merchantID</merchantID>
			<processType>$processType</processType>
			<invoiceNo>$invoiceNo</invoiceNo> 
			<hashValue>$hash</hashValue>
			</PaymentProcessRequest>";  

	include_once('pkcs7.php');
	
	$pkcs7 = new pkcs7();
	$payload = $pkcs7->encrypt($xml,"./keys/demo2.crt"); //Encrypt payload
	
 	
				
	include_once('HTTP.php');
	
	//Send request to 2C2P PGW and get back response
	$http = new HTTP();
 	$response = $http->post("https://demo2.2c2p.com/2C2PFrontend/PaymentActionV2/PaymentAction.aspx","paymentRequest=".$payload);
	 
	//Decrypt response message and display  
	$response = $pkcs7->decrypt($response,"./keys/demo2.crt","./keys/demo2.pem","2c2p");   
	echo "Response:<br/><textarea style='width:100%;height:80px'>". $response."</textarea>"; 
 
	//Validate response Hash
	$resXml=simplexml_load_string($response); 
	$res_version = $resXml->version;
	$res_respCode = $resXml->respCode;
	$res_processType = $resXml->processType;
	$res_invoiceNo = $resXml->invoiceNo;
	$res_amount = $resXml->amount;
	$res_status = $resXml->status;
	$res_approvalCode = $resXml->approvalCode;
	$res_referenceNo = $resXml->referenceNo;
	$res_transactionDateTime = $resXml->transactionDateTime;
	$res_paidAgent = $resXml->paidAgent;
	$res_paidChannel = $resXml->paidChannel;
	$res_maskedPan = $resXml->maskedPan;
	$res_eci = $resXml->eci;
	$res_paymentScheme = $resXml->paymentScheme;
	$res_processBy = $resXml->processBy;
	$res_refundReferenceNo = $resXml->refundReferenceNo;
	$res_userDefined1 = $resXml->userDefined1;
	$res_userDefined2 = $resXml->userDefined2;
	$res_userDefined3 = $resXml->userDefined3;
	$res_userDefined4 = $resXml->userDefined4;
	$res_userDefined5 = $resXml->userDefined5;
	
	//Compute response hash
	$res_stringToHash = $res_version.$res_respCode.$res_processType.$res_invoiceNo.$res_amount.$res_status.$res_approvalCode.$res_referenceNo.$res_transactionDateTime.$res_paidAgent.$res_paidChannel.$res_maskedPan.$res_eci.$res_paymentScheme.$res_processBy.$res_refundReferenceNo.$res_userDefined1.$res_userDefined2.$res_userDefined3.$res_userDefined4.$res_userDefined5 ; 
	$res_responseHash = strtoupper(hash_hmac('sha1',$res_stringToHash,$secretKey, false));  	//Calculate response Hash Value 
	echo "<br/>hash: ".$res_responseHash."<br/>"; 
	if($resXml->hashValue == strtolower($res_responseHash)){ echo "valid response"; } 
	else{ echo "invalid response"; }
	
?>  