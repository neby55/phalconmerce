<?php
/**
 * Phalconmerce
 * an e-commerce framework based on Phalcon PHP framework
 * 
 * Authors :
 *    Benjamin CORDIER <ben@progweb.fr>
 */

namespace Backend\Controllers\Abstracts;

use Backend\Controllers\ControllerBase;
use Backend\Forms\ConfigurableForm;
use Backend\Forms\ProductHasAttributeForm;
use Backend\Forms\ProductAttributeSetForm;
use Backend\Forms\DesignForm;
use Backend\Forms\GroupedForm;
use Phalconmerce\Models\Popo\Abstracts\AbstractFinalProduct;
use Phalconmerce\Models\Popo\Abstracts\AbstractProduct;
use Phalconmerce\Models\Popo\Generators\Popo\PhpClass;
use Phalconmerce\Models\Popo\Attribute;
use Phalconmerce\Models\Popo\AttributeSet;
use Phalconmerce\Models\Popo\AttributeValue;
use Phalconmerce\Models\Popo\Product;
use Phalconmerce\Models\Popo\ConfigurableProduct;
use Phalconmerce\Models\Popo\ConfiguredProduct;
use Phalconmerce\Models\Popo\GroupedProduct;
use Phalconmerce\Models\Popo\GroupedProductHasProduct;
use Phalconmerce\Models\Popo\ProductHasAttribute;
use Phalconmerce\Models\Utils;
use Phalconmerce\Services\BackendService;

class AbstractProductController extends ControllerBase {
	/**
	 * Shows the index action
	 */
	public function indexAction() {
		parent::indexAction();
		$getSearch = $this->request->get('search', 'string');
		$getType = $this->request->get('type', 'int');
		$getStatus = $this->request->get('status', 'int');

		// Search filters
		$findOptions = array('bind'=>array());
		if (!empty($getSearch)) {
			$findOptions['conditions'] = (isset($findOptions['conditions']) ? $findOptions['conditions'].' AND ' : '').'(name LIKE :search: OR sku LIKE :search: OR shortDescription LIKE :search:) ';
			$findOptions['bind']['search'] = '%'.$getSearch.'%';
		}
		if (!empty($getType)) {
			$findOptions['conditions'] = (isset($findOptions['conditions']) ? $findOptions['conditions'].' AND ' : '').' coreType = :type: ';
			$findOptions['bind']['type'] = $getType;
		}
		if (!empty($getStatus)) {
			$findOptions['conditions'] = (isset($findOptions['conditions']) ? $findOptions['conditions'].' AND ' : '').' status = :status: ';
			$findOptions['bind']['status'] = $getStatus;
		}
		$findOptions['order'] = 'id DESC';
		// Get all Products
		$list = Product::find($findOptions);

		$this->view->setVar('list', $list);
		$this->view->setVar('getSearch', $getSearch);
		$this->view->setVar('getType', $getType);
		$this->view->setVar('getStatus', $getStatus);

		$this->view->setVar('listActionProperties', Product::getBackendListProperties());
	}

	/**
	 * @param AbstractFinalProduct $finalProduct
	 */
	protected function configureEditView($finalProduct) {
		if (is_a($finalProduct, PhpClass::POPO_ABSTRACT_NAMESPACE.'\AbstractFinalProduct')) {
			if (is_a($finalProduct, PhpClass::POPO_ABSTRACT_NAMESPACE . '\AbstractSimpleProduct')) {
				$this->view->setVar('displayTabFinalProduct', false);
			}
			else if (is_a($finalProduct, PhpClass::POPO_ABSTRACT_NAMESPACE . '\AbstractGroupedProduct')) {
				$finalProduct->loadRelatedProducts();
				$this->view->setVar('displayTabFinalProduct', true);
				$this->view->setVar('tabFinalProduct', 'grouped');
				$this->view->setVar('listActionProperties', Product::getBackendListProperties());
				$this->view->formGroupedProduct = new GroupedForm($finalProduct);

			}
			else if (is_a($finalProduct, PhpClass::POPO_ABSTRACT_NAMESPACE . '\AbstractConfigurableProduct')) {
				$finalProduct->loadConfiguredProducts();
				$this->view->setVar('displayTabFinalProduct', true);
				$this->view->setVar('tabFinalProduct', 'configurable');
				// Getting attributes
				$attributesResult = $finalProduct->getRelatedProduct()->getAttribute();
				if (!empty($attributesResult) && $attributesResult->count() > 0) {
					$listActionProperties = array();
					/** @var \Phalconmerce\Models\Popo\Abstracts\AbstractAttribute $currentAttribute */
					foreach ($attributesResult as $currentAttribute) {
						$listActionProperties['attribute_'.$currentAttribute->id] =  $this->di->get('backendService')->t($currentAttribute->label);
					}
					$listActionProperties['status'] = array(
						'label' => $this->di->get('backendService')->t('Status'),
						'values' => BackendService::getBackendListStatusValues()
					);
					$this->view->setVar('listActionProperties', $listActionProperties);
				}
				else {
					$this->view->setVar('listActionProperties', array(
						'sku' => $this->di->get('backendService')->t('SKU'),
						'name' => $this->di->get('backendService')->t('Name'),
						'status' => array(
							'label' => $this->di->get('backendService')->t('Status'),
							'values' => BackendService::getBackendListStatusValues()
						)
					));
				}
				$this->view->formConfigurableProduct = new ConfigurableForm($finalProduct);
			}
			else if (is_a($finalProduct, PhpClass::POPO_ABSTRACT_NAMESPACE . '\AbstractConfiguredProduct')) {
				//$finalProduct->loadConfigurableProduct();
				$this->view->pick($finalProduct->getSource() . '/edit');
			}
			$this->view->setVar('finalProduct', $finalProduct);
		}
	}

