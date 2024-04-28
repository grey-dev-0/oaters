import 'bootstrap';
import breadcrumb from "../components/breadcrumb";
import bootbox from "bootbox";
import navbar from "../components/navbar"
import jQuery from "jquery";
import {defineAsyncComponent} from "vue";

jQuery.ajaxSettings.headers = {'X-CSRF-TOKEN': jQuery('[name="csrf-token"]').attr('content')};

function load(app){
    breadcrumb.load(app);
    navbar.load(app);
    loadComponents(app, {Alert: 'alert', Card: 'card', VLoader: 'loader-wrapper'});
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

export default {load, loadBundles, loadComponents, jQuery, bootbox};