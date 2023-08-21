<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Billing
            </h2>
        </template>
        <div class=" mx-auto mb-4 flex justify-between items-center">
            <filter-search v-model="form.search" class="w-full max-w-md mr-4" @reset="reset">
            </filter-search>
            <inertia-link class="btn btn-blue" :href="route('billing.invoices.pending_orders')">
                <span>View Pending Orders </span>
            </inertia-link>
            <inertia-link class="btn btn-blue" :href="route('billing.invoices.create')">
                <span>Create </span>
                <span class="hidden md:inline">Invoice</span>
            </inertia-link>
        </div>
        <div class=" mx-auto">
            <div class="bg-white rounded shadow overflow-x-auto">
                <table class="w-full whitespace-no-wrap">
                    <thead class="bg-gray-50">
                    <tr class="text-left font-bold">
                        <th class="px-6 pt-4 pb-4 font-medium text-gray-500">ID</th>
                        <th class="px-6 pt-4 pb-4 font-medium text-gray-500">Consultation</th>
                        <th class="px-6 pt-4 pb-4 font-medium text-gray-500">Patient</th>
                        <th class="px-6 pt-4 pb-4 font-medium text-gray-500">Doctor</th>
                        <th class="px-6 pt-4 pb-4 font-medium text-gray-500">Patient Amount</th>
                        <th class="px-6 pt-4 pb-4 font-medium text-gray-500">Copayer</th>
                        <th class="px-6 pt-4 pb-4 font-medium text-gray-500">Balance</th>
                        <th class="px-6 pt-4 pb-4 font-medium text-gray-500">Date</th>
                        <th class="px-6 pt-4 pb-4 font-medium text-gray-500">Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="invoice in invoices.data" :key="invoice.id"
                        class="hover:bg-gray-100 focus-within:bg-gray-100">
                        <td class="border-t">
                            <span class="px-6 py-4 flex items-center">
                                 <inertia-link :href="route('billing.invoices.show', invoice.id)"
                                               tabindex="-1" class="text-indigo-600 hover:text-indigo-900">
                                    {{ invoice.id }}
                                </inertia-link>
                            </span>
                        </td>
                        <td class="border-t">
                             <span class="px-6 py-4 flex items-center" v-if="invoice.consultation_id">
                                 <inertia-link :href="route('consultations.show', invoice.consultation_id)"
                                               tabindex="-1" class="text-indigo-600 hover:text-indigo-900">
                                    {{ invoice.consultation_id }}
                                </inertia-link>
                            </span>
                            <span class="px-6 py-4 flex items-center" v-else>
                                {{ invoice.consultation_id }}
                            </span>
                        </td>
                        <td class="border-t">
                             <span class="px-6 py-4 flex items-center" v-if="invoice.patient">
                                 <inertia-link :href="route('patients.show', invoice.patient_id)"
                                               tabindex="-1" class="text-indigo-600 hover:text-indigo-900">
                                    {{ invoice.patient.name }}
                                </inertia-link>
                            </span>
                        </td>
                        <td class="border-t">
                             <span class="px-6 py-4 flex items-center" v-if="invoice.doctor">
                                 <inertia-link :href="route('users.show', invoice.doctor_id)"
                                               tabindex="-1" class="text-indigo-600 hover:text-indigo-900">
                                    {{ invoice.doctor.name }}
                                </inertia-link>
                            </span>
                        </td>
                        <td class="border-t">
                           <span class="px-6 py-4 flex items-center">
                                {{ invoice.cash_amount }}
                            </span>
                        </td>
                        <td class="border-t">
                           <span class="px-6 py-4 flex items-center">
                                {{ invoice.co_payer_amount }}
                            </span>
                        </td>
                        <td class="border-t">
                           <span class="px-6 py-4 flex items-center">
                                {{ invoice.balance }}
                            </span>
                        </td>
                        <td class="border-t">
                           <span class="px-6 py-4 flex items-center">
                                {{ invoice.date }}
                            </span>
                        </td>
                        <td class="border-t w-px pr-2">
                            <div class=" flex items-center space-x-2">
                                <inertia-link :href="route('billing.invoices.show', invoice.id)"
                                              tabindex="-1" class="text-indigo-600 hover:text-indigo-900">
                                    View
                                </inertia-link>
                                <inertia-link :href="route('billing.invoices.edit', invoice.id)"
                                              tabindex="-1" class="text-indigo-600 hover:text-indigo-900">
                                    Edit
                                </inertia-link>
                                <a href="#" @click="deleteAction(invoice.id)" class="text-red-600 hover:text-red-900">Delete</a>
                            </div>
                        </td>
                    </tr>
                    <tr v-if="invoices.data.length === 0">
                        <td class="border-t px-6 py-4" colspan="9">No invoices found.</td>
                    </tr>
                    </tbody>
                </table>

            </div>
            <pagination :links="invoices.links"/>
        </div>
        <jet-confirmation-modal :show="confirmingDeletion" @close="confirmingDeletion = false">
            <template #title>
                Delete Record
            </template>

            <template #content>
                Are you sure you want to delete record?
            </template>

            <template #footer>
                <jet-secondary-button @click.native="confirmingDeletion = false">
                    Nevermind
                </jet-secondary-button>

                <jet-danger-button class="ml-2" @click.native="destroy" :class="{ 'opacity-25': form.processing }"
                                   :disabled="form.processing">
                    Delete Record
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

export default {
    metaInfo: {title: 'Billing'},
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
    },
    props: {
        invoices: Object,
        filters: Object,

    },
    data() {
        return {
            form: {
                search: this.filters.search,
                processing: false
            },
            confirmingDeletion: false,
            selectedRecord: null,
            pageTitle: "Billing",
            pageDescription: "Manage Billing",

        }
    },
    watch: {
        form: {
            handler: _.debounce(function () {
                let query = pickBy(this.form)
                this.$inertia.get(this.route('billing.invoices.index', Object.keys(query).length ? query : {}))
            }, 500),
            deep: true,
        },
    },
    methods: {
        reset() {
            this.form = mapValues(this.form, () => null)
        },
        deleteAction(id) {
            this.confirmingDeletion = true
            this.selectedRecord = id
        },
        destroy() {

            this.$inertia.delete(this.route('billing.invoices.destroy', this.selectedRecord))
            this.confirmingDeletion = false
        },
    },
}
</script>

<style scoped>

</style>
