@php
$edit ??= false;
@endphp

<modal id="{{$id}}" ref="{{$ref}}" static size="lg" color="{{$color}}">
    <template #header>{{$title}}@if($edit) - @{{ openDepartment.name }}@endif</template>
    <vue-form id="{{$id}}-form" ref="{{$ref}}Form" large="3" ajax action="{{url('r/departments/'.($edit? 'update' : 'create'))}}">
        @if($edit)
            <input type="hidden" name="id" :value="openDepartment.id">
        @endif
        <vue-field name="en[name]" type="text" id="{{($edit)? 'e_' : ''}}name-en">{{trans('common::words.name')}} ({{trans('common::words.english')}})</vue-field>
        <vue-field name="ar[name]" type="text" id="{{($edit)? 'e_' : ''}}name-ar">{{trans('common::words.name')}} ({{trans('common::words.arabic')}})</vue-field>
        <vue-field name="manager_id" type="autocomplete" id="{{($edit)? 'e_' : ''}}manager-id" url="{{route('ruby::contacts.search')}}">{{trans('ruby::departments.head')}}</vue-field>
        <vue-field name="contact_id" type="select2" multiple id="{{($edit)? 'e_' : ''}}contact-id" url="{{route('ruby::contacts.search')}}">
            {{trans('ruby::words.staff')}}
        </vue-field>
    </vue-form>
    <template #footer>
        <div class="btn btn-outline-secondary" data-dismiss="modal">Cancel</div>
        <div class="btn btn-{{$color}} text-white" @click="submit('{{$ref}}')">Save</div>
    </template>
</modal>
