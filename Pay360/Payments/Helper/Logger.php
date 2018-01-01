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
    const MAGE_CODE = 'app/code/';
    const LOG_FILE = 'var/log/pay360debug.log';
    const DEBUG_CONFIG = 'payment/pay360/test';

    protected $_enabled = false; 
    protected $_config;
    protected $_psrLogger;

    function __construct(
        \Psr\Log\LoggerInterface $psrLogger,
        \Pay360\Payments\Model\Config $config
    )
    {
        $this->_config = $config;
        $this->_psrLogger = $psrLogger;
        $this->_psrLogger->pushHandler(new \Monolog\Handler\StreamHandler(self::LOG_FILE));
        $this->_enabled = $this->_config->getValue(self::DEBUG_CONFIG);
    }

    /**
     * write log content to custom log file
     * $content array
     */
    public function write($content)
    {
        if ($this->_enabled) {
            try {
                // debug fired function
                $last_step = debug_backtrace()[0];
                $file = $last_step['file'];
                $explode = explode(self::MAGE_CODE, $file);
                if (count($explode) > 1) {
                    list($pwd, $file) = $explode;
                }
                else {
                    $file = $explode[0];
                }
                $line = $last_step['line'];
                $this->_psrLogger->debug("Called From ".self::MAGE_CODE."{$file}:{$line}");

                // debug $content
                if (is_array($content)) {
                    $this->_psrLogger->debug("Array : ". serialize($content));
                }
                else {
                    $content = strval($content);
                    $this->_psrLogger->debug("String : {$content}");
                }
            }
            catch (\Exception $e) {
                $this->_psrLogger->debug("Error : ".$e->getMessage());
            }
        }
    }
}
