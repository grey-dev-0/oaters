function loadComponents(view, bundleName){
    var components = window[bundleName].default;
    for(var component in components)
        view.component(component, components[component]);
}