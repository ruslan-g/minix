<?php

namespace Minix;

use Minix\Exceptions\MethodNotAllowedException;

class Route {

	/**
	 * @var RouteList
	 */
	private $routeList;

	public function __construct() {

		$this->routeList = new RouteList();
	}

	/**
	 * @param $path
	 * @return bool
	 * @throws \Exception
	 * @throws MethodNotAllowedException
	 */
	public function dispatch($uri) {

		$routes = Application::getInstance()->getRoutes();
		$request = Application::getInstance()->getRequest();

		$checkForAlternativeMethods = false;

		foreach ($routes->routeList as $route) {

			if ($route->getUri() === $uri) {

				if ($route->getMethod() !== $request->method()) {
					$checkForAlternativeMethods = true;
					continue;
				}

				$action = explode('@', $route->getAction());

				if (count($action) === 2) {
					$controller = new $action[0]();
					$method = $action[1];
					return $controller->$method();
				} else {
					throw new \Exception('Not valid action in router');
				}
			}
		}

		if ($checkForAlternativeMethods) {
			throw new MethodNotAllowedException;
		}

		return false;
	}

	public function get($uri, $action) {

		$this->addRoute('GET', $uri, $action);
	}

	public function post($uri, $action) {

		$this->addRoute('POST', $uri, $action);
	}

	public function delete($uri, $action) {

		$this->addRoute('DELETE', $uri, $action);
	}

	public function put($uri, $action) {

		$this->addRoute('PUT', $uri, $action);
	}

	private function addRoute($method, $uri, $action) {

		$this->routeList->add(new DataTransferObjects\Route($method, $uri, $action));
	}
}