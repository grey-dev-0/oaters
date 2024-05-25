<template>
    <li :class="style" :date-toggle="collapseTarget? 'collapse' : false" :data-target="collapseTarget || false">
        <slot></slot>
    </li>
</template>

<script>
export default {
    name: 'ListItem',
    props: {
        collapseTarget: String
    },
    computed: {
        style(){
            let theClass ='list-group-item';
            if(this.collapseTarget)
                theClass += ' cursor-pointer';
            return theClass;
        }
    }
}
</script>