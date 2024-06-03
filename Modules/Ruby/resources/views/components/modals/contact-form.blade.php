@props(['id', 'ref', 'color', 'title', 'edit' => false])

<modal id="{{$id}}" ref="{{$ref}}" static size="lg" color="{{$color}}">
    <template #header>{{$title}}@if($edit) - @{{ openContact.name }}@endif</template>
    <vue-form id="{{$id}}-form" ref="{{$ref}}Form" large="3" ajax action="{{url('r/contacts/'.($edit? 'update' : 'create'))}}">
        @if($edit)
            <input type="hidden" name="id" :value="openContact.id">
        @endif
        <tabs active-tab="#user">
            <template #navigation>
                <tab target="#user">{{trans('ruby::contacts.user')}}</tab>
                <tab target="#contact">{{trans('ruby::contacts.contact')}}</tab>
                <tab target="#applicant">{{trans('ruby::contacts.applicant')}}</tab>
            </template>
            <pane id="user">
                <vue-field name="username" type="text" id="{{($edit)? 'e_' : ''}}username">{{trans('sapphire::admin.login.username')}}</vue-field>
                <vue-field name="password" type="password" id="{{($edit)? 'e_' : ''}}password">{{trans('sapphire::admin.login.password')}}</vue-field>
                <vue-field name="password_confirmation" type="password" id="{{($edit)? 'e_' : ''}}password-confirmation">{{trans('sapphire::admin.login.password_confirm')}}</vue-field>
            </pane>
            <pane id="contact">
                <h4>TBD</h4>
            </pane>
            <pane id="applicant">
                <h4>TBD 2</h4>
            </pane>
        </tabs>
    </vue-form>
    <template #footer>
        <div class="btn btn-outline-secondary" data-dismiss="modal">Cancel</div>
        <div class="btn btn-{{$color}} text-white" @click="submit('{{$ref}}')">Save</div>
    </template>
</modal>