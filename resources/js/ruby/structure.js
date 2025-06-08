import {createApp} from "vue";
import common from "../common.js";
import {reduce} from "lodash";

let app = createApp({
    name: 'LempManager',
    mounted(){
        setTimeout(() => {
            const transformedData = reduce(subordinates, (result, item) => {
                const managerName = item.manager.name;
                const memberName = item.member.name;
                if(!result[managerName]){
                    result[managerName] = [];
                }
                if(!result[memberName]){
                    result[memberName] = [];
                }
                result[managerName].push(memberName);
                return result;
            }, {});
            this.$refs.chart.draw(transformedData);
        }, 1000);
    }
});

common.load(app);
common.loadComponents(app, {OrgChart: 'org-chart'});
app.mount('#app');