<template>
    <div :class="cardClass">
        <div v-if="ranged" :class="headerClass">
            <h4 :class="'float-left mb-0 pt-1' + (whiteTitle? ' text-white' : '')">{{title}}</h4>
            <input autocomplete="off" type="text" :id="id + '-range'" class="form-control float-right" :placeholder="rangeTitle || title" :value="defaultRange">
        </div>
        <h4 v-else :class="headerClass + (whiteTitle? ' text-white' : '')">{{title}}</h4>
        <div v-if="loading" :class="bodyClass">
            <h1 class="text-center chart-loader"><i class="fas fa-spin fa-spinner fa-pulse"></i></h1>
        </div>
        <div v-else :class="bodyClass">
            <canvas :id="id" :class="(type == 'pie' || type == 'doughnut')? 'm-auto' : ''"></canvas>
        </div>
    </div>
</template>

<script>
import Chart from 'chart.js/auto';
import {jQuery as $} from '../js/common';

let bootbox;
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
        defaultRange: String,
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
            }
            if(this.centerHeader)
                theClass += ' text-center';
            if(this.ranged)
                theClass += ' pt-2 pb-2';
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
        initRangePicker: function(){
            var chart = this;
            $('#' + this.id + '-range').daterangepicker({
                showDropdowns: true,
                autoUpdateInput: false,
                opens: 'left',
                locale: {
                    cancelLabel: 'Clear'
                }
            }).on('apply.daterangepicker', function(e, picker){
                var startDate = picker.startDate.format('YYYY-MM-DD');
                var endDate = picker.endDate.format('YYYY-MM-DD');
                $(this).val((startDate == endDate)? startDate : (startDate + ' to ' + endDate));
                chart.load();
            }).on('cancel.daterangepicker', function(){
                $(this).val(chart.defaultRange || '');
                chart.load();
            });
        },
        load: function(){
            $.ajax({
                url: this.url,
                type: 'POST',
                data: {
                    range: $('#' + this.id + '-range').val()
                },
                success: response => {
                    this.datasets = response.datasets;
                    this.labels = response.labels;
                    this.loading = false;
                    this.$nextTick(() => {
                        this.draw();
                    });
                },
                error: function(xhr){
                    bootbox.alert(xhr.responseJSON.message);
                    console.error(xhr.responseText);
                }
            });
        },
        draw: function(){
            if(this.chart != null)
                this.chart.destroy();
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
        }
    },
    mounted: function(){
        bootbox = this.$root.bootbox();
        if(this.ranged)
            this.initRangePicker();
        this.load();
    }
}
</script>

<style lang="scss">
.card-header{
    .form-control.float-right{
        width: initial !important;
    }
}
.card-body{
    position: relative;
    min-height: 164px;
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