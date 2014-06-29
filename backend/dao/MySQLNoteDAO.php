<?php
require_once("entity/NoteEntity.php");
require_once("dao/iNoteDAO.php");
require_once("core/MySQLDAO.php");

/**
 * Class MySQLNoteDAO
 * Class providing access to the MySQL storage for notes
 */
class MySQLNoteDAO extends MySQLDAO implements iNoteDAO {

    // the name of the table that stores the notes
	const TABLE_NAME = 'note';

    /**
     * Inserts into the database an entity of type Note
     * @param NoteEntity $note
     * @throws Exception if the note could not be created
     * @return bool indicates success
     */
    public function create(NoteEntity $note) {
		
		$query = $this->getDBConnection()->prepare(
                'INSERT INTO ' . self::TABLE_NAME . '(ownerId, title, content, createdDate)
				 VALUES (?, ?, ?, CURRENT_TIMESTAMP)');

		$query->bind_param('iss', $note->getOwnerId(), $note->getTitle(), $note->getContent());
		$result = $query->execute();

		if (!$result) {
			throw new Exception($this->getDBConnection()->connect_error, $this->getDBConnection()->connect_errno);	
		}
		
		$query->close();
				
		return true;
	}

    /**
     * Retrieves from the database the list of notes belonging to a user.
     * @param $ownerId int the id of the user
     * @throws Exception if the list of notes could not be retrieved
     * @return array the list of notes
     */
    public function read($ownerId) {

		$query = $this->getDBConnection()->prepare(
                'SELECT id, title, content, createdDate FROM ' . self::TABLE_NAME .
				 ' WHERE ownerId=? AND deleted=0 ORDER BY createdDate DESC');

		$query->bind_param('i', $ownerId);
		$execute = $query->execute();
		if (!$execute) {
			throw new Exception($this->getDBConnection()->connect_error, $this->getDBConnection()->connect_errno);	
		}
		
		$result = $query->get_result();

		$arrayOfNotes = array();
		
	    while ($entry = $result->fetch_assoc()) {
			$noteEntity = new NoteEntity();
			$noteEntity->setId($entry['id']);
			$noteEntity->setOwnerId($ownerId);
			$noteEntity->setTitle($entry['title']);
			$noteEntity->setContent($entry['content']);
			$noteEntity->setDateCreated($entry['createdDate']);

			$arrayOfNotes[] = $noteEntity;
		}
		
		$query->close();

		return $arrayOfNotes;
		
	}

    /**
     * Deletes a note from the database.
     * @param NoteEntity $note
     * @throws Exception if the note could not be deleted
     * @return bool indicates success
     */
    public function delete(NoteEntity $note) {

		$query = $this->getDBConnection()->prepare(
                'UPDATE ' . self::TABLE_NAME . ' SET deleted=1 WHERE (id=? AND ownerId=?)');

		$query->bind_param('ii', $note->getId(), $note->getOwnerId());
		$result = $query->execute();

		if (!$result) {
			throw new Exception($this->getDBConnection()->connect_error, $this->getDBConnection()->connect_errno);	
		}
		
		$query->close();

		return true;
		
	}

}