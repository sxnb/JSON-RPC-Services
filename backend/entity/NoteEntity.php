<?php
require_once("core/AbstractEntity.php");

/**
 * Class NoteEntity
 * Contains the fields of a note, as well as setters and getters.
 */
class NoteEntity extends AbstractEntity {
	
	public function getId() {
		return $this->_id;
	}

	public function setId($id) {
		$this->_id = $id;
	}

	public function getOwnerId() {
		return $this->_ownerId;
	}

	public function setOwnerId($ownerId) {
		$this->_ownerId = $ownerId;
	}

	public function getTitle() {
		return $this->_title;
	}

	public function setTitle($title) {
		$this->_title = $title;
	}

	public function getContent() {
		return $this->_content;
	}

	public function setContent($content) {
		$this->_content = $content;
	}

	public function getDateCreated() {
		return $this->_dateCreated;
	}

	public function setDateCreated($dateCreated) {
		$this->_dateCreated = $dateCreated;
	}

    // the note fields
	private $_id;
	private $_ownerId;
	private $_title;
	private $_content;
	private $_dateCreated;

	/* 
	 * Array with the field names that need to be serialized
	 */
	protected $_fields = array('id', 'ownerId', 'title', 'content', 'dateCreated');

}