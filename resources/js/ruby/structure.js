import {createApp} from "vue";
import common from "../common.js";
import {reduce as _reduce} from "lodash";

let app = createApp({
    name: 'LempManager',
    date(){
        return {
            members: null
        };
    },
    mounted(){
        const transformedData = _reduce(subordinates, (result, item) => {
            const managerName = item.manager.name, memberName = item.member.name;
            if(!result[managerName])
                result[managerName] = [];
            if(!result[memberName])
                result[memberName] = [];
            result[managerName].push(memberName);
            return result;
        }, {});
        this.members = transformedData;
    }
});

common.load(app);
common.loadComponents(app, {OrgChart: 'org-chart'});
app.mount('#app');