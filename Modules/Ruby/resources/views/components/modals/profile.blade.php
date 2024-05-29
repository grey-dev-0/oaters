<modal id="profile-modal" ref="profileModal" color="primary" size="lg" :on-close="closeContact">
    <template #header>Profile - @{{openContact.name}}</template>
    <v-loader v-if="!openContact.addresses"></v-loader>
    <div :class="'row' + (openContact.addresses? '' : ' invisible')">
        <div class="col-12 col-sm-6 col-md-4">
            <avatar v-if="openContact.id" :name="openContact.name" :image="openContact.image"></avatar>
            <h5 class="text-center mt-2 mb-0 font-weight-bolder">@{{openContact.name}}</h5>
            <small class="d-block text-center text-muted mb-2" v-if="openContact.roles">@{{ openContact.roles[0].title }}</small>
            <small class="d-block text-muted mb-2" v-if="openContact.role">@{{openContact.role.title}}</small>
            <p v-if="openContact.phones" v-for="(phone, i) in openContact.phones" :class="'text-truncate' + (openContact.phones && i != openContact.phones.length - 1? ' mb-0' : '')" :title="phone.number">
                <i class="fas fa-phone fa-fw mr-1" v-if="i == 0"></i>
                <i class="fa fa-fw mr-1" v-else></i>
                <strong v-if="openContact.phones.length == 1 || phone.default">@{{phone.number}}</strong>
                <template v-else>@{{phone.number}}</template>
            </p>
            <p v-if="openContact.emails" v-for="(email, i) in openContact.emails" :class="'text-truncate' + (openContact.emails && i != openContact.emails.length - 1? ' mb-0' : '')" :title="email.address">
                <i class="fas fa-envelope fa-fw mr-1" v-if="i == 0"></i>
                <i class="fa fa-fw mr-1" v-else></i>
                <strong v-if="openContact.emails.length == 1 || email.default">@{{email.address}}</strong>
                <template v-else>@{{email.address}}</template>
            </p>
            <p v-if="openContact.managed_departments" v-for="(department, i) in openContact.managed_departments" :class="'text-truncate' + (openContact.managed_departments && i != openContact.managed_departments.length - 1? ' mb-0' : '')" :title="department.name">
                <i class="fas fa-briefcase fa-fw mr-1" v-if="i == 0"></i>
                <i class="fa fa-fw mr-1" v-else></i>
                @{{department.name}}
            </p>
            <p v-if="openContact.departments" v-for="(department, i) in openContact.departments" :class="'text-truncate' + (openContact.departments && i != openContact.departments.length - 1? ' mb-0' : '')" :title="department.name">
                <i class="fas fa-house-chimney-user fa-fw mr-1" v-if="i == 0"></i>
                <i class="fa fa-fw mr-1" v-else></i>
                @{{department.name}}
            </p>
        </div>
        <div class="col-12 col-sm-6 col-md-8">
            <h5 class="m-0 font-weight-bolder">{{trans('ruby::contacts.job')}}</h5>
            <p>@{{openContact.job}}</p>
            <template v-if="openContact.applicant && openContact.applicant.degree">
                <h5 class="m-0 font-weight-bolder">{{trans('ruby::applicants.degree')}}</h5>
                <p>@{{openContact.applicant.degree.name}} - {{trans('common::words.year')}} @{{ openContact.applicant.degree_date }}</p>
            </template>
            <template v-if="openContact.applicant && openContact.applicant.tenure">
                <h5 class="m-0 font-weight-bolder">{{trans('ruby::applicants.tenure')}}</h5>
                <p>
                    @{{tenureYears}} {{trans('common::words.years')}}
                    <template v-if="tenureMonths">@{{tenureMonths}} {{trans('common::words.months')}}</template>
                </p>
            </template>
            <template v-if="openContact.applicant">
                <h5 class="m-0 font-weight-bolder">{{trans('ruby::applicants.recruited_at')}}</h5>
                <p>@{{openContact.applicant.recruited_at_formatted}}</p>
            </template>
            <template v-if="openContact.addresses && openContact.addresses.length">
                <h5 class="m-0 font-weight-bolder">{{trans('common::words.addresses')}}</h5>
                <ul>
                    <li v-for="address in openContact.addresses">@{{address.country.name}}</li>
                </ul>
            </template>
            <template v-if="openContact.leaves && openContact.leaves.length">
                <h5 class="font-weight-bolder">{{trans('ruby::contacts.recent_leaves')}}</h5>
                <timeline class="mb-2">
                    <template v-for="leave in openContact.leaves">
                        <timeline-entry color="success">
                            <strong>@{{leave.ends_at}}</strong> @{{locale.leaves.types[leave.type]}} ({{trans('common::words.end')}})
                        </timeline-entry>
                        <template-entry v-if="leave.current" color="amber-2">
                            <strong>@{{leave.current}}</strong> @{{locale.leaves.types[leave.type]}} ({{trans('common::words.today')}}, {{trans('ruby::contacts.on_leave')}})
                        </template-entry>
                        <timeline-entry color="primary">
                            <strong>@{{leave.starts_at}}</strong> @{{locale.leaves.types[leave.type]}} ({{trans('common::words.start')}})
                        </timeline-entry>
                    </template>
                </timeline>
            </template>
            <list v-if="openContact.applicant && openContact.applicant.documents && openContact.applicant.documents.length">
                <list-item collapse-target="#documents"><strong>{{trans('common::words.documents')}}</strong></list-item>
                <list-item id="documents" collapse>
                    <list>
                        <list-item v-for="document in openContact.applicant.documents">
                            @{{document.title}}
                            <a :href="document.download_url" target="_blank" class="btn btn-sm btn-outline-primary float-right" title="{{trans('common::words.download')}}"><i class="fa fas fa-download mr-2"></i>{{trans('common::words.download')}}</a>
                        </list-item>
                    </list>
                </list-item>
            </list>
        </div>
    </div>
</modal>