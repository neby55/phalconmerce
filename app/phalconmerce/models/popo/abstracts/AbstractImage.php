<?php

namespace Phalconmerce\Models\Popo\Abstracts;

use Phalcon\Di;
use Phalconmerce\Models\AbstractModel;

abstract class AbstractImage extends AbstractModel {

	/**
	 * @Primary
	 * @Identity
	 * @Column(type="integer", nullable=false)
	 * @var int
	 */
	public $id;

	/**
	 * @Column(type="integer", nullable=false)
	 * @var int
	 */
	public $fk_product_id;

	/**
	 * @Column(type="string", length=255, nullable=true)
	 * @var string
	 */
	public $localFile;

	/**
	 * @Column(type="string", length=255, nullable=true)
	 * @var string
	 */
	public $externalUrl;

	/**
	 * @Column(type="string", length=255, nullable=true)
	 * @var string
	 */
	public $cloudinaryPublicId;

	/**
	 * @Column(type="integer", length=4, nullable=false, default=99)
	 * @var int
	 */
	public $position;

	const TYPE_LOCAL = 1;
	const TYPE_EXTERNAL = 2;
	const TYPE_CLOUDINARY = 3;

	public static $types = array(
		1 => 'local',
		2 => 'external',
		3 => 'cloudinary',
	);

	/**
	 * @return int
	 */
	public function getType() {
		if (!empty($this->localFile)) {
			return self::TYPE_LOCAL;
		}
		else if (!empty($this->cloudinaryPublicId)) {
			return self::TYPE_CLOUDINARY;
		}
		else if (!empty($this->cloudinaryPublicId)) {
			return self::TYPE_CLOUDINARY;
		}
		return 0;
	}

	/**
	 * @return string
	 */
	public function getTypeLabel() {
		$typeId = $this->getType();
		if (array_key_exists($typeId, self::$types)) {
			return self::$types[$typeId];
		}
		return '';
	}

	/**
	 * @param array $options
	 * @return mixed|null|string
	 */
	public function getSrc($options=array()) {
		$typeId = $this->getType();

		switch ($typeId) {
			case self::TYPE_LOCAL:
				return Di::getDefault()->get('config')->imageUri.'/'.$this->localFile;
			case self::TYPE_EXTERNAL:
				return $this->externalUrl;
			case self::TYPE_CLOUDINARY:
				return cloudinary_url_internal($this->cloudinaryPublicId, $options);
		}
		return Di::getDefault()->get('config')->imageUri.'/'.Di::getDefault()->get('config')->image404Uri;
	}
}