<template>
    <div class="autocomplete-group">
        <input type="text" class="form-control autocomplete-input" :id="id + '-input'" :placeholder="placeholder" v-model="query" @keyup="search()" autocomplete="off">
        <input type="hidden" class="autocomplete-value" :id="id + '-value'" :name="name || null" v-model="selection">
        <span v-if="!!selection" class="autocomplete-clear text-muted" @click="clear">&times;</span>
        <ul :class="dropdownClass" :id="id + '-selection'">
            <li class="dropdown-item" v-for="(value, key) in suggestions" :key="key" @click="select(key, value)">{{value}}</li>
        </ul>
    </div>
</template>

<script>
let $ = window.$;
let _ = window._;

export default {
    name: "Autocomplete",
    props: {
        id: {
            type: String,
            required: true
        },
        name: String,
        placeholder: String,
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
        }
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
        search: _.debounce(function(){
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
        }
    },
    mounted: function(){
        if(this.$parent.$options.name != 'VueField')
            return;
        var autocomplete = this;
        this.$nextTick(function(){
            this.$parent.$parent.emitter.on('destroy', function(fieldName){
                if(autocomplete.name == fieldName)
                    autocomplete.clear();
            });
        })
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