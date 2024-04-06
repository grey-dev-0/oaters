import breadcrumb from "../components/breadcrumb";
import navbar from "../components/navbar"
import {defineAsyncComponent} from "vue";

function load(app){
    breadcrumb.load(app);
    navbar.load(app);
    loadComponents(app, {Card: 'card'});
}

function loadBundles(app, bundles){
    bundles.forEach(bundle => {
        bundle.load(app);
    });
}

function loadComponents(app, components){
    for(let name in components)
        app.component(name, defineAsyncComponent(() => import(`../components/${components[name]}.vue`)));
}

export default {load, loadBundles, loadComponents};