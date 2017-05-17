<?php
	ob_start();
	session_start();
	error_reporting(E_ALL);
	require_once("../config.php");
	require '../shopify.php';
?>
<head>
<script>
!function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?
 n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;
 n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;
 t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,
 document,'script','//connect.facebook.net/en_US/fbevents.js');
</script>
<?php
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

$verified_data = "Verified: ".$verified."<br>".$data;

$myfile = file_put_contents('response.txt', $data.PHP_EOL , FILE_APPEND | LOCK_EX);

if(!empty($data)){
	$cartDetail = json_decode($data, true);

	$item_id = "";
	$price = 0;
foreach($cartDetail['line_items'] as $items) {
	$item_id .= $items['id'].",";
	$price += $items['original_line_price'];
}

	$item_ids = substr($item_id, 0, -1);
    $query = mysqli_query($conn, "SELECT * FROM tbl_facebook_pixel_accounts WHERE store_id = ".$_GET['storeid']."");
    while($row = mysqli_fetch_array($query))
    {

?>
	<script>
        fbq('init', <?php echo $row['facebook_pixel_id'] ?>);
    </script>    
<?php
    }
?>
<script>
    fbq('track', 'AddToCart', {
  		content_ids: ['<?php echo $item_ids; ?>'],
  		content_type: 'product',
  		value: <?php echo $price; ?>,
  		currency: ''
	});
</script>
<?php
}
?>
</head>