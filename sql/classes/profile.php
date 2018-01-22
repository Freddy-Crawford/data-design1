<?php
/**
 * Typical profile for a blog sharing site
 *
 * this profile is an example of date collected from a blogging site that allows for sharing of writings.
 * This can be extended to include name , phone number , and email address
 *
 * @author Freddy Crawford <fcrawford@cnm.edu>
 **/
class Profile {
	/**
	 * id for this profile; this is the primary key
	 **/
	private $profileId;
	/**
	 * first name for the person who ons this profile
	 **/
	private $firstName;
	/**
	 * last name of the person who owns the profile
	 **/
	private $lastName;

	/**
	 * accessor method for profileId
	 *
	 * @return value of profile id
	 **/
	public function getprofileId() {
		return ($this->profileId);
	}

	/**
	 * mutator method for profile id
	 *
	 * @param int $newprofileId new value of profile id
	 * @throws UnexpectedValueException if $newprofileId is not an integer
	 **/
	/**
	 * @return mixed
	 */
	public function setprofileId($newprofileId) {
		//verify the profile id is valid
		$newprofileId = filter_has_var($newprofileId, FILTER_VALIDATE_INT);
		if($newprofileId === false) {
			throw(new UnexpectedValueException("profile id is not a valid integer"));
		}
		//convert and store the profile id
		$this->profileId = intval($newprofileId);
	}

	/**
	 * accessor method for firstName
	 * @return value of firstName
	 **/
	public function getfirstName() {
		return ($this->firstName);
	}

	/**
	 * mutator method for last name
	 *
	 * @param int $newlastName new value of last name
	 * @throws UnexpectedValueException if $newlastName is not an string
	 **/
	/**
	 * @return mixed
	 */
	public function setlastName($newfirstName) {
		//verify the first name is valid
		$newfirstName = filter_has_str($newfirstName, FILTER_VALIDATE_STR);
		if($newfirstName === false) {
			throw(new UnexpectedValueException("first name is not a valid string"));
		}
		//convert and store the first name
		$this->firstName = strval($newfirstName);
	}

	/**
	 * accessor method for lastName
	 * @return value of lastName
	 **/
	public function getlastName() {
		return ($this->lastName);
	}
		/**
		 * mutator method for last name
		 *
		 * @param int $newlastName new value of last name
		 * @throws UnexpectedValueException if $newlastName is not an string
		 **/
		/**
		 * @return mixed
		 */
		public function setlastName($newlastName) {
			//verify the last name is valid
			$newlastName = filter_has_str($newlastName, FILTER_VALIDATE_STR);
			if($newlastName === false) {
				throw(new UnexpectedValueException("last name is not a valid string"));
			}
		  		//convert and store the last name
		   		$this->lastName = strval($newlastName);
			}
}
?>