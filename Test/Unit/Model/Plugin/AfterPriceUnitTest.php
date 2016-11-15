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
namespace Magenerds\GermanLaw\Test\Unit\Model\Plugin;

use Magenerds\GermanLaw\Model\Plugin\AfterPrice;
use Magento\Framework\Pricing\Render;
use Magento\Framework\View\Element\BlockInterface;
use Magento\Framework\View\LayoutInterface;

/**
 * Class AfterPriceUnitTest
 * @package Magenerds\GermanLaw\Test
 */
class AfterPriceUnitTest extends \PHPUnit_Framework_TestCase
{

    /**
     * The class instance to test
     *
     * @var \Magenerds\GermanLaw\Model\Plugin\AfterPrice $_testInstance
     */
    protected $_testInstance;

    /**
     * Prepares the test environment
     *
     * @return void
     */
    public function setUp()
    {
        $block = $this->getMock(BlockInterface::class, ['setTemplate', 'toHtml']);
        $block->expects($this->any())
            ->method('setTemplate')
            ->will($this->returnValue(null));
        $block->expects($this->any())
            ->method('toHtml')
            ->will($this->returnValue('it worked'));

        $layout = $this->getMock(LayoutInterface::class);
        $layout->expects($this->once())
            ->method('createBlock')
            ->will($this->returnValue($block));

        // instantiate the test class
        $this->_testInstance = new AfterPrice(
            $layout
        );
    }

    /**
     * Test a standard call of the afterRender method
     *
     * @return void
     */
    public function testAfterRender()
    {
        $this->assertEquals(
            'it worked',
            $this->_testInstance->afterRender(
            $this->getMock(Render::class, [], [], '', false),
            ''
        ));
    }

    /**
     * Test if the lazy loading of the afterRender method works (hence the "once()" for the mocked createBlock method)
     *
     * @return void
     */
    public function testAfterRenderLazyLoading()
    {
        $this->_testInstance->afterRender(
            $this->getMock(Render::class, [], [], '', false),
            ''
        );
        $this->assertEquals(
            'it worked',
            $this->_testInstance->afterRender(
                $this->getMock(Render::class, [], [], '', false),
                ''
            ));
    }

    /**
     * Test if a given prefix is correctly used within the afterRender method
     *
     * @return void
     */
    public function testAfterRenderWithGivenPrefix()
    {
        $this->assertEquals(
            'I am sure it worked',
            $this->_testInstance->afterRender(
                $this->getMock(Render::class, [], [], '', false),
                'I am sure '
            ));
    }
}
