import './bootstrap';

/*
 * Calendar
 */
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


/*
 * General UI
 */
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

/*
 * Edit Day
 */
import autosize from "autosize/dist/autosize";

autosize(document.querySelector('#comment'));

/*
 * Year in Review
 */
import StackedCards from "./stackedCards.js";

if (document.querySelectorAll(".mycards").length > 0) {
    const stackedCard = new StackedCards({
        selector: '.mycards',
        layout: "slide",
        transformOrigin: "center",
    });
    stackedCard.init();
}

/*
 * Year Months
 */
for (const month of [1,2,3,4,5,6,7,8,9,10,11,12]) {
    if (document.getElementById("calendar-" + month)) {
        const calendarEl = document.getElementById('calendar-' + month);

        const calendar = new Calendar(calendarEl, {
            headerToolbar: false,
            navLinks: false, // can click day/week names to navigate views
            editable: false,
            dayMaxEventRows: 6, // allow "more" link when too many events
            events: function(info, successCallback, failureCallback) {
                if (typeof window.tellmolly_events[month] == "undefined") {
                    successCallback(
                       []
                    )
                } else {
                    successCallback(
                        window.tellmolly_events[month]
                    )
                }

            },
            themeSystem: 'bootstrap5',
            firstDay: 1, // monday
            contentHeight: "auto",
            plugins: [bootstrapPlugin, dayGridPlugin, interactionPlugin],

            eventClick: function (info) {
                if (info.jsEvent.target.classList.contains('fc-bg-event')) {
                    window.location.href = info.event.url
                }
            },
            initialDate: new Date(window.tellmolly_year, month - 1, 1)
        });

        calendar.render();
    }
}
