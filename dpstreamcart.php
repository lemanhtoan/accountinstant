<?php
include 'app/Mage.php';
Mage::app();
// Need for start the session
Mage::getSingleton('core/session', array('name' => 'frontend'));
try {
    $product_id = $_GET["id"]; // Replace id with your product id
    $qty = '1'; // Replace qty with your qty
    $product = Mage::getModel('catalog/product')->load($product_id);
    $cart = Mage::getModel('checkout/cart');
    $cart->init();
    $cart->addProduct($product, array('qty' => $qty));
    $cart->save();
    Mage::getSingleton('checkout/session')->setCartWasUpdated(true);
    Mage::getSingleton('core/session')->addSuccess('Product added successfully');
    header('Location: ' . '/onestepcheckout/index/');
} catch (Exception $e) {
    echo $e->getMessage();
}
?>