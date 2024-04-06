import Breadcrumb from "./breadcrumb.vue";
import BcItem from "./item.vue";

function load(app){
    app.component('Breadcrumb', Breadcrumb);
    app.component('BcItem', BcItem);
}

export default {load}