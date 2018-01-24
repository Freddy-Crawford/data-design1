<?php
namespace Edu\Cnm\fcrawforrd\DataDesign;

require_once("autoload.php");
require_once(dirname(__DIR__, 2) . "/vendor/autoload.php");

use Ramsey\Uuid\Uuid;

/**
 * Small Cross Section of a Medium article Comment Message
 *
 * This comment can be considered a small example of what services like Medium store when commemnts are submitted and
 * received using Medium. This can easily be extended to emulate more features of Medium.
 *
 *
 * @author Freddy Crawford <fcrawford@mycnm.edu>
 * @author Dylan McDonald <dmcdonald21@cnm.edu>
 * @version 3.0.0
 **/
class comment implements \JsonSerializable {
	use ValidateDate;
	use ValidateUuid;
	/**
	 * id for this comment; this is the primary key
	 * @var Uuid $commentId
	 **/
	private $commentId;
	/**
	 * id of the Profile that sent this comment; this is a foreign key
	 * @var Uuid $commentProfileId
	 **/
	private $commentProfileId;
	/**
	 * actual textual content of this comment
	 * @var string $tweetContent
	 **/
	private $commentContent;
	/**
	 * date and time this Tweet was sent, in a PHP DateTime object
	 * @var \DateTime $tweetDate
	 **/
	private $commentDate;

	/**
	 * constructor for this comment
	 *
	 * @param string|Uuid $newcommentId id of this comment or null if a new comment
	 * @param string|Uuid $newcommentProfileId id of the Profile that sent this comment
	 * @param string $newcommentContent string containing actual comment data
	 * @param \DateTime|string|null $newcommentDate date and time comment was sent or null if set to current date and time
	 * @throws \InvalidArgumentException if data types are not valid
	 * @throws \RangeException if data values are out of bounds (e.g., strings too long, negative integers)
	 * @throws \TypeError if data types violate type hints
	 * @throws \Exception if some other exception occurs
	 * @Documentation https://php.net/manual/en/language.oop5.decon.php
	 **/
	public function __construct($newCommentId, $newCommentProfileId, string $newCommentContent, $newCommentDate = null) {
		try {
			$this->setTweetId($newCommentId);
			$this->setTweetProfileId($newCommentProfileId);
			$this->setTweetContent($newComentContent);
			$this->setTweetDate($newCommentDate);
		}
			//determine what exception type was thrown
		catch(\InvalidArgumentException | \RangeException | \Exception | \TypeError $exception) {
			$exceptionType = get_class($exception);
			throw(new $exceptionType($exception->getMessage(), 0, $exception));
		}
	}

	/**
	 * accessor method for comment id
	 *
	 * @return Uuid value of comment id
	 **/
	public function getCommentId() : Uuid {
		return($this->commentId);
	}

	/**
	 * mutator method for comment id
	 *
	 * @param Uuid/string $newCommentId new value of comment id
	 * @throws \RangeException if $newCommentId is not positive
	 * @throws \TypeError if $newCommentId is not a uuid or string
	 **/
	public function setCommentId( $newCommentId) : void {
		try {
			$uuid = self::validateUuid($newCommentId);
		} catch(\InvalidArgumentException | \RangeException | \Exception | \TypeError $exception) {
			$exceptionType = get_class($exception);
			throw(new $exceptionType($exception->getMessage(), 0, $exception));
		}

		// convert and store the commnt id
		$this->commentId = $uuid;
	}

	/**
	 * accessor method for comment profile id
	 *
	 * @return Uuid value of comment profile id
	 **/
	public function getCommentProfileId() : Uuid{
		return($this->CommentProfileId);
	}

	/**
	 * mutator method for comment profile id
	 *
	 * @param string | Uuid $newComentProfileId new value of comment profile id
	 * @throws \RangeException if $newCommentId is not positive
	 * @throws \TypeError if $newCommentProfileId is not an integer
	 **/
	public function setCommentProfileId( $newCommentProfileId) : void {
		try {
			$uuid = self::validateUuid($newCommentProfileId);
		} catch(\InvalidArgumentException | \RangeException | \Exception | \TypeError $exception) {
			$exceptionType = get_class($exception);
			throw(new $exceptionType($exception->getMessage(), 0, $exception));
		}

		// convert and store the profile id
		$this->commentProfileId = $uuid;
	}

	/**
	 * accessor method for comment content
	 *
	 * @return string value of comment content
	 **/
	public function getCommentContent() :string {
		return($this->commentContent);
	}

