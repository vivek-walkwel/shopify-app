<?php
	ob_start();
	session_start();
	error_reporting(E_ALL);
	require_once("../config.php");
	require '../shopify.php';

$webhookContent = "";

function verify_webhook($data, $hmac_header)
{
  $calculated_hmac = base64_encode(hash_hmac('sha256', $data, SHOPIFY_SECRET, true));
  return ($hmac_header == $calculated_hmac);
}


$hmac_header = $_SERVER['HTTP_X_SHOPIFY_HMAC_SHA256'];
$data = file_get_contents('php://input');
$verified = verify_webhook($data, $hmac_header);
error_log(var_export($verified, true)); 

//while (!feof($webhook)) {
    //$webhookContent .= fread($webhook, 4096);
//}
//fclose($webhook);

//$fp = fopen("response.txt","wb");
//fwrite($fp,$webhookContent);
//fclose($fp);


$myfile = file_put_contents('response.txt', $data.PHP_EOL , FILE_APPEND | LOCK_EX);

if(!empty($data)){
	$hook_type = "uninstall";
    $query = mysqli_query($conn, "INSERT INTO webhook_response(storeid, hook_type, hook_response, hook_read) VALUES('".$_GET['storeid']."', '".$hook_type."', '".$data."', 0)") or die(mysqli_error($query));

    $query2 = mysqli_query($conn, "DELETE FROM tbl_facebook_pixel_accounts WHERE store_id = '".$_GET['storeid']."'") or die(mysqli_error($query2));

    $query3 = mysqli_query($conn, "DELETE FROM tbl_usersettings WHERE id = '".$_GET['storeid']."'") or die(mysqli_error($query3));

    $query4 = mysqli_query($conn, "DELETE FROM tbl_webhooks WHERE store_id = '".$_GET['storeid']."'") or die(mysqli_error($query4));

    $query5 = mysqli_query($conn, "DELETE FROM app_payments WHERE store_id = '".$_GET['storeid']."'") or die(mysqli_error($query5));

	session_unset();
	unset($_SESSION);
	session_destroy();
}
?>
