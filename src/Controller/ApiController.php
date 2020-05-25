<?php
namespace Minix\Controller;

use Minix\Application;
use Minix\Model\MaxScore;
use Minix\Model\Score;
use Minix\Model\Transaction;
use Minix\Request;
use Minix\Response;
use Minix\Service\Transaction as TransactionService;

class ApiController extends BaseController {

	/**
	 * @return bool
	 */
	public function timestampAction() {

		$data = [
			'Timestamp' => time()
		];

		echo $this->response->jsonResponse($data);
		return true;
	}

	/**
	 * @return bool
	 * @throws \Exception
	 */
	public function transactionAction() {

		$request = Application::getInstance()->getRequest();
		$transactionRaw = json_decode($request->raw());
		$verifier = $transactionRaw->Verifier;
		// @todo validate + move to service
		$transaction = new TransactionService();
		$this->validateRequired($transactionRaw, ['Verifier', 'TransactionId', 'CurrencyAmount', 'UserId']);
		$this->validateInteger('TransactionId', $transactionRaw->TransactionId);
		$this->validateInteger('CurrencyAmount', $transactionRaw->CurrencyAmount);
		$this->validateInteger('UserId', $transactionRaw->UserId);

		if (!$transaction->verify(new Transaction($transactionRaw), $verifier)) {
			echo $this->response
				->setError(true)
				->setErrorMessage('Wrong params')
				->jsonResponse();
		} else {
			if ($transaction->create(new Transaction($transactionRaw))) {
				echo $this->response->jsonResponse(['Success' => true]);
			} else {
				echo $this->response
					->setError(true)
					->setErrorMessage('Transaction exists')
					->jsonResponse(['Success' => true]);
			}
		}

		return true;
	}

	public function transactionStatsAction() {

		$request = Application::getInstance()->getRequest();

		$raw = json_decode($request->raw());
		$userId = (int) $raw->UserId;
		$model = new Transaction();

		echo $this->response->jsonResponse([
			'UserId' => $userId,
			'TransactionCount' => $model->getCountByUserId($userId),
			'CurrencySum' => $model->getAmountSummaryByUserId($userId)
		]);


		return true;
	}

	public function scorePostAction() {

		$request = Application::getInstance()->getRequest();

		// @todo validate + move to service
		$raw = json_decode($request->raw());
		$this->validateRequired($raw, ['UserId', 'LeaderboardId', 'Score']);
		$this->validateInteger('UserId', $raw->UserId);
		$this->validateInteger('LeaderboardId', $raw->LeaderboardId);
		$this->validateInteger('Score', $raw->Score);
		$userId = (int) $raw->UserId;
		$scoreModel = new Score($raw);
		$maxScoreModel = new MaxScore($raw);
		$maxScore = $scoreModel->getMaxScore($raw->LeaderboardId, $raw->UserId);
		if (!$maxScore || $maxScore < $raw->Score) {
			$scoreModel->create();
			if (!$maxScore) {
				$maxScoreModel->create();
			} else {
				$maxScoreModel->update();
			}
			$maxScore = $raw->Score;
		}

		$score = $maxScoreModel->getMaxScoreAndRank($raw->LeaderboardId, $userId);

		echo $this->response->jsonResponse([
			'UserId' => $userId,
			'LeaderboardId' => $raw->LeaderboardId,
			'Score' => $maxScore,
			'Rank' => (int) $score->rank
		]);


		return true;
	}

	public function leaderboardGetAction() {

		$request = Application::getInstance()->getRequest();

		// @todo validate + move to service
		$raw = json_decode($request->raw());

		$this->validateRequired($raw, ['UserId', 'LeaderboardId', 'Offset', 'Limit']);
		$this->validateInteger('UserId', $raw->UserId);
		$this->validateInteger('LeaderboardId', $raw->LeaderboardId);
		$this->validateInteger('Offset', $raw->Offset);
		$this->validateInteger('Limit', $raw->Limit);

		$userId = (int) $raw->UserId;
		$leaderboardId = (int) $raw->LeaderboardId;
		$offset = (int) $raw->Offset;
		$limit = (int) $raw->Limit;
		$maxScoreModel = new MaxScore();
		$maxScoreAndRank = $maxScoreModel->getMaxScoreAndRank($raw->LeaderboardId, $userId);

		$scoreModel = new Score();
		$scores = $scoreModel->getScores($leaderboardId, $userId, $offset, $limit);
		$result = [
			'UserId' => $userId,
			'LeaderboardId' => $leaderboardId,
			'Score' => $maxScoreAndRank->max_score,
			'Rank' => $maxScoreAndRank->rank,
		];
		foreach ($scores as $score) {
			/* @var \Minix\Model\Score $score */
			$result['Entries'][] = [
				'UserId' => $userId,
				'Score' => $score->score,
				'Rank' => $maxScoreAndRank->rank
			];
		}
		echo $this->response->jsonResponse($result);


		return true;
	}

	public function userSaveAction() {

		$request = Application::getInstance()->getRequest();

		// @todo validate + move to service
		$raw = json_decode($request->raw());

		// @todo move to DB
		$userId = (int) $raw->UserId;
		$data = $raw->Data;
		$client = new \MongoDB\Client("mongodb://localhost:27017");
		$collection = $client->selectCollection('local', 'user');

		$properties = get_object_vars($data);
		foreach ($properties as $key => $dataValue) {
			$cursor = $collection->find(['user_id' => $userId, "data.$key" => ['$exists' => true]]);

			if (!count($cursor->toArray())) {
				$document = [
					'user_id' => $userId,
					'data' => [$key => $dataValue]
				];
				$collection->insertOne($document);
			} else {
				$collection->updateOne(['user_id' => $userId, "data.$key" => ['$exists' => true]], ['$set' => ["data.$key" => $dataValue]]);
			}
		}

		echo $this->response->jsonResponse(['Success' => true]);


		return true;
	}

	/**
	 * @return bool
	 * @throws \Exception
	 */
	public function userLoadAction() {

		$request = Application::getInstance()->getRequest();

		// @todo validate + move to service

		$raw = json_decode($request->raw());

		// @todo move to DB
		$userId = (int) $raw->UserId;
		$client = new \MongoDB\Client("mongodb://localhost:27017");
		$collection = $client->selectCollection('local', 'user');

		$cursor = $collection->find(['user_id' => $userId]);
		$object = new \stdClass();
		foreach($cursor as $document) {

			$attributes = get_object_vars($document->data);
			//var_dump($attributes);

			foreach ($attributes as $key => $value) {
				$object->$key = $value;
			}

		}
		echo $this->response->jsonResponse($object);


		return true;
	}
}