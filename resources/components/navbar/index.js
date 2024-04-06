import Navbar from './navbar.vue';
import NavItem from './item.vue';

function load(app){
    app.component('Navbar', Navbar);
    app.component('NavItem', NavItem);
}

export default {load}