<?php
session_start();
error_reporting(E_ALL);
require_once("../config.php");
require '../shopify.php';
header('Content-Type: application/javascript');
?>

 !function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?
 n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;
 n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;
 t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,
 document,'script','//connect.facebook.net/en_US/fbevents.js');
<?php 
    $query = mysqli_query($conn, "SELECT * FROM tbl_facebook_pixel_accounts WHERE store_id = ".$_GET['storeid']."");
    //$query = mysqli_query($conn, "SELECT * FROM tbl_facebook_pixel_accounts");
    while($row = mysqli_fetch_array($query))
    {
?>
        fbq('init', <?php echo $row['facebook_pixel_id'] ?>);    
<?php
    }
?>
    fbq('track', "PageView");
    var app_url = "https://pixiflexapp.com";
    var storeid = <?php echo $_GET['storeid']; ?>;
    var thankurl = document.URL;
    var lastSegment = thankurl.split('/').pop();

    if(lastSegment != "thank_you") {
    var product_id = $('[name=id]').val();
    var url = document.URL;
    var matches = url.match(/:\/\/(?:www\.)?(.[^/]+)(.*)/);
    if(matches[2] == "/cart") {
    $.ajaxSetup({
        complete: function(xhr, textStatus) {
            var data = JSON.parse(xhr.responseText);
           
            if(data.id) {
            debugger;
                fbq('track', 'AddToCart', {
                    content_ids: [''+data.id+''],
                    content_type: 'product',
                    value: data.price,
                    currency: ''
                });
            }
        },
        success: function(result) {
            
        },
        error: function(response) {
            
        }
    });
    }
    
    if(product_id == "" || typeof product_id === 'undefined') {
    
    }
    else {
    data = "productid="+product_id+"&storeid="+storeid;
        $.ajax({
            type: 'POST',
            data: data,
            dataType: 'json',
            url: app_url+"/webhook/capture.php?mode=1",
            success:function(data) {
                fbq('track', 'ViewContent', {
                    content_ids: [''+data.id+''],
                    content_type: 'product',
                    value: data.price,
                    currency: ''
                });
            },
            error:function(data) {
                console.log(data);
            }
        });
    }
    
   
    if(matches[2] == "/cart") {
    var hrefs = $('form[action="/cart"]').find('a[href]');
    
    var alength = hrefs.length;
    var variantid = "";
    var vnid = "";
    $.each(hrefs, function(key, value){
        variantid = urlParam('variant', value.href);
        if(variantid == "" || typeof variantid === undefined) {

        }
        else if(vnid == variantid || typeof vnid === undefined){
            
        }
        else {
            vnid = variantid;
            data = "variantid="+vnid+"&storeid="+storeid;
            $.ajax({
                type: 'POST',
                data: data,
                dataType: 'json',
                url: app_url+"/webhook/capture.php?mode=2",
                success:function(data) {
                    fbq('track', 'AddToCart', {
                        content_ids: [''+data.id+''],
                        content_type: 'product',
                        value: data.price,
                        currency: ''
                    });
                },
                error:function(data) {
                    console.log(data);
                }
            });
        }
    })
        
    

        
    }

    function urlParam(name, url) {
        if (!url) {
         url = window.location.href;

        }
        var results = new RegExp('[\\?&]' + name + '=([^&#]*)').exec(url);
        if (!results) { 
            return "";
        }
        return results[1] || "";
    }

    data = "storeid="+storeid;
    if(matches[2] == "/account/register") {
        //customer create to pixel
        $.ajax({
            type: 'POST',
            data: data,
            dataType: 'json',
            url: app_url+"/webhook/capture.php?mode=3",
            success:function(data) {
                if(data != 0) {
                    fbq('track', "CompleteRegistration");
                }
            },
            error:function(data) {
                console.log(data);
            }
        });
        }

        $('[name=checkout]').click(function(e) {
            debugger;
            fbq('track', "InitiateCheckout");
        });
    }
    else if(lastSegment == "thank_you") {
        data = "storeid="+storeid;
        //order create to pixel

        xhr = new XMLHttpRequest();
        url = app_url+"/webhook/capture.php?mode=4";

        xhr.open('POST', url);
        xhr.responseType = 'json';
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onload = function(req, url) {
            if (req.currentTarget.status === 200) {
                //alert('Something went wrong.  Name is now ' + xhr.responseText);
                if(req.currentTarget.response != 0) {
                    var res = JSON.parse(req.currentTarget.response);
                    fbq('track', 'Purchase', {
                        content_ids: [''+res.id+''],
                        content_type: 'product',
                        value: ''+res.total_price+'',
                        currency: ''+res.currency+''
                    });
                }
            }
            else if (xhr.status !== 200) {
                console.log('Request failed.  Returned status of ' + xhr.status);
            }
        };
        xhr.send(encodeURI('storeid=' + storeid));

    }

    
    
    
    
    
    
<!-- End Facebook Pixel Code -->