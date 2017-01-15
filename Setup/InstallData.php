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
 * @subpackage Setup
 * @copyright  Copyright (c) 2016 TechDivision GmbH (http://www.techdivision.com)
 * @link       http://www.techdivision.com/
 * @author     Florian Sydekum <f.sydekum@techdivision.com>
 */
namespace Magenerds\GermanLaw\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\CheckoutAgreements\Api\Data\AgreementInterface;

/**
 * Class InstallData
 * @package Magenerds\GermanLaw\Setup
 */
class InstallData implements InstallDataInterface
{
    /**
     * @var \Magento\Framework\App\Config\ConfigResource\ConfigInterface
     */
    protected $_configInterface;

    /**
     * @var \Magento\Framework\Setup\SampleData\Executor
     */
    protected $_executor;

    /**
     * @var \Magenerds\GermanLaw\Setup\CmsInstaller
     */
    protected $_cmsInstaller;

    /**
     * @var \Magenerds\GermanLaw\Setup\TaxInstaller
     */
    protected $_taxInstaller;

    /**
     * Constructor.
     *
     * @param \Magento\Framework\App\Config\ConfigResource\ConfigInterface $configInterface
     * @param \Magento\Framework\Setup\SampleData\Executor $executor
     * @param \Magenerds\GermanLaw\Setup\CmsInstaller $cmsInstaller
     * @param \Magenerds\GermanLaw\Setup\TaxInstaller $taxInstaller
     */
    public function __construct(
        \Magento\Framework\App\Config\ConfigResource\ConfigInterface $configInterface,
        \Magento\Framework\Setup\SampleData\Executor $executor,
        \Magenerds\GermanLaw\Setup\CmsInstaller $cmsInstaller,
        \Magenerds\GermanLaw\Setup\TaxInstaller $taxInstaller
    ){
        $this->_configInterface = $configInterface;
        $this->_executor = $executor;
        $this->_cmsInstaller = $cmsInstaller;
        $this->_taxInstaller = $taxInstaller;
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        $this->_configInterface->saveConfig('general/country/default', 'DE', ScopeConfigInterface::SCOPE_TYPE_DEFAULT, 0);
        $this->_configInterface->saveConfig('general_locale_code', 'de_DE', ScopeConfigInterface::SCOPE_TYPE_DEFAULT, 0);
        $this->_configInterface->saveConfig('general/region/state_required', 'CA,EE,FI,FR,LV,LT,RO,ES,US', ScopeConfigInterface::SCOPE_TYPE_DEFAULT, 0);
        $this->_configInterface->saveConfig('general/locale/timezone', 'Europe/Berlin', ScopeConfigInterface::SCOPE_TYPE_DEFAULT, 0);
        $this->_configInterface->saveConfig('general/locale/weight_unit', 'kgs', ScopeConfigInterface::SCOPE_TYPE_DEFAULT, 0);
        $this->_configInterface->saveConfig('general/locale/firstday', '1', ScopeConfigInterface::SCOPE_TYPE_DEFAULT, 0);
        $this->_configInterface->saveConfig('currency/options/base', 'EUR', ScopeConfigInterface::SCOPE_TYPE_DEFAULT, 0);
        $this->_configInterface->saveConfig('currency/options/default', 'EUR', ScopeConfigInterface::SCOPE_TYPE_DEFAULT, 0);
        $this->_configInterface->saveConfig('currency/options/allow', 'EUR', ScopeConfigInterface::SCOPE_TYPE_DEFAULT, 0);
        $this->_configInterface->saveConfig('sendfriend/email/enabled', 0, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, 0);
        $this->_configInterface->saveConfig('newsletter/subscription/confirm', 1, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, 0);
        $this->_configInterface->saveConfig('customer/create_account/confirm', 1, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, 0);
        $this->_configInterface->saveConfig('customer/address/prefix_options', 'Herr;Frau;Firma', ScopeConfigInterface::SCOPE_TYPE_DEFAULT, 0);
        $this->_configInterface->saveConfig('customer/address/middlename_show', 0, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, 0);
        $this->_configInterface->saveConfig('tax/calculation/price_includes_tax', 1, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, 0);
        $this->_configInterface->saveConfig('tax/calculation/discount_tax', 1, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, 0);
        $this->_configInterface->saveConfig('tax/defaults/country', 'DE', ScopeConfigInterface::SCOPE_TYPE_DEFAULT, 0);
        $this->_configInterface->saveConfig('tax/display/type', 2, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, 0);
        $this->_configInterface->saveConfig('tax/display/shipping', 2, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, 0);
        $this->_configInterface->saveConfig('tax/cart_display/price', 2, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, 0);
        $this->_configInterface->saveConfig('tax/cart_display/subtotal', 2, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, 0);
        $this->_configInterface->saveConfig('tax/cart_display/shipping', 2, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, 0);
        $this->_configInterface->saveConfig('tax/sales_display/price', 2, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, 0);
        $this->_configInterface->saveConfig('tax/sales_display/subtotal', 2, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, 0);
        $this->_configInterface->saveConfig('tax/sales_display/shipping', 2, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, 0);
        $this->_configInterface->saveConfig('checkout/options/enable_agreements', 1, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, 0);
        $this->_configInterface->saveConfig('shipping/origin/country_id', 'DE', ScopeConfigInterface::SCOPE_TYPE_DEFAULT, 0);
        $this->_configInterface->saveConfig('shipping/origin/postcode', '', ScopeConfigInterface::SCOPE_TYPE_DEFAULT, 0);
        $this->_configInterface->saveConfig('germanlaw/price/shipping_page', 'versandkosten', ScopeConfigInterface::SCOPE_TYPE_DEFAULT, 0);

        $this->_executor->exec($this->_cmsInstaller);
        $this->_executor->exec($this->_taxInstaller);

        $setup->endSetup();
    }
}
