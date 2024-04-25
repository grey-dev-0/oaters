<template>
    <table class="table table-hover table-responsive d-table" :id="datatableId">
        <thead>
        <tr>
            <slot></slot>
        </tr>
        </thead>
        <tbody></tbody>
        <tfoot>
        <slot name="footer"></slot>
        </tfoot>
    </table>
</template>

<script>
import 'datatables.net';
import DataTable from 'datatables.net-bs4';
import 'datatables.net-bs4/css/dataTables.bootstrap4.min.css';
import emitter from "mitt";
let $;

export default {
    name: 'VueDatatable',
    props: {
        datatableId: {
            type: String
        },
        deferred: {
            type: Boolean,
            default: false
        },
        url: {
            type: String,
            default: window.location.href
        },
        sort: {
            type: Array,
            default: function(){
                return [[0, 'asc']];
            }
        },
        dom: {
            type: String,
            default: '<"d-flex justify-content-between align-items-center mx-0 row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 text-right"f>>t<"d-flex justify-content-between mx-0 row mt-2"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>'
        },
        ajaxComplete: {
            default: null
        },
        ajaxData: {
            type: Object,
            default: function(){
                return {};
            }
        },
        renderActions: Function
    },
    data: function(){
        return {
            columns: [],
            dataTable: null
        };
    },
    methods: {
        getRef(){
            for(var ref in this.$root.$refs)
                if(this.$root.$refs[ref] === this)
                    return ref;
        },
        init: function(){
            if(this.dataTable !== null)
                this.dataTable.destroy();
            this.dataTable = $('#' + this.datatableId).DataTable({
                order: this.sort,
                columns: this.columns,
                autoWidth: false,
                responsive: true,
                scrollX: true,
                dom: this.dom,
                lengthMenu: [25, 50, 100, 200],
                pageLength: 25,
                processing: true,
                serverSide: true,
                ajax: {
                    url: this.url,
                    type: 'POST',
                    data: this.ajaxData,
                    complete: this.ajaxComplete
                }
            });
            this.$root.emitter.emit('initialized', {ref: this.getRef()});
        }
    },
    created(){
        $ = this.$root.jQuery();
        this.$root.emitter = emitter();
        DataTable.type('date', 'className', '');
        DataTable.type('numeric', 'className', '');
    }
};
</script>

<style lang="scss">
.dt-container{
    margin: 16px 0 16px;

    td{
        padding: 8px 18px !important;
        vertical-align: inherit !important;
    }

    .btn-sm{
        min-width: 32px;
    }

    .nowrap{
        white-space: nowrap;
    }

    .dt-info{
        float: left;
    }

    .dt-paging{
        float: right;
    }
}
</style>
