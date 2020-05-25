<?php
namespace Minix\Controller;

use Minix\Response;

class ErrorController extends BaseController {

	/**
	 *
	 */
	public function notFoundAction() {

		echo $this->response
			->setError(true)
			->setStatus(Response::STATUS_NOT_FOUND)
			->setErrorMessage('Not Found')
			->jsonResponse();
	}

	/**
	 * @param \Exception $exception
	 */
	public function exceptionHandlerAction(\Exception $exception) {

		echo $this->response
			->setError(true)
			->setErrorMessage($exception->getMessage())
			->jsonResponse();
	}
}