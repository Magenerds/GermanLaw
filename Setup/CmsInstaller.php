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

use Magento\Framework\Setup;

/**
 * Class CmsInstaller
 * @package Magenerds\GermanLaw\Setup
 */
class CmsInstaller implements Setup\SampleData\InstallerInterface
{
    /**
     * @var \Magento\Framework\Setup\SampleData\FixtureManager
     */
    protected $_fixtureManager;

    /**
     * @var \Magento\Framework\File\Csv
     */
    protected $_csvReader;

    /**
     * @var \Magento\Cms\Model\PageFactory
     */
    protected $_pageFactory;

    /**
     * @var \Magento\Cms\Api\PageRepositoryInterface
     */
    protected $_pageRepository;

    /**
     * @param \Magento\Framework\Setup\SampleData\Context $sampleDataContext
     * @param \Magento\Cms\Model\PageFactory $pageFactory
     * @param \Magento\Cms\Api\PageRepositoryInterface $pageRepository
     */
    public function __construct(
        \Magento\Framework\Setup\SampleData\Context $sampleDataContext,
        \Magento\Cms\Model\PageFactory $pageFactory,
        \Magento\Cms\Api\PageRepositoryInterface $pageRepository
    ) {
        $this->_fixtureManager = $sampleDataContext->getFixtureManager();
        $this->_csvReader = $sampleDataContext->getCsvReader();
        $this->_pageFactory = $pageFactory;
        $this->_pageRepository = $pageRepository;
    }

    /**
     * @param array $fixtures
     * @throws \Exception
     */
    public function install()
    {
        $fixtures = ['Magenerds_GermanLaw::fixtures/pages.csv'];

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
                $row = $data;

                /** @var $page \Magento\Cms\Api\Data\PageInterface */
                $page = $this->_pageFactory->create()
                    ->load($row['identifier'], 'identifier')
                    ->addData($row)
                    ->setStores([\Magento\Store\Model\Store::DEFAULT_STORE_ID]);

                $this->_pageRepository->save($page);
            }
        }
    }
}
