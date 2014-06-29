<?php
require_once("../config.php");
set_include_path(get_include_path() . PATH_SEPARATOR . $path);

/**
 * Class NoteTest
 * Tests the methods implemented by the Authentication Service and Note Service
 */
class NoteTest extends PHPUnit_Framework_TestCase {

    /**
     * Performs a JSON-RPC request
     *
     * @param $payload array the body of the request
     * @param null $token string the authentication token, which may be missing
     * @return array the response of the request
     */
    public function makeRequest($payload, $token = null) {
	    $curl = curl_init();
		
		curl_setopt($curl, CURLOPT_URL, REQUEST_ENDPOINT_ADDRESS);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_POST, 1);
		curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($payload));
		curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: text/plain')); 
		if ($token) {
			curl_setopt($curl, CURLOPT_HTTPHEADER, array('Authorization: ' . $token));
		}
		
		$result = curl_exec($curl);
		$response = array('status' => curl_getinfo($curl, CURLINFO_HTTP_CODE), 'payload' => json_decode($result, true));
		curl_close($curl);
		return $response;

	}

    /**
     * This test attempts to create a note with an unauthorized request
     */
    public function testNoteUnauthorized() {

		$requestCreate = array(
			'jsonrpc' => '2.0',
			'id' => '1',
			'method' => 'Note.create',
			'params' => array(
							'title' => 'testNote',
							'content' => 'testNoteContent'
						)
			);
					
		$response = $this->makeRequest($requestCreate);
		$this->assertArrayHasKey('status', $response);
		$this->assertArrayHasKey('payload', $response);		

		// check whether the request returned a status 400 (ERROR)
		$this->assertEquals(400, $response['status']);
		$payload = $response['payload'];

		$this->assertArrayHasKey('id', $payload);
		$this->assertArrayHasKey('jsonrpc', $payload);
		$this->assertArrayHasKey('error', $payload);
		$this->assertArrayHasKey('message', $payload['error']);
		$this->assertEquals('1', $payload['id']);
		$this->assertEquals('2.0', $payload['jsonrpc']);
		$this->assertEquals('Not authorized', $payload['error']['message']);

    }

    /**
     * This test performs the following steps and verifies the output of each:
     * - authenticates the test user
     * - creates a note
     * - reads the notes
     * - deletes the note
     * - deauthenticates the test user
     */
    public function testNote() {
		$request = array(
			'jsonrpc' => '2.0',
			'id' => '2',
			'method' => 'Auth.authenticate',
			'params' => array(
							'userName' => 'test',
							'password' => 'password'
						)
			);
					
		$response = $this->makeRequest($request);
		
		$this->assertArrayHasKey('status', $response);
		$this->assertArrayHasKey('payload', $response);		

		// check whether the request returned a status 200 (SUCCESS)
		$this->assertEquals(200, $response['status']);
		$payload = $response['payload'];
		
		$this->assertArrayHasKey('id', $payload);
		$this->assertArrayHasKey('jsonrpc', $payload);
		$this->assertArrayHasKey('result', $payload);
		$this->assertEquals('2', $payload['id']);
		$this->assertEquals('2.0', $payload['jsonrpc']);

		$this->assertEquals(64, strlen($payload['result']));
		$token = $payload['result'];
		
		// create a note

        $requestCreate = array(
            'jsonrpc' => '2.0',
            'id' => '3',
            'method' => 'Note.create',
            'params' => array(
                'title' => 'testNote',
                'content' => 'testNoteContent'
            )
        );

        $response = $this->makeRequest($requestCreate, $token);
		$this->assertArrayHasKey('status', $response);
		$this->assertArrayHasKey('payload', $response);		

		// check whether the request returned a status 200 (SUCCESS)
		$this->assertEquals(200, $response['status']);

		$payload = $response['payload'];
		$this->assertArrayHasKey('id', $payload);
		$this->assertArrayHasKey('jsonrpc', $payload);
		$this->assertArrayHasKey('result', $payload);
		$this->assertEquals('3', $payload['id']);
		$this->assertEquals('2.0', $payload['jsonrpc']);
		$this->assertEquals(true, $payload['result']);

		// read the notes and check whether they were added
		$requestCreate = array(
			'jsonrpc' => '2.0',
			'id' => '4',
			'method' => 'Note.read'
		);

		$response = $this->makeRequest($requestCreate, $token);
		$this->assertArrayHasKey('status', $response);
		$this->assertArrayHasKey('payload', $response);		

		// check whether the request returned a status 200 (SUCCESS)
		$this->assertEquals(200, $response['status']);

		$payload = $response['payload'];
		$this->assertArrayHasKey('id', $payload);
		$this->assertArrayHasKey('jsonrpc', $payload);
		$this->assertArrayHasKey('result', $payload);
		$this->assertEquals('4', $payload['id']);
		$this->assertEquals('2.0', $payload['jsonrpc']);
		$this->assertArrayHasKey(0, $payload['result']);
		
		$note = $payload['result'][0];
		$this->assertArrayHasKey('id', $note);
		$this->assertArrayHasKey('ownerId', $note);
		$this->assertArrayHasKey('title', $note);
		$this->assertArrayHasKey('content', $note);
		$this->assertArrayHasKey('dateCreated', $note);

		$this->assertEquals('testNote', $note['title']);
		$this->assertEquals('testNoteContent', $note['content']);

		$noteId = $note['id'];

		// delete the note
		$request = array(
			'jsonrpc' => '2.0',
			'id' => '5',
			'method' => 'Note.delete',
			'params' => array(
					'id' => $noteId
				)
			);

		$response = $this->makeRequest($request, $token);

		$this->assertArrayHasKey('status', $response);
		$this->assertArrayHasKey('payload', $response);		

		// check whether the request returned a status 200 (SUCCESS)
		$this->assertEquals(200, $response['status']);

		$payload = $response['payload'];
		$this->assertArrayHasKey('id', $payload);
		$this->assertArrayHasKey('jsonrpc', $payload);
		$this->assertArrayHasKey('result', $payload);
		$this->assertEquals('5', $payload['id']);
		$this->assertEquals('2.0', $payload['jsonrpc']);
		$this->assertEquals(true, $payload['result']);

		// finally, deAuthenticate
		$request = array(
			'jsonrpc' => '2.0',
			'id' => '6',
			'method' => 'Auth.deAuthenticate',
			'params' => array(
					'token' => $token
				)
			);
					
		$response = $this->makeRequest($request);
		$this->assertArrayHasKey('status', $response);
		$this->assertArrayHasKey('payload', $response);		

		// check whether the request returned a status 200 (SUCCESS)
		$this->assertEquals(200, $response['status']);

		$payload = $response['payload'];
		$this->assertArrayHasKey('id', $payload);
		$this->assertArrayHasKey('jsonrpc', $payload);
		$this->assertArrayHasKey('result', $payload);
		$this->assertEquals('6', $payload['id']);
		$this->assertEquals('2.0', $payload['jsonrpc']);
		$this->assertEquals(true, $payload['result']);
			
	}

}