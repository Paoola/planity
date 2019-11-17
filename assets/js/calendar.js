import $ from 'jquery';
import 'fullcalendar';
require('fullcalendar/dist/fullcalendar.css');

$(function() {

  var calendar = document.querySelector('#calendar');

  $('#calendar').fullCalendar({
    defaultView: 'agendaFourDay',
    groupByResource: true,
    header: {
      left: 'prev,next',
      center: 'title',
      right: 'agendaDay,agendaFourDay'
    },
    views: {
      agendaFourDay: {
        type: 'agenda',
        duration: { days: 3 }
      }
    },
    minTime: '09:00:00',
    maxTime: '19:00:00',
    nowIndicator: true,
    allDaySlot: false,
    events: JSON.parse(calendar.dataset.events)
  });

});