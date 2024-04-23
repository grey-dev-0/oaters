<template>
    <th>
        <slot></slot>
        <div class="d-none" ref="actions" v-if="!!$slots.actions">
            <slot name="actions"></slot>
        </div>
    </th>
</template>

<script>
let $;

export default {
    name: 'DtColumn',
    props: {
        data: [String, Function],
        render: [String, Function],
        name: {
            type: String,
            default: undefined
        },
        searchable: {
            type: Boolean,
            default: true
        },
        orderable: {
            type: Boolean,
            default: true
        },
        visible: {
            type: Boolean,
            default: true
        },
        width: {
            type: String,
            default: undefined
        },
        defaultContent: String,
        className: String
    },
    methods: {
        renderActions(row){
            let actions = [], elements = $(this.$refs.actions).children(), parameters, attribute, href;
            elements.each(function(index, element){
                element = $(element).clone();
                href = element.attr('href');
                parameters = row.actions;
                if(href)
                    element.attr('href', parameters.url);
                else
                    for(attribute in parameters)
                        element.data(attribute, parameters[attribute]);
                actions.push($('<div/>').append(element).html());
            });
            return actions.join(' ');
        }
    },
    created(){
        $ = this.$root.jQuery();
    },
    mounted(){
        let column = {},
            props = ['data', 'render', 'name', 'searchable', 'orderable', 'visible', 'defaultContent', 'className', 'width'];
        for(let i in props)
            if(this.$props[props[i]] !== undefined)
                column[props[i]] = this.$props[props[i]];
        if(!!this.$slots.actions)
            column.data = this.renderActions;
        this.$parent.columns.push(column);
        if(this.$parent.$slots.default().length == this.$parent.columns.length && !this.$parent.deferred)
            this.$parent.init();
    }
}
</script>
