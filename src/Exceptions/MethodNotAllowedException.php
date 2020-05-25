<?php

namespace Minix\Exceptions;

use Throwable;

class MethodNotAllowedException extends \Exception {

	public function __construct($message = "", $code = 0, Throwable $previous = null) {

		if (!$message) {
			$message = 'Method is not allowed';
		}

		parent::__construct($message, $code, $previous);
	}
}