<?php
require_once("core/MySQLDAO.php");

/**
 * Class MySQLAuthDAO
 * Class responsible for interacting with MySQL in order to provide authentication functionality.
 */
class MySQLAuthDAO extends MySQLDAO {
	
	const TABLE_USERS = 'user';
	const TABLE_TOKEN = 'token';

    /**
     * Authenticates a user and generates an authentication token.
     *
     * @param $userId
     * @param $password
     * @return string|null the authentication token to be used in further communication
     */
    public function authenticate($userId, $password) {
		$query = $this->getDBConnection()->prepare(
            'SELECT * FROM ' . self::TABLE_USERS . ' WHERE username=? and password=?');
				
		$query->bind_param('is', $userId, md5(SALT . $password));
		$query->execute();

		$result = $query->get_result();
		$user = $result->fetch_assoc();
		if (!$user) {
			return null;
		}
		
		$userId = $user['id'];
		$token = bin2hex(openssl_random_pseudo_bytes(32));
		
		$query = $this->getDBConnection()->prepare(
            'INSERT INTO ' . self::TABLE_TOKEN . ' (userId, token) VALUES (?, ?)');
				
		$query->bind_param('is', $userId, $token);
		$query->execute();
		$query->close();
				
		return $token;
	}

    /**
     * Logs out a user given an authentication token.
     *
     * @param $token
     * @return bool indicates success
     */
    public function deAuthenticate($token) {
		$query = $this->getDBConnection()->prepare(
            'DELETE FROM ' . self::TABLE_TOKEN . ' WHERE token=?');

		$query->bind_param('s', $token);
		$queryResponse = $query->execute();
		$query->close();
		
		if (!$queryResponse) {
			return false;
		}

		return true;
	}

    /**
     * Retrieves the userId based on a authentication token
     * @param $token
     * @return int|null the userId
     */
    public function getUserId($token) {
		
		$query = $this->getDBConnection()->prepare('SELECT * FROM ' . self::TABLE_TOKEN .
				' WHERE token=?');
				
		$query->bind_param('s', $token);
		
		$query->execute();

		$result = $query->get_result();
		$user = $result->fetch_assoc();
		if (!$user) {
			return null;
		}
		
		$userId = $user['userId'];
		
		return $userId;
	}

}