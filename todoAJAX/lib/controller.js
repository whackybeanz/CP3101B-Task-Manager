var views = new Array("#view-login", "#view-reg", "#view-about", "#view-allTasks", "#view-addTask", "#view-account");
var navs = new Array("#nav-login", "#nav-reg", "#nav-about-vis", "#nav-allTasks", "#nav-addTask", "#nav-account", "#nav-about-user", "#nav-logout");

function checkLogin(){
	$.getJSON("service/check_login.php", { }, function(reply){	
		if(reply['status'] == 'ok'){
			setUserView();
		} else {
			viewIsVisitor(true);
			showVisitorPage();
		}
	});
}

function setUserView(){
	$.getJSON("service/getAccountDetails.php", { }, function(reply){
		if(reply['status'] == 'ok'){
			viewIsVisitor(false);
			showUserPage();
		
			$("#msg-user").html("Welcome, " + htmlspecialchars(reply['form-data'][0]));
			$("#upd-user").html("<b>Username</b><br/>" + htmlspecialchars(reply['form-data'][0]) + "<br/><br/>");
			$("#upd-bday").val(reply['form-data'][1]);
			$("#upd-email").val(reply['form-data'][2]);
		}
	});
}

function viewIsVisitor(value){
	if(value){
		$("#nav-user").hide();
		$("#msg-user").hide();
		$("#nav-visitor").show();
		$("#msg-visitor").show();
	} else {
		$("#nav-user").show();
		$("#msg-user").show();
		$("#nav-visitor").hide();
		$("#msg-visitor").hide();
	}
}

function showVisitorPage(value){
	hideAllViews();
	defaultViewCSS();
	$(".legend").hide();

	switch(value){
		case "#nav-reg":
			$("#view-reg").show();
			break;
		case "#nav-about-vis":
			$("#view-about").show();
			break;
		default:
			value = "#nav-login";
			$("#view-login").show();
			break;
	}
	
	$(value).css({"background-color": "#EB9700", "color": "#000000"});
}

function hideAllViews(){
	for(var i = 0; i < views.length; i++){
		$(views[i]).hide();
	}
}

function defaultViewCSS(){
	for(var i = 0; i < navs.length; i++){
		$(navs[i]).removeAttr('style');
	}
}

function showUserPage(value){
	hideAllViews();
	defaultViewCSS();
	$(".legend").hide();

	switch(value){
		case "#nav-addTask":
			$("#view-addTask").show();
			setDurationOptions("add-task", 0, 0);
			break;
		case "#nav-account":
			$("#view-account").show();
			break;
		case "#nav-about-user":
			$("#view-about").show();
			break;
		case "#nav-logout":
			logout();
			cleanFormsAndStatus();
			break;
		default:
			value = "#nav-allTasks";
			$("#view-allTasks").show();
			getAllTasks();
			break;
	}
	
	$(value).css({"background-color": "#EB9700", "color": "#000000"});
}

function cleanFormsAndStatus(){
	var div_type = new Array("login", "reg", "upd", "add-task");
	var div_num = new Array(2, 5, 5, 2);

	for(var i = 0; i < div_type.length; i++){
		for(var j = 0; j < div_num[i]; j++){
			$("#" + div_type[i] + "-valid-" + j).html("");
		}
		$("#status-" + div_type[i]).html("");
		$("#form-" + div_type[i])[0].reset();
	}
}

