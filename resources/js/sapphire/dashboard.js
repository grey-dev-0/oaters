import Chart from "../../components/chart";
import Counter from "../../components/counter";
import VueTable from "../../components/table";
import {createApp} from "vue";
let libraries = Object.assign({}, {Chart}, {Counter}, VueTable);

let app = createApp({
    data: function(){
        return {
            chartRange: {
                start: null,
                end: null
            }
        };
    },
    computed: {
        defaultChartRange: function(){
            return this.chartRange.start.format('YYYY-MM-DD') + ' to ' + this.chartRange.end.format('YYYY-MM-DD');
        }
    },
    beforeMount: function(){
        this.chartRange.start = moment().subtract(1, 'M');
        this.chartRange.end = moment();
    }
});

import(
    /* webpackPreload: true */
    /* webpackChunkName: "js/common" */
    '../../components/common'
    ).then((common) => {
    common.load(app);
    for(let component in libraries)
        app.component(component, libraries[component]);
    app.mount('#app');
});