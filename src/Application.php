<?php
namespace Minix;

use Minix\Controller\ErrorController;
use Minix\Exceptions\MethodNotAllowedException;
use Minix\Request;
use Minix\Response;

class Application {

	/**
	 * @var string
	 */
	private $env;

	/**
	 * @var Request
	 */
	private $request;

	/**
	 * @var Response
	 */
	private $response;

	/**
	 * @var Application
	 */
	private static $instance;

	/**
	 * @return Application
	 */
	public static function getInstance() {

		if (null === static::$instance) {
			static::$instance = new Application();
		}

		return static::$instance;
	}

	public function setRequest(Request $request) {

		$this->request = $request;
	}

	/**
	 * @return \Minix\Request
	 */
	public function getRequest() {

		if (!($this->request instanceof Request)) {
			$this->request = new Request(new Route());
		}

		return $this->request;
	}

	/**
	 * @param \Minix\Response $response
	 */
	public function setResponse(Response $response) {

		$this->response = $response;
	}

	/**
	 * @return \Minix\Response
	 */
	public function getResponse() {

		if (!($this->response instanceof Response)) {
			$this->response = new Response();
		}

		return $this->response;
	}

	/**
	 * @throws \Exception
	 */
	public function run() {

		$uri = $this->getUri();
		$request = $this->getRequest();

		try {
			if (!$request->getRouter()->dispatch($uri)) {
				$controller = new ErrorController();
				$controller->notFoundAction();
			}
		} catch (MethodNotAllowedException $e) {
			$this->getResponse()->setStatus(Response::STATUS_METHOD_NOT_ALLOWED);
			$controller = new ErrorController();
			$controller->exceptionHandlerAction($e);
		} catch (\Exception $e) { // @todo: catch only application exceptions. PDO, etc.. should not be displayed
			$controller = new ErrorController();
			$controller->exceptionHandlerAction($e);
		}
	}

	/**
	 * @return \Minix\Route
	 */
	public function getRoutes() {

		$routes = $this->getAppPath() . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'routes.php';
		if (file_exists($routes)) {
			return require $routes;
		}
	}

	/**
	 * @param $env
	 */
	public function setEnvironment($env) {

		$this->env = $env;
	}

	/**
	 * @return string
	 */
	public function getEnvironment() {

		return $this->env;
	}

	/**
	 * @return mixed
	 */
	public function getUri() {

		return strtok($_SERVER["REQUEST_URI"], '?');
	}

	/**
	 * @return string
	 */
	public function getConfigPath() {

		return $this->getAppPath() . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'config';

	}

	/**
	 * @return mixed
	 */
	public function getAppPath() {

		return $_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . '..';
	}
}