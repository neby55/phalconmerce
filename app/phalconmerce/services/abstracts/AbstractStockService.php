<?php
/**
 * Phalconmerce
 * an e-commerce framework based on Phalcon PHP framework
 *
 * Authors :
 *    Benjamin CORDIER <ben@progweb.fr>
 */

namespace Phalconmerce\Services\Abstracts;

use Phalcon\Di;
use Phalcon\Logger;
use Phalconmerce\Models\Popo\Cart;
use Phalconmerce\Models\Popo\Product;
use Phalconmerce\Models\Popo\Order;
use Phalconmerce\Models\Utils;

abstract class AbstractStockService extends MainService {
	public function orderOK($orderId) {
		if ($orderId > 0) {
			/** @var \Phalconmerce\Models\Popo\Abstracts\AbstractOrder $orderObject */
			$order = Order::findFirstById($orderId);

			// Checking result and its type
			if (!empty($order) && is_a($order, '\Phalconmerce\Models\Popo\Abstracts\AbstractOrder')) {
				$cartList = $order->getCart();

				/** @var \Phalconmerce\Models\Popo\Abstracts\AbstractCart $currentCartObject */
				foreach ($cartList as $currentCartObject) {
					/** @var \Phalconmerce\Models\Popo\Abstracts\AbstractProduct $currentProduct */
					$currentProduct = $currentCartObject->getProduct();
					// Utils::debug($currentProduct);

					// Handling error
					if ($currentProduct->stock < $currentCartObject->quantity) {
						Di::getDefault()->get('logger')->error('Stock < Quantity ordered for product #' . $currentProduct->id . ' "' . $currentProduct->name . '" - order #' . $order->id);
					}
					// We can decrement
					else {
						// Just for information
						if ($currentProduct->stock == $currentCartObject->quantity) {
							Di::getDefault()->get('logger')->info('Stock = Quantity ordered for product #' . $currentProduct->id . ' "' . $currentProduct->name . '" - order #' . $order->id);
						}

						// Decrementation
						$currentProduct->stock -= $currentCartObject->quantity;
						$currentProduct->save();

						// Trigger event qty updated
						Di::getDefault()->get('eventsManager')->fire('stock-service:afterStockUpdate', $currentProduct);

						// If current product out of stock
						if ($currentProduct->stock <= 0) {
							// Trigger event qty updated
							Di::getDefault()->get('eventsManager')->fire('stock-service:productStockEmpty', $currentProduct);
						}
					}
				}
			}
		}
		else {
			Di::getDefault()->get('logger')->error('StockService::orderOk - $orderId is not a number');
		}
	}

	/**
	 * @param int $productId
	 * @return bool
	 */
	public function isProductAvailable($productId) {
		if ($productId > 0) {
			/** @var \Phalconmerce\Models\Popo\Abstracts\AbstractProduct $product */
			$product = Product::findFirstById($productId);

			// Checking result and its type
			if (!empty($product) && is_a($product, '\Phalconmerce\Models\Popo\Abstracts\AbstractProduct')) {
				return ($product->stock > 0);
			}
		}
		return false;
	}

	/**
	 * @param int $productId
	 * @param int $qty
	 * @return bool
	 */
	public function incrementProductStock($productId, $qty) {
		if ($productId > 0 && $qty > 0) {
			/** @var \Phalconmerce\Models\Popo\Abstracts\AbstractProduct $product */
			$product = Product::findFirstById($productId);

			// Checking result and its type
			if (!empty($product) && is_a($product, '\Phalconmerce\Models\Popo\Abstracts\AbstractProduct')) {
				$product->stock += $qty;

				return $product->save();
			}
		}
		return false;
	}
}