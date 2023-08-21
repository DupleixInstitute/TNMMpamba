<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <inertia-link class="text-indigo-400 hover:text-indigo-600" :href="route('loans.index')">Loans
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
                        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Loans</h2>
                        <inertia-link class="btn btn-blue" v-if="can('loans.create')"
                                      :href="route('loans.create',{loan_id:loan.id})">
                            <span>Create </span>
                            <span class="hidden md:inline">Loan</span>
                        </inertia-link>
                    </div>
                    <div class="mt-4 relative overflow-x-auto">
                        <table class="w-full whitespace-no-wrap table-auto">
                            <thead class="bg-gray-50">
                            <tr class="text-left font-bold">
                                <th class="px-6 pt-4 pb-4 font-medium text-gray-500">ID</th>
                                <th class="px-6 pt-4 pb-4 font-medium text-gray-500">Category</th>
                                <th class="px-6 pt-4 pb-4 font-medium text-gray-500">Amount</th>
                                <th class="px-6 pt-4 pb-4 font-medium text-gray-500">Status</th>
                                <th class="px-6 pt-4 pb-4 font-medium text-gray-500">Date</th>
                                <th class="px-6 pt-4 pb-4 font-medium text-gray-500">Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-if="!loans.data.length">
                                <td colspan="6" class="px-6 py-4 text-center">
                                    No Loans yet
                                </td>
                            </tr>
                            <tr v-for="loans in loans.data" :key="loan.id"
                                class="hover:bg-gray-100 focus-within:bg-gray-100">
                                <td class="border-t">
                                    <inertia-link :href="route('loans.loans.show', loan.id)"
                                                  tabindex="-1" class="px-6 py-4 flex items-center text-blue-600 hover:text-blue-900">
                                        {{ loan.id }}
                                    </inertia-link>
                                </td>
                                <td class="border-t">
                                    <span class="px-6 py-4 flex items-center"
                                          v-if="loan.loan_type==='in_clinic'">
                                        In-Clinic
                                    </span>
                                    <span class="px-6 py-4 flex items-center"
                                          v-if="loan.loan_type==='text'">
                                        Text
                                    </span>
                                    <span class="px-6 py-4 flex items-center"
                                          v-if="loan.loan_type==='video'">
                                        Video <font-awesome-icon icon="video" class="w-4 h-4 ml-2"></font-awesome-icon>
                                    </span>
                                </td>
                                <td class="border-t">
                                    <span class="px-6 py-4 flex items-center" v-if="loan.doctor">
                                    {{ loan.doctor.name }}
                                    </span>
                                </td>
                                <td class="border-t">
                                    <span class="px-6 py-4 flex items-center">
                                        <span v-if="loan.stage==='with_receptionist'">
                                            At Reception
                                        </span>
                                        <span v-if="loan.stage==='waiting_for_nurse'">
                                            Waiting For Nurse
                                        </span>
                                        <span v-if="loan.stage==='with_nurse'">
                                            With Nurse
                                        </span>
                                        <span v-if="loan.stage==='waiting_for_doctor'">
                                            Waiting For Doctor
                                        </span>
                                        <span v-if="loan.stage==='with_doctor'">
                                            With Doctor
                                        </span>
                                        <span v-if="loan.stage==='finalizing'">
                                            Finalizing
                                        </span>
                                        <span v-if="loan.stage==='complete'"
                                              class="px-2 rounded-full bg-green-100 text-green-800">
                                            Complete
                                        </span>
                                    </span>
                                </td>
                                <td class="border-t">
                                    <span class="px-6 py-4 flex items-center">
                                    {{ loan.date }}
                                    </span>
                                </td>
                                <td class="border-t w-px pr-2">
                                    <div class=" flex items-center space-x-2">
                                        <inertia-link :href="route('loans.show', loan.id)"
                                                      tabindex="-1" class="text-blue-600 hover:text-blue-900">
                                            View
                                        </inertia-link>
                                        <inertia-link v-if="can('loans.update')"
                                                      :href="route('loans.loans.edit', loan.id)"
                                                      tabindex="-1" class="text-indigo-600 hover:text-indigo-900">
                                            Edit
                                        </inertia-link>
                                        <a href="#" v-if="can('loans.destroy')"
                                           @click="deleteAction(loan.id)"
                                           class="text-red-600 hover:text-red-900">Delete</a>
                                    </div>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                    <pagination v-if="loans.data.length" :links="loans.links"/>
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
        loans: Object,

    },
    data() {
        return {
            form: {
                processing: false
            },
            confirmingDeletion: false,
            selectedRecord: null,
            pageTitle: "Loan Consultations",
            pageDescription: "Manage Loans",

        }
    },
    methods: {
        deleteAction(id) {
            this.confirmingDeletion = true
            this.selectedRecord = id
        },
        destroy() {

            this.$inertia.delete(this.route('loans.destroy', this.selectedRecord))
            this.confirmingDeletion = false
        },
    },
}
</script>

<style scoped>

</style>
