<template>
  <th>
    <slot></slot>
    <slot name="actions"></slot>
  </th>
</template>

<script>
var $ = window.$;

export default {
  name: 'DtColumn',
  props: {
    data: [String, Function],
    render: [String, Function],
    name: {
      type: String,
      default: undefined
    },
    searchable: {
      type: Boolean,
      default: true
    },
    orderable: {
      type: Boolean,
      default: true
    },
    visible: {
      type: Boolean,
      default: true
    },
    width: {
      type: String,
      default: undefined
    },
    defaultContent: String,
    className: String
  },
  methods: {
    hideActionsTemplate: function(){
      var element;
      this.$slots.actions().forEach(function(action){
        element = $(action.el);
        element.addClass('d-none');
      });
    },
    renderActions(row){
      var actions = [], element, idKey;
      this.$slots.actions().forEach(function(action){
        element = $(action.el).clone();
        idKey = element.attr('data-id');
        element.attr('data-id', row[idKey || 'id'] || '').removeClass('d-none');
        actions.push($('<div/>').append(element).html());
      });
      return actions.join(' ');
    }
  },
  mounted(){
    var column = {};
    var props = ['data', 'render', 'name', 'searchable', 'orderable', 'visible', 'defaultContent', 'className', 'width'];
    for(var i in props)
      if(this.$props[props[i]] !== undefined)
        column[props[i]] = this.$props[props[i]];
    if(this.$slots.actions !== undefined){
      column.data = this.renderActions;
      this.hideActionsTemplate();
    }
    this.$parent.columns.push(column);
    if(this.$parent.$slots.default().length == this.$parent.columns.length && !this.$parent.deferred)
      this.$parent.init();
  }
}
</script>
