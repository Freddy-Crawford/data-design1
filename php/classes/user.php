<?php
namespace Edu\Cnm\fcrawford\DataDesign;

require_once("autoload.php");
require_once(dirname(__DIR__, 2) . "/vendor/autoload.php");

use Ramsey\Uuid\Uuid;

/**
 * Small Cross Section of a user profile
 *
 * This user can be considered a small example of what services like Twitter store when messages are sent and
 * received using Twitter. This can easily be extended to emulate more features of Twitter.
 *
 * @author Dylan McDonald <dmcdonald21@cnm.edu>
 * @version 3.0.0
 **/
class user implements \JsonSerializable {
	use ValidateDate;
	use ValidateUuid;
	/**
	 * id for this user; this is the primary key
	 * @var Uuid $userId
	 **/
	private $userId;
	/**
	 * id of the Profile that sent this user; this is a foreign key
	 * @var Uuid $userProfileId
	 **/
	private $userProfileId;
	/**
	 * actual first name of this user
	 * @var string $userFirstName
	 **/
	private $userFirstName;
	/**
	 * last name of the user
	 * @var  $userLastName
	 **/
	private $userLastName;

	/**
	 * constructor for this user
	 *
	 * @param string|Uuid $newUserId id of this user or null if a new user
	 * @param string|Uuid $newUserProfileId id of the Profile that sent this user
	 * @param string|$newUserFirstName string containing actual user first name
	 * @param string|$newUserLastName string containing actual user last name
	 * @throws \InvalidArgumentException if data types are not valid
	 * @throws \RangeException if data values are out of bounds (e.g., strings too long, negative integers)
	 * @throws \TypeError if data types violate type hints
	 * @throws \Exception if some other exception occurs
	 * @Documentation https://php.net/manual/en/language.oop5.decon.php
	 **/
	public function __construct($newUserId, $newUserProfileId, string $newUserFirstName, $newUserLastName = null) {
		try {
			$this->setuserId($newUserId);
			$this->setuserProfileId($newUserProfileId);
			$this->setuserFirstName($newFirstName);
			$this->setuserLastName($newLastName);
		}
			//determine what exception type was thrown
		catch(\InvalidArgumentException | \RangeException | \Exception | \TypeError $exception) {
			$exceptionType = get_class($exception);
			throw(new $exceptionType($exception->getMessage(), 0, $exception));
		}
	}

	/**
	 * accessor method for user id
	 *
	 * @return Uuid value of user id
	 **/
	public function getuserId() : Uuid {
		return($this->userId);
	}

	/**
	 * mutator method for user id
	 *
	 * @param Uuid/string $newUserId new value of user id
	 * @throws \RangeException if $newUserId is not positive
	 * @throws \TypeError if $newUserId is not a uuid or string
	 **/
	public function setUserId( $newUserId) : void {
		try {
			$uuid = self::validateUuid($newUserId);
		} catch(\InvalidArgumentException | \RangeException | \Exception | \TypeError $exception) {
			$exceptionType = get_class($exception);
			throw(new $exceptionType($exception->getMessage(), 0, $exception));
		}

		// convert and store the user id
		$this->userId = $uuid;
	}

	/**
	 * accessor method for user profile id
	 *
	 * @return Uuid value of user profile id
	 **/
	public function getUserProfileId() : Uuid{
		return($this->userProfileId);
	}

	/**
	 * mutator method for user profile id
	 *
	 * @param string | Uuid $newUserProfileId new value of user profile id
	 * @throws \RangeException if $newProfileId is not positive
	 * @throws \TypeError if $newUserProfileId is not an integer
	 **/
	public function setUserProfileId( $newUserProfileId) : void {
		try {
			$uuid = self::validateUuid($newUserProfileId);
		} catch(\InvalidArgumentException | \RangeException | \Exception | \TypeError $exception) {
			$exceptionType = get_class($exception);
			throw(new $exceptionType($exception->getMessage(), 0, $exception));
		}

		// convert and store the profile id
		$this->userProfileId = $uuid;
	}

	/**
	 * accessor method for user first name
	 *
	 * @return string value of user first name
	 **/
	public function getUserFirstName() :string {
		return($this->userFirstName);
	}

