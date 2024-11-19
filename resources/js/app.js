import './bootstrap';

/*
 * Calendar
 */
import {Calendar} from '@fullcalendar/core';
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
        events: '/stateful-api/days',
        themeSystem: 'bootstrap5',
        firstDay: 1, // monday
        plugins: [bootstrapPlugin, dayGridPlugin, interactionPlugin],

        eventClick: function (info) {
            if (info.jsEvent.target.classList.contains('fc-bg-event')) {
                window.location.href = info.event.url
            }
        },
        dateClick: function (info) {
            if (info.jsEvent.target.classList.contains('fc-bg-event')) {
                // Handled in eventClick
                return;
            }

            window.location.href = window.location.origin + "/days/create?initial=" + info.dateStr
        }
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
autosize(document.querySelector('#grateful_for'));

const remainingIds = ['grateful_for']
for (const remainingSpan of remainingIds) {
    if (document.querySelector('#' + remainingSpan + "-Remaining")) {
        const parent = document.querySelector('#' + remainingSpan);
        const span = document.querySelector('#' + remainingSpan + "-Remaining");

        parent.addEventListener('input', function (evt) {
            updateLimit(span, parent)
        })
        updateLimit(span, parent)
    }
}

function updateLimit(span, input) {
    const max = input.getAttribute('maxlength');
    let current = input.value.length;
    span.textContent = max - current;
}


if (document.querySelector("#date")) {
    const dateInput = document.querySelector("#date");
    const initialDate = dateInput.value;
    const isNewDate = document.querySelector('input[name=category_id]:checked') === null;

    dateInput.addEventListener('change', function (evt) {
        if (!isNewDate && dateInput.value === initialDate) {
            hideDateInfo();
            return;
        }

        checkDate(dateInput.value);
    })

    if (isNewDate) {
        checkDate(dateInput.value);
    }

    function hideDateInfo() {
        document.querySelector('#existing-date-alert').classList.add('d-none');
    }

    function checkDate(date) {
        axios.post('/stateful-api/days/exists', {
            'date': date
        })
            .then(data => {
                if (data.data.exists) {
                    document.querySelector('#existing-date-link').href = data.data.route;
                    document.querySelector('#existing-date-alert').classList.remove('d-none');
                } else {
                    hideDateInfo()
                }
            })
    }
}

if (document.querySelector("#tag-form-button")) {
    const tagFormButton = document.querySelector("#tag-form-button");
    const tagFormInput = document.querySelector("#tag-form-input");
    const tagFormFeedback = document.querySelector("#tag-invalid-feedback");
    const tagFormInputGroup = document.querySelector("#tag-form-input-group");
    const tagFormItem = document.querySelector("#tag-form-item");

    tagFormButton.addEventListener('click', function(evt) {
        createTag(evt)
    })
    tagFormInput.addEventListener('keydown', function(evt) {
        // enter and return
        if (evt.keyCode === 13 || evt.keyCode === 169) {
            evt.preventDefault();
            createTag()
        }
    })

    function createTag() {
        tagFormInput.disabled = true;
        tagFormButton.disabled = true;

        tagFormFeedback.classList.remove('d-block');
        tagFormInput.classList.remove('is-invalid')
        tagFormInputGroup.classList.remove('has-validation')

        const template = `<li class="list-group-item">
            <input class="form-check-input me-1" name="tag_ids[]" type="checkbox" value="%slug%" id="tag-%slug%" checked>
            <label class="form-check-label stretched-link" for="tag-%slug%">%name%</label>
        </li>`;

        axios.post('/stateful-api/tags', {
            'name': tagFormInput.value
        })
            .then(data => {
                const div = document.createElement('div');

                div.innerHTML = template
                    .replaceAll('\%name\%', data.data.name)
                    .replaceAll('\%slug\%', data.data.slug)
                    .trim();

                tagFormItem.insertAdjacentElement('beforebegin', div.firstElementChild);

                tagFormInput.value = "";
            })
            .catch(err => {
                tagFormInputGroup.classList.add('has-validation')
                tagFormInput.classList.add('is-invalid')
                tagFormFeedback.textContent = err.response.data.errors.name[0];
                tagFormFeedback.classList.add('d-block');
            })
            .finally(() => {
                tagFormInput.disabled = false;
                tagFormButton.disabled = false;

                tagFormInput.focus()
            })
    }
}


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
for (const month of [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12]) {
    if (document.getElementById("calendar-" + month)) {
        const calendarEl = document.getElementById('calendar-' + month);

        const calendar = new Calendar(calendarEl, {
            headerToolbar: false,
            navLinks: false, // can click day/week names to navigate views
            editable: false,
            dayMaxEventRows: 6, // allow "more" link when too many events
            events: function (info, successCallback, failureCallback) {
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
            dateClick: function (info) {
                if (info.jsEvent.target.classList.contains('fc-bg-event')) {
                    // Handled in eventClick
                    return;
                }

                window.location.href = window.location.origin + "/days/create?initial=" + info.dateStr
            },
            initialDate: new Date(window.tellmolly_year, month - 1, 1)
        });

        calendar.render();
    }
}
