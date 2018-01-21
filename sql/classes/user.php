<?php
/**
 * Created by PhpStorm.
 * User: macbookpro
 * Date: 1/19/18
 * Time: 8:33 PM
 */
class User {

	private $userId;
	private $email;
	private $phone;

	function __construct( $userId, $email, $phone ) {
		$this->userId = $userId;
		$this->email = $email;
		$this->phone = $phone;
	}

	function getName() {
		return $this->userId;

	}

	function getemail() {
		return $this->email;


		function getphone() {
			return $this->phone;
		}
	}

}