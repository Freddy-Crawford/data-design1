<?php
namespace Edu\Cnm\DataDesign;

require_once("autoload.php");
require_once(dirname(__Dir__,2) . "/vendor/autoload.php");

use Ramsey\Uuid\Uuid;
/**
 * Cross Section of a Medium article
 *
 * This is an example of what it looks like when a user submits an article to blog sharing site Medium
 *
 * @author Freddy Crawford <fcrawford@cnm.edu>
 * @Dylan McDonald <dmcdonald21@cnm.edu>
 * @version 3.0.0
 **/
class article implements \JsonSerializable {
	use ValidateDate;
	use ValidateUuid;
	/**
	 * id is for this article; this is the primary  key
	 * @var Uuid $articleId
	 **/
	private $articleId;
	/**
	 *id of the profile that wrote the article; this is the foreign key
	 * @var Uuid $articleProfileId
	 **/
	private $articleProfileId;
	/**
	 * actual textual content of the article
	 * @var string $articleContent
	 **/
	private $articleContent;
	/**
	 * date and time article was submitted, in a php DateTime object
	 * @var \DateTime $articleDate
	 **/
	private $articleDate;

	/**
	 * constructor for this article
	 *
	 * @param string|Uuid $newArticleId of this article or null if a new article
	 * @param string|Uuid $newArticleProfileId id of the profile that wrote the article
	 * @param string $newArticleContent string containing actual article data
	 * @param \DateTime|string|null $newArticleDate dae and time article was submitted or null if set to current date and time
	 * @throws \InvalidArgumentException if data types are not valid
	 * @throws \RangeException if data values are out of bounds (e.g. , strings too long, negative integers)
	 * @throws \TypeError if data types violate type hints
	 * @throws \exception if some other exception occurs
	 * @Documentation https://php.net/manual/en/language.ooop5.decon.php
	 **/
	public function __construct($newArticleId, $newArticleProfileId, string $newArticleContent, $newArticleDate =
	null) {
		try {
			$this->setArticleId($newArticleId);
			$this->setArticleProfileId($newArticleProfileId);
			$this->setArticleContent($newArticleContent);
			$this->setArticleDate($newArticleDate);
		} //determine what exception type was thrown
		catch(\InvalidArgumentException | \RangeException | \Exception | \TypeError $exception) {
			$exceptionType = get_class($exception);
			throw(new $exceptionType($exception->getmessage(), 0, $exception));
		}
	}
	/**
	 * accessor method for article id
	 *
	 * @return Uuid value of article id
	 **/
	public function getArticleId() : Uuid {
		return ($this->ArticleId);
	}
	/** mutator method for article id
	 *
	 * @param Uuid/string $newArticleId new value of article id
	 * @throws \RangeException if $newArticleId is not positive
	 * @throws \TypeError if $newArticleId is not a Uuid or string
	 **/
	public function setArticleId( $newArticleId) : void {
		try {
			$uuid = self:: validateUuid($newArticleId);
		} catch(\InvalidArgumentException | \RangeException | \Exception | \TypeError $exception) {
			$exceptionType = get_class($exception);
			throw(new $exceptionType($exception->getmessage(), 0, $exception));
		}
		// convert and store the article id
		$this->articleId = $uuid;
		}

	/**
	 *accessor method for article profile id
	 *
	 * @return Uuid value of article profile id
	 **/
	public function getarticleProfileId() : Uuid{
		return($this->articleProfileId);
	}

	/**
	 * mutator method for article profile id
	 *
	 * @param string | Uuid $newArticleProfileId new value of article profile id
	 * @throws \RangeException if $newArticleProfileId is not positive
	 * @throws \TypeError if $newArticleProfileId is not an integer
	 **/
	public function setArticleProfileId( $newArticleProfileId) : void {
		try {
			$uuid = self::validateUuid($newArticleProfileId);
		} catch(\InvalidArgumentException | \RangeException | \Exception | \TypeError $exception) {
			$exceptionType = get_class(exception);
			throw(new $exceptionType($exception->getmessage(), 0, $exception));
		}
		// convert and store the profile id
		$this->ArticleProfileId = $uuid;
	}


	/**
	 * accessor method for Article content
	 *
	 * @return string value of article content
	 **/
	public function getArticleContent() :string {
		return($thisArticleContent);
	}

	/**
	 * mutator method for article content
	 *
	 * @param string $newArticleContent new value of article content
	 * @throws \InvalidArgumentException if $newArticleContent is not a string or insecure
	 * @throws \RangeException if $newArticleContent is >1600  characters
	 * @throws \TypeError if $newarticleContent is not a string
	 **/
	public function setArticleContent(string $newArticleContent) : void {
		// verify the article content is secure
		$newArticleContent = trim($newArticleContent);
		$newArticleContent = filter_var($newArticleContent, FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
		if(empty($newArticleContent) === true) {
			throw(new \InvalidArgumentException("article content is empty or unsecure"));
		}
		// verify the article content will fit in the database
		if(strlen($newArticleContent) > 1600) {
			throw(new \RangeException("article content too large"));
		}

		// store the article content
		$this->ArticleContent = $newArticleContent;
	}

	/**
	 * mutator method for article date
	 *
	 * @param \DateTime|string|null $newArticleDate article date as a DateTime object or string (or null to load the current time)
	 * @throws \InvalidArgumentException if $newArticleDate is not a valid object or string
	 * @throws \RangeException if $newArticleDate is a date does not exist
	 **/
	public function setArticleDate($newArticleDate = null) : void {
		// base case: if the date is null, use the current date and time
		if($newArticleDate === null) {
			$this->ArticleDate = new \DateTime();
			return;
		}

		// store the like date using the ValidateDate trait
		try {
			$newArticleDate = self::validateDateTime($newArticleDate);
		} catch(\InvalidArgumentException | \RangeException $exception) {
			$exceptionType = get_class($exception);
			throw(new $exceptionType($exception->getmessage(), 0, $exception));
		}
		$this->ArticleDate = $newArticleDate;
	}

	/**
	 * inserts this tweet into mySQL
	 *
	 * @param \PDO $pdo PDO connection object
	 * @throws \PDOException when mySQL related errors occur
	 * @throws \TypeError if $pdo is not a PDO connection object
	 **/
public function insert(\PDO $pdo) : void {


			// create query template
	$query = "INSERT INTO article(articleId,articleProfileId, articleContent, ArticleDate) VALUES(:articleId, :articleProfileId, :articleContent, :articleDate);
$statement = $pdo->prepare($query);
		
		
		// bind the member variables to the place holders in the template