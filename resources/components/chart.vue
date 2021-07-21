<template>
    <div :class="cardClass">
        <div v-if="ranged" :class="headerClass">
            <h4 :class="'float-left mb-0' + (whiteTitle? ' text-white' : '')">{{title}}</h4>
            <input autocomplete="off" type="text" class="form-control float-right" :placeholder="rangeTitle || title">
        </div>
        <h4 v-else :class="headerClass + (whiteTitle? ' text-white' : '')">{{title}}</h4>
        <div :class="bodyClass">
            <canvas :id="id" :class="(loading)? 'd-none' : ''"></canvas>
            <h1 v-if="loading" class="text-center chart-loader"><i class="fas fa-spin fa-spinner fa-pulse"></i></h1>
        </div>
    </div>
</template>

<script>
var $ = window.$;
var Chart = window.Chart;
var bootbox = window.bootbox;

export default {
    name: "Chart",
    props: {
        type: {
            type: String,
            default: 'line'
        },
        id: {
            type: String,
            required: true
        },
        'class': String,
        url: {
            type: String,
            required: true
        },
        title: {
            type: String,
            required: true
        },
        color: String,
        ranged: {
            type: Boolean,
            default: false
        },
        rangeTitle: String,
        centerHeader: {
            type: Boolean,
            default: false
        },
        noPadding: {
            type: Boolean,
            default: false
        }
    },
    data: function(){
        return {
            loading: true,
            datasets: [],
            labels: [],
            chart: null
        };
    },
    computed: {
        cardClass: function(){
            var theClass = 'card';
            if(this.class !== undefined)
                theClass += ' ' + this.class;
            return theClass;
        },
        headerClass: function(){
            var theClass = 'card-header';
            if(this.color !== undefined){
                theClass += ' bg-' + this.color;
                if(this.whiteTitle)
                    theClass += ' text-white';
                if(this.centerHeader)
                    theClass += ' text-center';
            }
            return theClass;
        },
        bodyClass: function(){
            return 'card-body' + (this.noPadding? ' p-0' : '');
        },
        whiteTitle: function(){
            return (this.color !== undefined && parseInt(this.color.replace(/[^0-9]/g, '')) <= 5);
        }
    },
    methods: {
        load: function(){
            var chart = this;
            $.ajax({
                url: this.url,
                type: 'POST',
                success: function(response){
                    chart.datasets = response.datasets;
                    chart.labels = response.labels;
                    chart.$nextTick(function(){
                        chart.draw();
                    });
                },
                error: function(xhr){
                    bootbox.alert(xhr.responseJSON.message);
                    console.error(xhr.responseText);
                }
            });
        },
        draw: function(){
            var canvas = $('#' + this.id), chartOptions = {
                legend: {
                    position: (this.type == 'pie' || this.type == 'doughnut')? 'bottom' : 'top'
                }
            };
            if(this.type != 'pie' && this.type != 'doughnut')
                chartOptions.tooltips = {mode: 'x'};
            canvas = canvas[0].getContext('2d');
            this.chart = new Chart(canvas, {
                type: this.type,
                data: {
                    datasets: this.datasets,
                    labels: this.labels
                },
                options: chartOptions
            });
            this.loading = false;
        }
    },
    mounted: function(){
        this.load();
    }
}
</script>

<style lang="scss">
.card-body{
    position: relative;
    .chart-loader{
        position: absolute;
        margin: auto;
        top: 0;
        right: 0;
        bottom: 0;
        left: 0;
        width: 2em;
        height: 2em;
        i{
            font-size: 2em;
        }
    }
}
</style>