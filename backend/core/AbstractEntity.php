<?php
/**
 * Class AbstractEntity
 * This class will be extended by every class implementing an object of type Entity.
 */
abstract class AbstractEntity implements jsonSerializable {

    /**
     * This method is responsible for serializing the entity. We need to be able to serialize
     * the entities in order to send them to the client as a JSON-RPC response.
     * @return array|mixed
     */
    function jsonSerialize() {
		$objectData = array();
		foreach ($this->_fields as $field) {
			$getterName = 'get' . ucfirst($field);
			$objectData[$field] = $this->$getterName();
		}
		return $objectData;
	}

}