<?php
	//session_start();
	//require_once("config.php");
	//require 'shopify.php';

	
	
	$sc = new ShopifyClient($_SESSION['shop'], $_SESSION['token'], SHOPIFY_API_KEY, SHOPIFY_SECRET);

	$gethooks = $sc->call('GET', '/admin/webhooks.json');

	
	foreach($gethooks as $val) {
		$delete = $sc->call('DELETE', '/admin/webhooks/'.$val['id'].'.json');
		$query = mysqli_query($conn, "DELETE FROM tbl_webhooks WHERE webhook_id = '".$val['id']."'");
	}
	
	$hooks_on = array('carts/create' => SITE_URL.'/webhook/hook_cart_create.php?storeid='.$_SESSION['store_id'],
		'carts/update' => SITE_URL.'/webhook/hook_cart_update.php?storeid='.$_SESSION['store_id'],
		'checkouts/create' => SITE_URL.'/webhook/hook_checkout_create.php?storeid='.$_SESSION['store_id'],
		'checkouts/update' => SITE_URL.'/webhook/hook_checkout_update.php?storeid='.$_SESSION['store_id'],
		'checkouts/delete' => SITE_URL.'/webhook/hook_checkouts_delete.php?storeid='.$_SESSION['store_id'],
		'customers/create' => SITE_URL.'/webhook/hook_customers_create.php?storeid='.$_SESSION['store_id'],
		'customers/update' => SITE_URL.'/webhook/hook_customers_update.php?storeid='.$_SESSION['store_id'],
		'order_transactions/create' => SITE_URL.'/webhook/hook_order_transactions_create.php?storeid='.$_SESSION['store_id'],
		'orders/create' => SITE_URL.'/webhook/orders_create.php?storeid='.$_SESSION['store_id'],
		'orders/updated' => SITE_URL.'/webhook/hook_orders_updated.php?storeid='.$_SESSION['store_id'],
		'orders/delete' => SITE_URL.'/webhook/hook_orders_delete.php?storeid='.$_SESSION['store_id'],
		'orders/paid' => SITE_URL.'/webhook/hook_orders_paid.php?storeid='.$_SESSION['store_id'],
		'app/uninstalled' => SITE_URL.'/webhook/hook_app_uninstall.php?storeid='.$_SESSION['store_id']);

		foreach($hooks_on as $topic => $address) {
			$webhook = $sc->call('POST', '/admin/webhooks.json', array("webhook" => array("topic"=> $topic, "address" => $address, "format" => "json")));
			if(isset($webhook['webhook']['id'])) {
				$hook_id = $webhook['webhook']['id'];
				$hook_url = $webhook['webhook']['address'];
			}
			else {
				$hook_id = $webhook['id'];
				$hook_url = $webhook['address'];
			}

			$query = mysqli_query($conn, "SELECT * FROM tbl_webhooks WHERE webhook_id = '".$hook_id."'");
			if(mysqli_num_rows($query) == 0) {
				$query = mysqli_query($conn, "INSERT INTO tbl_webhooks(store_id, webhook_id, hook_url) VALUES( '".$_SESSION['store_id']."', '".$hook_id."', '".$hook_url."' )");
				//echo $query;
			}
		}
	
?>