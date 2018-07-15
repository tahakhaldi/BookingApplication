<?php
// Initialize the session
session_start();

// If session variable is not set it will redirect to login page
if(!isset($_SESSION['username']) || empty($_SESSION['username'])){
  header("location:../index.php");
  exit;
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8"/>
<title>MediBook Clinic Application</title>
<link rel="icon" href="../favicon.png"/>
<link rel="stylesheet" href='../fullcalendar.min.css' />
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css" />
<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" />
<link rel="stylesheet" href='//maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap.min.css' />
<link rel="stylesheet" href='../bootstrap-datetimepicker.min.css' />
<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.css">
<script src='../lib/moment.min.js'></script>
<script src='../lib/jquery.min.js'></script>
<script src='../lib/jquery-ui.min.js'></script>
<script src='../fullcalendar.min.js'></script>
<script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.2/js/bootstrap.min.js"></script>
<script src='../lib/bootstrap-datetimepicker.min.js'></script>
<script src='../lib/jquery.ui-contextmenu.min.js'></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.js"></script>
<script>

	$(document).ready(function() {

		$('#calendar').fullCalendar({
    	customButtons: {
    	    createBooking: {
    	      text: 'New booking',
    	      click: function() {
    	    	$(':input').val('');
    	    	$('#bookingStartTime').val($.fullCalendar.formatDate(moment(), "Y-MM-DD HH:mm")); 
    	      	$('#bookingModal').modal('show');
    	      }
    	    },
    	    logoutSession: {
      	      text: 'Logout',
      	      click: function() {
  				$.confirm({
					theme: 'light',
				    title: 'Logout',
				    content: 'Are you sure you want to logout from your current session?',
				    type: 'red',
				    buttons: {
				        confirmdelete: {
				            text: 'Logout',
				            btnClass: 'btn-red',
				            keys: ['enter'],
				            action: function(){
				            	window.location.href = "../login/logout.php";
				            }
				        },
				        cancel: function () {
				        	return true; 	   
				        }
				    }
			    });
      	      }
      	    }
    	},	
      	header: {
        	left: 'prev,next today createBooking',
        	center: 'title',
        	right: 'month,agendaWeek,listWeek logoutSession'
      	},
      	defaultDate: moment(),
      	events: 'php/get-events.php',
      	eventRender: function(event, element) {
      		element.attr('data-event-id', event.id);
      		element.attr('data-event-firstname', event.firstname);
      		element.attr('data-event-lastname', event.lastname);
      		element.attr('data-event-reason', event.reason);
      		element.attr('data-event-physician', event.physician);
      		element.attr('data-event-age', event.age);
      		element.attr('data-event-gender', event.gender);
      		element.attr('data-event-color', event.color);
      		element.attr('data-event-start', $.fullCalendar.formatDate(event.start, "Y-MM-DD HH:mm"));
      		element.attr('data-event-end', $.fullCalendar.formatDate(event.end, "Y-MM-DD HH:mm"));
            element[0].className += ' eventmenu';
            var physician_name = event.physician.split('_');
            $(element).tooltip({title: "<div align='left'><b>Patient Full Name: </b>"+event.firstname+" "+event.lastname+"<br/><b>Patient Gender: </b>"+event.gender+"<br/><b>Physician Name: </b>"+"Dr. "+capitalizeFirstLetter(physician_name[0])+" "+capitalizeFirstLetter(physician_name[1])+"</div>", container:'body', placement:'right', html:true});
   	  	},
   	 	dayRender: function(day, cell) {
   	   		cell.attr('data-day-id', day.format());
   	   		cell.attr('data-day-start', $.fullCalendar.formatDate(day, "Y-MM-DD HH:mm"));
        	cell[0].className += ' daymenu';
 		},
        eventClick: function(event) {
			$('#patientId').val(event.id); 
			$('#patientFirstName').val(event.firstname); 
			$('#patientLastName').val(event.lastname);
			$('#patientReasonofVisit').val(event.reason); 
			$('#bookingDoctor').val(event.physician); 
			$('#patientBirth').val(event.age);
			$('#patientGender').val(event.gender);
			$('#bookingStartTime').val($.fullCalendar.formatDate(event.start, "Y-MM-DD HH:mm")); 
			$('#bookingEndTime').val($.fullCalendar.formatDate(event.end, "Y-MM-DD HH:mm")); 
        	$('#bookingModal').modal('show');
       	}  	  
    });

	$('#calendar').contextmenu({
		delegate: ".daymenu, .eventmenu",
		menu: [],
		select: function(event, ui) {
			if (ui.cmd == "edit") {
				var event_id = ui.target.closest(".eventmenu").attr("data-event-id");
				$('#patientId').val(event_id); 
				var event_firstname = ui.target.closest(".eventmenu").attr("data-event-firstname");
				$('#patientFirstName').val(event_firstname); 
				var event_lastname = ui.target.closest(".eventmenu").attr("data-event-lastname");
				$('#patientLastName').val(event_lastname); 
				var event_reason = ui.target.closest(".eventmenu").attr("data-event-reason");
				$('#patientReasonofVisit').val(event_reason); 
				var event_physician = ui.target.closest(".eventmenu").attr("data-event-physician");
				$('#bookingDoctor').val(event_physician);
				var event_age = ui.target.closest(".eventmenu").attr("data-event-age");
				$('#patientBirth').val(event_age);
				var event_gender = ui.target.closest(".eventmenu").attr("data-event-gender");
				$('#patientGender').val(event_gender); 
				var event_start = ui.target.closest(".eventmenu").attr("data-event-start");
				$('#bookingStartTime').val(event_start); 
				var event_end = ui.target.closest(".eventmenu").attr("data-event-end");
				$('#bookingEndTime').val(event_end); 
				$('#bookingModal').modal('show');
			} else if (ui.cmd == "new") {
				$(':input').val('');
				var day_start = ui.target.closest(".daymenu").attr("data-day-start");
				$('#bookingStartTime').val(day_start); 
				$('#bookingModal').modal('show');
			} else if (ui.cmd == "delete") {
				var event_id = ui.target.closest(".eventmenu").attr("data-event-id");
				$.confirm({
					theme: 'light',
				    title: 'Delete Booking',
				    content: 'Are you sure you want to delete this booking?',
				    type: 'red',
				    buttons: {
				        confirmdelete: {
				            text: 'Delete',
				            btnClass: 'btn-red',
				            keys: ['enter'],
				            action: function(){
				             	$.ajax({
				             		type: "POST",
				             		url: "delete_event.php",
				             		data: "&id=" + event_id,
				             		success: function(json) {
				             			$('#calendar').fullCalendar('removeEvents', event_id);
				             			$.alert('Booking Successfully Deleted');
				             		}
				             	});
				            }
				        },
				        cancel: function () {
				        	return true; 	   
				        }
				    }
			    }); 	
			} else if (ui.cmd == "approve") {
				var event_id = ui.target.closest(".eventmenu").attr("data-event-id");
				$.confirm({
					theme: 'light',
				    title: 'Approve Booking',
				    content: 'Are you sure you want to approve this booking?',
				    type: 'green',
				    buttons: {
				        confirmdelete: {
				            text: 'Approve',
				            btnClass: 'btn-green',
				            keys: ['enter'],
				            action: function(){
				             	$.ajax({
				             		type: "POST",
				             		url: "approve_event.php",
				             		data: "&id=" + event_id,
				             		success: function(json) {
				             			location.reload();
				             		}
				             	});
				            }
				        },
				        cancel: function () {
				        	return true; 	   
				        }
				    }
			    }); 	
			}
		},
		beforeOpen: function (event, ui) {
			var event_color = ui.target.closest(".eventmenu").attr("data-event-color");
			if( ui.target.closest(".eventmenu").length !== 0  && (event_color == "#31CD73")) {
                $("#calendar").contextmenu("replaceMenu", [               	
                	{title: "Edit Booking", cmd: "edit", uiIcon: "ui-icon-pencil"},
                	{title: "Delete Booking", cmd: "delete", uiIcon: "ui-icon-closethick"}
                 ]);
            } 
			else if( ui.target.closest(".eventmenu").length !== 0 ) {
                $("#calendar").contextmenu("replaceMenu", [               	
                	{title: "Edit Booking", cmd: "edit", uiIcon: "ui-icon-pencil"},
                	{title: "Delete Booking", cmd: "delete", uiIcon: "ui-icon-closethick"},
                	{title: "Approve Booking", cmd: "approve", uiIcon: "ui-icon-check"}
                 ]);
            } else if ( ui.target.closest(".daymenu").length !== 0 ) {
                $("#calendar").contextmenu("replaceMenu", [
                	{title: "New Booking", cmd: "new", uiIcon: "ui-icon-calendar"}
                ]);
            }    	    	
		}
	});

	$("#save_changes").click(function(e) {
	    e.preventDefault();	    
	    var event_id = $('#patientId').val();
	    var event_first_name = capitalizeFirstLetter($('#patientFirstName').val()); 
	    var event_last_name = capitalizeFirstLetter($('#patientLastName').val()); 
	    var event_reason = $('#patientReasonofVisit').val();
	    var event_physician_name = $('#bookingDoctor').val();
	    var event_birth = $('#patientBirth').val();
	    var event_gender = $('#patientGender').val();	    
		var event_start_date = $('#bookingStartTime').val();
		var event_end_date = $('#bookingEndTime').val();
		if (new Date(event_birth).getFullYear() >= new Date().getFullYear()-18) {
			$('#patientBirthDiv').addClass('has-error');
			$('#patientBirthDiv').append("<span class='help-block'>"+"Patient must be at least 18 years of age!"+'</span>');
			return;
		} else if (new Date(event_birth).getFullYear() < new Date().getFullYear()-99) {
			$('#patientBirthDiv').addClass('has-error');
			$('#patientBirthDiv').append("<span class='help-block'>"+"Patient cannot be over 100 years of age!"+'</span>');
			return;
		} else if (new Date(event_start_date).getTime() >= new Date(event_end_date).getTime()) {
			$('#bookingEndTimeDiv').addClass('has-error');
			$('#bookingEndTimeDiv').append("<span class='help-block'>"+"Booking End Date must be after Start Date!"+'</span>');
			return;
		} else if (bookingTimeLapseViolation(event_start_date, event_end_date)) {	
			$('#bookingEndTimeDiv').addClass('has-error');
			$('#bookingEndTimeDiv').append("<span class='help-block'>"+"Maximum booking time cannot be over 30 minutes!"+'</span>');	
			return;
		}
		var emptyTextBoxes = $('input:text').filter(function() { return $(this).val() == ""; });
		if(emptyTextBoxes.length !== 0 || ($('#bookingDoctor option:selected').text() == "") || ($('#patientGender option:selected').text() == "")){
		    emptyTextBoxes.each(function() {
		        $('#'+this.id+'Div').addClass('has-error');
		        $('#'+this.id+'Div').append("<span class='help-block'>"+"Cannot be empty!"+'</span>');	
		    });	
		    if($('#patientGender option:selected').text() == ""){
		    	$('#patientGenderDiv').addClass('has-error');  
		    	$('#patientGenderDiv').append("<span class='help-block'>"+"Cannot be empty!"+'</span>');	  
		    }
		    if($('#bookingDoctor option:selected').text() == ""){
		    	$('#bookingDoctorDiv').addClass('has-error'); 
		    	$('#bookingDoctorDiv').append("<span class='help-block'>"+"Cannot be empty!"+'</span>');	   
		    }
		    return;			       
		}
    	$.ajax({
 	   		url: 'save_events.php',
 	   		data: 'id='+event_id+'&firstname='+event_first_name+'&lastname='+event_last_name+'&reason='+event_reason+'&physician='+event_physician_name+'&age='+event_birth+'&gender='+event_gender+'&start='+event_start_date+'&end='+event_end_date,
 	   		type: "POST",
 	   		success: function(json) {
 	   			$('#bookingModal').modal('hide');
 	   			location.reload();
 	   		}
    	});
	});

	$('#bookingModal').on('hidden.bs.modal', function () { 
		$(".has-error").find('span.help-block').remove();  
		$(".has-error").removeClass("has-error"); 
		$(this).find("input,textarea,select").val('').end();
	});

  });

</script>
<style>

  body {
    background: url(../background2.jpg);
    background-repeat: no-repeat;
    background-size: 100% 100%;
    margin: 40px 10px;
    padding: 0;
    font-family: "Lucida Grande",Helvetica,Arial,Verdana,sans-serif;
    font-size: 14px;
  }

  #calendar {
    max-width: 900px;
    margin: 0 auto;
  }
  
  #block_container
  {
    text-align:center;
  }
  
  #bloc1, #bloc2
  {
    display:inline;
  }
  
  .fc h2 {
    font-size: 20px;
  }
  
  img {
    opacity: 1.5;
    filter: alpha(opacity=50); /* For IE8 and earlier */
  }
  
  .required:after { content:" *" ;color:red  }
  
  .tooltip > .tooltip-inner {color: #000; background-color: #fff;}

</style>
</head>
<body>
	
    <center><h3 style="color:#002B70"><b>MediBook Clinic Application</b></h3></center></br>
    <div id='calendar'></div></br>
    <div id="block_container">
        <div id="bloc1"><i class="fa fa-circle" style="font-size:20px;color:#31CD73; padding-right: 10px;"></i>Approved</div>  
        <div id="bloc2"><i class="fa fa-circle" style="font-size:20px;color:#3A87AD; padding-left: 10px; ; padding-right: 10px;"></i>Pending</div>   
    </div>
    
    <!-- Modal -->
    <div id="bookingModal" class="modal fade" role="dialog">
      <div class="modal-dialog">
    
        <!-- Modal content-->
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 class="modal-title">Booking Form</h4>
          </div>
          <div class="modal-body">
          	<input type="hidden" id="patientId"/>             	  
          	<label for="patientFirstName" class="required">Patient Full Name</label>
            <div class="row">           
                <div class='col-md-6'>
                    <div id="patientFirstNameDiv" class="form-group">
                        <input type="text" class="form-control form-control-sm" style="text-transform: capitalize;" id="patientFirstName" placeholder="First name"/>
                    </div>
                </div>
                <div class='col-md-6'>
                    <div id="patientLastNameDiv" class="form-group">
                        <input type="text" class="form-control form-control-sm" style="text-transform: capitalize;" id="patientLastName" placeholder="Last name"/>
                    </div>
                    <!-- 
                    <div class="form-group has-error">
                        <input type="text" class="form-control form-control-sm" style="text-transform: capitalize;" id="patientLastName" placeholder="Last name"/>
                   		<span class="help-block">Please correct the error</span>
                    </div>   
                    -->                 
                </div>
            </div>                             
                     	
            <div class="row">           
                <div class='col-md-6'>
                    <div id="patientBirthDiv" class="form-group">
                    	<label for="patientBirth" class="required">Patient Date of Birth</label>
                        <div class='input-group date'>
                            <input type='text' id='patientBirth' class="form-control" placeholder="YYYY-MM-DD"/>
                            <span class="input-group-addon"></span>
                        </div>
                    </div>
                </div>
                <div class='col-md-6'>
                    <div id="patientGenderDiv" class="form-group">
                    	<label for="patientGender" class="required">Patient Gender</label>
                    	<select class="form-control form-control-sm" id="patientGender">
                          	<option>Male</option>
                            <option>Female</option>
                      </select>
                    </div>
                </div>
            </div> 
                               
            <div id="bookingDoctorDiv" class="form-group">
                <label for="bookingDoctor" class="required">Physician Name</label>
                <select class="form-control form-control-sm" id="bookingDoctor">
                  <option value="david_warkentin">Dr. David Warkentin</option>
                  <option value="bruce_hoffman">Dr. Bruce Hoffman</option>
                  <option value="michael_omidi">Dr. Michael Omidi</option>
                  <option value="james_ojjeh">Dr. James Ojjeh</option>
                  <option value="sadir_alrawi">Dr. Sadir Alrawi</option>
                </select>
            </div>
            
            <div id="patientReasonofVisitDiv" class="form-group">
            	<label for="patientReasonofVisit">Reason of visit</label>
            	<textarea rows="2" class="form-control" id="patientReasonofVisit" placeholder="Please Explain."></textarea>
            </div>              
                   
            <div class="row">           
                <div class='col-md-6'>
                    <div id="bookingStartTimeDiv" class="form-group">
                    	<label for="bookingStartTime" class="required">Start Date/Time</label>
                        <div class='input-group date'>
                            <input type='text' id='bookingStartTime' class="form-control" placeholder="YYYY-MM-DD HH:mm"/>
                            <span class="input-group-addon"></span>
                        </div>
                    </div>
                </div>
                <div class='col-md-6'>
                    <div id="bookingEndTimeDiv" class="form-group">
                    	<label for="bookingEndTime" class="required">End Date/Time</label>
                        <div class='input-group date' >
                            <input type='text' id='bookingEndTime' class="form-control" placeholder="YYYY-MM-DD HH:mm"/>
                            <span class="input-group-addon"></span>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="row">          
                <div align='right' style='padding-right: 20px;'>
                    <font style='font-size:18px;' color="red">* </font>Required Fields
                </div>
            </div>
            
            <script type="text/javascript">
                $(function () {
                	$('#patientBirth').datetimepicker({
                    	format: 'Y-MM-DD',
                    	defaultDate: "2000-01-01"
                    });
                    $('#bookingStartTime').datetimepicker({
                    	format: 'Y-MM-DD HH:mm',
                    	stepping: 30
                    });
                    $('#bookingEndTime').datetimepicker({
                    	format: 'Y-MM-DD HH:mm',
                    	stepping: 30,
                        useCurrent: false //Important! See issue #1075
                    });
                    $("#bookingStartTime").on("dp.change", function (e) {
                        $('#bookingEndTime').data("DateTimePicker").minDate(e.date);
                    });
                    $("#bookingEndTime").on("dp.change", function (e) {
                        $('#bookingStartTime').data("DateTimePicker").maxDate(e.date);
                    });
                });
            </script>                
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            <button type="button" id= "save_changes" class="btn btn-primary">Save changes</button>
          </div>
        </div>  
      </div>
    </div> 
     
</body>
</html> 
<script>
    function capitalizeFirstLetter(string) {
        return string.charAt(0).toUpperCase() + string.slice(1);
    }
    function bookingTimeLapseViolation(start, end) {
        if((Math.floor((new Date(end) - new Date(start)) / 86400000) >= 1)
            || (Math.floor(((new Date(end) - new Date(start)) % 86400000) / 3600000) >= 1)
               || (Math.round((((new Date(end) - new Date(start)) % 86400000) % 3600000) / 60000) > 30)){
        	return true;
        } else {
            return false;
        }
    }
</script>