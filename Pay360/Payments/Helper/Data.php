<?php

namespace Pay360\Payments\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{

    /**
     * Check order view availability
     */
    protected function _canViewOrder($order)
    {
        $customerId = Mage::getSingleton('customer/session')->getCustomerId();
        $availableStates = Mage::getSingleton('sales/order_config')->getVisibleOnFrontStates();
        if ($order->getId() && $order->getCustomerId() && ($order->getCustomerId() == $customerId)
            && in_array($order->getState(), $availableStates, $strict = true)
            ) {
            return true;
        }
        return false;
    }

    /**
     * Try to load valid order by order_id and register it
     */
    protected function _loadValidOrder($increment_id = null) {
        if (!$increment_id) {
            return false;
        }

        $order = Mage::getModel('sales/order')->loadByIncrementId($increment_id);

        if ($this->_canViewOrder($order)) {
            Mage::register('current_order', $order);
            return true;
        }

        return false;
    }

    /**
     * rebuild cart content if payment failed
     */
    public function reinitCart($last_order_id) {
        if (!$this->_loadValidOrder($last_order_id)) {
            return;
        }

        $order = Mage::registry('current_order');
        $cart = Mage::getSingleton('checkout/cart');
        /* @var $cart Mage_Checkout_Model_Cart */

        $items = $order->getItemsCollection();
        foreach ($items as $item) {
            try {
                $cart->addOrderItem($item);
            } catch (Mage_Core_Exception $e){
                if (Mage::getSingleton('checkout/session')->getUseNotice(true)) {
                    Mage::getSingleton('checkout/session')->addNotice($e->getMessage());
                }
                else {
                    Mage::getSingleton('checkout/session')->addError($e->getMessage());
                }
                return false;
            } catch (Exception $e) {
                Mage::getSingleton('checkout/session')->addException($e,
                    Mage::helper('checkout')->__("Cannot add item '{$item->getName()}' to the shopping cart.")
                );
                return false;
            }
        }

        $cart->save();
    }

    /**
     * get template file for review section of onepage page
     */
    public function reviewhpf() {
        if (Mage::getSingleton('checkout/session')->getQuote()->getPayment()->getMethodInstance()->getCode() == Pay360_Payments_Model_Standard::CODE
            && Mage::getStoreConfig('payment/pay360_standard/payment_type')  == Pay360_Payments_Model_Source_Paymenttype::TYPE_HPF) {
            return 'pay360/review/info.phtml';
        }
        else {
            return 'checkout/onepage/review/info.phtml';
        }
    }
}
