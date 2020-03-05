<?php namespace Synapse\Sidebar\Block;

use Magento\Catalog\Model\ResourceModel\Eav\Attribute;
use Magento\Catalog\Model\ResourceModel\Product;
use Magento\Framework\View\Element\Template;
use \Magento\Framework\App\ObjectManager;


/**
 * Class:Sidebar
 * Synapse\Sidebar\Block
 *
 * @author      Synapse
 * @package     Synapse\Sidebar
 * @copyright   Copyright (c) 2020, Synapse. All rights reserved
 */
class Sidebar extends Template
{

    /**
     * @var \Magento\Catalog\Helper\Category
     */
    protected $_categoryHelper;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * @var \Magento\Catalog\Model\Indexer\Category\Flat\State
     */
    protected $categoryFlatConfig;

    /**
     * @var \Magento\Catalog\Model\CategoryFactory
     */
    protected $_categoryFactory;

    /** @var \Magento\Catalog\Model\ResourceModel\Product\Collection */
    protected $_productCollectionFactory;
	
    /** @var \Magento\Catalog\Helper\Output */
    private $helper;
	
    /**
     * @param Template\Context                                        $context
     * @param \Magento\Catalog\Helper\Category                        $categoryHelper
     * @param \Magento\Framework\Registry                             $registry
     * @param \Magento\Catalog\Model\Indexer\Category\Flat\State      $categoryFlatState
     * @param \Magento\Catalog\Model\CategoryFactory                  $categoryFactory
     * @param \Magento\Catalog\Model\ResourceModel\Product\Collection $productCollectionFactory
     * @param \Magento\Catalog\Helper\Output                          $helper
     * @param array                                                   $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Catalog\Helper\Category $categoryHelper,
        \Magento\Framework\Registry $registry,
        \Magento\Catalog\Model\Indexer\Category\Flat\State $categoryFlatState,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
		\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Catalog\Model\ResourceModel\Product\Collection $productCollectionFactory,
        \Magento\Catalog\Helper\Output $helper,
		\Synapse\Sidebar\Helper\Data $dataHelper,
        $data = [ ]
    )
    {
        $this->_categoryHelper           = $categoryHelper;
        $this->_coreRegistry             = $registry;
        $this->categoryFlatConfig        = $categoryFlatState;
        $this->_categoryFactory          = $categoryFactory;
        $this->_productCollectionFactory = $productCollectionFactory;
        $this->helper                    = $helper;
		$this->_dataHelper = $dataHelper;

        parent::__construct($context, $data);
    }
	
    /*
    * Get owner name
    * @return string
    */
	
    /**
     * Get all categories
     *
     * @param bool $sorted
     * @param bool $asCollection
     * @param bool $toLoad
     *
     * @return array|\Magento\Catalog\Model\ResourceModel\Category\Collection|\Magento\Framework\Data\Tree\Node\Collection
     */
    public function getCategories($sorted = false, $asCollection = false, $toLoad = true)
    {
        $cacheKey = sprintf('%d-%d-%d-%d', $this->getSelectedRootCategory(), $sorted, $asCollection, $toLoad);
        if ( isset($this->_storeCategories[ $cacheKey ]) )
        {
            return $this->_storeCategories[ $cacheKey ];
        }

        /**
         * Check if parent node of the store still exists
         */
        $category = $this->_categoryFactory->create();
		
		$categoryDepthLevel = $this->_dataHelper->getCategoryDepthLevel();

        $storeCategories = $category->getCategories($this->getSelectedRootCategory(), $recursionLevel = $categoryDepthLevel, $sorted, $asCollection, $toLoad);

        $this->_storeCategories[ $cacheKey ] = $storeCategories;

        return $storeCategories;
    }

    /**
     * getSelectedRootCategory method
     *
     * @return int|mixed
     */
    public function getSelectedRootCategory()
    {
        $category = $this->_scopeConfig->getValue(
            'synapse_sidebar/general/category'
        );

		if ( $category == 'current_category_children'){
			$currentCategory = $this->_coreRegistry->registry('current_category');
			if($currentCategory){
				return $currentCategory->getId();
			}
			return 1;
		}
		
		if ( $category == 'current_category_parent_children'){
			$currentCategory = $this->_coreRegistry->registry('current_category');
			if($currentCategory){
				$topLevelParent = $currentCategory->getPath();
				$topLevelParentArray = explode("/", $topLevelParent);
				if(isset($topLevelParent)){
					return $topLevelParentArray[2];
				}
			}
			return 1;
		}		
		
        if ( $category === null )
        {
            return 1;
        }

        return $category;
    }

