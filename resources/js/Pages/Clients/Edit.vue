<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <inertia-link class="text-indigo-400 hover:text-indigo-600" :href="route('members.index')">Members
                </inertia-link>
                <span class="text-indigo-400 font-medium">/</span> Edit
            </h2>
        </template>
        <div class=" mx-auto">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-4">
                <form @submit.prevent="submit" enctype="multipart/form-data">
                    <div class="mb-2">
                        <jet-label for="branch_id" value="Branch"/>
                        <Multiselect
                            id="branch_id"
                            v-model="form.branch_id"
                            :options="branches"
                        />
                        <jet-input-error :message="form.errors.branch_id" class="mt-2"/>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-2">
                        <div>
                            <jet-label for="first_name" value="First Name"/>
                            <jet-input id="first_name" type="text" class="block w-full"
                                       v-model="form.first_name"
                                       required
                                       autofocus autocomplete="first_name"/>
                            <jet-input-error :message="form.errors.first_name" class="mt-2"/>
                        </div>
                        <div class="">
                            <jet-label for="middle_name" value="Middle Name"/>
                            <jet-input id="middle_name" type="text" class=" block w-full"
                                       v-model="form.middle_name"
                                       autocomplete="middle_name"/>
                            <jet-input-error :message="form.errors.middle_name" class="mt-2"/>
                        </div>
                        <div class="">
                            <jet-label for="last_name" value="Last Name"/>
                            <jet-input id="last_name" type="text" class="block w-full" v-model="form.last_name"
                                       required autocomplete="last_name"/>
                            <jet-input-error :message="form.errors.last_name" class="mt-2"/>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-3 mt-4 gap-2">
                        <div>
                            <jet-label for="dob" value="Date of Birth"/>
                            <flat-pickr
                                v-model="form.dob"
                                class="form-control w-full"
                                placeholder="Select date"
                                required
                                id="dob"
                                name="dob">
                            </flat-pickr>
                            <jet-input-error :message="form.errors.dob" class="mt-2"/>

                        </div>
                        <div>
                            <jet-label for="gender" value="Gender"/>
                            <select
                                class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm w-full"
                                name="gender" v-model="form.gender" id="gender" required>
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                                <option value="other">Other</option>
                            </select>
                            <jet-input-error :message="form.errors.gender" class="mt-2"/>
                        </div>
                        <div>
                            <jet-label for="shares" value="Shares"/>
                            <jet-input id="shares" type="text" class="block w-full" v-model="form.shares"/>
                            <jet-input-error :message="form.errors.shares" class="mt-2"/>

                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-3 mt-4 gap-2">
                        <div>
                            <jet-label for="mobile" value="Mobile"/>
                            <jet-input id="mobile" type="text" class="block w-full" v-model="form.mobile"/>
                            <jet-input-error :message="form.errors.mobile" class="mt-2"/>

                        </div>
                        <div>
                            <jet-label for="tel" value="Tel"/>
                            <jet-input id="tel" type="text" class="block w-full" v-model="form.tel"/>
                            <jet-input-error :message="form.errors.tel" class="mt-2"/>

                        </div>
                        <div>
                            <jet-label for="zip" value="Zip"/>
                            <jet-input id="zip" type="text" class="block w-full" v-model="form.zip"/>
                            <jet-input-error :message="form.errors.zip" class="mt-2"/>

                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-3 mt-4 gap-2">
                        <div>
                            <jet-label for="email" value="Email"/>
                            <jet-input id="email" type="email" class="block w-full" v-model="form.email"/>
                            <jet-input-error :message="form.errors.email" class="mt-2"/>

                        </div>
                        <div>
                            <jet-label for="marital_status" value="Marital Status"/>
                            <select
                                class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm w-full"
                                name="marital_status" v-model="form.marital_status" id="marital_status">
                                <option :value="null"/>
                                <option value="married">Married</option>
                                <option value="single">Single</option>
                                <option value="divorced">Divorced</option>
                                <option value="widowed">Widowed</option>
                                <option value="other">Other</option>
                            </select>
                            <jet-input-error :message="form.errors.marital_status" class="mt-2"/>
                        </div>
                        <div>
                            <jet-label for="external_id" value="External ID"/>
                            <jet-input id="external_id" type="text" class="block w-full"
                                       v-model="form.external_id"/>
                            <jet-input-error :message="form.errors.external_id" class="mt-2"/>

                        </div>
                    </div>
                    <div class="grid  grid-cols-1 md:grid-cols-2 mt-4 gap-2">
                        <div>
                            <jet-label for="id_type" value="ID Type"/>
                            <select
                                class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm w-full"
                                name="id_type" v-model="form.id_type" id="id_type">
                                <option :value="null"/>
                                <option value="national">National</option>
                                <option value="license">Driver's License</option>
                                <option value="passport">Passport</option>
                                <option value="other">Other</option>
                            </select>
                            <jet-input-error :message="form.errors.id_type" class="mt-2"/>
                        </div>
                        <div>
                            <jet-label for="id_number" value="ID Number"/>
                            <jet-input id="id_number" type="text" class=" block w-full"
                                       v-model="form.id_number"/>
                            <jet-input-error :message="form.errors.id_number" class="mt-2"/>

                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-4 mt-4 gap-2">
                        <div>
                            <jet-label for="province_id" value="Province"/>
                            <Multiselect
                                id="province_id"
                                v-model="form.province_id"
                                :options="provinces"
                            />
                            <jet-input-error :message="form.errors.province_id" class="mt-2"/>
                        </div>
                        <div>
                            <jet-label for="district_id" value="District"/>
                            <Multiselect
                                id="district_id"
                                v-model="form.district_id"
                                :options="availableDistricts"
                            />
                            <jet-input-error :message="form.errors.district_id" class="mt-2"/>
                        </div>
                        <div>
                            <jet-label for="ward_id" value="Ward"/>
                            <Multiselect
                                id="district_id"
                                v-model="form.ward_id"
                                :options="availableWards"
                            />
                            <jet-input-error :message="form.errors.ward_id" class="mt-2"/>
                        </div> <div>
                        <jet-label for="village_id" value="Village"/>
                        <Multiselect
                            id="village_id"
                            v-model="form.village_id"
                            :options="availableVillages"
                        />
                        <jet-input-error :message="form.errors.village_id" class="mt-2"/>
                    </div>
                    </div>
                    <div class="grid  grid-cols-1 md:grid-cols-2 mt-4 gap-2">
                        <div>
                            <jet-label for="address" value="Address"/>
                            <textarea-input id="address" class=" block w-full"
                                            v-model="form.address" autocomplete="address"/>
                            <jet-input-error :message="form.errors.address" class="mt-2"/>

                        </div>
                        <div>
                            <jet-label for="postal_address" value="Postal Address"/>
                            <textarea-input id="postal_address" class="block w-full"
                                            v-model="form.postal_address" autocomplete="postal_address"/>
                            <jet-input-error :message="form.errors.postal_address" class="mt-2"/>

                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 mt-4 gap-2">
                        <div>
                            <jet-label for="photo" value="Photo"/>
                            <file-input v-model="form.photo" class="block w-full" id="photo" type="file"/>
                            <jet-input-error :message="form.errors.photo" class="mt-2"/>
                        </div>
                        <div>
                            <jet-label for="status" value="Status"/>
                            <select
                                class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm w-full"
                                name="active" v-model="form.status" id="status" required>
                                <option :value="null"/>
                                <option value="pending">Pending</option>
                                <option value="inactive">Inactive</option>
                                <option value="active">Active</option>
                                <option value="archived">Archived</option>
                            </select>
                            <jet-input-error :message="form.errors.status" class="mt-2"/>
                        </div>
                    </div>
                    <div class="flex items-center justify-end mt-4">

                        <jet-button class="ml-4" :class="{ 'opacity-25': form.processing }"
                                    :disabled="form.processing">
                            Save
                        </jet-button>
                    </div>
                </form>
            </div>
        </div>
        <teleport to="head">
            <title>{{ pageTitle }}</title>
            <meta property="og:description" :content="pageDescription">
        </teleport>
    </app-layout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout.vue'
