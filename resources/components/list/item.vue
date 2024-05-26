<template>
    <li :id="id" :class="style" :data-toggle="collapseTarget? 'collapse' : null" :data-target="collapseTarget || null" :role="collapseTarget? 'button' : null">
        <slot></slot>
    </li>
</template>

<script>
export default {
    name: 'ListItem',
    props: {
        id: String,
        collapseTarget: String,
        collapse: Boolean,
    },
    computed: {
        style(){
            let theClass ='list-group-item';
            if(this.collapseTarget)
                theClass += ' cursor-pointer';
            if(this.collapse)
                theClass += ' collapse p-0';
            return theClass;
        }
    }
}
</script>