	/**
	 * Saves current object in screen
	 */
	public function saveNewAction() {
		if (!$this->request->isPost()) {
			$this->dispatcher->forward(
				[
					"controller" => $this->dispatcher->getControllerName(),
					"action" => "index",
				]
			);
			return false;
		}

		$name = $this->request->getPost("name", "string");
		$type = $this->request->getPost("type", "int");
		$attributeSetId = $this->request->getPost("att_set_id", "int");

		// If creation
		$object = new Product();
		$object->name = $name;
		$object->fk_attribute_set_id = $attributeSetId;
		$object->coreType = $type;
		$object->sku = '';
		$object->priceVatExcluded = 0;
		$object->status = 0;

		if ($object->save() == false) {
			foreach ($object->getMessages() as $message) {
				$this->flash->error($message);
			}

			$this->dispatcher->forward(
				[
					"controller" => $this->dispatcher->getControllerName(),
					"action" => "new",
				]
			);
			return false;
		}
		else {
			$finalProduct = $object->getFinalProductObject();
			if (!empty($finalProduct)) {
				$finalProduct->save();
			}
		}

		$this->flash->success("Product successfully created");

		return $this->redirectToRoute('backend-controller-edit', array('id' => $object->id, 'controller' => $this->dispatcher->getControllerName()));
	}

	/**
	 * Saves current object in screen
	 */
	public function saveAttributeSetAction() {
		if (!$this->request->isPost()) {
			$this->dispatcher->forward(
				[
					"controller" => $this->dispatcher->getControllerName(),
					"action" => "index",
				]
			);
			return false;
		}

		$id = $this->request->getPost("id", "int", 0);

		if ($id <= 0) {
			$this->dispatcher->forward(
				[
					"controller" => $this->dispatcher->getControllerName(),
					"action" => "index",
				]
			);
			return false;
		}

		$object = Product::findFirstById($id);

		if (!$object) {
			$this->flash->error("Product does not exist");

			$this->dispatcher->forward(
				[
					"controller" => $this->dispatcher->getControllerName(),
					"action" => "edit",
					'params' => array($id)
				]
			);
			return false;
		}

		$form = new ProductAttributeSetForm($object);

		$data = $this->request->getPost();
		if (!$form->isValid($data, $object)) {
			$this->view->formAttributeSet = $form;
			foreach ($form->getMessages() as $message) {
				$this->flash->error($message);
			}

			$this->dispatcher->forward(
				[
					"controller" => $this->dispatcher->getControllerName(),
					"action" => "edit",
					'params' => array($id)
				]
			);
			return false;
		}

		$hasErrors = false;
		if (is_object($object->getAttributeSet())) {
			/** @var AttributeSetHasAttribute $currentAttributeSetHasAttribute */
			foreach ($object->getAttributeSet()->getAttributeSetHasAttribute(['order' => 'position']) as $currentAttributeSetHasAttribute) {
				/** @var Attribute $currentAttribute */
				$currentAttribute = $currentAttributeSetHasAttribute->getAttribute();
				$currentFieldValue = $data[$currentAttribute->name];

				if (is_object($currentAttribute)) {
					/** @var ProductHasAttribute $currentLine */
					$currentLine = ProductHasAttribute::findFirst(array(
						'fk_product_id = :fk_product_id: AND fk_attribute_id = :fk_attribute_id:',
						'bind' => array(
							'fk_product_id' => $id,
							'fk_attribute_id' => $currentAttribute->id,
						)
					));

					// If creation
					if (!is_object($currentLine)) {
						$currentLine = new ProductHasAttribute();
						$currentLine->fk_attribute_id = $currentAttribute->id;
						$currentLine->fk_product_id = $id;
						$currentLine->isRequired = $currentAttributeSetHasAttribute->isRequired;
					}

					// Setting the value
					if ($currentAttribute->type == Attribute::TYPE_DROPDOWN) {
						$currentLine->fk_attribute_value_id = $currentFieldValue;
					}
					else {
						$currentLine->value = $currentFieldValue;
					}

					// Save to DB
					if ($currentLine->save() == false) {
						$this->view->formAttributeSet = $form;
						foreach ($currentLine->getMessages() as $message) {
							$this->flash->error($message);
						}
						$hasErrors = true;
					}
				}
			}
		}

		if ($hasErrors) {
			$this->dispatcher->forward(
				[
					"controller" => $this->dispatcher->getControllerName(),
					"action" => "edit",
					'params' => array($id)
				]
			);
			return false;
		}

		$form->clear();

		$this->view->formAttributeSet = $form;

		$this->flashSession->success("Product was updated successfully");

		return $this->redirectToRoute('backend-controller-edit', array('id' => $object->id, 'controller' => $this->dispatcher->getControllerName(), 'fragment'=>'tab-5'));
	}

