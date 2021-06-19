<template>
    <div class="card counter">
        <h4 :class="headerClass">{{title}}</h4>
        <div class="card-body text-center">{{value}}</div>
        <div class="card-footer text-center" v-if="extraTitle !== undefined">
            {{extraTitle}}: {{extraValue}}
        </div>
    </div>
</template>

<script>
export default {
    name: "Counter",
    props: {
        title: {
            type: String,
            required: true
        },
        value: {
            type: String,
            required: true
        },
        extraTitle: String,
        extraValue: String,
        color: String,
        whiteText: {
            type: Boolean,
            default: false
        }
    },
    computed: {
        headerClass: function(){
            var theClass = 'card-header mb-0 text-center';
            if(this.color !== undefined){
                theClass += ' bg-' + this.color;
                if(parseInt(this.color.replace(/[^0-9]/g, '')) <= 5)
                    theClass += ' text-white';
            }
            return theClass;
        }
    }
}
</script>

<style lang="scss">
.card.counter{
    .card-body{
        font-weight: bolder;
        font-size: 2em;
    }
}
</style>