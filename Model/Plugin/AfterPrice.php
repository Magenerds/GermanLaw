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
 * @copyright  Copyright (c) 2019 TechDivision GmbH (https://www.techdivision.com)
 * @link       https://www.techdivision.com/
 * @author     Florian Sydekum <f.sydekum@techdivision.com>
 */
namespace Magenerds\GermanLaw\Model\Plugin;

use Magento\Framework\Pricing\Render;
use Magento\Framework\Pricing\SaleableInterface;

/**
 * Class AfterPrice
 * @package Magenerds\GermanLaw\Model\Plugin
 */
class AfterPrice
{
    /**
     * Hold final price code
     *
     * @var string
     */
    const FINAL_PRICE = 'final_price';

    /**
     * Hold tier price code
     *
     * @var string
     */
    const TIER_PRICE = 'tier_price';

    /**
     * Hold layout
     *
     * @var \Magento\Framework\View\LayoutInterface
     */
    protected $_layout;

    /**
     * Hold after price html string
     *
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
     * @oaram \Closure $closure
     * @param array $params
     * @return string
     */
    public function aroundRender(Render $subject, \Closure $closure, ...$params)
    {
        // run default render first
        $renderHtml = $closure(...$params);

        try{
            // Get Price Code and Product
            list($priceCode, $productInterceptor) = $params;
            $emptyTierPrices = empty($productInterceptor->getTierPrice());

            // If it is final price block and no tier prices exist set additional render
            // If it is tier price block and tier prices exist set additional render
            if ((static::FINAL_PRICE === $priceCode && $emptyTierPrices) || (static::TIER_PRICE === $priceCode && !$emptyTierPrices)) {
                $renderHtml .= $this->_getAfterPriceHtml();
            }
        } catch (\Exception $ex) {
            // if an error occurs, just render the default since it is preallocated
            return $renderHtml;
        }

        return $renderHtml;
    }

    /**
     * Renders and caches the after price html
     *
     * @return null|string
     */
    protected function _getAfterPriceHtml()
    {
        if (null === $this->_afterPriceHtml) {
            $afterPriceBlock = $this->_layout->createBlock('Magenerds\GermanLaw\Block\AfterPrice', 'after_price');
            $afterPriceBlock->setTemplate('Magenerds_GermanLaw::price/after.phtml');
            $this->_afterPriceHtml = $afterPriceBlock->toHtml();
        }

        return $this->_afterPriceHtml;
    }
}
