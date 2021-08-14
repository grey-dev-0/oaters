(function(){
    var view = Vue.createApp({
        data: function(){
            return {
                chartRange: {
                    start: null,
                    end: null
                }
            };
        },
        computed: {
            defaultChartRange: function(){
                return this.chartRange.start.format('YYYY-MM-DD') + ' to ' + this.chartRange.end.format('YYYY-MM-DD');
            }
        },
        beforeMount: function(){
            this.chartRange.start = moment().subtract(1, 'M');
            this.chartRange.end = moment();
        }
    });
    loadComponents(view, 'admin_dashboard');
    view.mount('#app');
})();