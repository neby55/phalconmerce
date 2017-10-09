<?php

namespace Backend\Forms;

use Phalcon\Forms\Element\Hidden;
use Phalcon\Forms\Element\Select;
use Phalcon\Mvc\Model\Query;
use Phalcon\Tag;
use Phalcon\Validation\Validator\PresenceOf;
use Phalconmerce\Models\Popo\Attribute;
use Phalconmerce\Models\Utils;

class ProductHasAttributeAddForm extends FormBase {
	public function initialize($entity = null, $options = array()) {
		// Adding hidden input
		$this->add(new Hidden("id"));

		$excludedAttributeIds = array();
		// Adding attributeSets elements
		if (is_object($entity->getAttributeSet())) {
			foreach ($entity->getAttributeSet()->getAttributeSetHasAttribute(['order' => 'position']) as $currentAttributeSetHasAttribute) {
				if ($currentAttributeSetHasAttribute->getAttribute()) {
					$excludedAttributeIds[] = $currentAttributeSetHasAttribute->getAttribute()->id;
				}
			}
		}

		// All attributes
		$attributeSelectValues = array();
		$pdoStatement = $this->db->prepare(
			'SELECT id, label
			FROM attribute WHERE status = :status AND id NOT IN (
				SELECT fk_attribute_id
				FROM product_has_attribute
				WHERE fk_product_id = :pro_id
			) AND id NOT IN (
				SELECT fk_attribute_id
				FROM product
				INNER JOIN attribute_set_has_attribute ON attribute_set_has_attribute.fk_attribute_set_id = product.fk_attribute_set_id
				WHERE product.id = :pro_id
			)'
		);
		$pdoStatement->bindValue(':status', 1, \PDO::PARAM_INT);
		$pdoStatement->bindValue(':pro_id', $entity->id, \PDO::PARAM_INT);
		if ($pdoStatement->execute() !== false) {
			while (($row = $pdoStatement->fetch(\PDO::FETCH_ASSOC)) !== false) {
				$attributeSelectValues[$row['id']] = $row['label'];
			}
		}

		// Dropdown of attributes
		$item = new Select(
			'fk_attribute_id',
			$attributeSelectValues
		);
		$item->setAttribute('class', 'form-control');
		$item->addValidators(array(
			new PresenceOf(array(
				'message' => $this->di->get('backendService')->t('This field is required')
			))
		));
		$item->setLabel('Attribut');
		$this->addElement('fk_attribute_id', $item);
		$this->addHelpBlock('fk_attribute_id', 'If attribute is not in the dropdown, you can '.Tag::linkTo([$this->url->get(array('for'=>'backend-controller-new', 'controller'=>'attribute')), 'add it by clicking here', "class" => '']));

		// Dropdown for required ?
		$item = new Select(
			'isRequired',
			[
				2 => $this->di->get('backendService')->t('No'),
				1 => $this->di->get('backendService')->t('Yes')
			]
		);
		$item->setAttribute('class', 'form-control');
		$item->setFilters(array('int'));
		$item->addValidators(array(
			new PresenceOf(array(
				'message' => $this->di->get('backendService')->t('This field is required')
			))
		));
		$item->setLabel('Required ?');
		$this->addElement('isRequired', $item);

		$this->addElementsToForm();
	}
}