<?php

namespace Minix;

class Request {

	const POST = 'POST';

	const GET = 'GET';

	/**
	 * @var Route
	 */
	private $router;

	/**
	 * @param Route $router
	 */
	public function __construct(Route $router) {

		$this->router = $router;
	}

	/**
	 * @return Route
	 */
	public function getRouter() {

		return $this->router;
	}

	/**
	 * @param $key
	 * @param string $default
	 * @return string
	 */
	public function param($key, $default = '') {

		if (isset($_GET[$key])) {
			return $_GET[$key];
		}

		if (isset($_POST[$key])) {
			return $_POST[$key];
		}

		return $default;
	}

	/**
	 * @return string
	 */
	public function raw() {

		return file_get_contents('php://input');
	}

	/**
	 * @return mixed
	 */
	public function method() {

		return $_SERVER['REQUEST_METHOD'];
	}

}