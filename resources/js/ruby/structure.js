import {createApp} from "vue";
import common from "../common.js";
import {reduce as _reduce} from "lodash";

let app = createApp({
    name: 'LempManager',
    data(){
        return {
            members: null,
            departments: null
        };
    },
    mounted(){
        const departments = {}, deptMembers = [];
        const transformedData = _reduce(subordinates, (result, item) => {
            const managerName = item.manager.name, memberName = item.member.name, departmentName = item.department.name;
            if(!result[managerName])
                result[managerName] = [];
            if(!result[memberName])
                result[memberName] = [];
            result[managerName].push(memberName);
            if(!departments[departmentName])
                departments[departmentName] = [];
            if(!departments[departmentName].includes(managerName) && !deptMembers.includes(managerName))
                departments[departmentName].push(managerName);
            if(!departments[departmentName].includes(memberName)){
                departments[departmentName].push(memberName);
                deptMembers.push(memberName);
            }
            return result;
        }, {});
        this.members = transformedData;
        this.departments = departments;
    }
});

common.load(app);
common.loadComponents(app, {OrgChart: 'org-chart'});
app.mount('#app');