    /**
     * @param        $category
     * @param string $html
     * @param int    $level
     *
     * @return string
     */
    public function getChildCategoryView($category, $html = '', $level = 1)
    {	
        // Check if category has children
        if ( $category->hasChildren() )
        {

	$objectManager = \Magento\Framework\App\ObjectManager::getInstance(); // Instance of Object Manager
 
	$currentCategory = $objectManager->get('Magento\Framework\Registry')->registry('current_category');
 
	$categoryFactory = $objectManager->get('\Magento\Catalog\Model\CategoryFactory');// Instance of Category Model
	$categoryId = $category->getId(); // YOUR CATEGORY ID
	$category = $categoryFactory->create()->load($categoryId);
	// Parent Categories
	$parentCategories = $category->getParentCategories();
	// Children  Categories
	$childCategories = $category->getChildrenCategories();
	
	//echo "Current Cat sidebar: " . $currentCategory->getName() . "<br> Id : ". $currentCategory->getId() . "<br>";

            // echo "<pre>"; print_r(count($childCategories));

            if ( count($childCategories) > 0 )
            {

                $html .= '<ul class="o-list o-list--unstyled">';

                // Loop through children categories
                foreach ( $childCategories as $childCategory )
                {

                    $html .= '<li class="level' . $level .'">';
                    $html .= '<a href="' . $this->getCategoryUrl($childCategory) . '" title="' . $childCategory->getName() . '" class="' . ($currentCategory->getId() == $childCategory->getId() ? 'active' : '') . '">' . $childCategory->getName() . '</a>';

/*                     if ( $childCategory->hasChildren() )
                    {
                        if ( $this->isActive($childCategory) )
                        {
                            $html .= '<span class="expanded"><i class="fa fa-minus"></i></span>';
                        }
                        else
                        {
                            $html .= '<span class="expand"><i class="fa fa-plus"></i></span>';
                        }
                    } */

                    if ( $childCategory->hasChildren() )
                    {
                        $html .= $this->getChildCategoryView($childCategory, '', ($level + 1));
                    }

                    $html .= '</li>';
                }
                $html .= '</ul>';
            }
        }

        return $html;
    }

    /**
     * Retrieve subcategories
     * DEPRECATED
	 *
     * @param $category
     *
     * @return array
     */
	 
    public function getSubcategories($category)
    {

        if ( $this->categoryFlatConfig->isFlatEnabled() && $category->getUseFlatResource() )
        {
            return (array)$category->getChildrenNodes();
        }

        return $category->getChildrenCategories();
    }
	

    /**
     * Get current category
     *
     * @param \Magento\Catalog\Model\Category $category
     *
     * @return Category
     */
    public function isActive($category)
    {
        $activeCategory = $this->_coreRegistry->registry('current_category');
        $activeProduct  = $this->_coreRegistry->registry('current_product');

        if ( !$activeCategory )
        {

            // Check if we're on a product page
            if ( $activeProduct !== null )
            {
                return in_array($category->getId(), $activeProduct->getCategoryIds());
            }

            return false;
        }

        // Check if this is the active category
        if ( $this->categoryFlatConfig->isFlatEnabled() && $category->getUseFlatResource() AND
            $category->getId() == $activeCategory->getId()
        )
        {
            return true;
        }

        // Check if a subcategory of this category is active
        $childrenIds = $category->getAllChildren(true);
        if ( !is_null($childrenIds) AND in_array($activeCategory->getId(), $childrenIds) )
        {
            return true;
        }

        // Fallback - If Flat categories is not enabled the active category does not give an id
        return (($category->getName() == $activeCategory->getName()) ? true : false);
    }

    /**
     * Return Category Id for $category object
     *
     * @param $category
     *
     * @return string
     */
    public function getCategoryUrl($category)
    {
        return $this->_categoryHelper->getCategoryUrl($category);
    }
}
