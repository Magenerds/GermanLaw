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
 * @subpackage Test
 * @copyright  Copyright (c) 2016 TechDivision GmbH (http://www.techdivision.com)
 * @link       http://www.techdivision.com/
 * @author     Bernhard Wick <b.wick@techdivision.com>
 */
namespace Magenerds\GermanLaw\Test\Unit\Block;

use Magenerds\GermanLaw\Block\AfterPrice;
use Magento\Catalog\Model\Product;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Registry;
use Magento\Framework\Url;
use Magento\Framework\View\Element\Template\Context;
use Magento\Store\Model\StoreManager;
use Magento\Tax\Api\TaxCalculationInterface;

require_once __DIR__ . DIRECTORY_SEPARATOR . 'UnderscoreFunction.php';

/**
 * Class AfterPriceUnitTest
 * @package Magenerds\GermanLaw\Test
 */
class AfterPriceUnitTest extends \PHPUnit_Framework_TestCase
{

    /**
     * The class instance to test
     *
     * @var \Magenerds\GermanLaw\Block\AfterPrice $_testInstance
     */
    protected $_testInstance;

    /**
     * The store config used within the tempalate context
     *
     * @var \Magento\Framework\View\Element\Template\Context|\PHPUnit_Framework_MockObject_MockObject $_scopeConfig
     */
    protected $_scopeConfig;

    /**
     * The registry used within our test class
     *
     * @var \Magento\Framework\Registry|\PHPUnit_Framework_MockObject_MockObject $_registry
     */
    protected $_registry;

    /**
     * The calculation used within our test class
     *
     * @var \Magento\Tax\Api\TaxCalculationInterface|\PHPUnit_Framework_MockObject_MockObject $_calculation
     */
    protected $_calculation;

    /**
     * Prepares the test environment
     *
     * @return void
     */
    public function setUp()
    {
        $storeManager = $this->getMock(StoreManager::class, ['getStore'], [], '', false);
        $storeManager->expects($this->any())
            ->method('getStore')
            ->will($this->returnValue(null));

        $session = $this->getMock(Session::class, ['getCustomerId'], [], '', false);
        $session->expects($this->any())
            ->method('getCustomerId')
            ->will($this->returnValue(123456));

        $urlBuilder = $this->getMock(Url::class, ['getUrl'], [], '', false);
        $urlBuilder->expects($this->any())
            ->method('getUrl')
            ->will($this->returnValue('https://test-url.de'));

        // build up a mock context to transport the exposed scope config, store manager and session mock
        $this->_scopeConfig = $this->getMock(ScopeConfigInterface::class);
        $context = $this->getMock(Context::class, ['getScopeConfig', 'getStoreManager', 'getSession', 'getUrlBuilder'], [], '', false);
        $context->expects($this->any())
            ->method('getScopeConfig')
            ->will($this->returnValue($this->_scopeConfig));
        $context->expects($this->any())
            ->method('getStoreManager')
            ->will($this->returnValue($storeManager));
        $context->expects($this->any())
            ->method('getSession')
            ->will($this->returnValue($session));
        $context->expects($this->any())
            ->method('getUrlBuilder')
            ->will($this->returnValue($urlBuilder));

        // build a mock registry and expose it over a class property
        $this->_registry = $this->getMock(Registry::class);

        $this->_calculation = $this->getMock(TaxCalculationInterface::class);

        // instantiate the test class
        $this->_testInstance = new AfterPrice(
            $context,
            $this->_registry,
            $this->_calculation,
            $session
        );
    }

    /**
     * Tests if asterisk check works for TRUE
     *
     * @return void
     */
    public function testIsAsteriskTrue()
    {
        $this->_scopeConfig->expects($this->once())
            ->method('getValue')
            ->will($this->returnValue(true));

        $this->assertTrue($this->_testInstance->isAsterisk());
    }

    /**
     * Tests if asterisk check works for FALSE
     *
     * @return void
     */
    public function testIsAsteriskFalse()
    {
        $this->_scopeConfig->expects($this->once())
            ->method('getValue')
            ->will($this->returnValue(false));

        $this->assertNotTrue($this->_testInstance->isAsterisk());
    }

    /**
     * Tests if asterisk check works for FALSE
     *
     * @return void
     */
    public function testIsEnabledTrue()
    {
        $this->_scopeConfig->expects($this->once())
            ->method('getValue')
            ->will($this->returnValue(true));

        $this->assertTrue($this->_testInstance->isEnabled());
    }

    /**
     * Tests if asterisk check works for FALSE
     *
     * @return void
     */
    public function testIsEnabledFalse()
    {
        $this->_scopeConfig->expects($this->once())
            ->method('getValue')
            ->will($this->returnValue(false));

        $this->assertNotTrue($this->_testInstance->isEnabled());
    }

    /**
     * Test if the tax is correctly appended if a product is given
     *
     * @return void
     */
    public function testGetTaxTextWithProduct()
    {
        $mockProduct = $this->getMock(Product::class, array('getTaxClassId'), [], '', false);
        $mockProduct->expects($this->once())
            ->method('getTaxClassId')
            ->will($this->returnValue(1));

        $this->_registry->expects($this->once())
            ->method('registry')
            ->will($this->returnValue($mockProduct));

        $this->_scopeConfig->expects($this->once())
            ->method('getValue')
            ->will($this->returnValue('test tax test with rate: %s'));

        $this->_calculation->expects($this->once())
            ->method('getCalculatedRate')
            ->will($this->returnValue(19.0));

        $this->assertEquals('test tax test with rate: 19%', $this->_testInstance->getTaxText());
    }

    /**
     * Test if the tax rate is ignored if no ticket is given
     *
     * @return void
     */
    public function testGetTaxTextWithoutProduct()
    {
        $this->_registry->expects($this->once())
            ->method('registry')
            ->will($this->returnValue(null));

        $this->_scopeConfig->expects($this->once())
            ->method('getValue')
            ->will($this->returnValue('test tax test with rate: %s'));

        $this->assertEquals('test tax test with rate: ', $this->_testInstance->getTaxText());
    }

    /**
     * Test if a product link is added if the correct placeholder is used
     *
     * @return void
     */
    public function testGetTaxTextWithProductAndShippingPageLink()
    {
        $mockProduct = $this->getMock(Product::class, array('getTaxClassId'), [], '', false);
        $mockProduct->expects($this->once())
            ->method('getTaxClassId')
            ->will($this->returnValue(1));

        $this->_registry->expects($this->once())
            ->method('registry')
            ->will($this->returnValue($mockProduct));

        $this->_scopeConfig->expects($this->exactly(2))
            ->method('getValue')
            ->will($this->returnValue('link to tax test with rate: []'));

        $this->_calculation->expects($this->once())
            ->method('getCalculatedRate')
            ->will($this->returnValue(19.0));

        $this->assertEquals('link to tax test with rate: <a href="https://test-url.de"></a>', $this->_testInstance->getTaxText());
    }

    /**
     * Test if the text is not altered if there are no substituation markers within it
     *
     * @return void
     */
    public function testGetTaxTextWithProductNoSubstitution()
    {
        $taxTestFromConfig = 'test tax test';

        $mockProduct = $this->getMock(Product::class, array('getTaxClassId'), [], '', false);
        $mockProduct->expects($this->once())
            ->method('getTaxClassId')
            ->will($this->returnValue(1));

        $this->_registry->expects($this->once())
            ->method('registry')
            ->will($this->returnValue($mockProduct));

        $this->_scopeConfig->expects($this->once())
            ->method('getValue')
            ->will($this->returnValue($taxTestFromConfig));

        $this->_calculation->expects($this->once())
            ->method('getCalculatedRate')
            ->will($this->returnValue(19.0));

        $this->assertEquals($taxTestFromConfig, $this->_testInstance->getTaxText());
    }
}
