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
            let actions = $('<div/>'), elements = $(this.$refs.actions).children(), idKey;
            elements.each(function(index, element){
                element = $(element).clone();
                idKey = element.attr('data-id');
                element.attr('data-id', row[idKey || 'id'] || '');
                actions.append(element).append(' ');
            });
            if(this.$parent.renderActions)
                this.$parent.renderActions(row, actions.children());
            return actions.html();
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
