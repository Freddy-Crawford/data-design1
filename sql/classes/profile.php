<?php
/**
 * Created by PhpStorm.
 * User: macbookpro
 * Date: 1/19/18
 * Time: 8:32 PM
 */namespace Edu\Cnm\DataDesign;

require_once ("autoloader.php");
require_once (dirname(_DIR_,2) . "/vendor/autoload.php");

use Ramsey\Uuid\Uuid;
/**
 * @author Dylan McDonald <dmcdonald21@cnm.edu>
 * @version 3.0.0
 **/

class profile implements \JsonSerializable {
	use ValidateDate;
	use ValidateUuid;
	/**id for this profile; this is the primary key
	 * @var Uuid $profileId
	 **/
	private $profileId;
	/**
	 * @var Uuid $firstname
	 **/
	private $firstName;
	/**last name of profile user
	 * @var $lastName
	 **/
	private $lastName;
	/**
	 * constructor for this profile
	 * @param string\Uuid $profileId of this profile or null if invalid
	 * @param string\Uuid $firstName attached to profile
	 * @param string\Uuid $lastName attached to profile
	 * @throws \InvalidArgumentException if data types are not valid
	 * @throws \RangeException if data values are out of bounds
	 * @throws \TypeError if data types violate type hints
	 * @throws \Exception if some other exception occurs
	 * @Documentation https://php.net/manual/en/language.oop5.decon.php
	 **/
	public function _construct($profileId,$firstName,$lastName = null) {
		try {
			$this->setprofilId($newprofileId);
			$this->setfirstName($newfrstName);
			$this->setlastName($newlastName);
		}
			catch(\InvalidArgumentException | \ RangeException | \Exception | \TypeError $exception)
			{
				$exceptionType = get_class($exception->getMessage(), 0, $exception));
			}
	}
