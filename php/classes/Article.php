<?php
namespace Edu\Cnm\DataDesign;

require_once("autoload.php");
require_once(dirname(__DIR__, 2) . "/vendor/autoload.php");

use Ramsey\Uuid\Uuid;

/**
 * Small Cross Section of a Article  on Medium
 *
 * This article can be considered a small example of what services like Medium store when messages are sent and
 * received using Medium. This can easily be extended to emulate more features of Medium.
 *
 * @author Freddy Crawford <fcrawford@cnm.edu>
 * @author Dylan McDonald <dmcdonald21@cnm.edu>
 * @version 3.0.0
 **/
class article implements \JsonSerializable {
	use ValidateDate;
	use ValidateUuid;
	/**
	 * id for this article; this is the primary key
	 * @var Uuid $articleId
	 **/
	private $articleId;
	/**
	 * id of the Profile that sent this article; this is a foreign key
	 * @var Uuid $articleProfileId
	 **/
	private $articleProfileId;
	/**
	 * actual textual content of this article
	 * @var string $articleContent
	 **/
	private $articleContent;
	/**
	 * date and time this article was sent, in a PHP DateTime object
	 * @var \DateTime $articleDate
	 **/
	private $articleDate;

	/**
	 * constructor for this article
	 *
	 * @param string|Uuid $newarticleId id of this article or null if a new article
	 * @param string|Uuid $newarticleProfileId id of the Profile that sent this article
	 * @param string $newarticleContent string containing actual article data
	 * @param \DateTime|string|null $newarticleDate date and time article was sent or null if set to current date and time
	 * @throws \InvalidArgumentException if data types are not valid
	 * @throws \RangeException if data values are out of bounds (e.g., strings too long, negative integers)
	 * @throws \TypeError if data types violate type hints
	 * @throws \Exception if some other exception occurs
	 * @Documentation https://php.net/manual/en/language.oop5.decon.php
	 **/
	public function __construct($newarticleId, $newarticleProfileId, string $newarticleContent, $newarticleDate = null) {
		try {
			$this->setarticleId($newarticleId);
			$this->setarticleProfileId($newarticleProfileId);
			$this->setarticleContent($newarticleContent);
			$this->setarticleDate($newarticleDate);
		}
			//determine what exception type was thrown
		catch(\InvalidArgumentException | \RangeException | \Exception | \TypeError $exception) {
			$exceptionType = get_class($exception);
			throw(new $exceptionType($exception->getMessage(), 0, $exception));
		}
	}

	/**
	 * accessor method for article id
	 *
	 * @return Uuid value of article id
	 **/
	public function getarticleId() : Uuid {
		return($this->articleId);
	}

	/**
	 * mutator method for article id
	 *
	 * @param Uuid/string $newarticleId new value of article id
	 * @throws \RangeException if $newarticleId is not positive
	 * @throws \TypeError if $newarticleId is not a uuid or string
	 **/
	public function setarticleId( $newarticleId) : void {
		try {
			$uuid = self::validateUuid($newarticleId);
		} catch(\InvalidArgumentException | \RangeException | \Exception | \TypeError $exception) {
			$exceptionType = get_class($exception);
			throw(new $exceptionType($exception->getMessage(), 0, $exception));
		}

		// convert and store the article id
		$this->articleId = $uuid;
	}

	/**
	 * accessor method for article profile id
	 *
	 * @return Uuid value of article profile id
	 **/
	public function getarticleProfileId() : Uuid{
		return($this->articleProfileId);
	}

	/**
	 * mutator method for article profile id
	 *
	 * @param string | Uuid $newarticleProfileId new value of article profile id
	 * @throws \RangeException if $newProfileId is not positive
	 * @throws \TypeError if $newarticleProfileId is not an integer
	 **/
	public function setarticleProfileId( $newarticleProfileId) : void {
		try {
			$uuid = self::validateUuid($newarticleProfileId);
		} catch(\InvalidArgumentException | \RangeException | \Exception | \TypeError $exception) {
			$exceptionType = get_class($exception);
			throw(new $exceptionType($exception->getMessage(), 0, $exception));
		}

		// convert and store the profile id
		$this->articleProfileId = $uuid;
	}

	/**
	 * accessor method for article content
	 *
	 * @return string value of article content
	 **/
	public function getarticleContent() :string {
		return($this->articleContent);
	}

