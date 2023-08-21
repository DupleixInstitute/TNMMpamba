<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <inertia-link class="text-indigo-400 hover:text-indigo-600" :href="route('loans.index')">Loans
                </inertia-link>
                <span class="text-indigo-400 font-medium">/</span> {{ loan.id }}
            </h2>
        </template>
        <div class="mx-auto">
            <div class="md:flex md:items-start">
                <div class="bg-white relative shadow-xl mb-4 w-full md:w-3/12">
                    <div class="col-span-12 lg:col-span-4 xxl:col-span-3 flex lg:block flex-col-reverse">
                        <div class="intro-y box sm:mt-4 lg:mt-0">
                            <loan-menu :loan="loan"></loan-menu>
                        </div>
                    </div>

                </div>
                <div class="w-full md:w-9/12 md:ml-4 bg-white">
                    <table class="border-collapse w-full border border-gray-400 bg-white text-sm shadow-sm">
                        <tbody>
                        <tr>
                            <td class="w-1/2 border border-gray-300 font-semibold p-4 text-gray-900">Member</td>
                            <td class="w-1/2 border border-gray-300 p-4 text-gray-500">
                                <inertia-link class="flex items-center focus:text-indigo-500"
                                              :href="route('members.show', loan.member_id)" v-if="loan.member">
                                    <img v-if="loan.member.profile_photo_url" class="block w-5 h-5 rounded-full mr-2 -my-2"
                                         :src="loan.member.profile_photo_url">
                                    {{ loan.member.name }}
                                </inertia-link>
                            </td>
                        </tr>
                        <tr>
                            <td class="w-1/2 border border-gray-300 font-semibold p-4 text-gray-900">Category</td>
                            <td class="w-1/2 border border-gray-300 p-4 text-gray-500">
                                <span class="" v-if="loan.category">
                                {{loan.category.name}}
                            </span>
                            </td>
                        </tr>
                        <tr>
                            <td class="w-1/2 border border-gray-300 font-semibold p-4 text-gray-900">Amount</td>
                            <td class="w-1/2 border border-gray-300 p-4 text-gray-500">
                                {{ $filters.formatNumber(loan.amount) }}
                            </td>
                        </tr>
                        <tr>
                            <td class="w-1/2 border border-gray-300 font-semibold p-4 text-gray-900">Status</td>
                            <td class="w-1/2 border border-gray-300 p-4 text-gray-500">
                                <span v-if="loan.status=='received'"
                                      class="px-2 rounded-full bg-yellow-100 text-yellow-800">
                                        received
                                    </span>
                                <span v-if="loan.status=='in_progress'"
                                      class="px-2 rounded-full bg-blue-100 text-blue-800">
                                        in progress
                                    </span>
                                <span v-if="loan.status=='approved'"
                                      class="px-2 rounded-full bg-green-100 text-green-800">
                                        approved
                                    </span>
                                <span v-if="loan.status=='done'"
                                      class="px-2 rounded-full bg-green-100 text-green-800">
                                        done
                                    </span>
                                <span v-if="loan.status=='cancelled'"
                                      class="px-2 rounded-full bg-red-100 text-red-800">
                                        cancelled
                                    </span>
                                <span v-if="loan.status=='rejected'"
                                      class="px-2 rounded-full bg-red-100 text-red-800">
                                        rejected
                                    </span>
                            </td>
                        </tr>
                        <tr>
                            <td class="w-1/2 border border-gray-300 font-semibold p-4 text-gray-900">Received Date</td>
                            <td class="w-1/2 border border-gray-300 p-4 text-gray-500">{{ loan.date }}</td>
                        </tr>
                        <tr>
                            <td class="w-1/2 border border-gray-300 font-semibold p-4 text-gray-900">Description</td>
                            <td class="w-1/2 border border-gray-300 p-4 text-gray-500">{{ loan.description }}</td>
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
import LoanMenu from '@/Pages/Loans/LoanMenu.vue'

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
        LoanMenu,
    },
    props: {
        loan: Object,
        filters: Object,
        roles: Object,

    },
    data() {
        return {

            confirmingLoanDeletion: false,
            selectedRecord: null,
            pageTitle: "Loans",
            pageDescription: "Manage Loans",

        }
    },
    watch: {
        form: {
            handler: _.debounce(function () {
                let query = pickBy(this.form)
                this.$inertia.get(this.route('loans.index', Object.keys(query).length ? query : {}))
            }, 500),
            deep: true,
        },
    },
    methods: {
        reset() {
            this.form = mapValues(this.form, () => null)
        },
        deleteAction(id) {
            this.confirmingLoanDeletion = true
            this.selectedRecord = id
        },
        destroy() {

            this.$inertia.delete(this.route('loans.destroy', this.selectedRecord))
            this.confirmingLoanDeletion = false
        },
    },
}
</script>

<style scoped>

</style>
