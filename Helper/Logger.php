<?php
/**
 * Debuger to see output from Payment gateway
 *
 * @category    Pay360
 * @package     Pay360_Payments
 */
namespace  Pay360\Payments\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Psr\Log\LoggerInterface;

class Logger extends AbstractHelper
{
    private $expected_error_msgs = array(
        'No such entity with cartId' => 'Quote not available',
        '409 Conflict' => 'Request conflicts with existing record'
    );
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var Data
     */
    private $pay360Helper;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param LoggerInterface $logger
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        Data $pay360Helper,
        LoggerInterface $logger
    ) {
        parent::__construct($context);
        $this->pay360Helper = $pay360Helper;
        $this->logger = $logger;
    }

    /**
     * write log content to custom log file
     * $content array
     */
    public function write($content)
    {
        try {
            $this->logMixed($content);
        } catch (Exception $e) {
            // do nothing
        }
        
    }

    /**
     * custom logger to log exception content to log file
     *
     * @param Exception
     * @return void
     */
    public function logException(\Exception $e)
    {
        if ($this->pay360Helper->isDebugOn()) {

            $original_message = $e->getMessage();
            if ($translated_message = $this->translateErrorMsg($original_message)) {
                $this->logger->error(__('Recognized Error :'), $translated_message);
            }
            else {
                $this->logger->error(
                    $original_message,
                    ['detail' => $e->getTraceAsString()]
                );
            }

            if (method_exists($e, 'getResponseBody')) {
                $this->logger->info(
                    __('Response Body'),
                    ['json' => $e->getResponseBody()]
                );
            }
        }
    }

    /**
     * check to see if error message is expected or not
     *
     * @param string
     * @return boolean
     */
    public function translateErrorMsg($error_message)
    {
        foreach ($this->expected_error_msgs as $msg => $meaning ){
            if (strpos($error_message, $msg) !== false) {
                return $meaning;
            }
        }

        return null;
    }

    /**
     * custom logger to log exception content to log file
     *
     * @param array
     * @return void
     */
    public function logMixed($mixed_data)
    {
        if ($this->pay360Helper->isDebugOn()) {
            $this->logger->info(
                __('Info Array'),
                $mixed_data
            );
        }
    }
}

