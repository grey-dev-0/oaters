import {defineAsyncComponent} from "vue";

function load(app){
    app.component('Timeline', defineAsyncComponent(() => import('./timeline.vue')));
    app.component('TimelineEntry', defineAsyncComponent(() => import('./timeline-entry.vue')));
}

export default {load};