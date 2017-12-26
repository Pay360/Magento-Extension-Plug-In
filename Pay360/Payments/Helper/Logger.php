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
    const MAGE_CODE = '/app/code/';

    protected $_enabled = false; 
    protected $_config;
    protected $_systemlogger;

    function __construct(
        \Magento\Payment\Model\Method\Logger $systemlogger,
        \Pay360\Payments\Model\Config $config
    )
    {
        $this->_config = $config;
        $this->_systemlogger = $systemlogger;
        $this->_enabled = $this->_config->getValue('payment/pay360/test');
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
                $this->_systemlogger->debug("Called From ".self::MAGE_CODE."{$file}:{$line}");
            }
            catch (\Exception $e) {
                $this->_systemlogger->debug(array('error' => $e->getMessage()));
            }

            if (is_array($content)) {
                foreach ($content as $key=>$value) {
                    if ($key && $value) {
                        $this->_systemlogger->debug(array($key => $value));
                    }
                }
            }
            else {
                $content = strval($content);
                $this->_systemlogger->debug(array('content' => $content));
            }
        }
    }
}
