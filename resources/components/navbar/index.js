import Navbar from './navbar.vue';
import NavItem from './item.vue';
import LanguageSwitcher from './language-switcher.vue';

function load(app){
    app.component('Navbar', Navbar);
    app.component('NavItem', NavItem);
    app.component('LanguageSwitcher', LanguageSwitcher);
}

export default {load}
