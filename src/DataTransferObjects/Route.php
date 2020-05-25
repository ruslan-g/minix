<?php
namespace Minix\DataTransferObjects;

class Route {

	private $method;

	private $action;

	private $uri;

	public function __construct($method, $uri, $action) {

		$this->method = $method;

		$this->action = $action;

		$this->uri = $uri;
	}

	/**
	 * @return mixed
	 */
	public function getMethod() {

		return $this->method;
	}

	/**
	 * @param mixed $method
	 */
	public function setMethod($method) {

		$this->method = $method;
	}

	/**
	 * @return mixed
	 */
	public function getAction() {

		return $this->action;
	}

	/**
	 * @param mixed $action
	 */
	public function setAction($action) {

		$this->action = $action;
	}

	/**
	 * @return mixed
	 */
	public function getUri() {

		return $this->uri;
	}

	/**
	 * @param mixed $uri
	 */
	public function setUri($uri) {

		$this->uri = $uri;
	}


}