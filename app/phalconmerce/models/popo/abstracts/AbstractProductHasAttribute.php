<?php

namespace Phalconmerce\Models\Popo\Abstracts;

use Phalconmerce\Models\AbstractModelManyToMany;

abstract class AbstractProductHasAttribute extends AbstractModelManyToMany {

	/**
	 * @Primary
	 * @Column(type="integer", nullable=false)
	 * @var int
	 */
	public $fk_product_id;

	/**
	 * @Primary
	 * @Column(type="integer", nullable=false)
	 * @var int
	 */
	public $fk_attribute_id;

	/**
	 * @Column(type="boolean", nullable=false)
	 * @Index
	 * @var boolean
	 */
	public $isRequired;

	/**
	 * @Column(type="integer", nullable=true)
	 * @var int
	 */
	public $fk_attribute_value_id;

	/**
	 * @Column(type="string", length=255, nullable=true)
	 * @Index
	 * @var string
	 */
	public $value;

	public function getCurrentValue() {
		if ($this->fk_attribute_value_id > 0) {
			$attributeValue = $this->getAttributeValue();
			if (!empty($attributeValue)) {
				return $attributeValue->value;
			}
		}
		return $this->value;
	}
}