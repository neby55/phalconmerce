<?php

namespace Backend\Forms;

use Phalcon\Forms\Element\Hidden;
use Phalcon\Forms\Element\Select;
use Phalcon\Forms\Element\Text;
use Phalcon\Validation\Validator\PresenceOf;
use Phalconmerce\Models\Popo\Attribute;
use Phalconmerce\Models\Popo\ProductHasAttribute;
use Phalconmerce\Models\Utils;

class ProductAttributeSetForm extends FormBase {
	public function initialize($entity = null, $options = array()) {
		// Adding hidden input
		$this->add(new Hidden("id"));

		// Adding attributeSets elements
		if (is_object($entity->getAttributeSet())) {
			foreach ($entity->getAttributeSet()->getAttributeSetHasAttribute(['order'=>'position']) as $currentAttributeSetHasAttribute) {
				$item = null;
				$currentAttribute = $currentAttributeSetHasAttribute->getAttribute();

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
				if ($currentAttributeSetHasAttribute->isRequired) {
					$item->addValidators(array(
						new PresenceOf(array(
							'message' => $this->di->get('backendService')->t('This field is required')
						))
					));
				}

				// for getting values
				$currentProductHasAttribute = ProductHasAttribute::findFirst(array(
					'fk_product_id = :fk_product_id: AND fk_attribute_id = :fk_attribute_id:',
					'bind' => array(
						'fk_product_id' => $entity->id,
						'fk_attribute_id' => $currentAttribute->id
					)
				));
				if (is_object($currentProductHasAttribute)) {
					if ($currentAttribute->type == Attribute::TYPE_DROPDOWN) {
						$item->setDefault($currentProductHasAttribute->getAttributeValue()->id);
					}
					else {
						$item->setDefault($currentProductHasAttribute->value);
					}
				}

				// Ajout au formulaire
				if (isset($item)) {
					$item->setLabel($currentAttribute->label);
					$this->addElement($currentAttribute->name, $item);
					$this->addHelpBlock($currentAttribute->name, $currentAttribute->helpText);
				}
			}
		}

		// Now add to the form
		$this->addElementsToForm();
	}


}