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
use Phalcon\Forms\Element\Text;
use Phalcon\Validation\Validator\PresenceOf;
use Phalconmerce\Models\Popo\Abstracts\AbstractProduct;
use Phalconmerce\Models\Popo\Product;

class ConfigurableForm extends FormBase {
	public function initialize($entity = null, $options = array()) {
		/** @var \Phalconmerce\Models\Popo\Abstracts\AbstractConfigurableProduct $entity  */
		if (is_object($entity) && is_a($entity, '\Phalconmerce\Models\Popo\Abstracts\AbstractConfigurableProduct')) {
			// Adding hidden input
			$this->add(new Hidden("id"));

			// Asking only for the new name
			$item = new Text('name');
			$item->setAttribute('class', 'form-control');
			$item->setFilters(array('string'));
			$item->setAttribute('maxlength', 128);
			$item->setDefault($entity->name);
			$item->addValidators(array(
				new PresenceOf(array(
					'message' => $this->di->get('backendService')->t('This field is required')
				))
			));
			$this->addElement('name', $item);

			$this->addElementsToForm();
		}
	}
}