import { createApp } from "vue";
import Echo from "laravel-echo";
import Pusher from "pusher-js";
import App from "./components/App.vue";

const notifications = document.getElementById("article-notifications");

if (notifications?.dataset.pusherKey && notifications.dataset.pusherCluster) {
    window.Pusher = Pusher;
    window.Echo = new Echo({
        broadcaster: "pusher",
        key: notifications.dataset.pusherKey,
        cluster: notifications.dataset.pusherCluster,
        forceTLS: true,
    });

    createApp(App).mount(notifications);
}
