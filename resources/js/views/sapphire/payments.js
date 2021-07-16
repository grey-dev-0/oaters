(function(){
    var app = Vue.createApp({
        computed: {
            dataTable: function(){
                return this.$refs.paymentsTable.dataTable;
            }
        }
    });
    loadComponents(app, 'tenants');
    app.mount('#app');
})();