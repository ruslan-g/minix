<?php

namespace Minix\Model;

use Minix\Db;

abstract class Model {

	/**
	 * @var Db
	 */
	protected $db;

	/**
	 * @var string
	 */
	protected $primaryKey;

	/**
	 * @var string
	 */
	protected $tableName;

	/**
	 * @var array
	 */
	protected $attributes;

	/**
	 * @param $object
	 */
	public function __construct(\stdClass $object = null) {

		$this->db = Db::getInstance();

		$att = [];

		if ($object) {
			$attributes = get_object_vars($object);
			foreach($attributes as $attribute => $value) {
				$att[$this->snakeCase($attribute)] = $value;
			}
		}

		$this->attributes = $att;
	}

	/**
	 * @return bool
	 */
	public function exists() {

		$statement = $this->db->connection->prepare('SELECT ' . $this->primaryKey . ' FROM ' . $this->tableName . ' WHERE ' . $this->primaryKey . ' = ?');
		$statement->execute([$this->getPrimaryKeyValue()]);

		return $statement->rowCount() > 0;
	}

	/**
	 * @return null
	 */
	public function getPrimaryKeyValue() {
		if (isset($this->attributes[$this->primaryKey])) {
			return $this->attributes[$this->primaryKey];
		}
		return null;
	}

	/**
	 * @param $word
	 * @return mixed
	 */
	public function snakeCase($word) {
		return preg_replace(
			'/(^|[a-z])([A-Z])/e',
			'strtolower(strlen("\\1") ? "\\1_\\2" : "\\2")',
			$word
		);
	}

	/**
	 * @param $word
	 * @return mixed
	 */
	public function camelCase($word) {
		return preg_replace('/(^|_)([a-z])/e', 'strtoupper("\\2")', $word);
	}
}