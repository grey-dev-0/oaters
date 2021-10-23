<modal id="{{$id}}" ref="{{$ref}}" static size="lg" color="{{$color}}">
    <template #header>
        <h3 class="text-white m-0">{{$title}}@if($edit?? false) - @{{ openSubscription.name }}@endif</h3>
        <span class="close text-muted" data-dismiss="modal">&times;</span>
    </template>
    <vue-form id="{{$id}}-form" ref="{{$ref}}Form" ajax action="{{url('sa/subscriptions/create')}}">
        <vue-field name="tenant_id">{{trans('sapphire::admin.subscriptions.tenant')}}</vue-field>
        <vue-field name="module_ids" type="select2" multiple id="{{($edit?? false)? 'e_' : ''}}modules">
            {{trans('sapphire::admin.subscriptions.modules')}}
            <template #options>
                @foreach($modules as $module)
                    <option value="{{$module->id}}">{{$module->name}}</option>
                @endforeach
            </template>
        </vue-field>
        <vue-field name="price" type="number">{{trans('common::words.price')}}</vue-field>
        <vue-field name="discount" type="number">{{trans('common::words.discount')}}</vue-field>
        <vue-field name="discount_type" type="radio">
            {{trans('common::words.type')}}
            <template #options>
                <radio id="{{($edit?? false)? 'e_' : ''}}percent" name="discount_type" value="0">{{trans('common::words.percentage')}}</radio>
                <radio id="{{($edit?? false)? 'e_' : ''}}fixed" name="discount_type" value="1">{{trans('common::words.fixed')}}</radio>
            </template>
        </vue-field>
    </vue-form>
</modal>
