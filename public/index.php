<!DOCTYPE html>
<html>
<head>
<meta charset='utf-8' />
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
    	    	$('#bookingStartTime').val($.fullCalendar.formatDate(moment(), "Y-MM-DD HH:mm:ss")); 
    	      	$('#bookingModal').modal('show');
    	      }
    	    },
    	    logoutSession: {
      	      text: 'Logout',
      	      click: function() {
      	      	//
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
      		element.attr('data-event-start', $.fullCalendar.formatDate(event.start, "Y-MM-DD HH:mm:ss"));
      		element.attr('data-event-end', $.fullCalendar.formatDate(event.end, "Y-MM-DD HH:mm:ss"));
            element[0].className += ' eventmenu';
            $(element).tooltip({title: "<div align='left'><b>Patient Full Name: </b>"+event.firstname+" "+event.lastname+"<br/><b>Patient Gender: </b>"+event.gender+"<br/><b>Physician Name: </b>"+event.physician+"</div>", container:'body', placement:'right', html:true});
   	  	},
   	 	dayRender: function(day, cell) {
   	   		cell.attr('data-day-id', day.format());
   	   		cell.attr('data-day-start', $.fullCalendar.formatDate(day, "Y-MM-DD HH:mm:ss"));
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
			$('#bookingStartTime').val($.fullCalendar.formatDate(event.start, "Y-MM-DD HH:mm:ss")); 
			$('#bookingEndTime').val($.fullCalendar.formatDate(event.end, "Y-MM-DD HH:mm:ss")); 
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
	    var event_firstname = $('#patientFirstName').val(); 
	    var event_lastname = $('#patientLastName').val(); 
	    var event_reason = $('#patientReasonofVisit').val();
	    var event_physician = $('#bookingDoctor').val();
	    var event_age = $('#patientBirth').val();
	    var event_gender = $('#patientGender').val();	    
		var event_start = $('#bookingStartTime').val();
		var event_end = $('#bookingEndTime').val();		
    	$.ajax({
 	   		url: 'save_events.php',
 	   		data: 'id='+event_id+'&firstname='+event_firstname+'&lastname='+event_lastname+'&reason='+event_reason+'&physician='+event_physician+'&age='+event_age+'&gender='+event_gender+'&start='+event_start+'&end='+event_end ,
 	   		type: "POST",
 	   		success: function(json) {
 	   			$('#bookingModal').modal('hide');
 	   			location.reload();
 	   		}
    	});
	});

	$('#bookingModal').on('hidden.bs.modal', function () {    
	    $(':input').val('');
	});

  });

</script>
<style>

  body {
    background: url(../background.jpg);
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

</style>
</head>
<body>
	
    <center><h3><b>MediBook Clinic Application</b></h3></center></br>
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
                    <div class="form-group">
                        <input type="text" class="form-control form-control-sm" style="text-transform: capitalize;" id="patientFirstName" placeholder="First name"/>
                    </div>
                </div>
                <div class='col-md-6'>
                    <div class="form-group">
                        <input type="text" class="form-control form-control-sm" style="text-transform: capitalize;" id="patientLastName" placeholder="Last name"/>
                    </div>
                </div>
            </div>                             
                     	
            <div class="row">           
                <div class='col-md-6'>
                    <div class="form-group">
                    	<label for="patientBirth" class="required">Patient Birth</label>
                        <div class='input-group date'>
                            <input type='text' id='patientBirth' class="form-control" placeholder="yyyy-mm-dd"/>
                            <span class="input-group-addon"></span>
                        </div>
                    </div>
                </div>
                <div class='col-md-6'>
                    <div class="form-group">
                    	<label for="patientGender" class="required">Patient Gender</label>
                    	<select class="form-control form-control-sm" id="patientGender">
                          	<option>Male</option>
                            <option>Female</option>
                      </select>
                    </div>
                </div>
            </div> 
                               
            <div class="form-group">
                <label for="bookingDoctor" class="required">Physician Name</label>
                <select class="form-control form-control-sm" id="bookingDoctor">
                  <option value="davidwarkentin">Dr. David Warkentin</option>
                  <option value="brucehoffman">Dr. Bruce Hoffman</option>
                  <option value="michaelomidi">Dr. Michael Omidi</option>
                  <option value="jamesojjeh">Dr. James Ojjeh</option>
                  <option value="sadiralrawi">Dr. Sadir Alrawi</option>
                </select>
            </div>
            
            <div class="form-group">
            	<label for="patientReasonofVisit">Reason of visit</label>
            	<textarea rows="2" class="form-control" id="patientReasonofVisit" placeholder="Please Explain."></textarea>
            </div>              
                   
            <div class="row">           
                <div class='col-md-6'>
                    <div class="form-group">
                    	<label for="bookingStartTime" class="required">Start Date/Time</label>
                        <div class='input-group date'>
                            <input type='text' id='bookingStartTime' class="form-control" placeholder="yyyy-mm-dd hh:mm:ss"/>
                            <span class="input-group-addon"></span>
                        </div>
                    </div>
                </div>
                <div class='col-md-6'>
                    <div class="form-group">
                    	<label for="bookingEndTime" class="required">End Date/Time</label>
                        <div class='input-group date' >
                            <input type='text' id='bookingEndTime' class="form-control" placeholder="yyyy-mm-dd hh:mm:ss"/>
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
                    	format: 'Y-MM-DD'
                    });
                    $('#bookingStartTime').datetimepicker({
                    	format: 'Y-MM-DD HH:mm:ss'
                    });
                    $('#bookingEndTime').datetimepicker({
                    	format: 'Y-MM-DD HH:mm:ss',
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