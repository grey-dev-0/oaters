import List from './list.vue';
import ListItem from './item.vue';

function load(app){
    app.component('List', List);
    app.component('ListItem', ListItem);
}

export default {load};