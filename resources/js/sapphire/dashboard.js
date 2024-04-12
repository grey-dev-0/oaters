import common from "../common.js";
import {createApp} from "vue";
import 'daterangepicker';
import 'daterangepicker/daterangepicker.css';
import moment from "moment";
import jQuery from "jquery";
import vueTable from '../../components/table';

const app = createApp({
    data: function(){
        return {
            chartRange: {
                start: null,
                end: null
            }
        };
    },
    methods: {
        jQuery(){
            return jQuery;
        }
    },
    computed: {
        defaultChartRange: function(){
            return this.chartRange.start.format('YYYY-MM-DD') + ' to ' + this.chartRange.end.format('YYYY-MM-DD');
        }
    },
    beforeMount: function(){
        jQuery.ajaxSettings.headers = {'X-CSRF-TOKEN': jQuery('[name="csrf-token"]').attr('content')};
        this.chartRange.start = moment().subtract(1, 'M');
        this.chartRange.end = moment();
    }
}), components = {
    Chart: 'chart',
    Counter: 'counter'
}, bundles = [vueTable];

common.load(app);
common.loadBundles(app, bundles);
common.loadComponents(app, components);
app.mount('#app');