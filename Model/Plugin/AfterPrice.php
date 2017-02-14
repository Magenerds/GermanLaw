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
 * @subpackage Model
 * @copyright  Copyright (c) 2016 TechDivision GmbH (http://www.techdivision.com)
 * @link       http://www.techdivision.com/
 * @author     Florian Sydekum <f.sydekum@techdivision.com>
 */
namespace Magenerds\GermanLaw\Model\Plugin;

/**
 * Class AfterPrice
 * @package Magenerds\GermanLaw\Model\Plugin
 */
class AfterPrice
{
    /**
     * @var \Magento\Framework\View\LayoutInterface
     */
    protected $_layout;

    /**
     * @var null|string
     */
    protected $_afterPriceHtml = null;

    /**
     * Constructor
     *
     * @param \Magento\Framework\View\LayoutInterface $layout
     */
    public function __construct(
        \Magento\Framework\View\LayoutInterface $layout
    ){
        $this->_layout = $layout;
    }

    /**
     * Plugin for price rendering in order to display after price information
     *
     * @param \Magento\Framework\Pricing\Render $subject
     * @param $renderHtml
     * @return string
     */
    public function afterRender(\Magento\Framework\Pricing\Render $subject, $renderHtml)
    {
        // check if html is empty
        if ($renderHtml == '' || str_replace("\n", "", $renderHtml) == '') {
            return $renderHtml;
        }

        return $renderHtml . $this->_getAfterPriceHtml();
    }

    /**
     * Renders and caches the after price html
     *
     * @return null|string
     */
    protected function _getAfterPriceHtml()
    {
        if (is_null($this->_afterPriceHtml)) {
            $afterPriceBlock = $this->_layout->createBlock('Magenerds\GermanLaw\Block\AfterPrice', 'after_price');
            $afterPriceBlock->setTemplate('Magenerds_GermanLaw::price/after.phtml');
            $this->_afterPriceHtml = $afterPriceBlock->toHtml();
        }

        return $this->_afterPriceHtml;
    }
}
