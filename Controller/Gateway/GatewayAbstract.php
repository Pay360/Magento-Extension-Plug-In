<?php
/**
 * Magento 2 Payment module from Pay360
 * Copyright (C) 2022  Pay360 by Capita
 *
 * This file is part of Pay360/Payments.
 *
 * Pay360/Payments is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

namespace Pay360\Payments\Controller\Gateway;

use Magento\Framework\Controller\ResultFactory;
use Pay360\Payments\Api\Data\SessionInterface as DataSessionInterface;

class GatewayAbstract extends \Magento\Framework\App\Action\Action
{

    protected $_resultPageFactory;

    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $_resultJsonFactory;

    protected $_resultRedirect;
    protected $_customerSession;
    protected $_nvp;
    protected $_pay360Helper;
    protected $_logger;
    protected $_sessionRepoistory;
    /**
     * @var DataSessionInterface
     */
    protected $_sessionData;
    protected $_transactionRepository;
    protected $_pay360Model;

    /**
     * @var \Magento\Framework\Json\EncoderInterface
     */
    protected $_jsonEncoder;

    /**
     * @var \Magento\Framework\Json\DecoderInterface
     */
    protected $_jsonDecoder;

    protected $_rawBody;

    /**
     * Constructor
     *
     * @param \Magento\Framework\App\Action\Context  $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Customer\Model\Session $customerSession,
        \Pay360\Payments\Model\Api\Nvp $nvp,
        \Pay360\Payments\Helper\Data $pay360Helper,
        \Pay360\Payments\Helper\Logger $pay360Logger,
        \Pay360\Payments\Model\Standard $pay360Model,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \Magento\Framework\Json\DecoderInterface $jsonDecoder,
        \Pay360\Payments\Api\SessionRepositoryInterface $sessionRepository,
        DataSessionInterface $sessionData,
        \Pay360\Payments\Api\TransactionRepositoryInterface $transactionRepository
    ) {
        parent::__construct($context);
        $this->_resultPageFactory = $resultPageFactory;
        $this->_resultJsonFactory = $resultJsonFactory;
        $this->_resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $this->_customerSession = $customerSession;
        $this->_nvp = $nvp;
        $this->_pay360Helper = $pay360Helper;
        $this->_logger = $pay360Logger;
        $this->_pay360Model = $pay360Model;
        $this->_jsonEncoder = $jsonEncoder;
        $this->_jsonDecoder = $jsonDecoder;
        $this->_sessionRepoistory = $sessionRepository;
        $this->_sessionData = $sessionData;
        $this->_transactionRepository = $transactionRepository;
    }

    /**
     * Return the raw body of the request, if present
     *
     * @return string|false Raw body, or false if not present
     */
    public function getRawBody()
    {
        if (null === $this->_rawBody) {
            $this->_rawBody = $this->getRequest()->getContent();
            $this->_rawBody = empty($this->_rawBody) ? '{}' : $this->_rawBody;
        }
        return $this->_rawBody;
    }

    /**
     * retrieve last order's incrementId
     *
     * @return string
     */
    public function getOrderId()
    {
        return $this->_pay360Helper->getOrderId();
    }

    /**
     * child classes handled this function
     */
    public function execute()
    {
    }
}