	/**
	 * Saves current object in screen
	 */
	public function saveAttributesCustomAction() {
		if (!$this->request->isPost()) {
			$this->dispatcher->forward(
				[
					"controller" => $this->dispatcher->getControllerName(),
					"action" => "index",
				]
			);
			return false;
		}

		$id = $this->request->getPost("id", "int", 0);

		if ($id <= 0) {
			$this->dispatcher->forward(
				[
					"controller" => $this->dispatcher->getControllerName(),
					"action" => "index",
				]
			);
			return false;
		}

		$productObject = Product::findFirstById($id);

		if (!$productObject) {
			$this->flash->error("Product does not exist");

			$this->dispatcher->forward(
				[
					"controller" => $this->dispatcher->getControllerName(),
					"action" => "edit",
					'params' => array($id)
				]
			);
			return false;
		}

		$form = new ProductHasAttributeForm($productObject);

		$data = $this->request->getPost();
		if (!$form->isValid($data, $productObject)) {
			$this->view->formProductHasAttribute = $form;
			foreach ($form->getMessages() as $message) {
				$this->flash->error($message);
			}

			$this->dispatcher->forward(
				[
					"controller" => $this->dispatcher->getControllerName(),
					"action" => "edit",
					'params' => array($id)
				]
			);
			return false;
		}

		$excludedAttributeIds = array();
		// Adding attributeSets elements
		if (is_object($productObject->getAttributeSet())) {
			foreach ($productObject->getAttributeSet()->getAttributeSetHasAttribute(['order' => 'position']) as $currentAttributeSetHasAttribute) {
				$excludedAttributeIds[] = $currentAttributeSetHasAttribute->getAttribute()->id;
			}
		}

		$hasErrors = false;
		if (!empty($productObject->getProductHasAttribute())) {
			/** @var ProductHasAttribute $currentProductHasAttribute */
			foreach ($productObject->getProductHasAttribute() as $currentProductHasAttribute) {
				if (!in_array($currentProductHasAttribute->fk_attribute_id, $excludedAttributeIds)) {
					/** @var Attribute $currentAttribute */
					$currentAttribute = $currentProductHasAttribute->getAttribute();
					$currentFieldValue = $data[$currentAttribute->name];

					if (is_object($currentAttribute)) {
						/** @var ProductHasAttribute $currentLine */
						$currentLine = ProductHasAttribute::findFirst(array(
							'fk_product_id = :fk_product_id: AND fk_attribute_id = :fk_attribute_id:',
							'bind' => array(
								'fk_product_id' => $id,
								'fk_attribute_id' => $currentAttribute->id,
							)
						));

						// If creation
						if (!is_object($currentLine)) {
							$currentLine = new ProductHasAttribute();
							$currentLine->fk_attribute_id = $currentAttribute->id;
							$currentLine->fk_product_id = $id;
							$currentLine->isRequired = $currentAttributeSetHasAttribute->isRequired;
						}

						// Setting the value
						if ($currentAttribute->type == Attribute::TYPE_DROPDOWN) {
							$currentLine->fk_attribute_value_id = $currentFieldValue;
						}
						else {
							$currentLine->value = $currentFieldValue;
						}

						// Save to DB
						if ($currentLine->save() == false) {
							$this->view->formProductHasAttribute = $form;
							foreach ($currentLine->getMessages() as $message) {
								$this->flash->error($message);
							}
							$hasErrors = true;
						}
					}
				}
			}
		}

		if ($hasErrors) {
			$this->dispatcher->forward(
				[
					"controller" => $this->dispatcher->getControllerName(),
					"action" => "edit",
					'params' => array($id)
				]
			);
			return false;
		}

		$form->clear();

		$this->view->formProductHasAttributeAdd = $form;

		$this->flashSession->success("Product's attribute successfully added");

		return $this->redirectToRoute('backend-controller-edit', array('id' => $id, 'controller' => $this->dispatcher->getControllerName(), 'fragment'=>'tab-2'));
	}

