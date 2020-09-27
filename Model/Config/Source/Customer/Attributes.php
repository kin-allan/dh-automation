<?php

namespace DigitalHub\Automation\Model\Config\Source\Customer;

use Magento\Framework\Option\ArrayInterface;
use Magento\Eav\Api\AttributeRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;

class Attributes implements ArrayInterface {

    /**
     * @var AttributeRepositoryInterface
     */
    protected $attributeRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    protected $criteriaBuilder;

    /**
     * Attribute list
     * @var array|null
     */
    private $list;


    /**
     * Constructor.
     * @param AttributeRepositoryInterface $attributeRepository
     */
    public function __construct(
        AttributeRepositoryInterface $attributeRepository,
        SearchCriteriaBuilder $criteriaBuilder
    ) {
        $this->attributeRepository  = $attributeRepository;
        $this->criteriaBuilder      = $criteriaBuilder;
    }

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        $data = [];

        foreach ($this->getListOptions() as $key => $value) {
            $data[] = [
                'value' => $key,
                'label' => $value
            ];
        }

        return $data;
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        return $this->getListOptions();
    }

    /**
     * Initialize list option
     * @return array
     */
    private function getListOptions()
    {
        if ($this->list == null) {
            $this->list = [];
            $attributes = $this->attributeRepository->getList('customer', $this->criteriaBuilder->create());

            foreach ($attributes->getItems() as $attribute) {
                $this->list[$attribute->getAttributeCode()] = $attribute->getFrontendLabel();                
            }
        }

        return $this->list;
    }
}
