import DtColumn from "./column.vue";
import VueDatatable from "./datatable.vue";
import {defineAsyncComponent} from "vue";

function load(app){
    app.component('DtColumn', DtColumn);
    app.component('VueDatatable', VueDatatable);
    app.component('VueDatafilter', defineAsyncComponent(() => import('./datafilter.vue')));
    app.component('DtFilter', defineAsyncComponent(() => import('./filter-input.vue')));
}

export default {load}