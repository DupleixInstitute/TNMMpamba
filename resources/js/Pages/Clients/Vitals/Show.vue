<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <inertia-link class="text-indigo-400 hover:text-indigo-600" :href="route('members.index')">Members
                </inertia-link>
                <span class="text-indigo-400 font-medium">/</span> {{ member.name }}
            </h2>
        </template>
        <div class=" mx-auto">
            <div class="md:flex md:items-start">
                <div class="bg-white relative shadow-xl mt-20 w-3/12 ">
                    <div class="col-span-12 lg:col-span-4 xxl:col-span-3 flex lg:block flex-col-reverse">
                        <div class="intro-y box mt-5 lg:mt-0">
                            <consultation-menu :member="member" :consultation="consultation"></consultation-menu>
                        </div>
                    </div>

                </div>
                <div class="w-9/12 ml-2 bg-white p-5">
                    <div class="flex justify-between ">
                        <h2 class="font-semibold text-xl text-gray-800 leading-tight">View Consultation</h2>
                        <inertia-link class="btn btn-blue" :href="route('members.consultations.index',member.id)">
                            <span>Back </span>
                        </inertia-link>
                    </div>
                    <div class="mt-4">
                        <form @submit.prevent="submit" enctype="multipart/form-data">
                            <div class="grid grid-cols-1 space-y-4">
                                <div>
                                    <jet-label for="date" value="Date"/>
                                    <flat-pickr
                                        v-model="form.date"
                                        class="form-control w-full"
                                        placeholder="Select date"
                                        name="date">
                                    </flat-pickr>
                                    <jet-input-error :message="form.errors.date" class="mt-2"/>

                                </div>
                                <div>
                                    <jet-label for="doctor_id" value="Doctor"/>
                                    <Multiselect
                                        id="doctor_id"
                                        v-model="form.doctor_id"
                                        mode="single"
                                        :searchable="true"
                                        :options="$page.props.doctors"
                                    />
                                    <jet-input-error :message="form.errors.doctor_id" class="mt-2"/>
                                </div>
                                <div>
                                    <jet-label for="nurse_id" value="Nurse"/>
                                    <Multiselect
                                        id="nurse_id"
                                        v-model="form.nurse_id"
                                        mode="single"
                                        :searchable="true"
                                        :options="$page.props.nurses"
                                    />
                                    <jet-input-error :message="form.errors.nurse_id" class="mt-2"/>
                                </div>
                                <div>
                                    <jet-label for="visible_to_member">
                                        <div class="flex items-center">
                                            <jet-checkbox name="visible_to_member" id="visible_to_member"
                                                          v-model:checked="form.visible_to_member"/>
                                            <div class="ml-2">
                                                Visible to Member
                                            </div>
                                        </div>
                                    </jet-label>
                                </div>
                                <div>
                                    <jet-label for="address" value="Description"/>
                                    <textarea-input id="description" class="mt-1 block w-full"
                                                    v-model="form.description"/>
                                    <jet-input-error :message="form.errors.description" class="mt-2"/>

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
import Icon from '@/Jetstream/Icon.vue'
import Pagination from '@/Jetstream/Pagination.vue'
import SearchFilter from '@/Jetstream/SearchFilter.vue'
import FilterSearch from '@/Jetstream/FilterSearch.vue'
import mapValues from 'lodash/mapValues'
import pickBy from 'lodash/pickBy'
import throttle from 'lodash/throttle'
import JetLabel from '@/Jetstream/Label.vue'
import SelectInput from '@/Jetstream/SelectInput.vue'
import JetConfirmationModal from '@/Jetstream/ConfirmationModal.vue'
import JetDangerButton from '@/Jetstream/DangerButton.vue'
import JetSecondaryButton from '@/Jetstream/SecondaryButton.vue'
import MemberMenu from '@/Pages/Members/MemberMenu.vue'
import ConsultationMenu from '@/Pages/Members/ConsultationMenu.vue'
import JetInput from "@/Jetstream/Input.vue";
import JetInputError from "@/Jetstream/InputError.vue";
import JetButton from "@/Jetstream/Button.vue";
import JetCheckbox from "@/Jetstream/Checkbox.vue";
import TextareaInput from "@/Jetstream/TextareaInput.vue";

export default {
    metaInfo: {title: 'Members'},
    components: {
        AppLayout,
        Icon,
        Pagination,
        SearchFilter,
        FilterSearch,
        JetLabel,
        JetInput,
        JetInputError,
        SelectInput,
        JetConfirmationModal,
        JetDangerButton,
        JetSecondaryButton,
        MemberMenu,
        JetButton,
        JetCheckbox,
        TextareaInput,
        ConsultationMenu,
    },
    props: {
        member: Object,
        consultation: Object,
        coPayers: Object,
        doctors: Object,
        nurses: Object,

    },
    data() {
        return {
            form: this.$inertia.form({
                doctor_id: null,
                nurse_id: null,
                date: null,
                visible_to_member: false,
                description: null,
            }),
            pageTitle: "Members",
            pageDescription: "View Consultations",

        }
    },

    methods: {
        submit() {
            this.form.post(this.route('members.consultations.store', this.member.id), {})
        },
    },
}
</script>

<style scoped>

</style>
