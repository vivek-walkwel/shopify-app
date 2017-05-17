<?php
	header('Access-Control-Allow-Origin: *');
	error_reporting(E_ALL);
	require_once("../config.php");
	require '../shopify.php';

	$query = mysqli_query($conn, "SELECT * FROM tbl_usersettings WHERE id = '".$_POST['storeid']."'");
	
	while($row = mysqli_fetch_array($query)) {
		$shopname= $row['store_name'];
		$token = $row['access_token'];
	}

	$sc = new ShopifyClient($shopname, $token, SHOPIFY_API_KEY, SHOPIFY_SECRET);

	if($_GET['mode'] == 1) {
		$product_detail = $sc->call('GET', '/admin/variants/'.$_POST['productid'].'.json', array());
		echo json_encode($product_detail, true);
	}

	else if($_GET['mode'] == 2) {
		$cart_detail = $sc->call('GET', '/admin/variants/'.$_POST['variantid'].'.json', array());
		echo json_encode($cart_detail, true);
	}

	//customer create data
	else if($_GET['mode'] == 3) {
		$account_res = "";
		$query = mysqli_query($conn, "SELECT * FROM webhook_response WHERE storeid = '".$_POST['storeid']."' AND hook_read = 0 AND hook_type = 'account'") or die(mysqli_error($query));
		if(mysqli_num_rows($query) >= 1) {
			while($row = mysqli_fetch_array($query)) {
				$id = $row['id'];
				$account_res .= $row['hook_response'].",";

				$uquery = mysqli_query($conn, "UPDATE webhook_response SET hook_read = 1 WHERE id = ".$id."");

			}
			$account_res = substr($account_res, 0, -1);
			echo json_encode($account_res, true);
		}
		else {
			echo 0;
		}
	}

	//order create data
	else if($_GET['mode'] == 4) {
		$order_res = "";
		$query = mysqli_query($conn, "SELECT * FROM webhook_response WHERE storeid = '".$_POST['storeid']."' AND hook_read = 0 AND hook_type = 'order'") or die(mysqli_error($query));
		if(mysqli_num_rows($query) >= 1) {
			while($row = mysqli_fetch_array($query)) {
				$id = $row['id'];
				$order_res .= $row['hook_response'].",";

			$uquery = mysqli_query($conn, "UPDATE webhook_response SET hook_read = 1 WHERE id = ".$id."");

			}
			$order_res = substr($order_res, 0, -1);
			echo json_encode($order_res, true);
		}
		else {
			echo 0;
		}
		
	}

?>