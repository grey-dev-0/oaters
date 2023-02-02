import Breadcrumb from "./breadcrumb";
import Card from  "./card";
import Navbar from "./navbar";

export function load(app){
    let components = {Breadcrumb, Card, Navbar};
    for(var library in components)
        if(components[library].name === undefined)
            for(var component in components[library])
                app.component(component, components[library][component]);
        else
            app.component(library, components[library]);
}
