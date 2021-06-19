<template>
    <li v-if="url !== undefined && !inDropdown" :class="'nav-item' + (active? ' active' : '')">
        <a class="nav-link" :href="url"><slot></slot></a>
    </li>
    <a :href="url" v-else-if="inDropdown" class="dropdown-item"><slot></slot></a>
    <li v-else :class="'nav-item dropdown' + (active? ' active' : '')">
        <a class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown"><slot name="label"></slot></a>
        <div :class="'dropdown-menu' + ($parent.bgColor != 'light'? ' ' + dropBgColor : '')">
            <slot></slot>
        </div>
    </li>
</template>

<script>
export default {
    name: 'NavItem',
    props: {
        url: String,
        inDropdown: {
            type: Boolean,
            default: false
        },
        active: {
            type: Boolean,
            default: false
        }
    },
    computed: {
        dropBgColor: function(){
            return 'bg-' + this.$parent.bgColor.replace(/-[0-9]+$/, '-10');
        }
    }
};
</script>