<?php

namespace Blaze\Myst\Api\Objects;


use Blaze\Myst\Api\BaseObject;

/**
 * Represents an error from the Api. An instance of this class will be returned to application if the api
 * returns an error.
 *
 * Class Error
 * @package Blaze\Myst\Api\Response
 */
class Error extends BaseObject
{
	/**
	 * Error constructor.
	 * @param array $data
	 */
	public function __construct(array $data)
	{
		$constructed_data = $data;
		if (is_array($data['errors'])){
			$this->errors = $data['errors'];
		} else {
			$this->errors[] = $data['errors'];
			$constructed_data['errors'] = [];
			$constructed_data['errors'][] = $data['errors'];
		}
		parent::__construct($constructed_data);
	}
}