	/**
	 * mutator method for comment content
	 *
	 * @param string $newCommentContent new value of comment content
	 * @throws \InvalidArgumentException if $newCommentContent is not a string or insecure
	 * @throws \RangeException if $newcommentContent is > 1600 characters
	 * @throws \TypeError if $newCommentContent is not a string
	 **/
	public function setCommentContent(string $newCommentContent) : void {
		// verify the comment content is secure
		$newCommentContent = trim($newCommentContent);
		$newCommentContent = filter_var($newCommentContent, FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
		if(empty($newCommentContent) === true) {
			throw(new \InvalidArgumentException("comment content is empty or insecure"));
		}

		// verify the comment content will fit in the database
		if(strlen($newCommentContent) > 1600) {
			throw(new \RangeException("comment content too large"));
		}

		// store the tweet content
		$this->commentContent = $newCommentContent;
	}

	/**
	 * accessor method for comment date
	 *
	 * @return \DateTime value of comment date
	 **/
	public function getCommentDate() : \DateTime {
		return($this->commentDate);
	}

	/**
	 * mutator method for comment date
	 *
	 * @param \DateTime|string|null $newCommentDate comment date as a DateTime object or string (or null to load the current time)
	 * @throws \InvalidArgumentException if $newCommentDate is not a valid object or string
	 * @throws \RangeException if $newCommentDate is a date that does not exist
	 **/
	public function setCommentDate($newCommentDate = null) : void {
		// base case: if the date is null, use the current date and time
		if($newCommentDate === null) {
			$this->CommentDate = new \DateTime();
			return;
		}

		// store the like date using the ValidateDate trait
		try {
			$newCommentDate = self::validateDateTime($newCommentDate);
		} catch(\InvalidArgumentException | \RangeException $exception) {
			$exceptionType = get_class($exception);
			throw(new $exceptionType($exception->getMessage(), 0, $exception));
		}
		$this->commentDate = $newCommentDate;
	}

	/**
	 * inserts this comment into mySQL
	 *
	 * @param \PDO $pdo PDO connection object
	 * @throws \PDOException when mySQL related errors occur
	 * @throws \TypeError if $pdo is not a PDO connection object
	 **/
	public function insert(\PDO $pdo) : void {

		// create query template
		$query = "INSERT INTO comment(commentId,commentProfileId, commentContent, commentDate) VALUES(:commentId, :commentProfileId, :commentContent, :commentDate)";
		$statement = $pdo->prepare($query);

		// bind the member variables to the place holders in the template
		$formattedDate = $this->commentDate->format("Y-m-d H:i:s.u");
		$parameters = ["commentId" => $this->commentId->getBytes(), "commentProfileId" => $this->commentProfileId->getBytes(), "tweetContent" => $this->tweetContent, "tweetDate" => $formattedDate];
		$statement->execute($parameters);
	}


	/**
	 * deletes this comment from mySQL
	 *
	 * @param \PDO $pdo PDO connection object
	 * @throws \PDOException when mySQL related errors occur
	 * @throws \TypeError if $pdo is not a PDO connection object
	 **/
	public function delete(\PDO $pdo) : void {

		// create query template
		$query = "DELETE FROM tweet WHERE tweetId = :tweetId";
		$statement = $pdo->prepare($query);

		// bind the member variables to the place holder in the template
		$parameters = ["tweetId" => $this->tweetId->getBytes()];
		$statement->execute($parameters);
	}

	/**
	 * updates this Tweet in mySQL
	 *
	 * @param \PDO $pdo PDO connection object
	 * @throws \PDOException when mySQL related errors occur
	 * @throws \TypeError if $pdo is not a PDO connection object
	 **/
	public function update(\PDO $pdo) : void {

		// create query template
		$query = "UPDATE comment SET commentProfileId = :commentProfileId, commentContent = :commentContent, commentDate = :commentDate WHERE commentId = :commentId";
		$statement = $pdo->prepare($query);


		$formattedDate = $this->comentDate->format("Y-m-d H:i:s.u");
		$parameters = ["commentId" => $this->commentId->getBytes(),"commentProfileId" => $this->commentProfileId->getBytes(), "commentContent" => $this->commentContent, "commentDate" => $formattedDate];
		$statement->execute($parameters);
	}

	/**
	 * gets the comment by commentId
	 *
	 * @param \PDO $pdo PDO connection object
	 * @param Uuid|string $commentId comment id to search for
	 * @return comment|null Tweet found or null if not found
	 * @throws \PDOException when mySQL related errors occur
	 * @throws \TypeError when a variable are not the correct data type
	 **/
	public static function getCommentByCommentId(\PDO $pdo, $commentId) : ?comment {
		// sanitize the commentId before searching
		try {
			$commentId = self::validateUuid($commentId);
		} catch(\InvalidArgumentException | \RangeException | \Exception | \TypeError $exception) {
			throw(new \PDOException($exception->getMessage(), 0, $exception));
		}

		// create query template
		$query = "SELECT commentId, commentProfileId, commentContent, commentDate FROM tweet WHERE commentId = :commentId";
		$statement = $pdo->prepare($query);

		// bind the comment id to the place holder in the template
		$parameters = ["commentId" => $commentId->getBytes()];
		$statement->execute($parameters);

		// grab the comment from mySQL
		try {
			$comment = null;
			$statement->setFetchMode(\PDO::FETCH_ASSOC);
			$row = $statement->fetch();
			if($row !== false) {
				$comment = new comment($row["commentId"], $row["commentProfileId"], $row["commentContent"], $row["commentDate"]);
			}
		} catch(\Exception $exception) {
			// if the row couldn't be converted, rethrow it
			throw(new \PDOException($exception->getMessage(), 0, $exception));
		}
		return($comment);
	}

	/**
	 * gets the comment by profile id
	 *
	 * @param \PDO $pdo PDO connection object
	 * @param Uuid|string $commentProfileId profile id to search by
	 * @return \SplFixedArray SplFixedArray of comment found
	 * @throws \PDOException when mySQL related errors occur
	 * @throws \TypeError when variables are not the correct data type
	 **/
	public static function getCommentByCommentProfileId(\PDO $pdo, $commentProfileId) : \SplFixedArray {

		try {
			$commentProfileId = self::validateUuid($commentProfileId);
		} catch(\InvalidArgumentException | \RangeException | \Exception | \TypeError $exception) {
			throw(new \PDOException($exception->getMessage(), 0, $exception));
		}

		// create query template
		$query = "SELECT commentId, commentProfileId, commentContent, commentDate FROM comment WHERE commentProfileId = :commentProfileId";
		$statement = $pdo->prepare($query);
		// bind the comment profile id to the place holder in the template
		$parameters = ["commentProfileId" => $commentProfileId->getBytes()];
		$statement->execute($parameters);
		// build an array of comments
		$comments = new \SplFixedArray($statement->rowCount());
		$statement->setFetchMode(\PDO::FETCH_ASSOC);
		while(($row = $statement->fetch()) !== false) {
			try {
				$comment = new comment($row["commentId"], $row["commentProfileId"], $row["commentContent"], $row["commentDate"]);
				$comments[$comments->key()] = $comment;
				$comments->next();
			} catch(\Exception $exception) {
				// if the row couldn't be converted, rethrow it
				throw(new \PDOException($exception->getMessage(), 0, $exception));
			}
		}
		return($comments);
	}

	/**
	 * gets the comment by content
	 *
	 * @param \PDO $pdo PDO connection object
	 * @param string $commentContent comment content to search for
	 * @return \SplFixedArray SplFixedArray of Tweets found
	 * @throws \PDOException when mySQL related errors occur
	 * @throws \TypeError when variables are not the correct data type
	 **/
	public static function getCommentByCommentContent(\PDO $pdo, string $CommentContent) : \SplFixedArray {
		// sanitize the description before searching
		$CommentContent = trim($CommentContent);
		$tweetContent = filter_var($tweetContent, FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
		if(empty($commentContent) === true) {
			throw(new \PDOException("comment content is invalid"));
		}

		// escape any mySQL wild cards
		$commentContent = str_replace("_", "\\_", str_replace("%", "\\%", $commentContent));

		// create query template
		$query = "SELECT commentId, commentProfileId, commentContent, commentDate FROM comment WHERE commentContent LIKE :commentContent";
		$statement = $pdo->prepare($query);

		// bind the comment content to the place holder in the template
		$commentContent = "%$commentContent%";
		$parameters = ["commentContent" => $commentContent];
		$statement->execute($parameters);

		// build an array of comments
		$comments = new \SplFixedArray($statement->rowCount());
		$statement->setFetchMode(\PDO::FETCH_ASSOC);
		while(($row = $statement->fetch()) !== false) {
			try {
				$comment = new comment($row["commentId"], $row["commentProfileId"], $row["commentContent"], $row["commentDate"]);
				$comments[$comments->key()] = $coment;
				$comments->next();
			} catch(\Exception $exception) {
				// if the row couldn't be converted, rethrow it
				throw(new \PDOException($exception->getMessage(), 0, $exception));
			}
		}
		return($comments);
	}

	/**
	 * gets all comments
	 *
	 * @param \PDO $pdo PDO connection object
	 * @return \SplFixedArray SplFixedArray of comments found or null if not found
	 * @throws \PDOException when mySQL related errors occur
	 * @throws \TypeError when variables are not the correct data type
	 **/
	public static function getAllComments(\PDO $pdo) : \SPLFixedArray {
		// create query template
		$query = "SELECT commentId, commentProfileId, commentContent, commentDate FROM comment";
		$statement = $pdo->prepare($query);
		$statement->execute();

		// build an array of comments
		$comments = new \SplFixedArray($statement->rowCount());
		$statement->setFetchMode(\PDO::FETCH_ASSOC);
		while(($row = $statement->fetch()) !== false) {
			try {
				$comment = new comment($row["commentId"], $row["commentProfileId"], $row["commentContent"], $row["commentDate"]);
				$comments[$comments->key()] = $comment;
				$comments->next();
			} catch(\Exception $exception) {
				// if the row couldn't be converted, rethrow it
				throw(new \PDOException($exception->getMessage(), 0, $exception));
			}
		}
		return ($comments);
	}

	/**
	 * formats the state variables for JSON serialization
	 *
	 * @return array resulting state variables to serialize
	 **/
	public function jsonSerialize() : array {
		$fields = get_object_vars($this);

		$fields["commentId"] = $this->commentId->toString();
		$fields["commentProfileId"] = $this->commentProfileId->toString();

		//format the date so that the front end can consume it
		$fields["commentDate"] = round(floatval($this->commentDate->format("U.u")) * 1000);
		return($fields);
	}
}
