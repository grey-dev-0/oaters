import {createApp} from "vue";
import common from "../common.js";
import Timeline from "../../components/timeline/index.js";

let app = createApp({
    data(){
        return {
            test: 'success'
        };
    },
    methods: {
        change(){
            this.test = 'danger';
        }
    }
}), bundles = [Timeline];

common.load(app);
common.loadBundles(app, bundles);
app.mount('#app');