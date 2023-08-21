<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Invoices
            </h2>
        </template>
        <div class=" mx-auto mb-4 flex justify-between items-center">
            <filter-search v-model="form.search" class="w-80 max-w-md mr-4" @reset="reset">
                <div class="w-80 mt-2 px-4 py-6 shadow-xl bg-white rounded">
                    <div class="mb-2">
                        <jet-label for="status" value="Status"/>
                        <select v-model="form.status"
                                class="mt-1 w-full border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm">
                            <option :value="null"/>
                            <option value="paid">Paid</option>
                            <option value="partially_paid">Partially Paid</option>
                            <option value="unpaid">Unpaid</option>
                        </select>
                    </div>
                    <div class="mb-2">
                        <jet-label for="sponsor" value="Sponsor"/>
                        <select v-model="form.sponsor"
                                class="mt-1 w-full border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm">
                            <option :value="null"/>
                            <option value="cash">Cash</option>
                            <option value="co_payer">CoPayer</option>
                        </select>
                    </div>
                    <div class="mb-2">
                        <jet-label for="filter_copayer" value="CoPayer"/>
                        <Multiselect
                            id="filter_copayer"
                            v-model="form.co_payer_id"
                            mode="single"
                            :searchable="true"
                            :options="$page.props.coPayers"
                        />
                    </div>
                    <div class="mb-2">
                        <jet-label for="filter_currency_id" value="Currency"/>
                        <Multiselect
                            id="filter_currency_id"
                            v-model="form.currency_id"
                            mode="single"
                            :searchable="true"
                            :options="$page.props.currencies"
                        />
                    </div>
                    <div class="mb-2">
                        <jet-label for="filter_date_range" value="Date Range"/>
                        <flat-pickr
                            v-model="form.date_range"
                            class="form-control w-full"
                            placeholder="Select date range"
                            :config="{mode:'range',dateFormat:'Y-m-d'}"
                            name="date_range">
                        </flat-pickr>
                    </div>
                </div>
            </filter-search>
        </div>
        <div class=" mx-auto">
            <div class="bg-white rounded shadow overflow-x-auto">
                <table class="w-full whitespace-no-wrap">
                    <thead class="bg-gray-50">
                    <tr class="text-left font-bold">
                        <th class="px-6 pt-4 pb-4 font-medium text-gray-500">ID</th>
                        <th class="px-6 pt-4 pb-4 font-medium text-gray-500">Reference</th>
                        <th class="px-6 pt-4 pb-4 font-medium text-gray-500">Currency</th>
                        <th class="px-6 pt-4 pb-4 font-medium text-gray-500">Cash</th>
                        <th class="px-6 pt-4 pb-4 font-medium text-gray-500">CoPayer</th>
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
                                 <inertia-link :href="route('portal.invoices.show', invoice.id)"
                                               tabindex="-1" class="text-indigo-600 hover:text-indigo-900">
                                    {{ invoice.id }}
                                </inertia-link>
                            </span>
                        </td>
                        <td class="border-t">
                            <span class="px-6 py-4 flex items-center">
                                 <inertia-link :href="route('portal.invoices.show', invoice.id)"
                                               tabindex="-1" class="text-indigo-600 hover:text-indigo-900">
                                    {{ invoice.reference }}
                                </inertia-link>
                            </span>
                        </td>
                        <td class="border-t">
                           <span class="px-6 py-4 flex items-center" v-if="invoice.currency">
                                {{ invoice.currency.code }}
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
                                <inertia-link
                                              :href="route('portal.invoices.show', invoice.id)"
                                              tabindex="-1" class="text-indigo-600 hover:text-indigo-900">
                                    View
                                </inertia-link>
                            </div>
                        </td>
                    </tr>
                    <tr v-if="invoices.data.length === 0">
                        <td class="border-t px-6 py-4" colspan="7">No invoices found.</td>
                    </tr>
                    </tbody>
                </table>

            </div>
            <pagination :links="invoices.links"/>
        </div>
        <teleport to="head">
            <title>{{ pageTitle }}</title>
            <meta property="og:description" :content="pageDescription">
        </teleport>
    </app-layout>

</template>

<script>


import AppLayout from '@/Pages/MemberPortal/Layouts/AppLayout.vue'
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
        currencies: Object,

    },
    data() {
        return {
            form: {
                search: this.filters.search,
                status: this.filters.status,
                currency_id: this.filters.currency_id,
                co_payer_id: this.filters.co_payer_id,
                sponsor: this.filters.sponsor,
                doctor_id: this.filters.doctor_id,
                member_id: this.filters.member_id,
                date_range: this.filters.date_range,
                processing: false
            },
            confirmingDeletion: false,
            selectedRecord: null,
            pageTitle: "Invoices",
            pageDescription: "Manage Invoices",

        }
    },
    watch: {
        form: {
            handler: _.debounce(function () {
                let query = pickBy(this.form)
                this.$inertia.get(this.route('portal.invoices.index', Object.keys(query).length ? query : {}))
            }, 1000),
            deep: true,
        },
    },
    methods: {
        reset() {
            this.form = mapValues(this.form, () => null)
        },
    },
}
</script>

<style scoped>

</style>
