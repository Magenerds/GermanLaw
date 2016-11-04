<?php
/**
 * Magenerds\GermanLaw\Block\AfterPrice
 *
 * Copyright (c) 2016 TechDivision GmbH
 * All rights reserved
 *
 * This product includes proprietary software developed at TechDivision GmbH, Germany
 * For more information see http://www.techdivision.com/
 *
 * To obtain a valid license for using this software please contact us at
 * license@techdivision.com
 */

/**
 * @category   Magenerds
 * @package    Magenerds_GermanLaw
 * @subpackage Test
 * @copyright  Copyright (c) 2016 TechDivision GmbH (http://www.techdivision.com)
 * @link       http://www.techdivision.com/
 * @author     Bernhard Wick <b.wick@techdivision.com>
 */
namespace Magenerds\GermanLaw\Block;

/**
 * Class AfterPriceTest
 * @package Magenerds\GermanLaw\Test
 */
class AfterPriceTest extends \PHPUnit_Framework_TestCase
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
        $storeManager = $this->getMock('\Magento\Store\Model\StoreManagerInterface');
        $storeManager->expects($this->any())
            ->method('getStore')
            ->will($this->returnValue(null));

        $session = $this->getMock('\Magento\Customer\Model\Session', [], [], '', false);
        $session->expects($this->any())
            ->method('getCustomerId')
            ->will($this->returnValue(123456));

        $urlBuilder = $this->getMock('\Magento\Framework\UrlInterface', [], [], '', false);
        $urlBuilder->expects($this->any())
            ->method('getUrl')
            ->will($this->returnValue('https://test-url.de'));

        // build up a mock context to transport the exposed scope config, store manager and session mock
        $this->_scopeConfig = $this->getMock('\Magento\Framework\App\Config\ScopeConfigInterface');
        $context = $this->getMock('\Magento\Framework\View\Element\Template\Context', [], [], '', false);
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
        $this->_registry = $this->getMock('\Magento\Framework\Registry');

        $this->_calculation = $this->getMock('\Magento\Tax\Api\TaxCalculationInterface');

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
        $mockProduct = $this->getMock('\Magento\Catalog\Model\Product', array('getTaxClassId'), [], '', false);
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
        $mockProduct = $this->getMock('\Magento\Catalog\Model\Product', array('getTaxClassId'), [], '', false);
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

        $mockProduct = $this->getMock('\Magento\Catalog\Model\Product', array('getTaxClassId'), [], '', false);
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

/**
 * Function to mock the Magento __() function for this namespace
 *
 * @param string $text The text to return
 * @return string
 */
function __($text) {
    return $text;
}
