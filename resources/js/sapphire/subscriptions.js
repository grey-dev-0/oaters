import {createApp} from "vue";
import common, {jQuery as $, bootbox, renderVueTemplate} from "../common.js";
import Datatable from "../../components/datatable";
import Form from "../../components/form";

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
        },
        renderActions(){
            let view = this, appInstance = this.$appInstance;
            const editSubscription = e => {
                view.openSubscription.id = $(e.target).closest('[data-id]').data('id');
                view.openSubscription.name = view.dataTable.row($(e.target).closest('tr')).data().tenant.name;
                view.$nextTick(() => view.$refs.updateSubscription.show(() => {
                    // Form will handle loading the subscription data
                }));
            };
            const revokeSubscription = e => {
                const subscriptionId = $(e.target).closest('[data-id]').data('id');
                bootbox.confirm({
                    message: 'Are you sure?',
                    centerVertical: true,
                    callback: answer => {
                        if(answer) {
                            // Handle revoke logic
                        }
                    }
                });
            };
            const deleteSubscription = e => {
                const subscriptionId = $(e.target).closest('[data-id]').data('id');
                bootbox.confirm({
                    message: 'Are you sure?',
                    centerVertical: true,
                    callback: answer => {
                        if(answer) {
                            // Handle delete logic
                        }
                    }
                });
            };
            $('[vue-template]').each(function(){
                renderVueTemplate(this, appInstance, {methods: {editSubscription, revokeSubscription, deleteSubscription}});
            });
        }
    },
    computed: {
        dataTable: function(){
            return this.$refs.subscriptionsTable.dataTable;
        }
    }
}), bundles = [Datatable, Form], components = {Modal: 'modal'};

app.config.globalProperties.$appInstance = app;
common.load(app);
common.loadBundles(app, bundles);
common.loadComponents(app, components);
app.mount('#app');
