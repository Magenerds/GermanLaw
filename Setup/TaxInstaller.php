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
 * @copyright  Copyright (c) 2019 TechDivision GmbH (https://www.techdivision.com)
 * @link       https://www.techdivision.com/
 * @author     Florian Sydekum <f.sydekum@techdivision.com>
 */

namespace Magenerds\GermanLaw\Setup;

use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\File\Csv;
use Magento\Framework\Setup;
use Magento\Framework\Setup\SampleData\Context;
use Magento\Framework\Setup\SampleData\FixtureManager;
use Magento\Tax\Api\Data\TaxClassInterface;
use Magento\Tax\Api\Data\TaxRateInterfaceFactory;
use Magento\Tax\Api\Data\TaxRuleInterfaceFactory;
use Magento\Tax\Api\TaxClassRepositoryInterface;
use Magento\Tax\Api\TaxRateRepositoryInterface;
use Magento\Tax\Api\TaxRuleRepositoryInterface;
use Magento\Tax\Model\Calculation\RateFactory;
use Magento\Tax\Model\ClassModel;
use Magento\Tax\Model\ClassModelFactory;

/**
 * Class TaxInstaller
 *
 * @package Magenerds\GermanLaw\Setup
 */
class TaxInstaller implements Setup\SampleData\InstallerInterface
{
    /**
     * @var TaxRuleRepositoryInterface
     */
    protected $_taxRuleRepository;

    /**
     * @var TaxRuleInterfaceFactory
     */
    protected $_ruleFactory;

    /**
     * @var TaxRateRepositoryInterface
     */
    protected $_taxRateRepository;

    /**
     * @var TaxRateInterfaceFactory
     */
    protected $_rateFactory;

    /**
     * @var RateFactory
     */
    protected $_taxRateFactory;

    /**
     * @var SearchCriteriaBuilder
     */
    protected $_criteriaBuilder;

    /**
     * @var FilterBuilder
     */
    protected $_filterBuilder;

    /**
     * @var FixtureManager
     */
    protected $_fixtureManager;

    /**
     * @var Csv
     */
    protected $_csvReader;

    /**
     * @var ClassModelFactory
     */
    protected $_taxClassFactory;

    /**
     * @var TaxClassRepositoryInterface
     */
    protected $_taxClassRepository;

    /**
     * @param Context $sampleDataContext
     * @param TaxRuleRepositoryInterface $taxRuleRepository
     * @param TaxRuleInterfaceFactory $ruleFactory
     * @param TaxRateRepositoryInterface $taxRateRepository
     * @param TaxRateInterfaceFactory $rateFactory
     * @param RateFactory $taxRateFactory
     * @param SearchCriteriaBuilder $criteriaBuilder
     * @param FilterBuilder $filterBuilder
     * @param TaxClassInterface $taxClass
     * @param TaxClassRepositoryInterface $taxClassRepository
     */
    public function __construct(
        Context $sampleDataContext,
        TaxRuleRepositoryInterface $taxRuleRepository,
        TaxRuleInterfaceFactory $ruleFactory,
        TaxRateRepositoryInterface $taxRateRepository,
        TaxRateInterfaceFactory $rateFactory,
        RateFactory $taxRateFactory,
        SearchCriteriaBuilder $criteriaBuilder,
        FilterBuilder $filterBuilder,
        ClassModelFactory $taxClassFactory,
        TaxClassRepositoryInterface $taxClassRepository
    ) {
        $this->_fixtureManager = $sampleDataContext->getFixtureManager();
        $this->_csvReader = $sampleDataContext->getCsvReader();
        $this->_taxRuleRepository = $taxRuleRepository;
        $this->_ruleFactory = $ruleFactory;
        $this->_taxRateRepository = $taxRateRepository;
        $this->_rateFactory = $rateFactory;
        $this->_taxRateFactory = $taxRateFactory;
        $this->_criteriaBuilder = $criteriaBuilder;
        $this->_filterBuilder = $filterBuilder;
        $this->_taxClassFactory = $taxClassFactory;
        $this->_taxClassRepository = $taxClassRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function install()
    {
        $fixtures = ['Magenerds_GermanLaw::fixtures/tax_rates.csv'];

        foreach ($fixtures as $fileName) {
            $fileName = $this->_fixtureManager->getFixture($fileName);
            if (!file_exists($fileName)) {
                continue;
            }

            $rows = $this->_csvReader->getData($fileName);
            $header = array_shift($rows);

            foreach ($rows as $row) {
                $data = [];
                foreach ($row as $key => $value) {
                    $data[$header[$key]] = $value;
                }
                if ($this->_rateFactory->create()->loadByCode($data['code'])->getId()) {
                    continue;
                }
                $taxRate = $this->_rateFactory->create();
                $taxRate->setCode($data['code'])
                    ->setTaxCountryId($data['tax_country_id'])
                    ->setTaxRegionId($data['tax_region_id'])
                    ->setTaxPostcode($data['tax_postcode'])
                    ->setRate($data['rate']);
                $this->_taxRateRepository->save($taxRate);
            }

            $fixtureFile = 'Magenerds_GermanLaw::fixtures/tax_rules.csv';
            $fixtureFileName = $this->_fixtureManager->getFixture($fixtureFile);
            if (!file_exists($fileName)) {
                continue;
            }

            $rows = $this->_csvReader->getData($fixtureFileName);
            $header = array_shift($rows);

            foreach ($rows as $row) {
                $data = [];
                foreach ($row as $key => $value) {
                    $data[$header[$key]] = $value;
                }
                $filter = $this->_filterBuilder->setField('code')
                    ->setConditionType('=')
                    ->setValue($data['code'])
                    ->create();
                $criteria = $this->_criteriaBuilder->addFilters([$filter])->create();
                $existingRates = $this->_taxRuleRepository->getList($criteria)->getItems();
                if (!empty($existingRates)) {
                    continue;
                }

                $filter = $this->_filterBuilder->setField('class_name')
                    ->setConditionType('=')
                    ->setValue($data['tax_product_class'])
                    ->create();
                $criteria = $this->_criteriaBuilder->addFilters([$filter])->create();
                $existingTaxClasses = $this->_taxClassRepository->getList($criteria)->getItems();

                if (!empty($existingTaxClasses)) {
                    $productClassId = 2;
                    foreach ($existingTaxClasses as $taxClass) {
                        $productClassId = $taxClass->getClassId();
                    }
                } else {
                    $taxClass = $this->_taxClassFactory->create();
                    $taxClass->setClassName($data['tax_product_class'])
                        ->setClassType(ClassModel::TAX_CLASS_TYPE_PRODUCT);
                    $this->_taxClassRepository->save($taxClass);
                    $productClassId = $taxClass->getClassId();
                }

                $taxRate = $this->_taxRateFactory->create()->loadByCode($data['tax_rate']);
                $taxRule = $this->_ruleFactory->create();
                $taxRule->setCode($data['code'])
                    ->setTaxRateIds([$taxRate->getId()])
                    ->setCustomerTaxClassIds([$data['tax_customer_class']])
                    ->setProductTaxClassIds([$productClassId])
                    ->setPriority($data['priority'])
                    ->setCalculateSubtotal($data['calculate_subtotal'])
                    ->setPosition($data['position']);
                $this->_taxRuleRepository->save($taxRule);
            }
        }
    }
}
