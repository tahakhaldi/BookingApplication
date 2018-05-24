<!DOCTYPE html>
<html>
<head>
<meta charset='utf-8' />
<link href='../fullcalendar.min.css' rel='stylesheet' />
<script src='../lib/moment.min.js'></script>
<script src='../lib/jquery.min.js'></script>
<script src='../fullcalendar.min.js'></script>
<script>

	$(document).ready(function() {

		$('#calendar').fullCalendar({
      		header: {
        	left: 'prev,next today',
        	center: 'title',
        	right: 'month,agendaWeek,agendaDay,listWeek'
      	},
      	defaultDate: '2018-03-12',
      	navLinks: true, // can click day/week names to navigate views
      	editable: true,
      	eventLimit: true, // allow "more" link when too many events
      	events: 'php/get-events.php',
      	eventRender: function(event, element, view) {
    		if (event.allDay === 'true') {
    	    	event.allDay = true;
    	    } else {
    	     	event.allDay = false;
    	    }
   	  	},

   		selectable: true,
    	selectHelper: true,
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
         	   			alert('Added Successfully');
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

	    editable: true,
    	eventDrop: function(event, delta) {
        	var start = $.fullCalendar.formatDate(event.start, "Y-MM-DD HH:mm:ss");
        	var end = $.fullCalendar.formatDate(event.end, "Y-MM-DD HH:mm:ss");
        	$.ajax({
     	   		url: 'update_events.php',
     	   		data: 'title='+ event.title+'&start='+ start +'&end='+ end +'&id='+ event.id ,
     	   		type: "POST",
     	   		success: function(json) {
     	    		alert("Updated Successfully");
     	   		}
        	});
    	},
        eventClick: function(event) {
         	var decision = confirm("Do you really want to do that?"); 
         	if (decision) {
             	$.ajax({
             		type: "POST",
             		url: "delete_event.php",
             		data: "&id=" + event.id,
             		success: function(json) {
             			$('#calendar').fullCalendar('removeEvents', event.id);
             			alert("Updated Successfully");
             		}
             	});
         	}
       	},
        eventResize: function(event) {
     	   	var start = $.fullCalendar.formatDate(event.start, "yyyy-MM-dd HH:mm:ss");
     	   	var end = $.fullCalendar.formatDate(event.end, "yyyy-MM-dd HH:mm:ss");
     	   	$.ajax({
     	    	url: 'update_events.php',
     	    	data: 'title='+ event.title+'&start='+ start +'&end='+ end +'&id='+ event.id ,
     	    	type: "POST",
     	    	success: function(json) {
     	     		alert("Updated Successfully");
     	    	}
     	   	});
     	}
   	  
    });

  });

</script>
<style>

  body {
    margin: 40px 10px;
    padding: 0;
    font-family: "Lucida Grande",Helvetica,Arial,Verdana,sans-serif;
    font-size: 14px;
  }

  #calendar {
    max-width: 900px;
    margin: 0 auto;
  }

</style>
</head>
<body>
  <center><h1>Clinic Booking Application</h1></center>
  <div id='calendar'></div>
</body>
</html>