<?php

namespace Minix;

class Config {

	public static function get() {

		$app = Application::getInstance();
		$env = $app->getEnvironment();
		$filename = $env . '.env.php';

		$configFile = $app->getConfigPath() . DIRECTORY_SEPARATOR . $filename;

		if (file_exists($configFile)) {
			return include $configFile;
		}
	}
}