	/**
	 * Saves current object in screen
	 */
	public function saveAttributesCustomAddAction() {
		if (!$this->request->isPost()) {
			$this->dispatcher->forward(
				[
					"controller" => $this->dispatcher->getControllerName(),
					"action" => "index",
				]
			);
			return false;
		}

		$productId = $this->request->getPost("id", "int", 0);
		$attributeId = $this->request->getPost("fk_attribute_id", "int", 0);
		$isRequired = $this->request->getPost("isRequired", "int", 0);

		if ($productId <= 0) {
			$this->dispatcher->forward(
				[
					"controller" => $this->dispatcher->getControllerName(),
					"action" => "index",
				]
			);
			return false;
		}

		$productObject = Product::findFirstById($productId);

		if (!$productObject) {
			$this->flash->error("Product does not exist");

			$this->dispatcher->forward(
				[
					"controller" => $this->dispatcher->getControllerName(),
					"action" => "edit",
					'params' => array($productId)
				]
			);
			return false;
		}

		$form = new ProductHasAttributeAddForm($productObject);

		$data = $this->request->getPost();
		if (!$form->isValid($data, $productObject)) {
			$this->view->formProductHasAttributeAdd = $form;
			foreach ($form->getMessages() as $message) {
				$this->flash->error($message);
			}

			$this->dispatcher->forward(
				[
					"controller" => $this->dispatcher->getControllerName(),
					"action" => "edit",
					'params' => array($productId)
				]
			);
			return false;
		}

		$newObject = new ProductHasAttribute();
		$newObject->isRequired = $isRequired;
		$newObject->fk_attribute_id = $attributeId;
		$newObject->fk_product_id = $productId;

		if ($newObject->save() == false) {
			foreach ($newObject->getMessages() as $message) {
				$this->flash->error($message);
			}

			$this->dispatcher->forward(
				[
					"controller" => $this->dispatcher->getControllerName(),
					"action" => "edit",
					'params' => array($productId)
				]
			);
			return false;
		}

		$form->clear();

		$this->view->formProductHasAttributeAdd = $form;

		$this->flashSession->success("Product's attribute successfully added");

		return $this->redirectToRoute('backend-controller-edit', array('id' => $productId, 'controller' => $this->dispatcher->getControllerName(), 'fragment'=>'tab-2'));
	}

	public function saveGroupedProductAddAction() {
		if (!$this->request->isPost()) {
			$this->dispatcher->forward(
				[
					"controller" => $this->dispatcher->getControllerName(),
					"action" => "index",
				]
			);
			return false;
		}

		$groupedProductId = $this->request->getPost("id", "int");
		$childId = $this->request->getPost("child_id", "int");

		// If creation
		if (empty($groupedProductId)) {
			$object = new GroupedProduct();
		}
		else {
			$object = GroupedProduct::findFirstById($groupedProductId);
		}

		if (!$object) {
			$this->flash->error("Grouped Product does not exist");

			$this->dispatcher->forward(
				[
					"controller" => $this->dispatcher->getControllerName(),
					"action" => "edit",
				]
			);
			return false;
		}

		$form = new GroupedForm;

		$data = $this->request->getPost();
		if (!$form->isValid($data, $object)) {
			$this->view->formGroupedProduct = $form;
			foreach ($form->getMessages() as $message) {
				$this->flash->error($message);
			}

			$this->dispatcher->forward(
				[
					"controller" => $this->dispatcher->getControllerName(),
					"action" => "edit",
					'params' => array('id' => $object->fk_product_id)
				]
			);
			return false;
		}

		$groupedProductHasProduct = new GroupedProductHasProduct();
		$groupedProductHasProduct->fk_grouped_product_id = $object->id;
		$groupedProductHasProduct->fk_product_id = $childId;

		if ($groupedProductHasProduct->create() == false) {
			$this->view->formGroupedProduct = $form;
			foreach ($groupedProductHasProduct->getMessages() as $message) {
				$this->flash->error($message);
			}

			$this->dispatcher->forward(
				[
					"controller" => $this->dispatcher->getControllerName(),
					"action" => "edit",
					'params' => array('id' => $object->fk_product_id)
				]
			);
			return false;
		}

		$form->clear();

		$this->view->formGroupedProduct = $form;

		$this->flash->success("Product was successfully added");

		return $this->redirectToRoute('backend-controller-edit', array('id' => $object->fk_product_id, 'controller' => $this->dispatcher->getControllerName(), 'fragment'=>'tab-final-grouped'));
	}

