import {createApp} from "vue";
import common from "../common.js";
import Datatable from "../../components/datatable";
import Form from "../../components/form";

let $ = common.jQuery, bootbox = common.bootbox;
let app = createApp({
    data: function(){
        return {
            locale: window.locale,
            openSubscription: {
                id: 0,
                name: null
            }
        }
    },
    methods: {
        jQuery(){
            return $;
        },
        renderModules: function(modules){
            let rendered = [];
            modules.forEach((module) => {
                rendered.push('<div class="badge badge-'+this.moduleColor(module.name)+'">'+module.name+'</div>');
            });
            return rendered.join(' ');
        },
        renderAmount: function(row){
            return '$' + row.price;
        },
        renderPaid: function(paid){
            return paid? window.locale.common.yes : window.locale.common.no;
        },
        moduleColor: function(module){
            switch(module){
                case 'amethyst': return 'info';
                case 'topaz': return 'warning';
                case 'emerald': return 'success';
                case 'ruby': return 'danger';
                case 'sapphire': return 'primary';
                default: return 'secondary';
            }
        },
        addSubscription: function(){
            this.$refs.createSubscription.show(() => {
                this.$refs.createSubscriptionForm.reset();
            });
        }
    },
    computed: {
        dataTable: function(){
            return this.$refs.subscriptionsTable.dataTable;
        }
    }
}), bundles = [Datatable, Form], components = {Modal: 'modal'};

common.load(app);
common.loadBundles(app, bundles);
common.loadComponents(app, components);
app.mount('#app');

$('body').on('click', '#subscriptions-table .delete', () => {
    bootbox.confirm({
        message: 'Are you sure?',
        centerVertical: true,
        callback: answer => {
            console.log('The answer is ', answer);
        }
    });
});