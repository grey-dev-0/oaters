<template>
    <table :id="id" :class="tableClass">
        <thead>
        <tr>
            <slot></slot>
        </tr>
        </thead>
        <tbody>
        <template v-if="!!$slots.rows">
            <slot name="rows"></slot>
        </template>
        </tbody>
    </table>
</template>

<script>
export default {
    name: "VueTable",
    data: function(){
        return {
            columns: []
        };
    },
    props: {
        id: String,
        'class': String,
        url: String
    },
    computed: {
        tableClass: function(){
            var tableClass = 'table';
            if(this.class !== undefined)
                tableClass += ' ' + this.class;
            return tableClass;
        }
    }
}
</script>