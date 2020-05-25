<?php

namespace Minix;

class Response {

	/**
	 *
	 */
	const STATUS_OK = 200;

	/**
	 *
	 */
	const STATUS_NOT_FOUND = 404;

	/**
	 *
	 */
	const STATUS_METHOD_NOT_ALLOWED = 405;

	/**
	 * @var int
	 */
	private $status = self::STATUS_OK;

	/**
	 * @var bool
	 */
	private $error = false;

	/**
	 * @var String
	 */
	private $errorMessage;

	/**
	 * @param $data
	 * @return String
	 */
	public function jsonResponse($data = null) {

		http_response_code($this->status);
		header('Content-Type: application/json');

		if ($this->error) {
			$data = ['Error' => true, 'ErrorMessage' => $this->errorMessage];
		}

		return json_encode($data);
	}

	/**
	 * @param $status
	 * @return Response
	 */
	public function setStatus($status) {

		$this->status = $status;
		return $this;
	}

	/**
	 * @param $error
	 * @return Response
	 */
	public function setError($error) {

		$this->error = $error;
		return $this;
	}

	/**
	 * @param $message
	 * @return Response
	 */
	public function setErrorMessage($message) {

		$this->errorMessage = $message;
		return $this;
	}
}