	/**
	 * mutator method for user first name
	 *
	 * @param string $newUserFirstName new value of user first name
	 * @throws \InvalidArgumentException if $newUserFirstName is not a string or insecure
	 * @throws \RangeException if $newUserContent is > 140 characters
	 * @throws \TypeError if $newUserFirstName is not a string
	 **/
	public function setUserFirstName(string $newUserFirstName) : void {
		// verify the user first name is secure
		$newuserFirstName = trim($newUserFirstName);
		$newuserFirstName = filter_var($newuserFirstName, FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
		if(empty($newuserFirstName) === true) {
			throw(new \InvalidArgumentException("user content is empty or insecure"));
		}

		// verify the user first name will fit in the database
		if(strlen($newUserFirstName) > 140) {
			throw(new \RangeException("user first name too large"));
		}

		// store the user first name
		$this->userFirstName = $newuserFirstName;
	}
	/**
	 * accessor method for user last name
	 *
	 * @return string value of user last name
	 **/
	public function getUserLastName() :string {
		return($this->userFirstName);
	}

	/**
	 * mutator method for user last name
	 *
	 * @param string $newUserLastName new value of user last name
	 * @throws \InvalidArgumentException if $newUserLastName is not a string or insecure
	 * @throws \RangeException if $newUserLast is > 140 characters
	 * @throws \TypeError if $newUserLastName is not a string
	 **/
	public function setUserLastName(string $newUserLastName) : void {
		// verify the user first name is secure
		$newuserLastName = trim($newUserLastName);
		$newuserLastName = filter_var($newuserLastName, FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
		if(empty($newuserContent) === true) {
			throw(new \InvalidArgumentException("user content is empty or insecure"));
		}

		// verify the user last name will fit in the database
		if(strlen($newUserLastName) > 140) {
			throw(new \RangeException("user last name too large"));
		}

		// store the user first name
		$this->userLastName = $newuserLastName;
	}


	/**
	 * inserts this user into mySQL
	 *
	 * @param \PDO $pdo PDO connection object
	 * @throws \PDOException when mySQL related errors occur
	 * @throws \TypeError if $pdo is not a PDO connection object
	 **/
	public function insert(\PDO $pdo) : void {

		// create query template
		$query = "INSERT INTO user(userId,userProfileId, userContent, userDate) VALUES(:userId, :userProfileId, :userContent, :userDate)";
		$statement = $pdo->prepare($query);

		// bind the member variables to the place holders in the template
		$parameters = ["userId" => $this->userId->getBytes(), "userProfileId" => $this->userProfileId->getBytes(), "userFirstName" => $this->userFirstName, "userLastName" => $this->userLastName];
		$statement->execute($parameters);
	}


	/**
	 * deletes this user from mySQL
	 *
	 * @param \PDO $pdo PDO connection object
	 * @throws \PDOException when mySQL related errors occur
	 * @throws \TypeError if $pdo is not a PDO connection object
	 **/
	public function delete(\PDO $pdo) : void {

		// create query template
		$query = "DELETE FROM user WHERE userId = :userId";
		$statement = $pdo->prepare($query);

		// bind the member variables to the place holder in the template
		$parameters = ["userId" => $this->userId->getBytes()];
		$statement->execute($parameters);
	}

	/**
	 * updates this user in mySQL
	 *
	 * @param \PDO $pdo PDO connection object
	 * @throws \PDOException when mySQL related errors occur
	 * @throws \TypeError if $pdo is not a PDO connection object
	 **/
	public function update(\PDO $pdo) : void {

		// create query template
		$query = "UPDATE user SET userProfileId = :userProfileId, userFirstName = :userFirstName, userLastName = :userLastName WHERE userId = :userId";
		$statement = $pdo->prepare($query);


		$parameters = ["userId" => $this->userId->getBytes(),"userProfileId" => $this->userProfileId->getBytes(), "userFirstName" => $this->userFirstName, "userLastName" => $this->userLastName];
		$statement->execute($parameters);
	}

	/**
	 * gets the user by userId
	 *
	 * @param \PDO $pdo PDO connection object
	 * @param Uuid|string $userId user id to search for
	 * @return user|null user found or null if not found
	 * @throws \PDOException when mySQL related errors occur
	 * @throws \TypeError when a variable are not the correct data type
	 **/
	public static function getuserByuserId(\PDO $pdo, $userId) : ?user {
		// sanitize the userId before searching
		try {
			$userId = self::validateUuid($userId);
		} catch(\InvalidArgumentException | \RangeException | \Exception | \TypeError $exception) {
			throw(new \PDOException($exception->getMessage(), 0, $exception));
		}

		// create query template
		$query = "SELECT userId, userProfileId, userFirstName, userLastName FROM user WHERE userId = :userId";
		$statement = $pdo->prepare($query);

		// bind the user id to the place holder in the template
		$parameters = ["userId" => $userId->getBytes()];
		$statement->execute($parameters);

		// grab the user from mySQL
		try {
			$user = null;
			$statement->setFetchMode(\PDO::FETCH_ASSOC);
			$row = $statement->fetch();
			if($row !== false) {
				$user = new user($row["userId"], $row["userProfileId"], $row["userFirst"], $row["userLastName"]);
			}
		} catch(\Exception $exception) {
			// if the row couldn't be converted, rethrow it
			throw(new \PDOException($exception->getMessage(), 0, $exception));
		}
		return($user);
	}

	/**
	 * gets the user by profile id
	 *
	 * @param \PDO $pdo PDO connection object
	 * @param Uuid|string $userProfileId profile id to search by
	 * @return \SplFixedArray SplFixedArray of users found
	 * @throws \PDOException when mySQL related errors occur
	 * @throws \TypeError when variables are not the correct data type
	 **/
	public static function getuserByuserProfileId(\PDO $pdo, $userProfileId) : \SplFixedArray {

		try {
			$userProfileId = self::validateUuid($userProfileId);
		} catch(\InvalidArgumentException | \RangeException | \Exception | \TypeError $exception) {
			throw(new \PDOException($exception->getMessage(), 0, $exception));
		}

		// create query template
		$query = "SELECT userId, userProfileId, userFirstName, userLastName FROM user WHERE userProfileId = :userProfileId";
		$statement = $pdo->prepare($query);
		// bind the user profile id to the place holder in the template
		$parameters = ["userProfileId" => $userProfileId->getBytes()];
		$statement->execute($parameters);
		// build an array of users
		$users = new \SplFixedArray($statement->rowCount());
		$statement->setFetchMode(\PDO::FETCH_ASSOC);
		while(($row = $statement->fetch()) !== false) {
			try {
				$user = new user($row["userId"], $row["userProfileId"], $row["userFirstName"], $row["userLastName"]);
				$users[$users->key()] = $user;
				$users->next();
			} catch(\Exception $exception) {
				// if the row couldn't be converted, rethrow it
				throw(new \PDOException($exception->getMessage(), 0, $exception));
			}
		}
		return($users);
	}

	/**
	 * gets the user by First name
	 *
	 * @param \PDO $pdo PDO connection object
	 * @param string $userFirstName user first name to search for
	 * @return \SplFixedArray SplFixedArray of users found
	 * @throws \PDOException when mySQL related errors occur
	 * @throws \TypeError when variables are not the correct data type
	 **/
	public static function getuserByuserFirstName(\PDO $pdo, string $userFirstName) : \SplFixedArray {
		// sanitize the description before searching
		$userFirstName = trim($userFirstName);
		$userFirstName = filter_var($userFirstName, FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
		if(empty($userContent) === true) {
			throw(new \PDOException("user content is invalid"));
		}

		// escape any mySQL wild cards
		$userFirstName = str_replace("_", "\\_", str_replace("%", "\\%", $userFirstName));

		// create query template
		$query = "SELECT userId, userProfileId, userFirstName, userLastName FROM user WHERE userFirstName LIKE :userFirstName";
		$statement = $pdo->prepare($query);

		// bind the user firstName to the place holder in the template
		$userFirstName = "%$userFirstName%";
		$parameters = ["userFirstName" => $userFirstName];
		$statement->execute($parameters);

		// build an array of users
		$users = new \SplFixedArray($statement->rowCount());
		$statement->setFetchMode(\PDO::FETCH_ASSOC);
		while(($row = $statement->fetch()) !== false) {
			try {
				$user = new user($row["userId"], $row["userProfileId"], $row["userFirstName"], $row["userLastName"]);
				$users[$users->key()] = $user;
				$users->next();
			} catch(\Exception $exception) {
				// if the row couldn't be converted, rethrow it
				throw(new \PDOException($exception->getMessage(), 0, $exception));
			}
		}
		return($users);
	}

	/**
	 * gets all users
	 *
	 * @param \PDO $pdo PDO connection object
	 * @return \SplFixedArray SplFixedArray of users found or null if not found
	 * @throws \PDOException when mySQL related errors occur
	 * @throws \TypeError when variables are not the correct data type
	 **/
	public static function getAllusers(\PDO $pdo) : \SPLFixedArray {
		// create query template
		$query = "SELECT userId, userProfileId, userFirstName, userLastName FROM user";
		$statement = $pdo->prepare($query);
		$statement->execute();

		// build an array of users
		$users = new \SplFixedArray($statement->rowCount());
		$statement->setFetchMode(\PDO::FETCH_ASSOC);
		while(($row = $statement->fetch()) !== false) {
			try {
				$user = new user($row["userId"], $row["userProfileId"], $row["userFirstName"], $row["userLastName"]);
				$users[$users->key()] = $user;
				$users->next();
			} catch(\Exception $exception) {
				// if the row couldn't be converted, rethrow it
				throw(new \PDOException($exception->getMessage(), 0, $exception));
			}
		}
		return ($users);
	}

	/**
	 * formats the state variables for JSON serialization
	 *
	 * @return array resulting state variables to serialize
	 **/
	public function jsonSerialize() : array {
		$fields = get_object_vars($this);

		$fields["userId"] = $this->userId->toString();
		$fields["userProfileId"] = $this->userProfileId->toString();

		return($fields);
	}
}
