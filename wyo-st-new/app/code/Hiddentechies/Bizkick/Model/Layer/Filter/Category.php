<?php

 namespace Hiddentechies\Bizkick\Model\Layer\Filter;

  use Magento\Catalog\Model\Layer\Filter\AbstractFilter;
  use Magento\Catalog\Model\Layer\Filter\DataProvider\Category as CategoryDataProvider;

class Category extends AbstractFilter
{
	/**
	 * @var \Magento\Framework\Escaper
	 */
	private $escaper;

	/**
	 * @var CategoryDataProvider
	 */
	private $dataProvider;
	protected $_logger;
	/**
	 * @param \Magento\Catalog\Model\Layer\Filter\ItemFactory $filterItemFactory
	 * @param \Magento\Store\Model\StoreManagerInterface $storeManager
	 * @param \Magento\Catalog\Model\Layer $layer
	 * @param \Magento\Catalog\Model\Layer\Filter\Item\DataBuilder $itemDataBuilder
	 * @param \Magento\Catalog\Model\CategoryFactory $categoryFactory
	 * @param \Magento\Framework\Escaper $escaper
	 * @param CategoryManagerFactory $categoryManager
	 * @param array $data
	 */
	public function __construct(  
		\Magento\Catalog\Model\Layer\Filter\ItemFactory $filterItemFactory,
		\Magento\Store\Model\StoreManagerInterface $storeManager,
		\Magento\Catalog\Model\Layer $layer,
		\Magento\Catalog\Model\Layer\Filter\Item\DataBuilder $itemDataBuilder,
		\Magento\Framework\Escaper $escaper,
		\Magento\Catalog\Model\Layer\Filter\DataProvider\CategoryFactory $categoryDataProviderFactory,
		\Psr\Log\LoggerInterface $logger, //log injection

		array $data = []
	) {
		parent::__construct(
			$filterItemFactory,
			$storeManager,
			$layer,
			$itemDataBuilder,
			$data
		);
		$this->escaper = $escaper;
		$this->_requestVar = 'cat';
		$this->dataProvider = $categoryDataProviderFactory->create(['layer' => $this->getLayer()]);
		$this->_logger = $logger;
	}

	/**
	 * Apply category filter to product collection
	 *
	 * @param   \Magento\Framework\App\RequestInterface $request
	 * @return  $this
	 */
	public function apply(\Magento\Framework\App\RequestInterface $request)
	{
		$categoryId = $request->getParam($this->_requestVar) ?: $request->getParam('id');
		if (empty($categoryId)) {
			return $this;
		}

		$this->dataProvider->setCategoryId($categoryId);

		$category = $this->dataProvider->getCategory();

		$this->getLayer()->getProductCollection()->addCategoryFilter($category);

		if ($request->getParam('id') != $category->getId() && $this->dataProvider->isValid()) {
			$this->getLayer()->getState()->addFilter($this->_createItem($category->getName(), $categoryId));
		}
		return $this;
	}

	/**
	 * Get filter value for reset current filter state
	 *
	 * @return mixed|null
	 */
	public function getResetValue()
	{
		return $this->dataProvider->getResetValue();
	}

	/**
	 * Get filter name
	 *
	 * @return \Magento\Framework\Phrase
	 */
	public function getName()
	{
		return __('Category');
	}

	/**
	 * Get data array for building category filter items
	 *
	 * @return array
	 */
	protected function _getItemsData()
	{
		$_objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$productCollection = $this->getLayer()->getProductCollection();

		$category = $this->dataProvider->getCategory();

		$optionsFacetedData = $productCollection->getFacetedData('category');
		$categories = $category->getChildrenCategories();
		$collectionSize = $productCollection->getSize();

		if ($category->getIsActive()) {
			
			foreach ($categories as $category) {
				
				if ($category->getIsActive() && isset($optionsFacetedData[$category->getId()]) && $this->isOptionReducesResults($optionsFacetedData[$category->getId()]['count'], $collectionSize)) {
					
					$this->itemDataBuilder->addItemData(
						$this->escaper->escapeHtml($category->getName().'pid'.$category->getParentId()),
						$category->getId(),
						$optionsFacetedData[$category->getId()]['count']
					);
				}
				$cat = $_objectManager->create('Magento\Catalog\Model\Category')->load($category->getId());
				$sub_category = $cat->getChildrenCategories();

				foreach ($sub_category as $category) {
					if ($category->getIsActive() && isset($optionsFacetedData[$category->getId()]) && $this->isOptionReducesResults($optionsFacetedData[$category->getId()]['count'], $collectionSize)) {
						$this->itemDataBuilder->addItemData(
							$this->escaper->escapeHtml($category->getName().'pid'.$category->getParentId()),
							$category->getId(),
							$optionsFacetedData[$category->getId()]['count']
						);
					}
					$cat = $_objectManager->create('Magento\Catalog\Model\Category')->load($category->getId());
					$sub_category = $cat->getChildrenCategories();

					foreach ($sub_category as $category) {
						if ($category->getIsActive() && isset($optionsFacetedData[$category->getId()]) && $this->isOptionReducesResults($optionsFacetedData[$category->getId()]['count'], $collectionSize)) {
							$this->itemDataBuilder->addItemData(
								$this->escaper->escapeHtml($category->getName().'pid'.$category->getParentId()),
								$category->getId(),
								$optionsFacetedData[$category->getId()]['count']
							);
						}

						$cat = $_objectManager->create('Magento\Catalog\Model\Category')->load($category->getId());
						$sub_category = $cat->getChildrenCategories();

						foreach ($sub_category as $category) {
							if ($category->getIsActive() && isset($optionsFacetedData[$category->getId()]) && $this->isOptionReducesResults($optionsFacetedData[$category->getId()]['count'], $collectionSize)) {
								$this->itemDataBuilder->addItemData(
									$this->escaper->escapeHtml($category->getName().'pid'.$category->getParentId()),
									$category->getId(),
									$optionsFacetedData[$category->getId()]['count']
								);
							}

							$cat = $_objectManager->create('Magento\Catalog\Model\Category')->load($category->getId());
							$sub_category = $cat->getChildrenCategories();

							foreach ($sub_category as $category) {
								if ($category->getIsActive() && isset($optionsFacetedData[$category->getId()]) && $this->isOptionReducesResults($optionsFacetedData[$category->getId()]['count'], $collectionSize)) {
									$this->itemDataBuilder->addItemData(
										$this->escaper->escapeHtml($category->getName().'pid'.$category->getParentId()),
										$category->getId(),
										$optionsFacetedData[$category->getId()]['count']
									);
								}

								$cat = $_objectManager->create('Magento\Catalog\Model\Category')->load($category->getId());
								$sub_category = $cat->getChildrenCategories();

								foreach ($sub_category as $category) {
									if ($category->getIsActive() && isset($optionsFacetedData[$category->getId()]) && $this->isOptionReducesResults($optionsFacetedData[$category->getId()]['count'], $collectionSize)) {
										$this->itemDataBuilder->addItemData(
											$this->escaper->escapeHtml($category->getName().'pid'.$category->getParentId()),
											$category->getId(),
											$optionsFacetedData[$category->getId()]['count']
										);
									}
								}
							}
						}
					}
				}
			}
		}
		return $this->itemDataBuilder->build();
	}
}