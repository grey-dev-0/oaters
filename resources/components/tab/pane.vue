<template>
    <div :class="style" :id="id">
        <slot></slot>
    </div>
</template>

<script>
export default {
    name: 'Pane',
    inject: ['activeTab'],
    props: {
        id: {
            type: String,
            required: true
        }
    },
    computed: {
        style(){
            let style = 'tab-pane fade';
            if(this.activeTab == '#' + this.id)
                style += ' show active';
            return style;
        }
    }
};
</script>