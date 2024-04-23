<template>
    <div class="modal fade" :id="id" :data-backdrop="(this.static)? 'static' : null" :data-keyboard="(this.static)? 'false' : null" :style="zIndex? ('z-index: ' + (1050 + zIndex * 10)) : null">
        <div :class="dialogClass">
            <div :class="'modal-content border-' + color">
                <div v-if="!!$slots.header" :class="'modal-header bg-' + color">
                  <component :is="titleTag" class="text-white m-0">
                      <slot name="header"></slot>
                  </component>
                  <span class="close text-muted" data-dismiss="modal">&times;</span>
                </div>
                <div :class="bodyClass">
                    <slot></slot>
                </div>
                <div v-if="!!$slots.footer" class="modal-footer">
                    <slot name="footer"></slot>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
let $;

export default {
    name: "Modal",
    props: {
        id: {
            type: String,
            required: true
        },
        size: String,
        'static': {
            type: Boolean,
            default: false
        },
        centered: {
            type: Boolean,
            default: false
        },
        color: {
            type: String,
            default: 'grey-10'
        },
        titleTag: {
            type: String,
            default: 'h3'
        },
        noPadding: {
            type: Boolean,
            default: false
        },
        zIndex: Number
    },
    computed: {
        dialogClass: function(){
            var theClass = 'modal-dialog';
            if(this.size)
                theClass += ' modal-' + this.size;
            if(this.centered)
                theClass += ' modal-dialog-centered';
            return theClass;
        },
        bodyClass: function(){
            var theClass = 'modal-body';
            if(this.noPadding)
                theClass += ' p-0';
            return theClass;
        }
    },
    methods: {
        show(reset){
            if(reset !== undefined)
                reset();
            $('#' + this.id).modal('show');
        },
        hide(){
            $('#' + this.id).modal('hide');
        }
    },
    created(){
        $ = this.$root.jQuery();
    },
    mounted(){
        if(this.zIndex)
            $('body').on('show.bs.modal', '#' + this.id, () => {
                setTimeout(() => {
                    $('.modal-backdrop.show:last-of-type').css('z-index', 1050 + this.zIndex * 10 - 1);
                }, 100);
            });
    }
}
</script>

<style>
.modal .close{
    cursor: pointer;
}
</style>