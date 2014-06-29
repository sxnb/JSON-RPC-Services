<?php
require_once("entity/NoteEntity.php");

/**
 * Interface iNoteDAO
 * Interface to be implemented by every Note Data Access Object. Offers an abstractization over
 * different database implementations (e.g. MySQL, Elasticsearch)
 */
interface iNoteDAO {

    /**
     * Inserts into the database an entity of type Note
     * @param NoteEntity $note
     * @return bool indicates success
     */
    public function create(NoteEntity $note);

    /**
     * Retrieves from the database the list of notes belonging to a user.
     * @param $ownerId int the id of the user
     * @return array the list of notes
     */
    public function read($ownerId);

    /**
     * Deletes a note from the database.
     * @param NoteEntity $note
     * @return bool indicates success
     */
    public function delete(NoteEntity $note);

}