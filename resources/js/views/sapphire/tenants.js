(function(){
    var view = Vue.createApp({
        data: function(){
            return {
                emitter: null
            };
        },
        methods: {
            renderActions: function(){
                return '<small class="text-muted">to be specified</small>';
            }
        },
        computed: {
            dataTable: function(){
                return this.$refs.tenantsTable.dataTable;
            }
        }
    });
    loadComponents(view, 'tenants');
    view.mount('#app');
})();