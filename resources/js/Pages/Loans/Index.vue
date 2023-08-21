<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Loans
            </h2>
        </template>
        <div class=" mx-auto  mb-4 flex justify-between items-center">
            <filter-search v-model="form.search" class="w-full max-w-md mr-4" @reset="reset">
                <div class="w-80 mt-2 px-4 py-6 shadow-xl bg-white rounded">
                    <div class="mb-2">
                        <jet-label for="status" value="Status"/>
                        <select v-model="form.status"
                                class="mt-1 w-full border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm">
                            <option :value="null"/>
                            <option value="received">Received</option>
                            <option value="approved">Approved</option>
                            <option value="rejected">Rejected</option>
                        </select>
                    </div>
                    <div class="mb-2">
                        <jet-label for="loan_category_id" value="Category"/>
                        <Multiselect
                            id="loan_category_id"
                            v-model="form.loan_category_id"
                            :options="categories"
                        />
                    </div>
                </div>
            </filter-search>
            <inertia-link class="btn btn-blue" v-if="can('loans.create')" :href="route('loans.create')">
                <span>Create </span>
                <span class="hidden md:inline">Loan</span>
            </inertia-link>
        </div>
        <div class=" mx-auto">
            <div class="bg-white rounded shadow overflow-x-auto">
                <table class="w-full whitespace-no-wrap table-auto">
                    <thead class="bg-gray-50">
                    <tr class="text-left font-bold">
                        <th class="px-6 pt-4 pb-4 font-medium text-gray-500">ID</th>
                        <th class="px-6 pt-4 pb-4 font-medium text-gray-500">Member</th>
                        <th class="px-6 pt-4 pb-4 font-medium text-gray-500">Category</th>
                        <th class="px-6 pt-4 pb-4 font-medium text-gray-500">Amount</th>
                        <th class="px-6 pt-4 pb-4 font-medium text-gray-500">Status</th>
                        <th class="px-6 pt-4 pb-4 font-medium text-gray-500">Date</th>
                        <th class="px-6 pt-4 pb-4 font-medium text-gray-500">Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-if="!loans.data.length">
                        <td colspan="7" class="px-6 py-4 text-center">
                            No Loans yet
                        </td>
                    </tr>
                    <tr v-for="loan in loans.data" :key="loan.id"
                        class="hover:bg-gray-100 focus-within:bg-gray-100">
                        <td class="border-t">
                            <inertia-link class="px-6 py-4 flex items-center" :href="route('loans.show', loan.id)"
                                          tabindex="-1">
                                {{ loan.id }}
                            </inertia-link>
                        </td>
                        <td class="border-t">
                            <inertia-link class="px-6 py-4 flex items-center focus:text-indigo-500"
                                          :href="route('members.show', loan.member_id)" v-if="loan.member">
                                <img v-if="loan.member.profile_photo_url" class="block w-5 h-5 rounded-full mr-2 -my-2"
                                     :src="loan.member.profile_photo_url">
                                {{ loan.member.name }}
                            </inertia-link>
                        </td>
                        <td class="border-t">
                            <span class="px-6 py-4 flex items-center" v-if="loan.category">
                                {{loan.category.name}}
                            </span>
                        </td>
                        <td class="border-t">
                            <span class="px-6 py-4 flex items-center">
                                 {{ $filters.formatNumber(loan.amount) }}
                            </span>
                        </td>
                        <td class="border-t">
                                 <span class="px-6 py-4 flex items-center">
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
                                </span>
                        </td>
                        <td class="border-t">
                            <span class="px-6 py-4 flex items-center">
                                 {{ loan.date }}
                            </span>
                        </td>

                        <td class="border-t w-px pr-2">
                            <div class=" flex items-center gap-4">
                                <inertia-link :href="route('loans.show', loan.id)"
                                              tabindex="-1" class="text-green-600 hover:text-green-900" title="View">
                                    <font-awesome-icon icon="search"/>
                                </inertia-link>
                                <inertia-link v-if="can('loans.update')"
                                              :href="route('loans.edit', loan.id)"
                                              tabindex="-1" class="text-indigo-600 hover:text-indigo-900" title="Edit">
                                    <font-awesome-icon icon="edit"/>
                                </inertia-link>
                                <a href="#" v-if="can('loans.destroy')" @click="deleteAction(loan.id)"
                                   class="text-red-600 hover:text-red-900" title="Delete">
                                    <font-awesome-icon icon="trash"/>
                                </a>
                            </div>
                        </td>
                    </tr>
                    </tbody>
                </table>

            </div>
            <pagination :links="loans.links"/>
        </div>
        <jet-confirmation-modal :show="confirmingLoanDeletion" @close="confirmingLoanDeletion = false">
            <template #title>
                Delete Record
            </template>

            <template #content>
                Are you sure you want to delete your account? Once record is deleted, all of its resources and
                data will be permanently deleted.
            </template>

            <template #footer>
                <jet-secondary-button @click.native="confirmingLoanDeletion = false">
                    Nevermind
                </jet-secondary-button>

                <jet-danger-button class="ml-2" @click.native="destroy" :class="{ 'opacity-25': form.processing }"
                                   :disabled="form.processing">
                    Delete Account
                </jet-danger-button>
            </template>
        </jet-confirmation-modal>
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
    },
    props: {
        loans: Object,
        filters: Object,
        categories: Object,

    },
    data() {
        return {
            form: {
                search: this.filters.search,
                province_id: this.filters.province_id,
                branch_id: this.filters.branch_id,
                district_id: this.filters.district_id,
                ward_id: this.filters.ward_id,
                village_id: this.filters.village_id,
                loan_category_id: this.filters.loan_category_id,
                member_id: this.filters.member_id,
                status: this.filters.status,
                processing: false
            },
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
