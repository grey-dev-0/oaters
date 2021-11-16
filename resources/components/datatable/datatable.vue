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
var $ = window.$;

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
      default: ''
    },
    sort: {
      type: Array,
      default: function(){
        return [[0, 'asc']];
      }
    },
    dom : {
      type: String,
      default: 'lfrtip'
    },
    ajaxComplete: {
      default: null
    },
    ajaxData:{
      type: Object,
      default: function(){
        return {};
      }
    }
  },
  data: function(){
    return {
      columns: [],
      dataTable: null
    };
  },
  methods: {
    init: function(){
      if(this.dataTable !== null)
        this.dataTable.destroy();
      this.dataTable = $('#'+this.datatableId).DataTable({
        order: this.sort,
        dom: this.dom,
        columns: this.columns,
        autoWidth: false,
        responsive: true,
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
    }
  }
};
</script>

<style lang="scss">
.dataTables_wrapper{
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
}
</style>
