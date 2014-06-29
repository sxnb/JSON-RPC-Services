<?php
require_once("config.php");

/**
 * Class AbstractDAO
 * This class will be extended by each class representing a Data Access Object that will use MySQL for storage.
 */
abstract class MySQLDAO {

    /**
     * Initializes the database connection
     */
    public function __construct() {
        $dbConnection = new mysqli(MYSQL_HOST, MYSQL_USER, MYSQL_PASSWORD, MYSQL_DBNAME);
        if($dbConnection->connect_errno > 0){
            throw new Exception($dbConnection->connect_error, $dbConnection->connect_errno);
        }
        $this->setDBConnection($dbConnection);
    }

    /**
     * This method retrieves the database connection object.
     * @return mixed
     */
    public function getDBConnection() {
		return $this->_dbConnection;
	}

    /**
     * This method sets the database connection object.
     * @param $connection
     */
    public function setDBConnection($connection) {
		$this->_dbConnection = $connection;
	}

    /**
     * The database connection object
     * @var mixed
     */
    private $_dbConnection = null;

}