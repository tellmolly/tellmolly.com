import './bootstrap';

import { Calendar } from '@fullcalendar/core';
import dayGridPlugin from '@fullcalendar/daygrid';
import bootstrapPlugin from '@fullcalendar/bootstrap5';
import interactionPlugin from '@fullcalendar/interaction';
import 'bootstrap-icons/font/bootstrap-icons.css'; // needs additional webpack config!

if (document.getElementById("calendar")) {
    const calendarEl = document.getElementById('calendar');

    const calendar = new Calendar(calendarEl, {
        headerToolbar: {
            start: 'title',
            center: '',
            right: 'prev today next'
        },
        navLinks: false, // can click day/week names to navigate views
        editable: false,
        dayMaxEventRows: 6, // allow "more" link when too many events
        events: '/api/days',
        themeSystem: 'bootstrap5',
        firstDay: 1, // monday
        plugins: [bootstrapPlugin, dayGridPlugin, interactionPlugin],

        eventClick: function(info) {
            if (info.jsEvent.target.classList.contains('fc-bg-event')) {
                window.location.href  = info.event.url
            }
        },
    });

    calendar.render();
}

import Swal from 'sweetalert2/src/sweetalert2'

document.querySelectorAll('form.confirm').forEach((form) => {
    form.addEventListener('submit', function (event) {
        event.preventDefault();

        Swal.fire({
            title: 'Are you sure?',
            text: 'Do you you want to delete this resource?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Delete!',
            focusCancel: true
        }).then((result) => {
            if (result.value) {
                form.submit();
            }
        })
    })
});
import autosize from "autosize/dist/autosize";

autosize(document.querySelector('#comment'));

import StackedCards from "./stackedCards.js";


if (document.querySelectorAll(".mycards").length > 0) {
    const stackedCard = new StackedCards({
        selector: '.mycards',
        layout: "slide",
        transformOrigin: "center",
    });
    stackedCard.init();
}
