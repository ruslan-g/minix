<?php
namespace Minix\Service;

use \Minix\Model\Transaction as TransactionModel;

class Transaction {

	private $secretKey = 'NwvprhfBkGuPJnjJp77UPJWJUpgC7mLz';

	/**
	 * @param TransactionModel $model
	 * @param $hash
	 * @return bool
	 */
	public function verify(TransactionModel $model, $hash) {

		return $hash == $this->createHash($model);
	}

	/**
	 * @param TransactionModel $model
	 * @return string
	 */
	public function createHash(TransactionModel $model) {

		return sha1($this->secretKey . $model->transactionId . $model->userId . $model->currencyAmount);
	}

	/**
	 * @param TransactionModel $model
	 * @return bool
	 */
	public function create(TransactionModel $model) {

		if (!$model->exists()) {
			return $model->create();
		}
		return false;
	}
}