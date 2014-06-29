<?php
require_once("core/AbstractService.php");
require_once("dao/MySQLAuthDAO.php");
require_once("service/errorCodes.php");

/**
 * Class AuthService
 * Responsible for authenticating/deauthenticating users.
 */
class AuthService extends AbstractService {

    /**
     * Initializes the Data Access Object
     */
    public function __construct() {
		$this->_authDAO = new MySQLAuthDAO();
	}

    /**
     * Authenticates a user and returns an authentication token.
     * @param array $params the parameters from the JSON-RPC request that must contain the userName and password
     * @return null|string the authentication token to be used in further communication
     * @throws Exception if one of the required fields was not provided
     */
    public function authenticate(array $params) {
		if ((!isset($params['userName'])) || (!isset($params['password']))) {
			throw new Exception(MSG_INVALID_CREDENTIALS, CODE_INVALID_CREDENTIALS);
		}
		return $this->_authDAO->authenticate($params['userName'], $params['password']);
	}

    /**
     * Deauthenticates a user.
     * @param array $params the parameters from the JSON-RPC request that must contain the token
     * @return bool indicates success
     * @throws Exception if the token was not provided in the request
     */
    public function deAuthenticate(array $params) {
		if (!isset($params['token'])) {
			throw new Exception(MSG_INVALID_CREDENTIALS, CODE_INVALID_CREDENTIALS);
		}
		return $this->_authDAO->deAuthenticate($params['token']);
	}

    /**
     * Retrieves the id of a user given an authentication token.
     * @param array $params the parameters from the JSON-RPC request that must contain the token
     * @return int|null the userId
     * @throws Exception if the token was not provided in the request
     */
    public function getUserId(array $params) {

		if (!isset($params['token'])) {
			throw new Exception(MSG_INVALID_CREDENTIALS, CODE_INVALID_CREDENTIALS);
		}

		return $this->_authDAO->getUserId($params['token']);

	}

    // the MySQLAuthDAO object responsible for communicating with the database
	private $_authDAO = null;

}