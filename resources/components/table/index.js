import {defineAsyncComponent} from "vue";

function load(app){
    app.component('VueTable', defineAsyncComponent(() => import('./table.vue')));
    app.component('Column', defineAsyncComponent(() => import('./column.vue')));
}

export default {load}