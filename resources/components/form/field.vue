<template>
    <template v-if="formOrientation.vertical">
        <!-- TODO Supporting vertical form mode -->
    </template>
    <template v-else>
        <div class="form-group row">
            <label :class="labelClass" ref="label">
                <slot></slot>
            </label>
            <div :class="inputClass">
                <input v-if="inputType == 1" :id="id" :class="elementClass" :name="name" :type="textType" v-model="value" :autocomplete="!autocomplete? 'off' : 'on'" :placeholder="placeholder" @focus="onFocus" @change="onChange">
                <select v-else-if="inputType == 2" :id="id" :class="elementClass" :name="name + (multiple? '[]' : '')" v-model="value" :multiple="multiple" @focus="onFocus" @change="onChange">
                    <option v-if="!multiple" value="">-- {{placeholder}} --</option>
                    <slot name="options"></slot>
                </select>
                <template v-else-if="inputType == 3">
                    <slot name="options"></slot>
                </template>
                <autocomplete v-else ref="autocomplete" :name="name" :id="id" :url="url" :placeholder="placeholder" :limit="limit" @change="onAutocompleteChange"></autocomplete>
                <small class="text-danger d-block" v-if="validation[name] && validation[name].length">
                    <span class="d-block" v-for="error in validation[name]">{{error}}</span>
                </small>
            </div>
        </div>
    </template>
</template>

<script>
import Autocomplete from "../autocomplete.vue";
import {jQuery as $} from '../../js/common';

export default {
    name: "VueField",
    components:{
        Autocomplete
    },
    inject: ['labelSize', 'formOrientation', 'setField', 'validation', 'emitter'],
    props: {
        id: String,
        name: String,
        autocomplete: {
            type: Boolean,
            default: false
        },
        type: {
            default: 'text',
            validator: function(value){
                let supportedTypes = ['text', 'number', 'password', 'email', 'tel', 'daterange', 'file', 'select', 'select2', 'checkbox', 'radio', 'autocomplete'];
                return supportedTypes.indexOf(value) != -1;
            }
        },
        ranged: Boolean,
        multiple: {
            type: Boolean,
            default: false
        },
        url: String,
        limit: Number
    },
    data: function(){
        return {
            value: null,
            placeholder: ''
        };
    },
    computed: {
        labelClass(){
            return 'col-12 col-sm-' + this.labelSize.small + ' col-md-' + this.labelSize.medium + ' col-lg-' +
                this.labelSize.large + (this.inputType != 3? ' col-form-label' : '');
        },
        inputClass(){
            return 'col-12 col-sm-' + (12 - this.labelSize.small) + ' col-md-' + (12 - this.labelSize.medium)
                + ' col-lg-' + (12 - this.labelSize.large);
        },
        elementClass(){
            let validation = this.validation[this.name] && this.validation[this.name].length || false;
            return this.type == 'file'? '' : ('form-control' + (validation? ' border-danger' : ''));
        },
        inputType(){
            switch(this.type){
                case 'text':
                case 'number':
                case 'password':
                case 'email':
                case 'tel':
                case 'file':
                case 'daterange':
                    return 1;
                case 'select':
                case 'select2':
                    return 2;
                case 'radio':
                case 'checkbox':
                    return 3;
                default:
                    return 4;
            }
        },
        textType(){
            return (this.type == 'daterange')? 'text' : this.type;
        }
    },
    provide(){
        return {
            setValue: this.setValue
        };
    },
    methods: {
        setValue(value){
            this.value = value;
            this.$nextTick(this.onChange);
        },
        onChange: function(){
            this.setField(this.name, this.value);
        },
        onFocus(){
            if(this.validation[this.name])
                this.validation[this.name] = [];
        },
        onAutocompleteChange: function(name, value){
            this.value = value;
            this.$nextTick(this.onChange);
        },
        construct: function(defaultValue){
            switch(this.type){
                case 'daterange': return this.initDatePicker(defaultValue);
                case 'select2': return this.initSelect(defaultValue);
                case 'autocomplete': return this.$nextTick(() => {
                    let value = defaultValue && defaultValue.id || undefined;
                    this.value = value
                    this.setField(this.name, value);
                    if(value)
                        this.$refs.autocomplete.select(value, defaultValue.text);
                });
            }
            this.setValue(defaultValue);
        },
        destroy: function(){
            let element = $('#' + this.id);
            if(this.type == 'select2' && element.hasClass('select2-hidden-accessible'))
                element.select2('destroy');
            if([3, 4].indexOf(this.inputType) != -1 && this.name !== undefined)
                this.emitter.emit('destroy', this.name);
        },
        initDatePicker: function(defaultValue){
            this.setValue(defaultValue);
            let field = this;
            $('#' + this.id).daterangepicker({
                showDropdowns: true,
                autoUpdateInput: false,
                singleDatePicker: !this.ranged,
                opens: 'right',
                locale: {
                    cancelLabel: 'Clear'
                }
            }).on('apply.daterangepicker', function(e, picker){
                let startDate = picker.startDate.format('YYYY-MM-DD');
                let endDate = picker.endDate.format('YYYY-MM-DD');
                $(this).val((startDate == endDate)? startDate : (startDate + ' to ' + endDate));
                field.setValue($(this).val());
            }).on('cancel.daterangepicker', function(){
                $(this).val('');
                field.setValue($(this).val());
            });
        },
        initSelect: function(defaultValue){
            let field = this, element = $('#' + this.id), options = {
                theme: 'bootstrap',
                placeholder: this.placeholder,
                width: '100%',
                allowClear: true
            };
            if(this.url){
                options.ajax = {
                    url: this.url,
                    type: 'POST',
                    data(parameters){
                        return {
                            query: parameters.term,
                            page: parameters.page,
                            limit: field.limit || 5
                        }
                    },
                    processResults: response => {
                        let results = [];
                        response = response.suggestions;
                        for(let key in response)
                            results.push({
                                id: key,
                                text: response[key]
                            });
                        return {results};
                    }
                };
                element.empty();
            }
            element.off().select2(options).on('change', function(){
                field.setValue($(this).val());
            });
            if(defaultValue){
                let values = [];
                for(let key in defaultValue){
                    values.push(key + '');
                    if(this.url)
                        element.append($('<option/>').attr('value', key).text(defaultValue[key]));
                }
                element.val(values).trigger('change');
            }
        }
    },
    mounted: function(){
        this.placeholder = $(this.$refs.label).text().trim();
        this.$nextTick(function(){
            this.emitter.on('init', (e) => {
                this.destroy();
                this.construct(e.defaults? e.defaults[this.name] : undefined);
                if(this.inputType == 3)
                    this.emitter.emit('init:sub', this.name);
            });
        });
    }
}
</script>