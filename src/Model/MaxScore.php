<?php
namespace Minix\Model;

class MaxScore extends Model {

	/**
	 * @var string
	 */
	protected $tableName = 'max_scores';

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
	public $maxScore;

	/**
	 * @param \stdClass
	 */
	public function __construct(\stdClass $score = null) {

		parent::__construct($score);
		if ($score) {
			$this->leaderboardId = $score->LeaderboardId;
			$this->userId = $score->UserId;
			$this->maxScore = $score->Score;
		}
	}

	/**
	 * @return bool
	 */
	public function create() {

		$statement = $this->db->connection->prepare('INSERT INTO ' . $this->tableName . ' SET leaderboard_id = ?, user_id = ?, max_score = ?');
		return $statement->execute([
			$this->leaderboardId,
			$this->userId,
			$this->maxScore
		]);
	}

	public function update() {

		$statement = $this->db->connection->prepare('UPDATE ' . $this->tableName . ' SET max_score = ? WHERE leaderboard_id = ? AND user_id = ?');
		return $statement->execute([
			$this->maxScore,
			$this->leaderboardId,
			$this->userId,
		]);
	}

	/**
	 * @param $userId
	 * @return string
	 */
	public function getMaxScore($leaderboardId, $userId) {

		$statement = $this->db->connection->prepare('SELECT max_score FROM ' . $this->tableName . ' WHERE user_id = ? AND leaderboard_id = ?');
		$statement->execute([$userId, $leaderboardId]);
		return (int) $statement->fetchColumn();
	}

	/**
	 * @param $leaderboardId
	 * @param $userId
	 * @return mixed
	 */
	public function getMaxScoreAndRank($leaderboardId, $userId) {

		$statement = $this->db->connection->prepare('
			SELECT max_score, FIND_IN_SET(max_score, (
				SELECT GROUP_CONCAT(max_score ORDER BY max_score DESC ) FROM '. $this->tableName .' )
			) AS rank
			FROM '. $this->tableName .'
			WHERE user_id =  ? AND leaderboard_id = ?');
		$statement->execute([$userId, $leaderboardId]);
		$row = $statement->fetchObject();
		return $row;
	}
}