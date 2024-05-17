<template>
    <div :class="'avatar w-100 overflow-hidden position-relative bg-' + color">
        <img v-if="showImage" class="w-100 h-100 d-block position-absolute" :src="image" alt="image" onerror="showImage = false">
        <strong v-else ref="initsElement" class="initials text-white w-100 h-100 text-center d-block position-absolute">{{initials}}</strong>
    </div>
</template>

<script>
let $;
export default {
    name: 'Avatar',
    props: {
        image: String,
        name: {
            type: String,
            required: true
        },
        color: {
            type: String,
            default: 'grey-2'
        }
    },
    computed: {
        initials(){
            let all = this.name.split(/ +/);
            let inits = [all[0].charAt(0).toUpperCase()];
            if(all.length > 1)
                inits[1] = all[all.length - 1].charAt(0).toUpperCase();
            return inits.join(' ');
        }
    },
    data(){
        return {
            fontSize: '12px',
            showImage: false
        };
    },
    methods: {
        setInitsFontSize(){
            let initsElement = this.$refs.initsElement;
            if(!initsElement)
                return;
            this.fontSize = Math.floor($(initsElement).closest('.avatar').outerWidth() / 2.0) + 'px';
        }
    },
    created(){
        $ = this.$root.jQuery();
    },
    mounted(){
        this.showImage = !!this.image;
        this.$nextTick(this.setInitsFontSize);
        $('body').on('shown.bs.modal', '.modal', () => {
            this.setInitsFontSize();
        });
    }
};
</script>

<style lang="scss">
.avatar{
    border-radius: 50%;

    img, strong{
        top: 0;
        left: 0;
    }

    strong{
        font-size: v-bind(fontSize);
        line-height: 2;
    }

    &::after{
        content: '';
        display: block;
        padding-bottom: 100%;
    }
}
</style>