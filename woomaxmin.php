<?php
/*
Plugin Name: WooMaxMin
Plugin URI: http://www.womaxmin.iitechnology.in
Description: An e-commerce add-on for WooCommerce, Add minimum and maximum purchase limit in your WooCommerce shop.
Version: 1.1
Author: Himanshu Bhuyan
*/
require_once( plugin_dir_path(__FILE__) . "admin.php" );

$options = get_option( 'woomaxmin_settings' );
if(isset($options['woomaxmin_maxvalue']) && isset($options['woomaxmin_minvalue']) &&$options['woomaxmin_minvalue'] != '' && $options['woomaxmin_maxvalue'] != '') {
    add_action( 'woocommerce_checkout_process', 'woomaxmin_order_amount' );
    add_action( 'woocommerce_before_cart' , 'woomaxmin_order_amount' );
    function remove_loop_button_pro(){
    	$options = get_option( 'woomaxmin_settings' );
    	if($options['woomaxmin_maxvalue'] != '0') {
	        global $woocommerce;
	        $gettotal = get_user_meta(get_current_user_id(),'woomaxmin_total', true);
	        $sale = get_post_meta( get_the_ID(), '_sale_price', true);
	        if($sale == '' || empty($sale)) {
	            $sale = get_post_meta( get_the_ID(), '_regular_price', true);
	        }
	        $gettotal = $gettotal + $woocommerce->cart->subtotal + $sale;
	        $previustotal = get_user_meta(get_current_user_id(),'woomaxmin_total', true);
	        if($previustotal == '' || empty($previustotal)){
	            (int)$remain = $options['woomaxmin_maxvalue'] - $gettotal;
	            if ($remain < 0 || $remain < $gettotal) {
	                $massage = ', You cross your purchase limit';
	            }elseif($remain == $gettotal) {
	                $massage = ', Now you can\'t buy any more product';
	            }
	            else {
	                $massage = ', You already Added '.get_woocommerce_currency_symbol(get_woocommerce_currency()).$woocommerce->cart->subtotal.' , Now you can buy only '.get_woocommerce_currency_symbol(get_woocommerce_currency()) . $remain.' product';
	            }
	        }else {
	            (int)$remain = $options['woomaxmin_maxvalue'] - get_user_meta(get_current_user_id(),'woomaxmin_total', true);
	            if ($remain < 0 || $remain < $woocommerce->cart->subtotal) {
	                $massage = ', You cross your purchase limit';
	            }elseif($remain == $woocommerce->cart->subtotal) {
	                $massage = ', Now you can\'t buy any more product';
	            }
	            else {
	                $massage = ', You already bought '.get_woocommerce_currency_symbol(get_woocommerce_currency()).get_user_meta(get_current_user_id(),'woomaxmin_total', true).', Now you can buy only '.get_woocommerce_currency_symbol(get_woocommerce_currency()) . $remain.' product';
	            }
	        }
	        if($gettotal > $options['woomaxmin_maxvalue']) {
	            remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 );
	        }
	    }
    }
    if(isset($options['woomaxmin_remove'])) {
    	foreach($options['woomaxmin_remove'] as $selected){        
	       if($selected == '2') {
	            add_action('woocommerce_before_single_product','remove_loop_button_pro');           
	       }
	    }
    }  
    function woomaxmin_order_amount() {
        $options = get_option( 'woomaxmin_settings' );
        global $woocommerce;
        (int) $minimum = $options['woomaxmin_minvalue'];
        (int) $maximum = $options['woomaxmin_maxvalue'];
        $gettotal = get_user_meta(get_current_user_id(),'woomaxmin_total', true);
        $gettotal = $gettotal + $woocommerce->cart->subtotal;
        if($options['woomaxmin_maxvalue'] != '0') {
            if($gettotal > $options['woomaxmin_maxvalue'] || $gettotal < $options['woomaxmin_minvalue']) {                                 
	            if ($gettotal < $options['woomaxmin_minvalue']){
	            	if( is_cart() ) {
	                    wc_clear_notices();
	                    wc_print_notice( 
	                        sprintf( $options['woomaxmin_error'].', Your current order total is %s.' , 
	                            $woocommerce->cart->subtotal
	                        ), 'error' 
	                    );
	                }elseif(is_checkout()) {
	                	wc_clear_notices();
	                    wc_add_notice( 
	                        sprintf( $options['woomaxmin_error'].', Your current order total is %s.' , 
	                            $woocommerce->cart->subtotal
	                        ), 'error' 
	                    );
	                }
	            }else {
	            	if( is_cart() ) {
	                    wc_clear_notices();
	                    wc_print_notice( 
	                        sprintf( $options['woomaxmin_error'].', Your current order total is %s.' , 
	                            $woocommerce->cart->subtotal
	                        ), 'error' 
	                    );

	                }else {
	                    wc_clear_notices();
	                    wc_add_notice( 
	                        sprintf( $options['woomaxmin_error'].', Your current order total is %s.' , 
	                            $woocommerce->cart->subtotal
	                        ), 'error' 
	                    );
	                }      
	            }    
	                             
            }else {
            	if($woocommerce->cart->subtotal > $maximum || $woocommerce->cart->subtotal < $minimum) {             
                    if ($woocommerce->cart->subtotal < $minimum){
		            	if( is_cart() ) {
		                    wc_clear_notices();
		                    wc_print_notice( 
		                        sprintf( $options['woomaxmin_error'].', Your current order total is %s.' , 
		                            $woocommerce->cart->subtotal
		                        ), 'error' 
		                    );
		                }
		                elseif(is_checkout()) {
		                	wc_clear_notices();
		                    wc_add_notice( 
		                        sprintf( $options['woomaxmin_error'].', Your current order total is %s.' , 
		                            $woocommerce->cart->subtotal
		                        ), 'error' 
		                    );
		                }
		            }else {
		            	if( is_cart() ) {
		                    wc_clear_notices();
		                    wc_print_notice( 
		                        sprintf( $options['woomaxmin_error'].', Your current order total is %s.' , 
		                            $woocommerce->cart->subtotal
		                        ), 'error' 
		                    );

		                }else {
		                    wc_clear_notices();
		                    wc_add_notice( 
		                        sprintf( $options['woomaxmin_error'].', Your current order total is %s.' , 
		                            $woocommerce->cart->subtotal
		                        ), 'error' 
		                    );
		                }      
		            }   
                }
            }
        }
        //Check Only minimum value
        elseif ($options['woomaxmin_maxvalue'] == '0') {
        	if ( $woocommerce->cart->subtotal < $minimum ) {                
                if( is_cart() ) {
                    wc_clear_notices();
                    wc_print_notice( 
                        sprintf( $options['woomaxmin_error'].', Your current order total is %s.' , 
                            $woocommerce->cart->subtotal
                        ), 'error' 
                    );

                } else {
                    wc_clear_notices();
                    wc_add_notice( 
                        sprintf( $options['woomaxmin_error'].', Your current order total is %s.' , 
                            $woocommerce->cart->subtotal
                        ), 'error' 
                    );

                }                
            }
        }
    }   
    add_action( 'woocommerce_thankyou', function( $order_id ){
    	$options = get_option( 'woomaxmin_settings' );
    	if($options['woomaxmin_maxvalue'] != '0' || empty($options['woomaxmin_onlycart'])) {
	        $order = new WC_Order( $order_id );
	        $orderTotal = $order->get_total();
	        $user_ID = get_current_user_id();
	        $getorderID = get_user_meta(get_current_user_id(),'woomaxmin_orderId', true);
	        $woomaxminKey = 'woomaxmin_total';
	        $addtotal = get_user_meta(get_current_user_id(),'woomaxmin_total', true);
	        if ( !$order->has_status( 'failed' ) ) {
	            if ($getorderID=='') {
	                update_user_meta($user_ID,'woomaxmin_orderId',$order->id);                
	                if ($addtotal==''){
	                    update_user_meta($user_ID,$woomaxminKey,$orderTotal);
	                }else {
	                    $newtotal = $addtotal + $orderTotal;
	                    update_user_meta($user_ID,$woomaxminKey,$newtotal);
	                }
	                
	            }else {
	                if($getorderID == $order->id) {
	                    //To do 
	                }else {
	                    update_user_meta($user_ID,'woomaxmin_orderId',$order->id);                
	                    if ($addtotal==''){
	                        update_user_meta($user_ID,$woomaxminKey,$orderTotal);
	                    }else {
	                        $newtotal = $addtotal + $orderTotal;
	                        update_user_meta($user_ID,$woomaxminKey,$newtotal);
	                    }
	                }
	            }
	        }
	    }
    });			    
}
?>
