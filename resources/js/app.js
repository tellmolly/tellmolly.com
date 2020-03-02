/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

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

import Swal from 'sweetalert2/src/sweetalert2.js'

document.querySelectorAll('form.confirm').forEach((form) => {
    form.addEventListener('submit', function (event) {
        event.preventDefault();

        Swal.fire({
            title: 'Are you sure?',
            text: 'Do you you want to delete this day?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Delete!'
        }).then((result) => {
            if (result.value) {
                form.submit();
            }
        })
    })
});

