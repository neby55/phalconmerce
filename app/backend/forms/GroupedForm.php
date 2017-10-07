<?php
/**
 * Phalconmerce
 * an e-commerce framework based on Phalcon PHP framework
 * 
 * Authors :
 *    Benjamin CORDIER <ben@progweb.fr>
 */

namespace Backend\Forms;

use Phalcon\Forms\Element\Hidden;
use Phalcon\Forms\Element\Select;
use Phalcon\Validation\Validator\PresenceOf;
use Phalconmerce\Models\Popo\Abstracts\AbstractProduct;
use Phalconmerce\Models\Popo\Product;

class GroupedForm extends FormBase {
	public function initialize($entity = null, $options = array()) {
		/** @var \Phalconmerce\Models\Popo\Abstracts\AbstractGroupedProduct $entity  */
		if (is_object($entity) && is_a($entity, '\Phalconmerce\Models\Popo\Abstracts\AbstractGroupedProduct')) {
			// Adding hidden input
			$item = new Hidden("id");
			$item->setDefault($entity->id);
			$this->add($item);

			// SELECT for all products
			$item = new Select(
				'child_id',
				Product::fkSelect()->getValues(array('coreType = '.AbstractProduct::PRODUCT_TYPE_SIMPLE), true)
			);
			$item->setAttribute('class', 'form-control');
			$item->setFilters(array('int'));
			$item->setLabel('Related simple products');
			$item->addValidators(array(
				new PresenceOf(array(
					'message' => $this->di->get('backendService')->t('This field is required')
				))
			));
			$this->addElement('child_id', $item);

			$this->addElementsToForm();
		}
	}
}