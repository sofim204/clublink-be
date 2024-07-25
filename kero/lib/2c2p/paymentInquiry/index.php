<?php
//Import necessary classes
require "../utils/PaymentGatewayHelper.php";
require "../enum/APIEnvironment.php";
require "../model/SignatureError.php";

//{"version":"1.1","respCode":"9042","respDesc":"Signature doesn't match."}
function jsonOut($api_version,$respCode="000",$respDesc="MW Error!",$invoice_no=""){
  $jdt = new stdClass();
  $jdt->version = $api_version;
  $jdt->respCode = $respCode;
  $jdt->respDesc = $respDesc;
  $jdt->invoice_no = $invoice_no;
  header("Content-Type: application/json");
  echo json_encode($jdt);
  die();
}

//Check request URI
if(!strpos($_SERVER['REQUEST_URI'], '/paymentInquiry')) {
  http_response_code(404);
  jsonOut($api_version,666,"Wrong request URI, please make sure it's `/paymentInquiry`","");
}

//Check request method
if($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(404);
    jsonOut($api_version,666,"Not in POST method","");
}

//Set API request enviroment
$api_env = APIEnvironment::SANDBOX . "/paymentInquiry";

//Request information
//Set API request version
$api_version = "1.1";

//Merchant's account information
//Get MerchantID when opening account with 2C2P
$mid = "702702000000350";
//Get SecretKey from 2C2P PGW dashboard
$secret_key = "034B8C25F4EDB0050CBD1970943A81AB85C4DD8E4EE3331747AB9D0043FF54F5";

//Inquiry information
//Note: Can be request with transaction id or invoice no
//Set your transaction id
//$transaction_id = "1345111";
//(Optional) Set your invoice no
//$invoice_no = "1564548988";
$invoice_no = '';
if(isset($_POST['invoice_no'])) $invoice_no = $_POST['invoice_no'];
if(strlen($invoice_no)<=4){
  jsonOut($api_version,666,"Invalid invoice number","");
}

//---------------------------------- Request ---------------------------------------//

//Build payment inquiry request
$payment_inquiry_request = new stdClass();
$payment_inquiry_request->version = $api_version;
$payment_inquiry_request->merchantID = $mid;
//$payment_inquiry_request->transactionID = $transaction_id;
$payment_inquiry_request->invoiceNo = $invoice_no;

//Important: Generate signature
//Init 2C2P PaymentGatewayHelper
$pgw_helper = new PaymentGatewayHelper();

//Generate signature of payload
$hashed_signature = $pgw_helper->generateSignature($payment_inquiry_request, $secret_key);

//Set hashed signature
$payment_inquiry_request->signature = $hashed_signature;

//---------------------------------- Response ---------------------------------------//

//Do Payment Inquiry API request
$encoded_payment_inquiry_response = $pgw_helper->requestAPI($api_env, $payment_inquiry_request);

//Important: Verify response signature
$is_valid_signature = $pgw_helper->validateSignature($encoded_payment_inquiry_response, $secret_key);

$respCode="666";
$respDesc="MW cause unknow error";
if($is_valid_signature) {
  //Parse api response
  $result = $pgw_helper->parseAPIResponse($encoded_payment_inquiry_response);
  //Get payment result
  //$invoice_no = $payment_inquiry_response->invoiceNo;
  //$resp_code = $payment_inquiry_response->respCode;
  if(isset($result->respDesc) && isset($result->respCode)){
    $respCode = $result->respCode;
    $respDesc = $result->respDesc;
  }
  if(isset($result->invoiceNo)) $invoice_no = $result->invoiceNo;
} else {
  //Return encoded error response
  $result = (json_encode(get_object_vars(new SignatureError($api_version))));
  if(isset($result->version)) $api_version = $result->version;
  if(isset($result->respCode)) $respCode = $result->respCode;
  if(isset($result->respDesc)) $respDesc = $result->respDesc;

}
jsonOut($api_version,$respCode,$respDesc,$invoice_no);
