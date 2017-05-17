<?php
$webhookContent = "";

$webhook = fopen('php://input' , 'rb');
while (!feof($webhook)) {
    $webhookContent .= fread($webhook, 4096);
}
fclose($webhook);

//$fp = fopen("response.txt","wb");
//fwrite($fp,$webhookContent);
//fclose($fp);

if(isset($webhookContent)) {
	$sql = mysqli_query($conn, "INSERT INTO `tbl_usersettings` (access_token, store_name) VALUES ('".$webhookContent."', '".$webhookContent."')");
}

//error_log($webhookContent);
?>