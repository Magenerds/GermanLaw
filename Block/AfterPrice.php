<?php
/**
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 */

/**
 * @category   Magenerds
 * @package    Magenerds_GermanLaw
 * @subpackage Block
 * @copyright  Copyright (c) 2016 TechDivision GmbH (http://www.techdivision.com)
 * @link       http://www.techdivision.com/
 * @author     Florian Sydekum <f.sydekum@techdivision.com>
 */
namespace Magenerds\GermanLaw\Block;

/**
 * Class AfterPrice
 * @package Magenerds\GermanLaw\Block
 */
class AfterPrice extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $_registry;

    /**
     * @var \Magento\Tax\Api\TaxCalculationInterface
     */
    protected $_taxCalculation;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_session;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $_urlBuilder;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Tax\Api\TaxCalculationInterface $taxCalculation
     * @param \Magento\Customer\Model\Session $session
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Tax\Api\TaxCalculationInterface $taxCalculation,
        \Magento\Customer\Model\Session $session,
        array $data = []
    ){
        $this->_scopeConfig = $context->getScopeConfig();
        $this->_registry = $registry;
        $this->_taxCalculation = $taxCalculation;
        $this->_session = $session;
        $this->_storeManager = $context->getStoreManager();
        $this->_urlBuilder = $context->getUrlBuilder();
        parent::__construct($context, $data);
    }

    /**
     * Returns the configuration if asterisk is used or not
     *
     * @return mixed
     */
    public function isAsterisk()
    {
        return $this->_scopeConfig->getValue('germanlaw/price/asterisk');
    }

    /**
     * Returns the configuration if module is enabled
     *
     * @return mixed
     */
    public function isEnabled()
    {
        return $this->_scopeConfig->getValue(
            'germanlaw/general/enabled',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Returns the configured tax text
     *
     * @return \Magento\Framework\Phrase|string|void
     */
    public function getTaxText()
    {
        /** @var $product \Magento\Catalog\Model\Product */
        $product = $this->_registry->registry('product');
        $taxText = __($this->_scopeConfig->getValue('germanlaw/price/tax_text', \Magento\Store\Model\ScopeInterface::SCOPE_STORE));

        $taxRate = 0;

        if ($product) {
            // calculate tax rate
            $taxRate = $this->_taxCalculation->getCalculatedRate(
                $product->getTaxClassId(),
                $this->_session->getCustomerId(),
                $this->_storeManager->getStore()
            );
        }

        if ($taxRate <= 0 || !$product) {
            $taxRate = '';
        } else {
            $taxRate .= '%';
        }

        // insert tax rate
        $taxText = sprintf($taxText, $taxRate);

        // insert link to shipping page
        if (strstr($taxText, '[') && strstr($taxText, ']') && $link = $this->_getCmsLink()) {
            $href = '<a href="'. $link . '">';
            $taxText = str_replace('[', $href, $taxText);
            $taxText = str_replace(']', '</a>', $taxText);
        }

        return $taxText;
    }

    /**
     * Returns the link to the configured shipping page
     *
     * @return string
     */
    protected function _getCmsLink()
    {
        return $this->_urlBuilder->getUrl(null, ['_direct' => $this->_scopeConfig->getValue('germanlaw/price/shipping_page')]);
    }
}
