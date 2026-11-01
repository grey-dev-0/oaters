<div vue-template>
    <span class="text-primary profile mr-2" role="button" title="{{trans('common::words.profile')}}" @click="showProfile"><i class="fas fa-lg fa-address-card"></i></span>
    <span class="text-success edit mr-2" role="button" title="{{trans('common::words.edit')}}" @click="editContact"><i class="far fa-lg fa-edit"></i></span>
    <span class="text-danger delete" role="button" title="{{trans('common::words.delete')}}" @click="deleteContact"><i class="fas fa-lg fa-trash-can"></i></span>
    <span class="text-warning reactivate" role="button" title="{{trans('common::words.reactivate')}}" @click="reactivateContact" style="display: none;"><i class="fas fa-lg fa-rotate-left"></i></span>
</div>
