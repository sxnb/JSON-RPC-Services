JSON-RPC-Services
=================

Server-side code for handling JSON-RPC requests.
The specifications of the JSON-RPC protocol are available here: http://www.jsonrpc.org/specification

## Overview
The backend code is organized in layers, as follows:
* request handler - dispatches requests to the appropriate service and method;
* service layer - implements the desired business logic;
* DAO layer - implements the communication with the storage.

## Content
* the Request handler class and endpoint;
* an Authentication service which exposes three methods: authenticate, deAuthenticate and getUserId;
* a Note service which allows the user to create, retrieve and remove notes;
* tests written in PHPUnit which check the two services mentioned above;
* a simple frontend which allows a user to manage his notes, using Ajax to call methods from these two services;
* SQL file which contain a one time setup script.

## Installation
### Prerequisites
* a web server that supports PHP (e.g. Apache)
* MySQL
* PHPUnit
* cURL

### Steps
* replace the paths in the following files:
	* `frontend/js/requests.js`
	* `backend/config.php`
* replace the MySQL credentials in `backend/config.php`
* execute the MySQL script located at `setup.sql`