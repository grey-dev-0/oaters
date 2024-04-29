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
                <input v-if="inputType == 1" :id="id" :class="(type != 'file')? 'form-control' : ''" :name="name" :type="textType" v-model="value" :autocomplete="!autocomplete? 'off' : 'on'" :placeholder="placeholder" @change="onChange">
                <select v-else-if="inputType == 2" :id="id" class="form-control" :name="name + (multiple? '[]' : '')" v-model="value" :multiple="multiple" @change="onChange">
                    <option value="">-- {{placeholder}} --</option>
                    <slot name="options"></slot>
                </select>
                <template v-else-if="inputType == 3">
                    <slot name="options"></slot>
                </template>
                <autocomplete v-else ref="autocomplete" :name="name" :id="id" :url="url" :placeholder="placeholder" :limit="limit" @change="onAutocompleteChange"></autocomplete>
            </div>
        </div>
    </template>
</template>

<script>
import Autocomplete from "../autocomplete.vue";
import select2 from 'select2';
import 'select2/dist/css/select2.min.css';
import 'select2-theme-bootstrap4/dist/select2-bootstrap.min.css';
let $;
select2();

export default {
    name: "VueField",
    components:{
        Autocomplete
    },
    inject: ['labelSize', 'formOrientation', 'setField', 'emitter'],
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
        labelClass: function(){
            return 'col-12 col-sm-' + this.labelSize.small + ' col-md-' + this.labelSize.medium + ' col-lg-' +
                this.labelSize.large + (this.inputType != 3? ' col-form-label' : '');
        },
        inputClass: function(){
            return 'col-12 col-sm-' + (12 - this.labelSize.small) + ' col-md-' + (12 - this.labelSize.medium)
                + ' col-lg-' + (12 - this.labelSize.large);
        },
        inputType: function(){
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
        textType: function(){
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
                        return {
                            results
                        };
                    }
                };
                if(defaultValue){
                    element.empty();
                    for(let key in defaultValue)
                        element.append($('<option/>').attr('value', key).attr('selected', true).text(defaultValue[key]));
                }
            }
            element.off().select2(options).on('change', function(){
                let value = $(this).val();
                field.value = value;
                field.setField(field.name, value);
            });
            this.$nextTick(() => element.trigger('change'));
        }
    },
    created(){
        $ = this.$root.jQuery();
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