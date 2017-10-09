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
			$item = new Hidden("id");
			$item->setDefault($entity->id);
			$this->add($item);

			// Asking for the new name
			$item = new Text('name');
			$item->setLabel($this->di->get('backendService')->t('Name'));
			$item->setAttribute('class', 'form-control');
			$item->setFilters(array('string'));
			$item->setAttribute('maxlength', 128);
			$item->setDefault($entity->getRelatedProduct()->name);
			$item->addValidators(array(
				new PresenceOf(array(
					'message' => $this->di->get('backendService')->t('This field is required')
				))
			));
			$this->addElement('name', $item);
			$this->addHelpBlock('name', $this->di->get('backendService')->t('You should add specific attributes to the name, like \'size:42 cut:slim\''));

			// Asking for new SKU
			$item = new Text('sku');
			$item->setLabel($this->di->get('backendService')->t('SKU'));
			$item->setAttribute('class', 'form-control');
			$item->setFilters(array('string'));
			$item->setAttribute('maxlength', 32);
			$item->setDefault($entity->getRelatedProduct()->sku);
			$item->addValidators(array(
				new PresenceOf(array(
					'message' => $this->di->get('backendService')->t('This field is required')
				))
			));
			$this->addElement('sku', $item);

			$this->addElementsToForm();
		}
	}
}