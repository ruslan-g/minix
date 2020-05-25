<?php

namespace Minix;

use PDO;

class Db {

	private static $instance;

	/**
	 * @var PDO $connection
	 */
	public $connection;

	/**
	 * @return Db
	 */
	public static function getInstance() {

		if (null === static::$instance) {
			static::$instance = new Db();
		}

		return static::$instance;
	}

	private function __construct() {

		$this->loadConnection();
	}

	private function loadConnection() {

		$config = Config::get();
		$this->connection = new PDO('mysql:host=' . $config['mysql']['host'] . ';dbname=' . $config['mysql']['dbname'], $config['mysql']['user'], $config['mysql']['password']);
		$this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	}
}