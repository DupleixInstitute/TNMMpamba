<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <inertia-link class="text-indigo-400 hover:text-indigo-600" :href="route('loans.index')">
                    Loans
                </inertia-link>
                <span class="text-indigo-400 font-medium">/</span> {{ loan.name }}
            </h2>
        </template>
        <div class="mx-auto">
            <div class="md:flex md:items-start">
                <div class="bg-white relative shadow-xl mb-4 mt-20 w-full md:w-3/12">
                    <div class="col-span-12 lg:col-span-4 xxl:col-span-3 flex lg:block flex-col-reverse">
                        <div class="intro-y box mt-5 lg:mt-0">
                            <loan-menu :loan="loan"></loan-menu>
                        </div>
                    </div>

                </div>
                <div class="w-full md:w-9/12 p-4 md:ml-4 bg-white sm:mt-4">
                    <div class="flex justify-between ">
                        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Login Details</h2>
                        <inertia-link class="btn btn-blue" v-if="can('loans.create')"
                                      :href="route('loans.login_details.create',loan.id)">
                            <span>Add </span>
                            <span class="hidden md:inline">User</span>
                        </inertia-link>
                    </div>
                    <div class="mt-4 relative overflow-x-auto">
                        <table class="w-full whitespace-no-wrap table-auto">
                            <thead class="bg-gray-50">
                            <tr class="text-left font-bold">
                                <th class="px-6 pt-4 pb-4 font-medium text-gray-500">Name</th>
                                <th class="px-6 pt-4 pb-4 font-medium text-gray-500">Email</th>
                                <th class="px-6 pt-4 pb-4 font-medium text-gray-500">Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-if="!loan.user">
                                <td colspan="3" class="px-6 py-4 text-center">
                                    No Users Yet
                                </td>
                            </tr>
                            <tr v-else
                                class="hover:bg-gray-100 focus-within:bg-gray-100">
                                <td class="border-t">
                                    <inertia-link v-if="loan.user"
                                                  :href="route('users.show', loan.user_id)"
                                                  tabindex="-1"
                                                  class="px-6 py-4 flex items-center text-indigo-600 hover:text-indigo-900">
                                        {{ loan.user.name }}
                                    </inertia-link>
                                </td>
                                <td class="border-t">
                                    <inertia-link
                                                  :href="route('users.show', loan.user.id)"
                                                  tabindex="-1"
                                                  class="px-6 py-4 flex items-center text-indigo-600 hover:text-indigo-900">
                                        {{ loanUser.user.email }}
                                    </inertia-link>
                                </td>
                                <td class="border-t">
                                    <inertia-link v-if="can('users.update')"
                                                  :href="route('users.edit', loan.user.id)"
                                                  tabindex="-1"
                                                  class="px-6 py-4 flex items-center text-indigo-600 hover:text-indigo-900">
                                        Edit
                                    </inertia-link>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <jet-confirmation-modal :show="confirmingDeletion" @close="confirmingDeletion = false">
            <template #title>
                Delete Record
            </template>

            <template #content>
                Are you sure you want to delete this record?
            </template>

            <template #footer>
                <jet-secondary-button @click.native="confirmingDeletion = false">
                    Nevermind
                </jet-secondary-button>

                <jet-danger-button class="ml-2" @click.native="destroy" :class="{ 'opacity-25': form.processing }"
                                   :disabled="form.processing">
                    Delete
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
import LoanMenu from '@/Pages/Loans/LoanMenu.vue'

export default {
    components: {
        AppLayout,
        Icon,
        Pagination,
        SearchFilter,
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
        files: Object,

    },
    data() {
        return {
            form: {
                processing: false
            },
            confirmingDeletion: false,
            selectedRecord: null,
            pageTitle: "Loan Login Details",
            pageDescription: "Loan Login Details",

        }
    },
    methods: {
        deleteAction(id) {
            this.confirmingDeletion = true
            this.selectedRecord = id
        },
        destroy() {

            this.$inertia.delete(this.route('loans.login_details.destroy', this.selectedRecord))
            this.confirmingDeletion = false
        },
    },
}
</script>

<style scoped>

</style>
