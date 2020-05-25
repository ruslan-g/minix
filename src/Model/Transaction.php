<?php

namespace Minix\Model;

class Transaction extends Model {

	/**
	 * @var string
	 */
	protected $primaryKey = 'transaction_id';

	/**
	 * @var string
	 */
	protected $tableName = 'transactions';

	/**
	 * @var int
	 */
	public $transactionId;

	/**
	 * @var int
	 */
	public $userId;

	/**
	 * @var int
	 */
	public $currencyAmount;

	/**
	 * @param \stdClass
	 */
	public function __construct(\stdClass $transaction = null) {

		parent::__construct($transaction);
		if ($transaction) {
			$this->transactionId = $transaction->TransactionId;
			$this->userId = $transaction->UserId;
			$this->currencyAmount = $transaction->CurrencyAmount;
		}
	}

	/**
	 * @return bool
	 */
	public function create() {

		$statement = $this->db->connection->prepare('INSERT INTO ' . $this->tableName . ' SET transaction_id = ?, user_id = ?, currency_amount = ?');
		return $statement->execute([
			$this->transactionId,
			$this->userId,
			$this->currencyAmount
		]);
	}

	/**
	 * @param $userId
	 * @return string
	 */
	public function getAmountSummaryByUserId($userId) {

		$statement = $this->db->connection->prepare('SELECT SUM(currency_amount) FROM ' . $this->tableName . ' WHERE user_id = ?');
		$statement->execute([$userId]);
		return (int) $statement->fetchColumn();
	}

	/**
	 * @param $userId
	 * @return string
	 */
	public function getCountByUserId($userId) {

		$statement = $this->db->connection->prepare('SELECT count(*) FROM ' . $this->tableName . ' WHERE user_id = ?');
		$statement->execute([$userId]);
		return (int) $statement->fetchColumn();
	}
}