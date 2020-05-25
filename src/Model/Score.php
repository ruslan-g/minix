<?php

namespace Minix\Model;

class Score extends Model {

	/**
	 * @var string
	 */
	protected $tableName = 'scores';

	protected $primaryKey = 'score_id';

	/**
	 * @var int
	 */
	public $leaderboardId;

	/**
	 * @var int
	 */
	public $userId;

	/**
	 * @var int
	 */
	public $score;

	/**
	 * @param \stdClass
	 */
	public function __construct(\stdClass $score = null) {

		parent::__construct($score);
		if ($score) {
			$this->leaderboardId = $score->LeaderboardId;
			$this->userId = $score->UserId;
			$this->score = $score->Score;
		}
	}

	/**
	 * @return bool
	 */
	public function create() {

		$statement = $this->db->connection->prepare('INSERT INTO ' . $this->tableName . ' SET leaderboard_id = ?, user_id = ?, score = ?');
		return $statement->execute([
			$this->leaderboardId,
			$this->userId,
			$this->score
		]);
	}

	public function update() {

		$statement = $this->db->connection->prepare('UPDATE ' . $this->tableName . ' SET score = ? WHERE leaderboard_id = ? AND user_id = ?');
		return $statement->execute([
			$this->score,
			$this->leaderboardId,
			$this->userId,
		]);
	}

	/**
	 * @param $userId
	 * @return string
	 */
	public function getMaxScore($leaderboardId, $userId) {

		$statement = $this->db->connection->prepare('SELECT MAX(score) FROM ' . $this->tableName . ' WHERE user_id = ? AND leaderboard_id = ?');
		$statement->execute([$userId, $leaderboardId]);
		return (int) $statement->fetchColumn();
	}

	/**
	 * @param $leaderboardId
	 * @param $userId
	 * @param $offset
	 * @param $limit
	 * @return array
	 */
	public function getScores($leaderboardId, $userId, $offset, $limit) {

		$offset = (int) $offset;
		$limit = (int) $limit;
		$statement = $this->db->connection->prepare("SELECT * FROM scores WHERE user_id = ? AND leaderboard_id = ? LIMIT $offset, $limit");
		$statement->execute([$userId, $leaderboardId]);
		return $statement->fetchAll(\PDO::FETCH_CLASS, 'Minix\Model\Score');
	}
}