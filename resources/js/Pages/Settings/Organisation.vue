<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Organisation Settings
            </h2>
        </template>
        <div class=" mx-auto">
            <div class="bg-white rounded shadow overflow-x-auto">

                <inertia-link v-if="can('tariffs')"
                              class="w-full border-t-2 border-gray-100 font-medium text-gray-600 py-2 px-4 w-full block hover:bg-gray-100 transition duration-150"
                              :href="route('tariffs.index')">
                    <font-awesome-icon icon="file-invoice" class="w-4 h-4 mr-2"></font-awesome-icon>
                    Manage Tariffs
                </inertia-link>
                <inertia-link v-if="can('icd10codes')"
                              class="hidden w-full border-t-2 border-gray-100 font-medium text-gray-600 py-2 px-4 w-full block hover:bg-gray-100 transition duration-150"
                              :href="route('icd10codes.index')">
                    <font-awesome-icon icon="user" class="w-4 h-4 mr-2"></font-awesome-icon>
                    Manage ICD-10 Codes
                </inertia-link>
                <inertia-link v-if="can('vitals')"
                              class="w-full border-t-2 border-gray-100 font-medium text-gray-600 py-2 px-4 w-full block hover:bg-gray-100 transition duration-150"
                              :href="route('vitals.index')">
                    <font-awesome-icon icon="heartbeat" class="w-4 h-4 mr-2"></font-awesome-icon>
                    Manage Vitals
                </inertia-link>
                <inertia-link v-if="can('co_payers')"
                              class="w-full border-t-2 border-gray-100 font-medium text-gray-600 py-2 px-4 w-full block hover:bg-gray-100 transition duration-150"
                              :href="route('co_payers.index')">
                    <font-awesome-icon icon="hospital-user" class="w-4 h-4 mr-2"></font-awesome-icon>
                    Manage CoPayers
                </inertia-link>
                <inertia-link v-if="can('payment_types')"
                              class="w-full border-t-2 border-gray-100 font-medium text-gray-600 py-2 px-4 w-full block hover:bg-gray-100 transition duration-150"
                              :href="route('payment_types.index')">
                    <font-awesome-icon icon="money-bill" class="w-4 h-4 mr-2"></font-awesome-icon>
                    Payment Methods
                </inertia-link>
                <inertia-link v-if="can('patient_relationships')"
                              class="w-full border-t-2 border-gray-100 font-medium text-gray-600 py-2 px-4 w-full block hover:bg-gray-100 transition duration-150"
                              :href="route('patient_relationships.index')">
                    <font-awesome-icon icon="users" class="w-4 h-4 mr-2"></font-awesome-icon>
                    Patient Relationships
                </inertia-link>
                <inertia-link v-if="can('tax_rates')"
                              class="w-full border-t-2 border-gray-100 font-medium text-gray-600 py-2 px-4 w-full block hover:bg-gray-100 transition duration-150"
                              :href="route('tax_rates.index')">
                    <font-awesome-icon icon="dollar-sign" class="w-4 h-4 mr-2"></font-awesome-icon>
                    Manage Tax Rates
                </inertia-link>
                <inertia-link v-if="can('currencies')"
                              class="w-full border-t-2 border-gray-100 font-medium text-gray-600 py-2 px-4 w-full block hover:bg-gray-100 transition duration-150"
                              :href="route('currencies.index')">
                    <font-awesome-icon icon="dollar-sign" class="w-4 h-4 mr-2"></font-awesome-icon>
                    Manage Currencies
                </inertia-link>
                <inertia-link v-if="can('branches')"
                              class="w-full border-t-2 border-gray-100 font-medium text-gray-600 py-2 px-4 w-full block hover:bg-gray-100 transition duration-150"
                              :href="route('branches.index')">
                    <font-awesome-icon icon="building" class="w-4 h-4 mr-2"></font-awesome-icon>
                    Manage Branches
                </inertia-link>
                <inertia-link v-if="can('users.roles')"
                              class="w-full border-t-2 border-gray-100 font-medium text-gray-600 py-2 px-4 w-full block hover:bg-gray-100 transition duration-150"
                              :href="route('users.roles.index')">
                    <font-awesome-icon icon="users" class="w-4 h-4 mr-2"></font-awesome-icon>
                    Manage Roles
                </inertia-link>
                <inertia-link v-if="can('communication.sms_gateways.index')"
                              class="w-full border-t-2 border-gray-100 font-medium text-gray-600 py-2 px-4 w-full block hover:bg-gray-100 transition duration-150"
                              :href="route('communication.sms_gateways.index')">
                    <font-awesome-icon icon="envelope" class="w-4 h-4 mr-2"></font-awesome-icon>
                    Manage SMS Gateways
                </inertia-link>
                <inertia-link v-if="can('referring_practitioners.index')"
                              class="w-full border-t-2 border-gray-100 font-medium text-gray-600 py-2 px-4 w-full block hover:bg-gray-100 transition duration-150"
                              :href="route('referring_practitioners.index')">
                    <font-awesome-icon icon="users" class="w-4 h-4 mr-2"></font-awesome-icon>
                    Manage Referring Practitioners
                </inertia-link>
                <inertia-link v-if="can('inventory.variations.index')"
                              class="w-full border-t-2 border-gray-100 font-medium text-gray-600 py-2 px-4 w-full block hover:bg-gray-100 transition duration-150"
                              :href="route('inventory.variations.index')">
                    <font-awesome-icon icon="shopping-cart" class="w-4 h-4 mr-2"></font-awesome-icon>
                    Manage Inventory Variations
                </inertia-link>
                <inertia-link v-if="can('inventory.brands.index')"
                              class="w-full border-t-2 border-gray-100 font-medium text-gray-600 py-2 px-4 w-full block hover:bg-gray-100 transition duration-150"
                              :href="route('inventory.brands.index')">
                    <font-awesome-icon icon="shopping-cart" class="w-4 h-4 mr-2"></font-awesome-icon>
                    Manage Inventory Brands
                </inertia-link>
                <inertia-link v-if="can('inventory.categories.index')"
                              class="w-full border-t-2 border-gray-100 font-medium text-gray-600 py-2 px-4 w-full block hover:bg-gray-100 transition duration-150"
                              :href="route('inventory.categories.index')">
                    <font-awesome-icon icon="shopping-cart" class="w-4 h-4 mr-2"></font-awesome-icon>
                    Manage Inventory Categories
                </inertia-link>
                <inertia-link v-if="can('inventory.units.index')"
                              class="w-full border-t-2 border-gray-100 font-medium text-gray-600 py-2 px-4 w-full block hover:bg-gray-100 transition duration-150"
                              :href="route('inventory.units.index')">
                    <font-awesome-icon icon="shopping-cart" class="w-4 h-4 mr-2"></font-awesome-icon>
                    Manage Inventory Units
                </inertia-link>

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
    props: {},
    data() {
        return {
            confirmingDeletion: false,
            selectedRecord: null,
            pageTitle: "Organisation Settings",
            pageDescription: "Manage Settings",
        }
    },

    methods: {},
}
</script>

<style scoped>

</style>
