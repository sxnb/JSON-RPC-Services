<?php
require_once("core/AbstractService.php");
require_once("entity/NoteEntity.php");
require_once("dao/MySQLNoteDAO.php");
require_once("service/errorCodes.php");

/**
 * Class NoteService
 * Responsible for implementing business logic to operate with notes.
 */
class NoteService extends AbstractService {

    /**
     * Initializes the database connection
     */
    public function __construct() {
		$this->_noteDAO = new MySQLNoteDAO();
	}

    /**
     * Creates a new note. The request must be authenticated.
     * @param array $params the parameters from the JSON-RPC request that must contain the note title and content
     * @return bool indicates success
     * @throws Exception if the note could not be created
     */
    public function create(array $params) {
		$userId = $this->getUserId();

		if (!$userId) {
			throw new Exception(MSG_NOT_AUTHORIZED, CODE_NOT_AUTHORIZED);
		}
		if (!isset($params['title'])) {
			throw new Exception(MSG_NOTE_TITLE_MISSING, CODE_NOTE_TITLE_MISSING);
		}
		if (!isset($params['content'])) {
			throw new Exception(MSG_NOTE_CONTENT_MISSING, CODE_NOTE_CONTENT_MISSING);
		}
		
		$noteEntity = new NoteEntity();
		$noteEntity->setOwnerId($userId);
		$noteEntity->setTitle($params['title']);
		$noteEntity->setContent($params['content']);

		return $this->_noteDAO->create($noteEntity);
	}

    /**
     * Retrieves the notes of the authenticated user.
     * @param array $params the parameters from the JSON-RPC request - not used in this method
     * @return array the list of notes belonging to the authenticated user
     * @throws Exception if the request is not authenticated
     */
    public function read(array $params) {
		$userId = $this->getUserId();
		if (!$userId) {
			throw new Exception(MSG_NOT_AUTHORIZED, CODE_NOT_AUTHORIZED);
		}

		return $this->_noteDAO->read($userId);		
	}

    /**
     * Deletes a note. The request must be authenticated and the note must belong to the current user.
     * @param array $params the parameters from the JSON-RPC request that should contain the id parameter
     * @return bool indicates success
     * @throws Exception if the note could not be deleted
     */
    public function delete(array $params) {
		$userId = $this->getUserId();
		if (!$userId) {
			throw new Exception(MSG_NOT_AUTHORIZED, CODE_NOT_AUTHORIZED);
		}
		if (!isset($params['id'])) {
			throw new Exception(MSG_NOTE_ID_MISSING, CODE_NOTE_ID_MISSING);
		}

		$noteEntity = new NoteEntity();
		$noteEntity->setOwnerId($userId);
		$noteEntity->setId($params['id']);
		
		return $this->_noteDAO->delete($noteEntity);		
	}

    // the iNoteDAO object responsible for communicating with the storage
	private $_noteDAO = null;

}