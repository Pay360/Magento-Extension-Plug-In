<?php
/**
 * Debuger to see output from Payment gateway
 *
 * @category    Pay360
 * @package     Pay360_Payments
 */
namespace  Pay360\Payments\Helper;

use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Pay360\Payments\Model\Config;
use Monolog\Handler\StreamHandler;

class Logger
{
    const LOG_FILE = 'var/log/pay360debug.log';
    const DEBUG_CONFIG = 'payment/pay360/test';

    protected $_enabled = false; 
    protected $_config;
    protected $_psrLogger;

    function __construct(
        LoggerInterface $psrLogger,
        Config $config
    )
    {
        $this->_config = $config;
        $this->_psrLogger = $psrLogger;
        $this->_enabled = $this->_config->getValue(self::DEBUG_CONFIG);
    }

    /**
     * write log content to custom log file
     * $content array
     */
    public function write($content)
    {
        if ($this->_enabled) {
            $this->_psrLogger->pushHandler(new StreamHandler(self::LOG_FILE));
            try {
                // debug fired function
                $last_step = debug_backtrace()[0];
                $file = $last_step['file'];
                $line = $last_step['line'];
                $function = $last_step['function'];
                $this->_psrLogger->log(\Monolog\Logger::DEBUG, "Called From {$file}:{$line}:{$function}");

                // debug $content
                if (is_array($content)) {
                    $this->_psrLogger->log(\Monolog\Logger::DEBUG, json_encode($content));
                }
                else {
                    $content = strval($content);
                    $this->_psrLogger->log(\Monolog\Logger::DEBUG, "String : {$content}");
                }
            }
            catch (\Exception $e) {
                $this->_psrLogger->log(\Monolog\Logger::DEBUG, "Error : ".$e->getMessage());
            }
            $this->_psrLogger->popHandler();
        }
    }
}
