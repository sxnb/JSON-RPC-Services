<?php

/**
 * Class RequestEndpoint
 * Handles the JSON-RPC requests. Initializes the service and calls the method indicated in the request.
 */
class RequestEndpoint {

    // Relative path to the folder containing the services
    const SERVICE_PATH = 'service/';

	// Request field names
	const ID = 'id';
	const REQUEST_METHOD = 'method';
    const REQUEST_PARAMS = 'params';
	const JSONRPC = 'jsonrpc';
	const ERROR = 'error';
	const RESULT = 'result';
	const CODE = 'code';
	const MESSAGE = 'message';

    // Request status codes
	const REQUEST_SUCCESS = 200;
	const REQUEST_FAIL = 400;
	const REQUEST_INTERNAL_ERROR = 500;

    // Error codes mentioned in the JSON-RPC documentation
    const PARSE_ERROR = -32700;
    const INVALID_REQUEST = -32600;
    const METHOD_NOT_FOUND = -32601;

    /**
     * Parses the request and dispatches it to the corresponding service.
     * @param $requestString string containing the json-encoded request
     * @return null
     */
    public function handleRequest($requestString) {
	
		set_exception_handler(array($this, 'exceptionHandler'));
	
		$headers = getallheaders();
		$token = $headers['Authorization'];		
	
		$request = json_decode($requestString, true);
		if (!is_array($request)) {
			// the request could not be deserialized properly
			$this->_buildFailResponse(self::PARSE_ERROR, 'Parse error', self::REQUEST_FAIL);
            return;
		}

        // check whether the required parameters were sent
		if ((!isset($request[self::REQUEST_METHOD])) ||
  			(!isset($request[self::JSONRPC]))) {
				// invalid request
				$this->_buildFailResponse(self::INVALID_REQUEST, 'Invalid request', self::REQUEST_FAIL);
                return;
			}
		
		$this->_id = $request[self::ID];
		$this->_jsonrpc = $request[self::JSONRPC];

        // identify the service and the method
		$serviceMethod = explode('.', $request[self::REQUEST_METHOD]);
		$serviceName = $serviceMethod[0];
		$methodName = $serviceMethod[1];
		
		$serviceClassName = $serviceName . 'Service';
		
		$servicePath = self::SERVICE_PATH . $serviceName . '/' . $serviceClassName . '.php';
		if (!file_exists($servicePath)) {
			// invalid service name
			$this->_buildFailResponse(self::METHOD_NOT_FOUND, 'Method not found', self::REQUEST_INTERNAL_ERROR);
            return;
		}

        // load the file containing the service implementation
		require_once($servicePath);

        // retrieve the method parameters from the request
		$params = array();
		if (isset($request[self::REQUEST_PARAMS])) {
			$params = $request[self::REQUEST_PARAMS];	
		}
		
		if (!class_exists($serviceClassName)) {
			// service does not exist
			$this->_buildFailResponse(self::METHOD_NOT_FOUND, 'Method not found', self::REQUEST_INTERNAL_ERROR);
            return;
		}

        // instantiate the service
		$service = new $serviceClassName;
		$service->setUserId($token);
		
		if (!method_exists($service, $methodName)) {
			// method does not exist
			$this->_buildFailResponse(self::METHOD_NOT_FOUND, 'Method not found', self::REQUEST_INTERNAL_ERROR);
            return;
		}

        // try to call the method with the parameters mentioned in the request
		try {
			$answer = $service->$methodName($params);
		} catch(Exception $e) {
			$this->_buildFailResponse($e->getCode(), $e->getMessage(), self::REQUEST_FAIL);
            return;
		}

		$this->_buildSuccessResponse($answer, self::REQUEST_SUCCESS);
	}

    /**
     * Custom handler for uncaught exceptions
     * @param $exception
     */
    public function exceptionHandler($exception) {
		$this->_buildFailResponse($this->_id, $this->_jsonrpc, $exception->getCode(), $exception->getMessage(), self::REQUEST_INTERNAL_ERROR);
	}

    /**
     * Dispatches a fail response.
     *
     * @param $errorCode int the error code
     * @param $errorMessage string the error message
     * @param $status int the response status
     */
    private function _buildFailResponse($errorCode, $errorMessage, $status) {
		header('content-type: text/javascript');
		http_response_code($status);
		$response = array(
			self::ID => $this->_id,
			self::ERROR => array(
				self::CODE => $errorCode,
				self::MESSAGE => $errorMessage
			),
			self::JSONRPC => $this->_jsonrpc
		);
		echo json_encode($response);
	}


    /**
     * Dispatches a success response.
     *
     * @param $result mixed the result of the request
     * @param $status the response status
     */
    private function _buildSuccessResponse($result, $status) {
	
		header('content-type: text/javascript');
		http_response_code($status);
		$response = array(
			self::ID => $this->_id,
			self::RESULT => $result,
			self::JSONRPC => $this->_jsonrpc
		);
		echo json_encode($response);
	}

    // the request Id
	private $_id = null;

    // the request JSON-RPC version
	private $_jsonrpc = null;

}