	public function saveConfiguredProductAddAction() {
		if (!$this->request->isPost()) {
			$this->dispatcher->forward(
				[
					"controller" => $this->dispatcher->getControllerName(),
					"action" => "index",
				]
			);
			return false;
		}

		$configurableProductId = $this->request->getPost("id", "int");
		$name = $this->request->getPost("name", "string");
		$sku = $this->request->getPost("sku", "string");

		// If creation
		if (empty($configurableProductId)) {
			$object = new ConfigurableProduct();
		}
		else {
			$object = ConfigurableProduct::findFirstById($configurableProductId);
		}

		if (!$object) {
			$this->flash->error("Configurable Product does not exist");

			$this->dispatcher->forward(
				[
					"controller" => $this->dispatcher->getControllerName(),
					"action" => "edit",
				]
			);
			return false;
		}

		$form = new ConfigurableForm();

		$data = $this->request->getPost();
		if (!$form->isValid($data, $object)) {
			$this->view->formConfigurableProduct = $form;
			foreach ($form->getMessages() as $message) {
				$this->flash->error($message);
			}

			$this->dispatcher->forward(
				[
					"controller" => $this->dispatcher->getControllerName(),
					"action" => "edit",
					'params' => array('id' => $object->fk_product_id)
				]
			);
			return false;
		}

		$originalProduct = $object->getProduct();
		$product = $originalProduct->cloneModel();
		if (!empty($product)) {
			$product->id = null;
			$product->name = $name;
			$product->sku = $sku;
			$product->coreType = Product::PRODUCT_TYPE_CONFIGURED;

			if ($product->create() == false) {
				foreach ($product->getMessages() as $message) {
					$this->flash->error($message);
				}

				$this->dispatcher->forward(
					[
						"controller" => $this->dispatcher->getControllerName(),
						"action" => "edit",
						'params' => array('id' => $object->fk_product_id)
					]
				);
				return false;
			}

			$productHasAttributesResultSet = $originalProduct->getProductHasAttribute();
			if (!empty($productHasAttributesResultSet) && $productHasAttributesResultSet->count() > 0) {
				/** @var \Phalconmerce\Models\Popo\Abstracts\AbstractProductHasAttribute $currentProductHasAttribute */
				foreach ($productHasAttributesResultSet as $currentProductHasAttribute) {
					$newProductHasAttribute = $currentProductHasAttribute->cloneModel();
					$newProductHasAttribute->fk_product_id = $product->id;
					$newProductHasAttribute->fk_attribute_value_id = null;
					$newProductHasAttribute->value = null;
					if ($newProductHasAttribute->create() == false) {
						$this->view->formConfigurableProduct = $form;
						foreach ($newProductHasAttribute->getMessages() as $message) {
							$this->flash->error($message);
						}
					}
				}
			}

			/** @var \Phalconmerce\Models\Popo\Abstracts\AbstractConfiguredProduct $configuredProduct */
			$configuredProduct = new ConfiguredProduct();
			$configuredProduct->fk_configurable_product_id = $object->id;
			$configuredProduct->fk_product_id = $product->id;

			if ($configuredProduct->create() == false) {
				$this->view->formConfigurableProduct = $form;
				foreach ($configuredProduct->getMessages() as $message) {
					$this->flash->error($message);
				}

				$this->dispatcher->forward(
					[
						"controller" => $this->dispatcher->getControllerName(),
						"action" => "edit",
						'params' => array('id' => $object->fk_product_id)
					]
				);
				return false;
			}
		}

		$form->clear();

		$this->view->formConfigurableProduct = $form;

		$this->flash->success("Product was successfully added");

		return $this->redirectToRoute('backend-controller-edit', array('id' => $object->fk_product_id, 'controller' => $this->dispatcher->getControllerName(), 'fragment'=>'tab-final-configurable'));
	}
}