<?php
/**
 * Debuger to see output from Payment gateway
 *
 * @category    Pay360
 * @package     Pay360_Payments
 */
namespace  Pay360\Payments\Helper;

class Logger
{
    const LOG_FILE_NAME = 'pay360_debug.log';
    const MAGE_CODE = '/app/code/';

    protected $_enabled = false; 

    function __construct()
    {
        $this->_enabled = Mage::getStoreConfig('payment/pay360_standard/test');
    }

    /**
     * write log content to custom log file
     * $content array
     */
    public function write($content)
    {
        if ($this->_enabled) {
            /*call origin*/
            try {
                $last_step = debug_backtrace()[0];
                $file = $last_step['file'];
                list($pwd, $file) = explode(self::MAGE_CODE, $file);
                $line = $last_step['line'];
                Mage::log("Called From ".self::MAGE_CODE."{$file}:{$line}", Zend_Log::DEBUG, self::LOG_FILE_NAME);
            }
            catch (Exception $e) {
                Mage::logException($e);
            }

            if (is_array($content)) {
                foreach ($content as $key=>$value) {
                    if ($key && $value) {
                        Mage::log("-- {$key} --", Zend_Log::DEBUG, self::LOG_FILE_NAME);
                        Mage::log($value, Zend_Log::DEBUG, self::LOG_FILE_NAME);
                    }
                }
            }
            else {
                $content = strval($content);
                Mage::log("String : {$content}", Zend_Log::DEBUG, self::LOG_FILE_NAME);
            }
        }
    }
}
