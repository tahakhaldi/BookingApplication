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
      		element.attr('data-event-title', event.title);
      		element.attr('data-event-color', event.color);
      		element.attr('data-event-start', $.fullCalendar.formatDate(event.start, "Y-MM-DD HH:mm:ss"));
      		element.attr('data-event-end', $.fullCalendar.formatDate(event.end, "Y-MM-DD HH:mm:ss"));
            element[0].className += ' eventmenu';
            $(element).tooltip({title: "<div align='left'><b>Patient Name: </b>"+event.title+"<br/><b>Patient Gender: </b>"+event.title+"<br/><b>Physician Name: </b>"+"Dr. David Warkentin"+"</div>", container:'body', placement:'right', html:true});
   	  	},
   	 	dayRender: function(day, cell) {
   	   		cell.attr('data-day-id', day.format());
   	   		cell.attr('data-day-start', $.fullCalendar.formatDate(day, "Y-MM-DD HH:mm:ss"));
        	cell[0].className += ' daymenu';
 		},
    	select: function(start, end, allDay) {
        	var title = prompt('Event Title:');
       		if (title) {
            	var start = $.fullCalendar.formatDate(start, "Y-MM-DD HH:mm:ss");
            	var end = $.fullCalendar.formatDate(end, "Y-MM-DD HH:mm:ss");
            	$.ajax({
         	   		url: 'add_events.php',
         	   		data: 'title='+ title+'&start='+ start +'&end='+ end,
         	   		type: "POST",
         	   		success: function(json) {
         	   			//alert('Added Successfully');
         	   		}
            	});
            	$('#calendar').fullCalendar('renderEvent',
            	{
             	   	title: title,
             	   	start: start,
             	   	end: end,
             	   	allDay: allDay
           		},true);
        	}
       		$('#calendar').fullCalendar('unselect');
    	},
        eventClick: function(event) {
			$('#patientId').val(event.id); 
			$('#patientFirstName').val(event.title); 
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
				var event_title = ui.target.closest(".eventmenu").attr("data-event-title");
				$('#patientFirstName').val(event_title); 
				var event_start = ui.target.closest(".eventmenu").attr("data-event-start");
				$('#bookingStartTime').val(event_start); 
				var event_end = ui.target.closest(".eventmenu").attr("data-event-end");
				$('#bookingEndTime').val(event_end); 
				$('#bookingModal').modal('show');
			} else if (ui.cmd == "new") {
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
		var event_title = $('#patientFirstName').val();
		var event_start = $('#bookingStartTime').val();
		var event_end = $('#bookingEndTime').val();
    	$.ajax({
 	   		url: 'update_events.php',
 	   		data: 'title='+ event_title+'&start='+ event_start +'&end='+ event_end +'&id='+ event_id ,
 	   		type: "POST",
 	   		success: function(json) {
 	   		$('#bookingModal').modal('hide');
 	   			$('#calendar').fullCalendar('refetchEvents');
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
          	<label for="patientFirstName">Patient Full Name</label>
            <div class="row">           
                <div class='col-md-6'>
                    <div class="form-group">
                        <input type="text" class="form-control form-control-sm" id="patientFirstName" placeholder="First name"/>
                    </div>
                </div>
                <div class='col-md-6'>
                    <div class="form-group">
                        <input type="text" class="form-control form-control-sm" id="patientLastName" placeholder="Last name"/>
                    </div>
                </div>
            </div>
                     	
            <div class="row">           
                <div class='col-md-6'>
                    <div class="form-group">
                    	<label for="patientBirth">Patient Birth</label>
                        <div class='input-group date'>
                            <input type='text' id='patientBirth' class="form-control" placeholder="yyyy-mm-dd"/>
                            <span class="input-group-addon"></span>
                        </div>
                    </div>
                </div>
                <div class='col-md-6'>
                    <div class="form-group">
                    	<label for="patientGender">Patient Gender</label>
                    	<select class="form-control form-control-sm" id="patientGender">
                          	<option>Male</option>
                            <option>Female</option>
                      </select>
                    </div>
                </div>
            </div> 
                               
            <div class="form-group">
                <label for="bookingDoctor">Physician Name</label>
                <select class="form-control form-control-sm" id="bookingDoctor">
                  <option>Dr. David Warkentin</option>
                  <option>Dr. Bruce Hoffman</option>
                  <option>Dr. Michael Omidi</option>
                  <option>Dr. James Ojjeh</option>
                  <option>Dr. Sadir Alrawi</option>
                </select>
            </div>
                   
            <div class="row">           
                <div class='col-md-6'>
                    <div class="form-group">
                    	<label for="bookingStartTime">Start Date/Time</label>
                        <div class='input-group date'>
                            <input type='text' id='bookingStartTime' class="form-control" placeholder="yyyy-mm-dd hh:mm:ss"/>
                            <span class="input-group-addon"></span>
                        </div>
                    </div>
                </div>
                <div class='col-md-6'>
                    <div class="form-group">
                    	<label for="bookingEndTime">End Date/Time</label>
                        <div class='input-group date' >
                            <input type='text' id='bookingEndTime' class="form-control" placeholder="yyyy-mm-dd hh:mm:ss"/>
                            <span class="input-group-addon"></span>
                        </div>
                    </div>
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