import JetButton from "@/Jetstream/Button.vue";
import JetInput from "@/Jetstream/Input.vue";
import JetInputError from "@/Jetstream/InputError.vue";
import JetCheckbox from "@/Jetstream/Checkbox.vue";
import JetLabel from "@/Jetstream/Label.vue";
import Select from "@/Jetstream/Select.vue";
import FileInput from "@/Jetstream/FileInput.vue";
import TextareaInput from "@/Jetstream/TextareaInput.vue";


export default {
    props: {
        countries: Object,
        branches: Object,
        provinces: Object,
        districts: Object,
        wards: Object,
        villages: Object,
        member: Object,
    },
    components: {
        Select,
        AppLayout,
        JetButton,
        JetInput,
        JetCheckbox,
        JetLabel,
        JetInputError,
        FileInput,
        TextareaInput,

    },
    data() {
        return {
            form: this.$inertia.form({
                '_method': 'PUT',
                first_name: this.member.first_name,
                middle_name: this.member.middle_name,
                last_name: this.member.last_name,
                shares: this.member.shares,
                gender: this.member.gender,
                email: this.member.email,
                country_id: this.member.country_id,
                nationality_id: this.member.nationality_id,
                branch_id: this.member.branch_id,
                province_id: this.member.province_id,
                district_id: this.member.district_id,
                ward_id: this.member.ward_id,
                village_id: this.member.village_id,
                mobile: this.member.mobile,
                id_type: this.member.id_type,
                id_number: this.member.id_number,
                tel: this.member.tel,
                zip: this.member.zip,
                external_id: this.member.external_id,
                address: this.member.address,
                postal_address: this.member.postal_address,
                dob: this.member.dob,
                marital_status: this.member.marital_status,
                occupation: this.member.occupation,
                employer_name: this.member.employer_name,
                employer_address: this.member.employer_address,
                employer_phone: this.member.employer_phone,
                description: this.member.description,
                is_active: this.member.is_active,
                status: this.member.status,
                photo: null,
            }),
            pageTitle: "Edit Member",
            pageDescription: "Edit Member",
        }

    },
    methods: {
        submit() {
            this.form.post(this.route('members.update', this.member.id), {})
        },

    },
    computed: {
        availableDistricts: function () {
            return this.districts.filter(item => {
                return item.province_id === this.form.province_id;

            })
        },
        availableWards: function () {
            return this.wards.filter(item => {
                return item.district_id === this.form.district_id;

            })
        },
        availableVillages: function () {
            return this.villages.filter(item => {
                return item.ward_id === this.form.ward_id;

            })
        }
    }
}
</script>
<style scoped>

</style>
