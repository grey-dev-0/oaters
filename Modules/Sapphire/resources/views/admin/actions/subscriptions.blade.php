<div vue-template data-id="">
    <div class="btn btn-sm btn-outline-primary edit mr-2" title="{{trans('common::words.edit')}}" @click="editSubscription"><i class="fas fa-pen"></i></div>
    <div class="btn btn-sm btn-outline-warning revoke mr-2" title="{{trans('sapphire::admin.subscriptions.revoke')}}" @click="revokeSubscription"><i class="fas fa-ban"></i></div>
    <div class="btn btn-sm btn-outline-danger delete" title="{{trans('common::words.delete')}}" @click="deleteSubscription"><i class="fas fa-trash-alt"></i></div>
</div>
