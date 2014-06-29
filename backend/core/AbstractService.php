<?php
require_once("service/auth/AuthService.php");

/**
 * Class AbstractService
 * The base class for the Service classes which contain all the business logic.
 * All services store the id of the user executing the request (if the request is unauthenticated then _userId is null)
 */
abstract class AbstractService {

    /**
     * Sets the userId parameter based on an authorization token.
     *
     * @param $token string the authorization token received from AuthService
     * @return bool indicates success
     */
    public function setUserId($token) {
		if ($token) {
			$authService = new AuthService();
			$this->_userId = $authService->getUserId(array('token' => $token));
            return true;
		} else {
			// unauthenticated request, do nothing
			return false;
		}
	}

    /**
     * Getter of the userId parameter.
     * @return int|null
     */
    public function getUserId() {
		return $this->_userId;
	}

	private $_userId = null;

}