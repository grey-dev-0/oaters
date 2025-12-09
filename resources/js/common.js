import 'bootstrap';
import breadcrumb from "../components/breadcrumb";
import bootbox from "bootbox";
import navbar from "../components/navbar"
import jQuery from "jquery";
import {compile, defineAsyncComponent, h, render} from "vue";

jQuery.ajaxSettings.headers = {'X-CSRF-TOKEN': jQuery('[name="csrf-token"]').attr('content')};

function load(app){
    breadcrumb.load(app);
    navbar.load(app);
    loadComponents(app, {Alert: 'alert', Avatar: 'avatar', Card: 'card', VLoader: 'loader-wrapper'});
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

function renderVueTemplate(template, app, options = {}) {
    const compiled = compile(template.innerHTML);
    const node = h({render: compiled, ...options});
    node.appContext = app.appContext;
    render(node, template.parentNode);
    template.remove();
}

export {jQuery, bootbox, renderVueTemplate};
export default {load, loadBundles, loadComponents};