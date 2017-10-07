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
use Backend\Forms\DesignForm;
use Phalconmerce\Models\Popo\Abstracts\AbstractFinalProduct;
use Phalconmerce\Models\Popo\Abstracts\AbstractProduct;

class AbstractProductController extends ControllerBase {
	/**
	 * @param \Phalconmerce\Models\Popo\Abstracts\AbstractFinalProduct $finalProduct
	 */
	public function addFinalProductForm($finalProduct) {
		if ($finalProduct->getProduct()->coreType != AbstractProduct::PRODUCT_TYPE_SIMPLE) {
			if (!isset($this->view->formFinalProduct) && !is_object($this->view->formFinalProduct)) {
				$this->view->formFinalProduct = new DesignForm($finalProduct);
			}
		}
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
						$currentLine->fk_attributevalue_id = $currentFieldValue;
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
							$currentLine->fk_attributevalue_id = $currentFieldValue;
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
}