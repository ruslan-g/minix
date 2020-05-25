<?php
namespace Minix\Controller;

use Minix\Application;
use Minix\Request;
use Minix\Response;

class BaseController {

	/**
	 * @var Response
	 */
	protected $response;

	/**
	 * @var Request
	 */
	protected $request;

	public function __construct() {

		$this->response = Application::getInstance()->getResponse();
		$this->request = Application::getInstance()->getRequest();
	}

	/**
	 * @todo move to validators
	 * @param $params
	 * @param $requiredParams
	 * @return true|array
	 */
	public function validateRequired($params, $requiredParams) {

		$errors = [];
		foreach ($requiredParams as $required) {
			if (!isset($params->$required)) {
				$errors[] = $required;
			}
		}
		if (count($errors) > 0) {
			throw new \Exception('Fields are required:' . implode(',', $errors));
		}

		return true;
	}

	public function validateInteger($fieldName, $value) {

		if (!is_int($value)) {
			throw new \Exception('Field ' . $fieldName . ' should be integer');
		}
	}

}