	/**
	 * mutator method for article content
	 *
	 * @param string $newarticleContent new value of article content
	 * @throws \InvalidArgumentException if $newarticleContent is not a string or insecure
	 * @throws \RangeException if $newarticleContent is > 140 characters
	 * @throws \TypeError if $newarticleContent is not a string
	 **/
	public function setarticleContent(string $newarticleContent) : void {
		// verify the article content is secure
		$newarticleContent = trim($newarticleContent);
		$newarticleContent = filter_var($newarticleContent, FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
		if(empty($newarticleContent) === true) {
			throw(new \InvalidArgumentException("article content is empty or insecure"));
		}

		// verify the article content will fit in the database
		if(strlen($newarticleContent) > 140) {
			throw(new \RangeException("article content too large"));
		}

		// store the article content
		$this->articleContent = $newarticleContent;
	}

	/**
	 * accessor method for article date
	 *
	 * @return \DateTime value of article date
	 **/
	public function getarticleDate() : \DateTime {
		return($this->articleDate);
	}

	/**
	 * mutator method for article date
	 *
	 * @param \DateTime|string|null $newarticleDate article date as a DateTime object or string (or null to load the current time)
	 * @throws \InvalidArgumentException if $newarticleDate is not a valid object or string
	 * @throws \RangeException if $newarticleDate is a date that does not exist
	 **/
	public function setarticleDate($newarticleDate = null) : void {
		// base case: if the date is null, use the current date and time
		if($newarticleDate === null) {
			$this->articleDate = new \DateTime();
			return;
		}

		// store the like date using the ValidateDate trait
		try {
			$newarticleDate = self::validateDateTime($newarticleDate);
		} catch(\InvalidArgumentException | \RangeException $exception) {
			$exceptionType = get_class($exception);
			throw(new $exceptionType($exception->getMessage(), 0, $exception));
		}
		$this->articleDate = $newarticleDate;
	}

	/**
	 * inserts this article into mySQL
	 *
	 * @param \PDO $pdo PDO connection object
	 * @throws \PDOException when mySQL related errors occur
	 * @throws \TypeError if $pdo is not a PDO connection object
	 **/
	public function insert(\PDO $pdo) : void {

		// create query template
		$query = "INSERT INTO article(articleId,articleProfileId, articleContent, articleDate) VALUES(:articleId, :articleProfileId, :articleContent, :articleDate)";
		$statement = $pdo->prepare($query);

		// bind the member variables to the place holders in the template
		$formattedDate = $this->articleDate->format("Y-m-d H:i:s.u");
		$parameters = ["articleId" => $this->articleId->getBytes(), "articleProfileId" => $this->articleProfileId->getBytes(), "articleContent" => $this->articleContent, "articleDate" => $formattedDate];
		$statement->execute($parameters);
	}


	/**
	 * deletes this article from mySQL
	 *
	 * @param \PDO $pdo PDO connection object
	 * @throws \PDOException when mySQL related errors occur
	 * @throws \TypeError if $pdo is not a PDO connection object
	 **/
	public function delete(\PDO $pdo) : void {

		// create query template
		$query = "DELETE FROM article WHERE articleId = :articleId";
		$statement = $pdo->prepare($query);

		// bind the member variables to the place holder in the template
		$parameters = ["articleId" => $this->articleId->getBytes()];
		$statement->execute($parameters);
	}

	/**
	 * updates this article in mySQL
	 *
	 * @param \PDO $pdo PDO connection object
	 * @throws \PDOException when mySQL related errors occur
	 * @throws \TypeError if $pdo is not a PDO connection object
	 **/
	public function update(\PDO $pdo) : void {

		// create query template
		$query = "UPDATE article SET articleProfileId = :articleProfileId, articleContent = :articleContent, articleDate = :articleDate WHERE articleId = :articleId";
		$statement = $pdo->prepare($query);


		$formattedDate = $this->articleDate->format("Y-m-d H:i:s.u");
		$parameters = ["articleId" => $this->articleId->getBytes(),"articleProfileId" => $this->articleProfileId->getBytes(), "articleContent" => $this->articleContent, "articleDate" => $formattedDate];
		$statement->execute($parameters);
	}

	/**
	 * gets the article by articleId
	 *
	 * @param \PDO $pdo PDO connection object
	 * @param Uuid|string $articleId article id to search for
	 * @return article|null article found or null if not found
	 * @throws \PDOException when mySQL related errors occur
	 * @throws \TypeError when a variable are not the correct data type
	 **/
	public static function getarticleByarticleId(\PDO $pdo, $articleId) : ?article {
		// sanitize the articleId before searching
		try {
			$articleId = self::validateUuid($articleId);
		} catch(\InvalidArgumentException | \RangeException | \Exception | \TypeError $exception) {
			throw(new \PDOException($exception->getMessage(), 0, $exception));
		}

		// create query template
		$query = "SELECT articleId, articleProfileId, articleContent, articleDate FROM article WHERE articleId = :articleId";
		$statement = $pdo->prepare($query);

		// bind the article id to the place holder in the template
		$parameters = ["articleId" => $articleId->getBytes()];
		$statement->execute($parameters);

		// grab the article from mySQL
		try {
			$article = null;
			$statement->setFetchMode(\PDO::FETCH_ASSOC);
			$row = $statement->fetch();
			if($row !== false) {
				$article = new article($row["articleId"], $row["articleProfileId"], $row["articleContent"], $row["articleDate"]);
			}
		} catch(\Exception $exception) {
			// if the row couldn't be converted, rethrow it
			throw(new \PDOException($exception->getMessage(), 0, $exception));
		}
		return($article);
	}

	/**
	 * gets the article by profile id
	 *
	 * @param \PDO $pdo PDO connection object
	 * @param Uuid|string $articleProfileId profile id to search by
	 * @return \SplFixedArray SplFixedArray of articles found
	 * @throws \PDOException when mySQL related errors occur
	 * @throws \TypeError when variables are not the correct data type
	 **/
	public static function getarticleByarticleProfileId(\PDO $pdo, $articleProfileId) : \SplFixedArray {

		try {
			$articleProfileId = self::validateUuid($articleProfileId);
		} catch(\InvalidArgumentException | \RangeException | \Exception | \TypeError $exception) {
			throw(new \PDOException($exception->getMessage(), 0, $exception));
		}

		// create query template
		$query = "SELECT articleId, articleProfileId, articleContent, articleDate FROM article WHERE articleProfileId = :articleProfileId";
		$statement = $pdo->prepare($query);
		// bind the article profile id to the place holder in the template
		$parameters = ["articleProfileId" => $articleProfileId->getBytes()];
		$statement->execute($parameters);
		// build an array of articles
		$articles = new \SplFixedArray($statement->rowCount());
		$statement->setFetchMode(\PDO::FETCH_ASSOC);
		while(($row = $statement->fetch()) !== false) {
			try {
				$article = new article($row["articleId"], $row["articleProfileId"], $row["articleContent"], $row["articleDate"]);
				$articles[$articles->key()] = $article;
				$articles->next();
			} catch(\Exception $exception) {
				// if the row couldn't be converted, rethrow it
				throw(new \PDOException($exception->getMessage(), 0, $exception));
			}
		}
		return($articles);
	}

	/**
	 * gets the article by content
	 *
	 * @param \PDO $pdo PDO connection object
	 * @param string $articleContent article content to search for
	 * @return \SplFixedArray SplFixedArray of articles found
	 * @throws \PDOException when mySQL related errors occur
	 * @throws \TypeError when variables are not the correct data type
	 **/
	public static function getarticleByarticleContent(\PDO $pdo, string $articleContent) : \SplFixedArray {
		// sanitize the description before searching
		$articleContent = trim($articleContent);
		$articleContent = filter_var($articleContent, FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
		if(empty($articleContent) === true) {
			throw(new \PDOException("article content is invalid"));
		}

		// escape any mySQL wild cards
		$articleContent = str_replace("_", "\\_", str_replace("%", "\\%", $articleContent));

		// create query template
		$query = "SELECT articleId, articleProfileId, articleContent, articleDate FROM article WHERE articleContent LIKE :articleContent";
		$statement = $pdo->prepare($query);

		// bind the article content to the place holder in the template
		$articleContent = "%$articleContent%";
		$parameters = ["articleContent" => $articleContent];
		$statement->execute($parameters);

		// build an array of articles
		$articles = new \SplFixedArray($statement->rowCount());
		$statement->setFetchMode(\PDO::FETCH_ASSOC);
		while(($row = $statement->fetch()) !== false) {
			try {
				$article = new article($row["articleId"], $row["articleProfileId"], $row["articleContent"], $row["articleDate"]);
				$articles[$articles->key()] = $article;
				$articles->next();
			} catch(\Exception $exception) {
				// if the row couldn't be converted, rethrow it
				throw(new \PDOException($exception->getMessage(), 0, $exception));
			}
		}
		return($articles);
	}

	/**
	 * gets all articles
	 *
	 * @param \PDO $pdo PDO connection object
	 * @return \SplFixedArray SplFixedArray of articles found or null if not found
	 * @throws \PDOException when mySQL related errors occur
	 * @throws \TypeError when variables are not the correct data type
	 **/
	public static function getAllarticles(\PDO $pdo) : \SPLFixedArray {
		// create query template
		$query = "SELECT articleId, articleProfileId, articleContent, articleDate FROM article";
		$statement = $pdo->prepare($query);
		$statement->execute();

		// build an array of articles
		$articles = new \SplFixedArray($statement->rowCount());
		$statement->setFetchMode(\PDO::FETCH_ASSOC);
		while(($row = $statement->fetch()) !== false) {
			try {
				$article = new article($row["articleId"], $row["articleProfileId"], $row["articleContent"], $row["articleDate"]);
				$articles[$articles->key()] = $article;
				$articles->next();
			} catch(\Exception $exception) {
				// if the row couldn't be converted, rethrow it
				throw(new \PDOException($exception->getMessage(), 0, $exception));
			}
		}
		return ($articles);
	}

	/**
	 * formats the state variables for JSON serialization
	 *
	 * @return array resulting state variables to serialize
	 **/
	public function jsonSerialize() : array {
		$fields = get_object_vars($this);

		$fields["articleId"] = $this->articleId->toString();
		$fields["articleProfileId"] = $this->articleProfileId->toString();

		//format the date so that the front end can consume it
		$fields["articleDate"] = round(floatval($this->articleDate->format("U.u")) * 1000);
		return($fields);
	}
}


























