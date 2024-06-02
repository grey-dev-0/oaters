import {defineAsyncComponent} from "vue";

function load(app){
    app.component('Tabs', defineAsyncComponent(() => import('./tabs.vue')));
    app.component('Tab', defineAsyncComponent(() => import('./tab.vue')));
    app.component('Pane', defineAsyncComponent(() => import('./pane.vue')));
}

export default {load};