//Substitute for htmlspecialchars in PHP
function htmlspecialchars(html){
	return html.replace(/&/g, "&amp;")
			  .replace(/</g, "&lt;")
			  .replace(/>/g, "&gt;")
			  .replace(/"/g, "&quot;")
			  .replace(/'/g, "&#039;");
}

function getAllTasks(){
	$.getJSON("service/getAllTasks.php", { }, function(reply){
		if(reply['status'] == 'ok'){
			$("#view-taskDetails").html("");
			$("#view-taskDetails").append("<h1>All Tasks</h1><button class='legend-toggle' onclick='showLegend();'>Show / Hide Legend </button><br/><br/>");
			for(var i = 0; i < reply['task-details'].length; i++){
				var html = "<div class='task-container'>";
				html += "<div class='edit-window' id='edit-task-" + reply['task-details'][i]['id'] + "'>";
				
				html += taskEditInfo(reply['task-details'][i]['id'], reply['task-details'][i]['task-name'], reply['task-details'][i]['total-hours'],
									reply['task-details'][i]['total-mins'],	reply['task-details'][i]['notes']);
				
				html += "</div>";
			
				html += "<div class='task-window' id='task-dets-" + reply['task-details'][i]['id'] + "'>";
				
				html += taskNameInfo(reply['task-details'][i]['task-name']);
			
				html += taskIconsInfo(reply['task-details'][i]['id'], reply['task-details'][i]['task-name']);
			
				html += taskDurationInfo(reply['task-details'][i]['total-hours'], reply['task-details'][i]['total-mins'],
										reply['task-details'][i]['units-done'], reply['task-details'][i]['done']);
					
				html += taskSquaresInfo(reply['task-details'][i]['total-units'], reply['task-details'][i]['units-done'], reply['task-details'][i]['id']);
				
				html += taskNotesInfo(reply['task-details'][i]['notes']);
				
				html += "</div>";
				
				html += "</div>";
				
				$("#view-taskDetails").append(html);
			}
			hideEditView();
		}
	});
}

function showLegend(){
	$(".legend").fadeToggle("400");
}

//Generate task editing window
function taskEditInfo(id, task_name, hrs, mins, notes){
	var html = "<h3 class='header-edit'>Editing Task: ";
	
	if(task_name.length > 93){
		html += htmlspecialchars(task_name).substring(0, 92) + "...";
	} else {
		html += htmlspecialchars(task_name);
	}
	html += "</h3>";
	
	html += "<div class='control-panel'>";
	html += "<input title='Discard Changes' type='submit' style=background:url(images/back.png);' onclick='showEditMenu(" + id + ", false); return false;' class='panel-icons' value='' />";
	html += "</div>";
	
	html += "<form id='form-edit-task'>";
	html += "<div class='edit-task-seg1'>";
	html += "<b>Task Name</b><span class='msg-impt'>*</span><div class='validate-input' id='edit-task-name-valid-" + id + "'></div><br/>";
	html += "<input type='text' size='30' maxlength='255' id='edit-task-name-" + id + "' value='" + htmlspecialchars(task_name) + "' /><br/><br/>";
	html += "<input type='submit' onclick='editTask(" + id + "); return false;' value='Save changes'>";
	html += "</div>";
	
	html += "<div class='edit-task-seg2'>";
	html += "<b>Duration</b><span class='msg-impt'>*</span><div class='validate-input' id='edit-task-duration-valid-" + id + "'></div><br/>";
	html += "<select id='edit-task-hrs-" + id + "'>";
	html += setEditHrs(hrs);
	html += "</select> hrs";
	
	html += "<select id='edit-task-mins-" + id + "'>";
	html += setEditMins(mins);
	html += "</select> mins<br/>";
	html += "<span class='msg-impt'>Warning: Changing duration of task will reset progress.</span>";
	html += "</div>";
	
	html += "<div class='edit-task-seg3'>";
	html += "<b>Notes</b><br/>";
	html += "<textarea rows='4' cols='25' class='no-resize' id='edit-task-notes-" + id + "'>" + notes + "</textarea>";
	html += "</div>";
	
	html += "</form>";
	
	return html;
}

function setEditHrs(hrs){
	var html = "";
	
	for(var i = 0; i < 25; i++){
		if(hrs == i){
			html += "<option selected='selected'>" + i + "</option>";
		} else {
			html += "<option>" + i + "</option>";
		}
	}
	return html;
}

function setEditMins(mins){
	var html = "";
	
	if(mins == 0){
		html += "<option selected='selected'>0</option>";
		html += "<option>30</option>";
	} else {
		html += "<option>0</option>";
		html += "<option selected='selected'>30</option>";
	}
	return html;
}

//Task name information
function taskNameInfo(task_name){
	var html = "<h3>";

	if(task_name.length > 107){
		html += htmlspecialchars(task_name).substring(0, 106) + "...";
	} else {
		html += htmlspecialchars(task_name);
	}
	html += "</h3>";
	
	return html;
}

//Generate control panel for tasks
function taskIconsInfo(id, task_name){
	var html = "<div class='control-panel'>";
	html += "<input title='Edit Task' type='submit' style='background:url(images/edit2.png);' onclick='showEditMenu(" + id + ", true); return false;' class='panel-icons' value='' /><br/>";
	html += "<input title='Delete Task' type='submit' style='background:url(images/delete2.png);' onclick='deleteTask(" + id + "); return false;' class='panel-icons' value='' />";
	html += "</div>";
	
	return html;
}

//Task duration information
function taskDurationInfo(total_hrs, total_mins, units_done, task_is_done){
	//Total Duration
	var html = "<div class='task-seg1'><br/>";
	html += "<b>Total Duration:</b> ";
	
	if(total_hrs > 0){
		html += total_hrs + " hrs ";
	} 
	if(total_mins > 0){
		html += total_mins + " mins";
	}
	html += "<br/>";
	
	//Duration done
	var hrs_done = Math.floor(units_done * 30 / 60);
	var mins_done = units_done * 30 % 60;
	
	html += "<b>Duration Done:</b> ";
	
	if(hrs_done > 0){
		html += hrs_done + " hrs ";
	}
	if(mins_done > 0){
		html += mins_done + " mins";
	}
	
	if(hrs_done == 0 && mins_done == 0){
		html += "N/A";
	}
	html += "<br/>";
	
	//Task completion status		
	if(task_is_done == 't'){
		html += "<span class='green-text'><b>Status:</b> Task complete</span>";
	} else {
		html += "<span class='red-text'><b>Status:</b> Task incomplete</span>";
	}
	html += "</div>";
	
	return html;
}

//Task squares information
function taskSquaresInfo(total_units, units_done, id){
	var html = "<div class='task-seg2'>";
	var counter = 0;
	var units_left = total_units - units_done;

	for(var j = 0; j < units_done; j++){
		if(counter % 12 == 0 && counter != 0){
			html += "<br/>";
		}
		html += "<input type='submit' class='green-box' onclick='changeDuration(" + (counter+1) + ", " + id + "); return false;' value='' /> ";
		counter++;
	}
	for(var j = 0; j < units_left; j++){
		if(counter % 12 == 0 && counter != 0){
			html += "<br/>";
		}
		html += "<input type='submit' class='red-box' onclick='changeDuration(" + (counter+1) + ", " + id + "); return false;' value='' /> ";
		counter++;
	}
	html += "</div>";
	
	return html;
}

//Task notes
function taskNotesInfo(notes){
	var html = "<div class='task-seg3'>";
	html += "<b>Notes</b><br/>";
	html += "<textarea rows='4' cols='25' class='no-resize' disabled>" + htmlspecialchars(notes) + "</textarea>";
	html += "</div>";
	
	return html;
}

function deleteTask(id){
	$.getJSON("service/deleteTask.php", { id: id }, function(reply){
		if(reply['status'] == 'ok'){
			deleteFade(id);
			window.setTimeout(
				function(){
					showUserPage();
				}, 300
			);
		}
	});
}

function deleteFade(task_id){
	var task_edit_id_name = "#edit-task-" + task_id;
	var task_dets_id_name = "#task-dets-" + task_id;

	$(task_edit_id_name).fadeOut("300");
	$(task_dets_id_name).fadeOut("300");
}

function editTask(id){
	$.getJSON("service/editTask.php", { id: id, 
								name: $("#edit-task-name-" + id).val(), 
								hrs: $("#edit-task-hrs-" + id).val(), 
								mins: $("#edit-task-mins-" + id).val(),
								notes: $("#edit-task-notes-" + id).val() }, function(reply){
		if(reply['status'] == 'ok'){
			var task_edit_id_name = "#edit-task-" + id;
			var task_dets_id_name = "#task-dets-" + id;
		
			$(task_edit_id_name).hide();
			$(task_dets_id_name).fadeIn("300");
			
			window.setTimeout(
				function(){
					showUserPage();
				}, 300
			);
		} else {
			$("#edit-task-name-valid-" + id).html("<span class='" + reply['status-class'][0] + "'>" + reply['messages'][0] + "</span>");
			$("#edit-task-duration-valid-" + id).html("<span class='" + reply['status-class'][1] + "'>" + reply['messages'][1] + "</span>");
			
			if(reply['messages'][2] != ""){
				alert(reply['messages'][2]);
			}
		}
	});
}

function changeDuration(num_units, task_id){
	$.getJSON("service/changeDuration.php", { num_units: num_units, task_id: task_id }, function(reply){
		if(reply['status'] == 'ok'){
			showUserPage();
			getAllTasks();
		}
	});
}

function setDurationOptions(){
	$("#add-task-hrs").html("");
	$("#add-task-mins").html("");
	
	for(var i = 0; i < 25; i++){
		$("#add-task-hrs").append("<option>" + i + "</option>");
	}
	$("#add-task-mins").append("<option>0</option>");
	$("#add-task-mins").append("<option>30</option>");
}

function hideEditView(){
	$(".edit-window").hide();
}

function showEditMenu(task_id, is_shown){
	var task_edit_id_name = "#edit-task-" + task_id;
	var task_dets_id_name = "#task-dets-" + task_id;
	
	if(is_shown){
		$(task_edit_id_name).fadeIn("400");
		$(task_dets_id_name).hide();
	} else {
		$(task_edit_id_name).hide();
		$(task_dets_id_name).fadeIn("400");
	}
}

function logout(){
	$.getJSON("service/logout.php", { }, function(reply){
		if(reply['status'] == 'ok'){
			viewIsVisitor(true);
			showVisitorPage();
		}
	});
}

$(function(){
	checkLogin();
		
	$("#nav-visitor").on("click", function(nav){
		var view = "#" + nav.target.id;
		showVisitorPage(view);
	});
	
	$("#nav-user").on("click", function(nav){
		viewIsVisitor(false);
		var view = "#" + nav.target.id;
		showUserPage(view);
	});
});