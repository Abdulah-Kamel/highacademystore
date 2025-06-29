/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */

import axios from 'axios';

window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allows your team to easily build robust real-time web applications.
 */

import Echo from 'laravel-echo';

import Pusher from 'pusher-js';

window.Pusher = Pusher;
console.log('from bootstrap js file')
window.Echo = new Echo({
    broadcaster: 'pusher',
    key: import.meta.env.VITE_PUSHER_APP_KEY,
    cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER ?? 'mt1',
    wsHost: import.meta.env.VITE_PUSHER_HOST ? import.meta.env.VITE_PUSHER_HOST : `ws-${import.meta.env.VITE_PUSHER_APP_CLUSTER}.pusher.com`,
    wsPort: import.meta.env.VITE_PUSHER_PORT ?? 80,
    wssPort: import.meta.env.VITE_PUSHER_PORT ?? 443,
    forceTLS: (import.meta.env.VITE_PUSHER_SCHEME ?? 'https') === 'https',
    enabledTransports: ['ws', 'wss'],
});
//Private Channel
window.Echo.private('MaterialChannel')
    .listen('NewMaterialPdfEvent', (data) => {
        $.ajax({
            url: "http://127.0.0.1:8000/notifications/increase",
            method: "Get",
            success: function (response) {
                if (response.notificationsIconHtml) {
                    $('.notificationIconContent').html(response.notificationsIconHtml)
                }
            }
        })
    })
    .listen('NewMaterialVideoEvent', (data) => {
        $.ajax({
            url: "http://127.0.0.1:8000/notifications/increase",
            method: "Get",
            success: function (response) {
                if (response.notificationsIconHtml) {
                    $('.notificationIconContent').html(response.notificationsIconHtml)
                }
            }
        })
    });
//Presence Channel
window.Echo.join(`online_students`)
    .here((students) => {
        console.log('here')
        console.log(students);
        students.forEach(student => {
            if (student.name !== 'admin') {
                let studentItem = `
                <li class="mb-3 pb-1">
                    <div class="d-flex align-items-start">
                        <div class="badge bg-label-secondary p-2 me-3 rounded"><i class="ti ti-user"></i></div>
                        <div class="d-flex justify-content-between w-100 flex-wrap gap-2">
                            <div class="me-2">
                                <h6 class="mb-0" style="font-weight: bold ; color: #00b300">${student.name}</h6>
                            </div>

                        </div>
                    </div>
                </li>`
                $('#students-list').append(studentItem);
            }
        });

    })
    .joining((student) => {
        console.log('joining')
        console.log(student);
            let studentItem = `
                <li class="mb-3 pb-1">
                    <div class="d-flex align-items-start">
                        <div class="badge bg-label-secondary p-2 me-3 rounded"><i class="ti ti-user"></i></div>
                        <div class="d-flex justify-content-between w-100 flex-wrap gap-2">
                            <div class="me-2">
                               <h6 class="mb-0" style="font-weight: bold ; color: #00b300">${student.name}</h6>
                            </div>

                        </div>
                    </div>
                </li>`
            $('#students-list').append(studentItem);

    })
    .leaving((student) => {
        console.log('leaving')
        console.log(student);
        $('#students-list li').each(function() {
            if ($(this).find('h6').text().trim() === student.name.trim()) {
                $(this).remove();
            }
        });
    })
    .error((error) => {
        console.error(error);
    });
