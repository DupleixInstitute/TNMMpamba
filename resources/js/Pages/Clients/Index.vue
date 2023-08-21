<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Clients
            </h2>
        </template>
        <div class=" mx-auto  mb-4 flex justify-between items-center">
            <filter-search v-model="form.search" class="w-full max-w-md mr-4" @reset="reset">
                <div class="w-80 mt-2 px-4 py-6 shadow-xl bg-white rounded">
                    <div class="mb-2">
                        <jet-label for="type" value="Type"/>
                        <select v-model="form.type"
                                class="mt-1 w-full border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm">
                            <option :value="null"/>
                            <option value="individual">Individual</option>
                            <option value="corporate">Corporate</option>
                        </select>
                    </div>
                    <div class="mb-2">
                        <jet-label for="gender" value="Gender"/>
                        <select v-model="form.gender"
                                class="mt-1 w-full border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm">
                            <option :value="null"/>
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                        </select>
                    </div>
                </div>
            </filter-search>
            <inertia-link class="btn btn-blue" v-if="can('clients.create')" :href="route('clients.create')">
                <span>Create </span>
                <span class="hidden md:inline">Client</span>
            </inertia-link>
        </div>
        <div class=" mx-auto">
            <div class="bg-white rounded shadow overflow-x-auto">
                <table class="w-full whitespace-no-wrap table-auto">
                    <thead class="bg-gray-50">
                    <tr class="text-left font-bold">
                        <th class="px-6 pt-4 pb-4 font-medium text-gray-500">ID</th>
                        <th class="px-6 pt-4 pb-4 font-medium text-gray-500">Name</th>
                        <th class="px-6 pt-4 pb-4 font-medium text-gray-500">Type</th>
                        <th class="px-6 pt-4 pb-4 font-medium text-gray-500">External ID</th>
                        <th class="px-6 pt-4 pb-4 font-medium text-gray-500">Mobile</th>
                        <th class="px-6 pt-4 pb-4 font-medium text-gray-500">Joining Date</th>
                        <th class="px-6 pt-4 pb-4 font-medium text-gray-500">Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-if="!clients.data.length">
                        <td colspan="7" class="px-6 py-4 text-center">
                            No Clients yet
                        </td>
                    </tr>
                    <tr v-for="client in clients.data" :key="client.id"
                        class="hover:bg-gray-100 focus-within:bg-gray-100">
                        <td class="border-t">
                            <inertia-link class="px-6 py-4 flex items-center" :href="route('clients.show', client.id)"
                                          tabindex="-1">
                                {{ client.id }}
                            </inertia-link>
                        </td>
                        <td class="border-t">
                            <inertia-link class="px-6 py-4 flex items-center focus:text-indigo-500"
                                          :href="route('clients.show', client.id)">
                                <img v-if="client.profile_photo_url" class="block w-5 h-5 rounded-full mr-2 -my-2"
                                     :src="client.profile_photo_url">
                                {{ client.name }}
                                <icon v-if="client.deleted_at" name="trash"
                                      class="flex-shrink-0 w-3 h-3 fill-gray-400 ml-2"/>
                            </inertia-link>
                        </td>
                        <td class="border-t">
                            <inertia-link class="px-6 py-4 flex items-center" :href="route('clients.show', client.id)"
                                          tabindex="-1">
                                <span v-if="client.type=='individual'">Individual</span>
                                <span v-if="client.type=='corporate'">Corporate</span>
                            </inertia-link>
                        </td>
                        <td class="border-t">
                            <inertia-link class="px-6 py-4 flex items-center" :href="route('clients.show', client.id)"
                                          tabindex="-1">
                                {{ client.external_id }}
                            </inertia-link>
                        </td>
                        <td class="border-t">
                            <inertia-link class="px-6 py-4 flex items-center" :href="route('clients.show', client.id)"
                                          tabindex="-1">
                                {{ client.mobile }}
                            </inertia-link>
                        </td>

                        <td class="border-t">
                            <inertia-link class="px-6 py-4 flex items-center" :href="route('clients.show', client.id)"
                                          tabindex="-1">
                                {{ client.join_date }}
                            </inertia-link>
                        </td>
                        <td class="border-t w-px pr-2">
                            <div class=" flex items-center gap-4">
                                <inertia-link :href="route('clients.show', client.id)"
                                              tabindex="-1" class="text-green-600 hover:text-green-900" title="View">
                                    <font-awesome-icon icon="search"/>
                                </inertia-link>
                                <inertia-link v-if="can('clients.update')"
                                              :href="route('clients.edit', client.id)"
                                              tabindex="-1" class="text-indigo-600 hover:text-indigo-900" title="Edit">
                                    <font-awesome-icon icon="edit"/>
                                </inertia-link>
                                <a href="#" v-if="can('clients.destroy')" @click="deleteAction(client.id)"
                                   class="text-red-600 hover:text-red-900" title="Delete">
                                    <font-awesome-icon icon="trash"/>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <tr v-if="clients.length === 0">
                        <td class="border-t px-6 py-4" colspan="7">No clients found.</td>
                    </tr>
                    </tbody>
                </table>

            </div>
            <pagination :links="clients.links"/>
        </div>
        <jet-confirmation-modal :show="confirmingClientDeletion" @close="confirmingClientDeletion = false">
            <template #title>
                Delete Account
            </template>

            <template #content>
                Are you sure you want to delete your account? Once your account is deleted, all of its resources and
                data will be permanently deleted.
            </template>

            <template #footer>
                <jet-secondary-button @click.native="confirmingClientDeletion = false">
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
        clients: Object,
        filters: Object,
        roles: Object,

    },
    data() {
        return {
            form: {
                search: this.filters.search,
                type: this.filters.type,
                province_id: this.filters.province_id,
                branch_id: this.filters.branch_id,
                district_id: this.filters.district_id,
                ward_id: this.filters.ward_id,
                village_id: this.filters.village_id,
                gender: this.filters.gender,
                processing: false
            },
            confirmingClientDeletion: false,
            selectedRecord: null,
            pageTitle: "Clients",
            pageDescription: "Manage Clients",

        }
    },
    watch: {
        form: {
            handler: _.debounce(function () {
                let query = pickBy(this.form)
                this.$inertia.get(this.route('clients.index', Object.keys(query).length ? query : {}))
            }, 500),
            deep: true,
        },
    },
    methods: {
        reset() {
            this.form = mapValues(this.form, () => null)
        },
        deleteAction(id) {
            this.confirmingClientDeletion = true
            this.selectedRecord = id
        },
        destroy() {

            this.$inertia.delete(this.route('clients.destroy', this.selectedRecord))
            this.confirmingClientDeletion = false
        },
    },
}
</script>

<style scoped>

</style>
