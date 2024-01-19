import Echo from "laravel-echo";

window.Pusher = require('pusher-js');

window.Echo = new Echo({
    authEndpoint: BROADCAST_AUTH_ENDPOINT,
    broadcaster: 'pusher',
    key: PUSHER_APP_KEY,
    cluster: PUSHER_APP_CLUSTER,
    encrypted: true
});

PNotify.prototype.options.delay = 1500;
window.Echo.private('App.Models.Auth.User.User.' + ADMIN_USER_ID)
    .notification((notification) => {
        let icon;
        let title;

        if (notification.type === "App\\Notifications\\Admin\\NewUser") {
            title = 'New ' + notification.role + ' registered';
            switch (notification.role) {
                case 'customer':
                    icon = 'fa fa-user-o';
                    break;
                case 'owner':
                    icon = 'fa fa-cutlery';
                    break;
                case 'delivery':
                    icon = 'fa fa-motorcycle';
                    break;
            }
        }

        if (notification.type === "App\\Notifications\\Admin\\NewOrder") {
            icon = 'fa fa-shopping-cart';
            title = 'New Order Placed';
        }

        document.getElementById('notification-sound').play();
        new PNotify({
            title: title,
            type: 'success',
            styling: 'bootstrap3',
            icon: icon,
            animate: {
                animate: true,
                in_class: 'lightSpeedIn',
                out_class: 'lightSpeedOut'
            },
            nonblock: {
                nonblock: true
            }
        });
    });

$("#user-notifications > #user-notifications-container").on("click", function (event) {
    $.ajax({
        url: URL_NOTIFICATION_MARK_AS_READ,
        type: "post",
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        data: {
            type: "App\\Notifications\\Admin\\NewUser"
        },
        success: function () {
            $("#user-notifications-count").text("0");
        }
    })
});

$("#delete-user-notifications").on("click", function (event) {
    event.preventDefault();
    $.ajax({
        url: URL_NOTIFICATION_DELETE,
        type: "post",
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        data: {
            type: "App\\Notifications\\Admin\\NewUser"
        },
        success: function () {
            $(".user-notifications-row").remove();
        }
    })
});

$("#order-notifications").on("click", function (event) {
    $.ajax({
        url: URL_NOTIFICATION_MARK_AS_READ,
        type: "post",
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        data: {
            type: "App\\Notifications\\Admin\\NewOrder"
        },
        success: function () {
            $("#order-notifications-count").text("0");
        }
    })
});

$("#delete-order-notifications").on("click", function (event) {
    event.preventDefault();
    $.ajax({
        url: URL_NOTIFICATION_DELETE,
        type: "post",
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        data: {
            type: "App\\Notifications\\Admin\\NewOrder"
        },
        success: function () {
            $(".order-notifications-row").remove();
        }
    })
});

// notification demo
// $(document).ready(function () {
//     var notificationData = [
//         {title: '[Demo] New Order Placed', icon: 'fa fa-shopping-cart'},
//         {title: '[Demo] New Customer Registered', icon: 'fa fa-user-o'},
//         {title: '[Demo] New Store Registered', icon: 'fa fa-cutlery'},
//         {title: '[Demo] New Deivery Registered', icon: 'fa fa-motorcycle'},
//     ];
//     window.setTimeout(function () {
//         var notification = notificationData[Math.floor(Math.random()*notificationData.length)];
//         document.getElementById('notification-sound').play();
//         new PNotify({
//             title: notification.title,
//             type: 'success',
//             styling: 'bootstrap3',
//             icon: notification.icon,
//             animate: {
//                 animate: true,
//                 in_class: 'lightSpeedIn',
//                 out_class: 'lightSpeedOut'
//             },
//             nonblock: {
//                 nonblock: true
//             }
//         });
//     }, 2000);
//     window.setInterval(function () {
//         var notification = notificationData[Math.floor(Math.random()*notificationData.length)];
//         document.getElementById('notification-sound').play();
//         new PNotify({
//             title: notification.title,
//             type: 'success',
//             styling: 'bootstrap3',
//             icon: notification.icon,
//             animate: {
//                 animate: true,
//                 in_class: 'lightSpeedIn',
//                 out_class: 'lightSpeedOut'
//             },
//             nonblock: {
//                 nonblock: true
//             }
//         });
//     }, 30000);
// });
