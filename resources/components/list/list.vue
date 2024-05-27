<template>
    <ul :class="style">
        <slot></slot>
    </ul>
</template>

<script>
export default {
    name: 'List',
    computed: {
        style(){
            let theClass ='v-list-group list-group';
            if(this.$parent.collapse)
                theClass += ' list-group-flush';
            return theClass;
        }
    }
};
</script>

<style lang="scss">
.v-list-group{
    .list-group-item[data-toggle="collapse"]{
        &:has(+ .collapse, +.collapsing)::after{
            text-rendering: auto;
            -webkit-font-smoothing: antialiased;
            padding-top: 2px;
        }

        &:has(+ .collapse:not(.show))::after{
            display: block;
            float: right;
            font: var(--fa-font-solid);
            content: '\f102';
        }

        &:has(+ .collapse.show, + .collapsing)::after{
            display: block;
            float: right;
            font: var(--fa-font-solid);
            content: '\f103';
        }
    }
}
</style>