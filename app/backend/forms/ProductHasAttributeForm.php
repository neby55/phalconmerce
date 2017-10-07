<?php

namespace Backend\Forms;

use Phalcon\Forms\Element\Hidden;
use Phalcon\Forms\Element\Select;
use Phalcon\Forms\Element\Text;
use Phalcon\Validation\Validator\PresenceOf;
use Phalconmerce\Models\Popo\Attribute;
use Phalconmerce\Models\Popo\ProductHasAttribute;
use Phalconmerce\Models\Utils;

class ProductHasAttributeForm extends FormBase {
	public function initialize($entity = null, $options = array()) {
		// Adding hidden input
		$this->add(new Hidden("id"));

		$excludedAttributeIds = array();
		// Adding attributeSets elements
		if (is_object($entity->getAttributeSet())) {
			foreach ($entity->getAttributeSet()->getAttributeSetHasAttribute(['order' => 'position']) as $currentAttributeSetHasAttribute) {
				$excludedAttributeIds[] = $currentAttributeSetHasAttribute->getAttribute()->id;
			}
		}

		// Get attributes linked to this product
		if (!empty($entity->getProductHasAttribute())) {
			/** @var ProductHasAttribute $currentProductHasAttribute */
			foreach ($entity->getProductHasAttribute() as $currentProductHasAttribute) {
				if (!in_array($currentProductHasAttribute->fk_attribute_id, $excludedAttributeIds)) {
					$item = null;
					$currentAttribute = $currentProductHasAttribute->getAttribute();

					// Select case
					if ($currentAttribute->type == Attribute::TYPE_DROPDOWN) {
						$values = array();
						foreach ($currentAttribute->getAttributeValue() as $currentAttributeValue) {
							$values[$currentAttributeValue->id] = $currentAttributeValue->value;
						}
						$item = new Select(
							$currentAttribute->name,
							$values
						);
					}
					else if ($currentAttribute->type == Attribute::TYPE_INT) {
						$item = new Text($currentAttribute->name);
						$item->setAttribute('class', 'form-control');
						$item->setFilters(array('int'));
						$item->setAttribute('maxlength', 16);
					}
					else if ($currentAttribute->type == Attribute::TYPE_FLOAT) {
						$item = new Text($currentAttribute->name);
						$item->setAttribute('class', 'form-control');
						$item->setFilters(array('float'));
						$item->setAttribute('maxlength', 16);
					}
					else if ($currentAttribute->type == Attribute::TYPE_STRING) {
						$item = new Text($currentAttribute->name);
						$item->setAttribute('class', 'form-control');
						$item->setFilters(array('string'));
						$item->setAttribute('maxlength', 128);
					}
					else if ($currentAttribute->type == Attribute::TYPE_BOOLEAN) {
						$item = new Select(
							$currentAttribute->name,
							[
								2 => $this->di->get('backendService')->t('No'),
								1 => $this->di->get('backendService')->t('Yes')
							]
						);
						$item->setAttribute('class', 'form-control');
						$item->setFilters(array('int'));
					}

					// If required
					if ($currentProductHasAttribute->isRequired) {
						$item->addValidators(array(
							new PresenceOf(array(
								'message' => $this->di->get('backendService')->t('This field is required')
							))
						));
					}

					// Current value
					if ($currentAttribute->type == Attribute::TYPE_DROPDOWN) {
						if ($currentProductHasAttribute->getAttributeValue()) {
							$item->setDefault($currentProductHasAttribute->getAttributeValue()->id);
						}
					}
					else {
						$item->setDefault($currentProductHasAttribute->value);
					}

					// Ajout au formulaire
					if (isset($item)) {
						$item->setLabel($currentAttribute->label);
						$this->addElement($currentAttribute->name, $item);
						$this->addHelpBlock($currentAttribute->name, $currentAttribute->helpText);
					}
				}
			}
		}

		$this->addElementsToForm();
	}
}