<modal id="profile-modal" ref="profileModal" color="primary" size="lg">
    <template #header>Profile - @{{openContact.name}}</template>
    <div class="row">
        <div class="col-12 col-sm-6 col-md-4">
            <avatar v-if="openContact.id" :name="openContact.name" :image="openContact.image"></avatar>
            <h5 class="text-center mt-2 mb-0 font-weight-bolder">@{{openContact.name}}</h5>
            <small class="d-block text-center text-muted mb-2" v-if="openContact.roles">@{{ openContact.roles[0].title }}</small>
            <small class="d-block text-muted mb-2" v-if="openContact.role">@{{openContact.role.title}}</small>
            <p v-if="openContact.phones" v-for="(phone, i) in openContact.phones" :class="openContact.phones && i != openContact.phones.length - 1? 'mb-0' : ''">
                <i class="fas fa-phone fa-fw mr-1" v-if="i == 0"></i>
                <i class="fa-fw mr-1" v-else></i>
                <strong v-if="openContact.phones.length == 1 || phone.default">@{{phone.number}}</strong>
                <template v-else>@{{phone.number}}</template>
            </p>
            <p v-if="openContact.emails" v-for="(email, i) in openContact.emails" :class="openContact.emails && i != openContact.emails.length - 1? 'mb-0' : ''">
                <i class="fas fa-envelope fa-fw mr-1" v-if="i == 0"></i>
                <i class="fa-fw mr-1" v-else></i>
                <strong v-if="openContact.emails.length == 1 || email.default">@{{email.address}}</strong>
                <template v-else>@{{email.address}}</template>
            </p>
        </div>
        <div class="col-12 col-sm-6 col-md-8">
            <h5 class="m-0 font-weight-bolder">{{trans('ruby::contacts.job')}}</h5>
            <p>@{{openContact.job}}</p>
            <template v-if="openContact.applicant && openContact.applicant.degree">
                <h5 class="m-0 font-weight-bolder">{{trans('ruby::applicants.degree')}}</h5>
                <p>@{{openContact.applicant.degree.name}}</p>
            </template>
            <template v-if="openContact.applicant && openContact.applicant.tenure">
                <h5 class="m-0 font-weight-bolder">{{trans('ruby::applicants.tenure')}}</h5>
                <p>@{{openContact.applicant.tenure}} {{trans('common::words.months')}}</p>
            </template>
            <template v-if="openContact.applicant">
                <h5 class="m-0 font-weight-bolder">{{trans('ruby::applicants.recruited_at')}}</h5>
                <p>@{{openContact.applicant.recruited_at}}</p>
            </template>
            <template v-if="openContact.addresses && openContact.addresses.length">
                <h5 class="m-0 font-weight-bolder">{{trans('common::words.addresses')}}</h5>
                <ul>
                    <li v-for="address in openContact.addresses">@{{address.country.name}}</li>
                </ul>
            </template>
        </div>
    </div>
</modal>