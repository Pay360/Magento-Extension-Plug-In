<?php
namespace Pay360\Payments\Block\System\Config;

use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Filesystem\Directory\ReadFactory;
use Magento\Framework\Component\ComponentRegistrarInterface;
use Magento\Framework\Component\ComponentRegistrar;

/**
 * Class Instructions
 */
class Version extends Field
{
    /**
     * Module name
     */
    const MODULE_NAME = 'Pay360_Payments';

    /**
     * @var string
     */
    protected $_template = 'Pay360_Payments::system/config/version.phtml';

    /**
     * @var ReadFactory
     */
    protected $readFactory;

    /**
     * @var ComponentRegistrarInterface
     */
    protected $componentRegistrar;

    /**
     * @var Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * VersionCompare constructor.
     * @param Context $context
     * @param ReadFactory $readFactory
     * @param ComponentRegistrarInterface $componentRegistrar
     * @param Magento\Store\Model\StoreManagerInterface storeManager
     */
    public function __construct(
        Context $context,
        ReadFactory $readFactory,
        ComponentRegistrarInterface $componentRegistrar,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    )
    {
        $this->readFactory = $readFactory;
        $this->componentRegistrar = $componentRegistrar;
        $this->_storeManager = $storeManager;

        parent::__construct($context);
    }

    /**
     * @return bool|mixed
     */
    public function getCurrentVersion()
    {
        if ($version = $this->getCurrentComposerVersion()) {
            return $version;
        }

        return false;
    }

    /**
     * @return mixed
     */
    protected function getCurrentComposerVersion()
    {
        try {
            $path = $this->componentRegistrar->getPath(
                ComponentRegistrar::MODULE,
                self::MODULE_NAME
            );

            $dirReader = $this->readFactory->create($path);
            $composerJsonData = $dirReader->readFile('composer.json');
            $data = json_decode($composerJsonData, true);

            return $data['version'] ?? false;

        } catch (\Exception $e) {
            // do nothing
        }
    }

    /**
     * @param AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(AbstractElement $element)
    {
        return $this->_toHtml();
    }
}
