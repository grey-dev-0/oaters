<modal id="attendance-settings" ref="attendanceSettings" static color="green-1">
    <template #header>{{trans('ruby::attendance.settings')}}</template>
    <vue-form id="attendance-settings-form" ref="attendanceSettingsForm" ajax action="{{url('r/attendance/settings')}}">
        <vue-field name="threshold" type="number" id="attendance-threshold">{{trans('ruby::attendance.threshold')}}</vue-field>
        <small class="form-text text-muted w-100">{{trans('ruby::attendance.threshold_hint')}}</small>
    </vue-form>
    <template #footer>
        <div class="btn btn-outline-secondary" data-dismiss="modal">{{trans('ruby::attendance.cancel')}}</div>
        <div class="btn btn-green-1 text-white" @click="submitSettings">{{trans('common::words.save')}}</div>
    </template>
</modal>
