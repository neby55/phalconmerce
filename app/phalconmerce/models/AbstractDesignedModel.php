<?php
/**
 * Created by PhpStorm.
 * User: proGweb
 * Date: 03/02/2017
 * Time: 13:11
 */

namespace Phalconmerce\Models;

use Phalcon\Mvc\Model;

class AbstractDesignedModel extends AbstractModel {
	/**
	 * Design slug
	 *
	 * @Column(type="string", length=32, nullable=true)
	 * @var string
	 */
	public $designSlug;

	/**
	 * Data used on Design (template)
	 * Gets from jsonDesignData which is stored in DB
	 * @var array
	 */
	public $designData;

	/**
	 * JSON representation of data for designs
	 *
	 * @Column(type="text", nullable=true, editable=false)
	 * @var string
	 */
	public $designJson;

	public function afterFetch() {
		$this->designData = json_decode($this->designJson, true);
	}

	/**
	 * @param Model\MetaDataInterface $metaData
	 * @param bool $exists
	 * @param mixed $identityField
	 * @return bool
	 */
	protected function _preSave(\Phalcon\Mvc\Model\MetaDataInterface $metaData, $exists, $identityField) {
		$this->designJson = json_encode($this->designData);

		return parent::_preSave($metaData, $exists, $identityField);
	}
}