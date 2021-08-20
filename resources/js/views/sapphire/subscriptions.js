(function(){
    var view, app = Vue.createApp({
        data: function(){
            return {
                locale: window.locale
            }
        },
        methods: {
            renderModules: function(modules){
                var rendered = [];
                modules.forEach(function(module){
                    rendered.push('<div class="badge badge-'+view.moduleColor(module.name)+'">'+module.name+'</div>');
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
            }
        },
        computed: {
            dataTable: function(){
                return this.$refs.subscriptionsTable.dataTable;
            }
        }
    });
    loadComponents(app, 'datatable_with_modal');
    view = app.mount('#app');
})();