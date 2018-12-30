/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

/*
import {library} from '@fortawesome/fontawesome-svg-core'
import {faTimes} from '@fortawesome/free-solid-svg-icons'
import {faChevronRight} from '@fortawesome/free-solid-svg-icons'
import {faChevronLeft} from '@fortawesome/free-solid-svg-icons'
import {faAngleDoubleLeft} from '@fortawesome/free-solid-svg-icons'
import {faAngleDoubleRight} from '@fortawesome/free-solid-svg-icons'

library.add(faTimes, faChevronLeft, faChevronRight, faAngleDoubleLeft, faAngleDoubleRight)
*/

if (document.getElementById("calendar")) {


    $('#calendar').fullCalendar({
        header: {
            left: '',
            center: 'title',
            right: 'prev, today, next'
        },
        navLinks: false, // can click day/week names to navigate views
        editable: false,
        eventLimit: true, // allow "more" link when too many events
        events: '/api/days',
        themeSystem: 'bootstrap4',
        firstDay: 1 // monday
    });
}
