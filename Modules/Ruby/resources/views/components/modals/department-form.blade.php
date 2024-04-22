<modal id="{{$id}}" ref="{{$ref}}" static size="lg" color="{{$color}}">
    <template #header>{{$title}}@if($edit?? false) - @{{ openDepartment.name }}@endif</template>
    <vue-form id="{{$id}}-form" ref="{{$ref}}Form" large="3" ajax action="{{url('r/departments/create')}}">
        @if($edit?? false)
            <input type="hidden" name="id" :value="openDepartment.id">
        @endif
        <vue-field name="en[name]" type="text" id="{{($edit?? false)? 'e_' : ''}}name-en">{{trans('common::words.name')}} ({{trans('common::words.english')}})</vue-field>
        <vue-field name="ar[name]" type="text" id="{{($edit?? false)? 'e_' : ''}}name-ar">{{trans('common::words.name')}} ({{trans('common::words.arabic')}})</vue-field>
        <vue-field name="manager_id" type="autocomplete" id="{{($edit?? false)? 'e_' : ''}}manager-id" url="{{route('ruby::contacts.search')}}">{{trans('ruby::departments.head')}}</vue-field>
        <vue-field name="contact_id" type="select2" multiple id="{{($edit?? false)? 'e_' : ''}}contact-id" url="{{route('ruby::contacts.search')}}">
            {{trans('ruby::words.staff')}}
        </vue-field>
    </vue-form>
</modal>
