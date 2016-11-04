<?php
/**
 * Magenerds\GermanLaw\Model\Plugin\AfterPrice
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
        return $renderHtml . $this->_getAfterPriceHtml();
    }

    /**
     * Renders and caches the after price html
     *
     * @return null|string
     */
    protected function _getAfterPriceHtml()
    {
        if (!$this->_afterPriceHtml) {
            $afterPriceBlock = $this->_layout->createBlock('Magenerds\GermanLaw\Block\AfterPrice', 'after_price');
            $afterPriceBlock->setTemplate('Magenerds_GermanLaw::price/after.phtml');
            $this->_afterPriceHtml = $afterPriceBlock->toHtml();
        }

        return $this->_afterPriceHtml;
    }
}
