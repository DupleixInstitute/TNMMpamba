<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <inertia-link class="text-indigo-400 hover:text-indigo-600" :href="route('members.index')">Members
                </inertia-link>
                <span class="text-indigo-400 font-medium">/</span> {{ member.name }}
            </h2>
        </template>
        <div class="mx-auto">
            <div class="md:flex md:items-start">
                <div class="bg-white relative shadow-xl mb-4 mt-20 w-full md:w-3/12">
                    <div class="col-span-12 lg:col-span-4 xxl:col-span-3 flex lg:block flex-col-reverse">
                        <div class="intro-y box mt-5 lg:mt-0">
                            <member-menu :member="member"></member-menu>
                        </div>
                    </div>

                </div>
                <div class="w-full md:w-9/12 md:ml-4 bg-white sm:mt-4">
                    <table class="border-collapse w-full border border-gray-400 bg-white text-sm shadow-sm">
                        <tbody>
                        <tr>
                            <td class="w-1/2 border border-gray-300 font-semibold p-4 text-gray-900">Branch</td>
                            <td class="w-1/2 border border-gray-300 p-4 text-gray-500">{{ member.branch?.name }}</td>
                        </tr>
                        <tr>
                            <td class="w-1/2 border border-gray-300 font-semibold p-4 text-gray-900">Status</td>
                            <td class="w-1/2 border border-gray-300 p-4 text-gray-500">
                                 <span
                                     class="text-xs font-semibold inline-block py-1 px-2 uppercase rounded text-yellow-600 bg-yellow-200 uppercase"
                                     v-if="member.status=='pending'">
                                        Pending
                                 </span>
                                <span
                                    class="text-xs font-semibold inline-block py-1 px-2 uppercase rounded text-yellow-600 bg-yellow-200 uppercase"
                                    v-if="member.status=='inactive'">
                                        Inactive
                                </span>
                                <span
                                    class="text-xs font-semibold inline-block py-1 px-2 uppercase rounded text-blue-600 bg-blue-200 uppercase"
                                    v-if="member.status=='archived'">
                                        Archived
                                </span>
                                <span
                                    class="text-xs font-semibold inline-block py-1 px-2 uppercase rounded text-green-600 bg-green-200 uppercase"
                                    v-if="member.status=='active'">
                                        Active
                                    </span>
                                <span
                                    class="text-xs font-semibold inline-block py-1 px-2 uppercase rounded text-red-600 bg-red-200 uppercase"
                                    v-if="member.status=='deceased'">
                                        Deceased
                                </span>
                                <span
                                    class="text-xs font-semibold inline-block py-1 px-2 uppercase rounded text-red-600 bg-red-200 uppercase"
                                    v-if="member.status=='deceased'">
                                        Deceased
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td class="w-1/2 border border-gray-300 font-semibold p-4 text-gray-900">External ID</td>
                            <td class="w-1/2 border border-gray-300 p-4 text-gray-500">{{ member.external_id }}</td>
                        </tr>
                        <tr>
                            <td class="w-1/2 border border-gray-300 font-semibold p-4 text-gray-900">Mobile</td>
                            <td class="w-1/2 border border-gray-300 p-4 text-gray-500">{{ member.mobile }}</td>
                        </tr>
                        <tr>
                            <td class="w-1/2 border border-gray-300 font-semibold p-4 text-gray-900">Tel</td>
                            <td class="w-1/2 border border-gray-300 p-4 text-gray-500">{{ member.tel }}</td>
                        </tr>
                        <tr>
                            <td class="w-1/2 border border-gray-300 font-semibold p-4 text-gray-900">Date of Birth</td>
                            <td class="w-1/2 border border-gray-300 p-4 text-gray-500">{{ member.dob }}</td>
                        </tr>
                        <tr>
                            <td class="w-1/2 border border-gray-300 font-semibold p-4 text-gray-900">Shares</td>
                            <td class="w-1/2 border border-gray-300 p-4 text-gray-500">{{ member.shares }}</td>
                        </tr>
                        <tr>
                            <td class="w-1/2 border border-gray-300 font-semibold p-4 text-gray-900">Email</td>
                            <td class="w-1/2 border border-gray-300 p-4 text-gray-500">{{ member.email }}</td>
                        </tr>
                        <tr>
                            <td class="w-1/2 border border-gray-300 font-semibold p-4 text-gray-900">Marital Status</td>
                            <td class="w-1/2 border border-gray-300 p-4 text-gray-500 capitalize">
                                {{ member.marital_status }}
                            </td>
                        </tr>
                        <tr>
                            <td class="w-1/2 border border-gray-300 font-semibold p-4 text-gray-900">ID Number</td>
                            <td class="w-1/2 border border-gray-300 p-4 text-gray-500 capitalize">
                                {{ member.id_number }}
                                <span v-if="member.id_number" class="capitalize">({{ member.id_number }})</span>
                            </td>
                        </tr>
                        <tr>
                            <td class="w-1/2 border border-gray-300 font-semibold p-4 text-gray-900">Zip</td>
                            <td class="w-1/2 border border-gray-300 p-4 text-gray-500">{{ member.zip }}</td>
                        </tr>
                        <tr>
                            <td class="w-1/2 border border-gray-300 font-semibold p-4 text-gray-900">Province</td>
                            <td class="w-1/2 border border-gray-300 p-4 text-gray-500">{{ member.province?.name }}</td>
                        </tr>
                        <tr>
                            <td class="w-1/2 border border-gray-300 font-semibold p-4 text-gray-900">District</td>
                            <td class="w-1/2 border border-gray-300 p-4 text-gray-500">{{ member.district?.name }}</td>
                        </tr>
                        <tr>
                            <td class="w-1/2 border border-gray-300 font-semibold p-4 text-gray-900">Ward</td>
                            <td class="w-1/2 border border-gray-300 p-4 text-gray-500">{{ member.ward?.name }}</td>
                        </tr>
                        <tr>
                            <td class="w-1/2 border border-gray-300 font-semibold p-4 text-gray-900">Village</td>
                            <td class="w-1/2 border border-gray-300 p-4 text-gray-500">{{ member.village?.name }}</td>
                        </tr>

                        <tr>
                            <td class="w-1/2 border border-gray-300 font-semibold p-4 text-gray-900">Address</td>
                            <td class="w-1/2 border border-gray-300 p-4 text-gray-500">{{ member.address }}</td>
                        </tr>
                        <tr>
                            <td class="w-1/2 border border-gray-300 font-semibold p-4 text-gray-900">Postal Address</td>
                            <td class="w-1/2 border border-gray-300 p-4 text-gray-500">{{ member.postal_address }}</td>
                        </tr>
                        </tbody>
                    </table>
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

export default {
    components: {
        AppLayout,
        Icon,
        Pagination,
        FilterSearch,
        JetLabel,
        SelectInput,
        JetConfirmationModal,
        JetDangerButton,
        JetSecondaryButton,
        MemberMenu,
    },
    props: {
        member: Object,
        filters: Object,
        roles: Object,

    },
    data() {
        return {

            confirmingMemberDeletion: false,
            selectedRecord: null,
            pageTitle: "Members",
            pageDescription: "Manage Members",

        }
    },
    watch: {
        form: {
            handler: _.debounce(function () {
                let query = pickBy(this.form)
                this.$inertia.get(this.route('members.index', Object.keys(query).length ? query : {}))
            }, 500),
            deep: true,
        },
    },
    methods: {
        reset() {
            this.form = mapValues(this.form, () => null)
        },
        deleteAction(id) {
            this.confirmingMemberDeletion = true
            this.selectedRecord = id
        },
        destroy() {

            this.$inertia.delete(this.route('members.destroy', this.selectedRecord))
            this.confirmingMemberDeletion = false
        },
    },
}
</script>

<style scoped>

</style>
