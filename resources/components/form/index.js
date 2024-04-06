import {defineAsyncComponent} from "vue";

function load(app){
    app.component('VueForm', defineAsyncComponent(() => import('./form.vue')));
    app.component('VueField', defineAsyncComponent(() => import('./field.vue')));
    app.component('Checkbox', defineAsyncComponent(() => import('./checkbox.vue')));
    app.component('Radio', defineAsyncComponent(() => import('./radio.vue')));
}

export default {load}