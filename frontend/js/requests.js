var endpoint = 'http://url/to/project/backend/index.php';

var token = '';

function authenticate() {
	username = document.getElementById("userId").value;
	password = document.getElementById("password").value;

	request = {
				"id": "1",
				"jsonrpc": "2.0",
				"method": "Auth.authenticate",
				"params": {
					"userName": username,
					"password": password
				}
			  };
	
	response = '';

	showLoading('Authenticating...');	
	$.ajax({
		type: "POST",
		url: endpoint,
		data: JSON.stringify(request),
		complete: function(text) {
			response = JSON.parse(text['responseText']);
			hideLoading();
			if (response['result'] == false) {
				alert("Failed to authenticate. Please check your credentials.");
			} else {
				token = response['result'];
				$("#authenticateDiv").hide();
				$("#addNotes").show();
				readNotes();
			}
		}
	});
}

function deAuthenticate() {
	request = {
				"id": "1",
				"jsonrpc": "2.0",
				"method": "Auth.deAuthenticate",
				"params": {
					"token": token
				}
			  };
	
	response = '';
	
	showLoading('Signing out...');
	$.ajax({
		type: "POST",
		url: endpoint,
		data: JSON.stringify(request),
		complete: function(text) {
			response = JSON.parse(text['responseText']);
			hideLoading();
			if (response['result'] == false) {
				alert("Failed to authenticate. Please check your credentials.");
			} else {
				token = response['result'];
				$("#authenticateDiv").show();
				$("#addNotes").hide();
				$("#notes").html("");
			}
		}
	});
}


function readNotes() {
	request = {
				"id": "1",
				"jsonrpc": "2.0",
				"method": "Note.read",
			  };
	
	response = '';
	
	showLoading('Retrieving the notes...');
	$.ajax({
		type: "POST",
		beforeSend: function (request)
		{
			request.setRequestHeader("Authorization", token);
		},
		url: endpoint,
		data: JSON.stringify(request),
		complete: function(text) {
			hideLoading();
			response = JSON.parse(text['responseText']);
			result = response['result'];
			$("#notes").html("");
			result.forEach(function(entry) {
				$("#notes").append('<b>Title: ' + entry['title'] + '</b><br />' + entry['content'] + 
				'<br /><a href="#" onClick="deleteNote(' + entry['id'] + ')">delete</a><br /><br />');
			});

		}
	});

}

function addNote() {
	title = document.getElementById("title").value;
	content = document.getElementById("content").value;

	request = {
				"id": "1",
				"jsonrpc": "2.0",
				"method": "Note.create",
				"params": {
					"title": title,
					"content": content
					}
			  };
	
	response = '';
	
	showLoading('Adding the note...');
	$.ajax({
		type: "POST",
		beforeSend: function (request)
		{
			request.setRequestHeader("Authorization", token);
		},
		url: endpoint,
		data: JSON.stringify(request),
		complete: function(text) {
			hideLoading();
			response = JSON.parse(text['responseText']);
			readNotes();
		}
	});

}

function deleteNote(id) {
    request = {
				"id": "1",
				"jsonrpc": "2.0",
				"method": "Note.delete",
				"params": {
					"id": id
					}
			  };
	
	response = '';
	
	showLoading('Removing the note...');
	$.ajax({
		type: "POST",
		beforeSend: function (request)
		{
			request.setRequestHeader("Authorization", token);
		},
		url: endpoint,
		data: JSON.stringify(request),
		complete: function(text) {
			hideLoading();
			response = JSON.parse(text['responseText']);
			readNotes();
		}
	});

}


function showLoading(text) {
	$("#loading").html('<img src="img/loading.gif" width="48" height="48" /><br />' + text);
	$("#loading").show();
}

function hideLoading() {
	$("#loading").hide();	
}
