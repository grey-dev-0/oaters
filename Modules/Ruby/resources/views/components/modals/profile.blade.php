<modal id="profile-modal" ref="profileModal" color="primary" size="lg">
    <template #header>Profile - @{{openContact.name}}</template>
    <div class="row">
        <div class="col-12 col-sm-6 col-md-4">
            <avatar v-if="openContact.id" :name="openContact.name" :image="openContact.image"></avatar>
            <strong class="d-block mt-4">@{{openContact.name}}</strong>
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
        </div>
    </div>
</modal>