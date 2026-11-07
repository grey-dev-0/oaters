@props(['id', 'ref', 'color', 'title', 'edit' => false, 'roles', 'countries', 'degrees'])

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
                <vue-field name="image" type="file" id="{{($edit)? 'e_' : ''}}image">{{trans('ruby::contacts.profile_image')}}</vue-field>
                <vue-field name="username" type="text" id="{{($edit)? 'e_' : ''}}username">{{trans('sapphire::admin.login.username')}}</vue-field>
                <vue-field name="password" type="password" id="{{($edit)? 'e_' : ''}}password">{{trans('sapphire::admin.login.password')}}@if($edit) ({{trans('common::words.unassigned')}})@endif</vue-field>
                <vue-field name="password_confirmation" type="password" id="{{($edit)? 'e_' : ''}}password-confirmation">{{trans('sapphire::admin.login.password_confirm')}}</vue-field>
            </pane>
            <pane id="contact">
                <vue-field name="name" type="text" id="{{($edit)? 'e_' : ''}}name">{{trans('common::words.name')}}</vue-field>
                <vue-field name="job" type="text" id="{{($edit)? 'e_' : ''}}job">{{trans('ruby::contacts.job')}}</vue-field>

                <div class="form-group row">
                    <label class="col-12 col-sm-6 col-md-3 col-lg-3 col-form-label">{{trans('common::words.role')}}</label>
                    <div class="col-12 col-sm-6 col-md-9 col-lg-9">
                        <select name="roles[]" id="{{($edit)? 'e_' : ''}}contact-roles" :class="'form-control' + (error('roles')? ' border-danger' : '')" multiple v-model="form.roles">
                            @foreach($roles as $roleId => $roleTitle)
                                <option value="{{$roleId}}">{{$roleTitle}}</option>
                            @endforeach
                        </select>
                        <small class="text-danger d-block" v-if="error('roles')">@{{ error('roles') }}</small>
                    </div>
                </div>

                <h6 class="col-12 font-weight-bolder">{{trans('common::words.emails')}}</h6>
                <div class="form-group row col-12 m-0" v-for="(email, i) in form.emails" :key="'email-' + i">
                    <div class="col-md-2 pt-2">
                        <div class="form-check m-0">
                            <input class="form-check-input" type="radio" :id="'{{($edit)? 'e_' : ''}}email-default-' + i" :checked="email.default" @change="markDefault('emails', i)">
                            <label class="form-check-label" :for="'{{($edit)? 'e_' : ''}}email-default-' + i">{{trans('ruby::contacts.default')}}</label>
                        </div>
                        <input type="hidden" :name="'emails[' + i + '][default]'" :value="email.default ? 1 : 0">
                    </div>
                    <div class="col-md-7 p-0">
                        <input type="email" :name="'emails[' + i + '][address]'" v-model="email.address" :class="'form-control' + (error('emails[' + i + '][address]')? ' border-danger' : '')" placeholder="{{trans('common::words.email')}}" required>
                        <small class="text-danger d-block" v-if="error('emails[' + i + '][address]')">@{{ error('emails[' + i + '][address]') }}</small>
                    </div>
                    <div class="col-md-2 text-right">
                        <span class="text-danger" role="button" title="{{trans('common::words.remove')}}" v-if="form.emails.length > 1" @click="removeRow('emails', i)"><i class="far fa-lg fa-trash-can"></i></span>
                    </div>
                </div>
                <div class="col-12 text-right mb-2">
                    <span class="text-primary" role="button" @click="addRow('emails')"><i class="fas fa-plus"></i> {{trans('ruby::contacts.add_email')}}</span>
                </div>

                <h6 class="col-12 font-weight-bolder">{{trans('common::words.phones')}}</h6>
                <div class="form-group row col-12 m-0" v-for="(phone, i) in form.phones" :key="'phone-' + i">
                    <div class="col-md-2 pt-2">
                        <div class="form-check m-0">
                            <input class="form-check-input" type="radio" :id="'{{($edit)? 'e_' : ''}}phone-default-' + i" :checked="phone.default" @change="markDefault('phones', i)">
                            <label class="form-check-label" :for="'{{($edit)? 'e_' : ''}}phone-default-' + i">{{trans('ruby::contacts.default')}}</label>
                        </div>
                        <input type="hidden" :name="'phones[' + i + '][default]'" :value="phone.default ? 1 : 0">
                    </div>
                    <div class="col-md-7 p-0">
                        <input type="tel" :name="'phones[' + i + '][number]'" v-model="phone.number" maxlength="16" :class="'form-control' + (error('phones[' + i + '][number]')? ' border-danger' : '')" placeholder="{{trans('common::words.phone')}}" required>
                        <small class="text-danger d-block" v-if="error('phones[' + i + '][number]')">@{{ error('phones[' + i + '][number]') }}</small>
                    </div>
                    <div class="col-md-2 text-right">
                        <span class="text-danger" role="button" title="{{trans('common::words.remove')}}" v-if="form.phones.length > 1" @click="removeRow('phones', i)"><i class="far fa-lg fa-trash-can"></i></span>
                    </div>
                </div>
                <div class="col-12 text-right mb-2">
                    <span class="text-primary" role="button" @click="addRow('phones')"><i class="fas fa-plus"></i> {{trans('ruby::contacts.add_phone')}}</span>
                </div>

                <h6 class="col-12 font-weight-bolder">{{trans('common::words.addresses')}}</h6>
                <div class="form-group row col-12 m-0" v-for="(address, i) in form.addresses" :key="'address-' + i">
                    <div class="col-md-5 pl-0">
                        <autocomplete :id="'{{($edit)? 'e_' : ''}}city-' + i" :name="'addresses[' + i + '][city_id]'" :url="citiesUrl" :limit="10"
                                      :selected-id="address.city_id? String(address.city_id) : ''" :selected-title="address.city_name"
                                      placeholder="{{trans('ruby::contacts.city')}}" @change="(name, cityId, cityName) => { address.city_id = cityId; address.city_name = cityName; }">
                        </autocomplete>
                    </div>
                    <div class="col-md-3 p-0">
                        <input type="text" :name="'addresses[' + i + '][line_1]'" v-model="address.line_1" class="form-control" placeholder="{{trans('common::words.address')}} 1">
                    </div>
                    <div class="col-md-2 p-0">
                        <input type="text" :name="'addresses[' + i + '][line_2]'" v-model="address.line_2" class="form-control" placeholder="{{trans('common::words.address')}} 2">
                    </div>
                    <div class="col-md-2 text-right">
                        <span class="text-danger" role="button" title="{{trans('common::words.remove')}}" @click="removeRow('addresses', i)"><i class="far fa-lg fa-trash-can"></i></span>
                    </div>
                </div>
                <div class="col-12 text-right">
                    <span class="text-primary" role="button" @click="addRow('addresses')"><i class="fas fa-plus"></i> {{trans('ruby::contacts.add_address')}}</span>
                </div>
            </pane>
            <pane id="applicant">
                <div class="form-group row">
                    <div class="col-12">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" id="applicant-mode-new" value="new" v-model="form.applicantMode">
                            <label class="form-check-label" for="applicant-mode-new">{{trans('ruby::contacts.new_applicant')}}</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" id="applicant-mode-existing" value="existing" v-model="form.applicantMode">
                            <label class="form-check-label" for="applicant-mode-existing">{{trans('ruby::contacts.existing_applicant')}}</label>
                        </div>
                        <input type="hidden" name="applicant_mode" :value="form.applicantMode">
                    </div>
                </div>
                <vue-field v-if="form.applicantMode == 'existing'" name="applicant_id" type="autocomplete" id="{{($edit)? 'e_' : ''}}applicant" url="{{route('ruby::contacts.applicants')}}" placeholder="{{trans('ruby::contacts.select_applicant')}}">{{trans('ruby::applicants.select')}}</vue-field>

                <div class="form-group row">
                    <label class="col-12 col-sm-3 col-md-3 col-lg-3 col-form-label">{{trans('ruby::applicants.nationality')}}</label>
                    <div class="col-12 col-sm-9 col-md-9 col-lg-9">
                        <select name="nationality" :class="'form-control' + (error('nationality')? ' border-danger' : '')" v-model="form.nationality">
                            <option value="">-- {{trans('ruby::applicants.nationality')}} --</option>
                            @foreach($countries as $countryId => $countryName)
                                <option value="{{$countryId}}">{{$countryName}}</option>
                            @endforeach
                        </select>
                        <small class="text-danger d-block" v-if="error('nationality')">@{{ error('nationality') }}</small>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-12 col-sm-3 col-md-3 col-lg-3 col-form-label">{{trans('ruby::applicants.degree')}}</label>
                    <div class="col-12 col-sm-9 col-md-9 col-lg-9">
                        <select name="degree" :class="'form-control' + (error('degree')? ' border-danger' : '')" v-model="form.degree">
                            <option value="">-- {{trans('ruby::applicants.degree')}} --</option>
                            @foreach($degrees as $degreeId => $degreeName)
                                <option value="{{$degreeId}}">{{$degreeName}}</option>
                            @endforeach
                        </select>
                        <small class="text-danger d-block" v-if="error('degree')">@{{ error('degree') }}</small>
                    </div>
                </div>
                <vue-field name="degree_date" type="number" id="{{($edit)? 'e_' : ''}}degree-date">{{trans('ruby::applicants.degree_date')}}</vue-field>
                <vue-field name="recruited_at" type="daterange" id="{{($edit)? 'e_' : ''}}recruited-at">{{trans('ruby::applicants.recruited_at')}}</vue-field>

                <h6 class="col-12 font-weight-bolder">{{trans('ruby::applicants.documents')}}</h6>
                <template v-for="document in form.existingDocuments" :key="'document-' + document.id">
                    <div class="form-group row col-12 m-0" v-if="!document.removed">
                        <div class="col-md-7 p-0">
                            <input type="text" :name="'document_titles[' + document.id + ']'" v-model="document.title" class="form-control" placeholder="{{trans('ruby::applicants.document_title')}}">
                        </div>
                        <div class="col-md-3">
                            <a :href="document.download_url" target="_blank" class="btn btn-sm btn-outline-primary" title="{{trans('common::words.download')}}"><i class="fa fa-download"></i></a>
                        </div>
                        <div class="col-md-2 text-right">
                            <span class="text-danger" role="button" title="{{trans('common::words.remove')}}" @click="document.removed = true"><i class="far fa-lg fa-trash-can"></i></span>
                        </div>
                        <input type="hidden" name="documents_removed[]" :value="document.id" v-if="document.removed">
                    </div>
                </template>
                <div class="form-group row col-12 m-0" v-for="(document, i) in form.newDocuments" :key="'new-document-' + i">
                    <div class="col-md-4 p-0">
                        <input type="text" name="document_new_titles[]" v-model="document.title" class="form-control" placeholder="{{trans('ruby::applicants.document_title')}}">
                    </div>
                    <div class="col-md-6">
                        <input type="file" name="documents[]" class="form-control-file">
                    </div>
                    <div class="col-md-2 text-right">
                        <span class="text-danger" role="button" title="{{trans('common::words.remove')}}" @click="removeRow('newDocuments', i)"><i class="far fa-lg fa-trash-can"></i></span>
                    </div>
                </div>
                <div class="col-12 text-right">
                    <span class="text-primary" role="button" @click="addRow('newDocuments')"><i class="fas fa-plus"></i> {{trans('ruby::applicants.documents')}}</span>
                </div>
            </pane>
        </tabs>
    </vue-form>
    <template #footer>
        <div class="btn btn-outline-secondary" data-dismiss="modal">Cancel</div>
        <div class="btn btn-{{$color}} text-white" @click="submit('{{$ref}}')">{{trans('common::words.save')}}</div>
    </template>
</modal>
