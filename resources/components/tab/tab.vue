<template>
<li class="nav-item">
    <button :class="style" data-toggle="tab" :data-target="target" type="button" role="tab"><slot></slot></button>
</li>
</template>

<script>
export default {
    name: 'Tab',
    inject: ['activeTab'],
    props: {
        target: {
            type: String,
            required: true
        }
    },
    data(){
        return {
            active: false
        };
    },
    computed: {
        style(){
            let style = 'nav-link';
            if(this.active)
                style += ' active';
            return style;
        }
    },
    mounted(){
        if(this.activeTab == this.target)
            this.active = true;
    }
};
</script>