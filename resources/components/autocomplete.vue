<template>
    <div class="autocomplete-group" v-if="!multiple">
        <input type="text" class="form-control autocomplete-input" :id="id + '-input'" :placeholder="placeholder" v-model="query" @keyup="search" @focus="search" @keyup.esc="close()" @focusout="close(true)" autocomplete="off" :required="required">
        <input type="hidden" class="autocomplete-value" :id="id + '-value'" :name="name || null" v-model="selection" :required="required">
        <span v-if="!!selection" class="autocomplete-clear text-muted" @click="clear">&times;</span>
        <ul :class="dropdownClass" :id="id + '-selection'">
            <li class="dropdown-item" v-for="(value, key) in suggestions" :key="key" @click="select(key, value)">{{value}}</li>
        </ul>
    </div>
    <select v-else class="form-control" :id="id" :name="name || null" multiple :required="required">
        <slot name="options"></slot>
    </select>
</template>

<script>
import {debounce as _debounce} from "lodash";
let $;

export default {
    name: "Autocomplete",
    props: {
        id: {
            type: String,
            required: true
        },
        name: String,
        placeholder: String,
        multiple: {
            type: Boolean,
            default: false
        },
        required: {
            type: Boolean,
            default: false
        },
        url: {
            type: String,
            required: true
        },
        queryKey: {
            type: String,
            default: 'query'
        },
        resultsKey: {
            type: String,
            default: 'suggestions'
        },
        limit: {
            type: Number,
            default: 5
        },
        selectedId: String,
        selectedTitle: String
    },
    data: function(){
        return {
            show: false,
            suggestions: [],
            query: '',
            selection: ''
        };
    },
    computed: {
        dropdownClass: function(){
            var classes = ['dropdown-menu', 'autocomplete-dropdown'];
            if(this.show && !Array.isArray(this.suggestions) && Object.values(this.suggestions).length)
                classes.push('show');
            return classes.join(' ');
        }
    },
    methods: {
        search: _debounce(function(e){
            var keyCode = e.keyCode || e.which;
            if(keyCode == 27)
                return;
            var request = {limit: this.limit};
            request[this.queryKey] = this.query;
            var component = this;
            $.ajax({
                url: this.url,
                type: 'POST',
                data: request,
                success: function(response){
                    var results = response[component.resultsKey];
                    var suggestions = component.suggestions = [];
                    if(Array.isArray(results))
                        results.forEach(function(result){
                            suggestions[result] = result;
                        });
                    else
                        suggestions = results;
                    component.suggestions = suggestions;
                    component.show = true;
                },
                error: function(xhr){
                    alert(xhr.responseText);
                }
            });
        }, 200),
        select: function(selectedKey, selectedValue){
            this.selection = selectedKey;
            this.query = selectedValue;
            this.show = false;
            this.$emit('change', this.name, selectedKey);
        },
        clear: function(){
            this.query = '';
            this.selection = '';
            this.$emit('change', this.name, null);
        },
        close(delayed){
            if(!delayed)
                this.suggestions = [];
            else{
                var component = this;
                setTimeout(function(){
                    component.suggestions = [];
                }, 250);
            }
        }
    },
    created(){
        $ = this.$root.jQuery();
    },
    mounted(){
        if(this.$parent.$options.name != 'VueField')
            return;
        if(this.selectedId !== undefined)
            this.selection = this.selectedId;
        if(this.selectedTitle !== undefined)
            this.query = this.selectedTitle;
        var component = this;
        this.$nextTick(function(){
            this.$parent.$parent.$parent.emitter.on('destroy', function(fieldName){
                if(component.name == fieldName)
                    component.clear();
            });
        });
        if(!this.multiple)
            return;
        var input = $('#' + this.id);
        input.select2({
            placeholder: this.placeholder,
            ajax: {
                url: this.url,
                type: 'POST',
                delay: 200,
                data(parameters){
                    var request = {limit: component.limit};
                    request[component.queryKey] = parameters.term;
                    return request;
                },
                processResults(response){
                    var results = response[component.resultsKey], processed = [];
                    for(var i in results)
                        processed.push({id: i, text: results[i]});
                    return {results: processed};
                }
            }
        });
        input.on('change', function(){
            component.$emit('change', component.name, $(this).val());
        });
    }
}
</script>

<style lang="scss">
.autocomplete-group{
    position: relative;

    .autocomplete-clear{
        display: block;
        position: absolute;
        top: 6px;
        right: 12px;
        cursor: pointer;
        font-size: 1.5em;
        line-height: 1;
    }

    .autocomplete-dropdown{
        li{
            cursor: pointer;
        }
    }
}
</style>