<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Members
            </h2>
        </template>
        <div class=" mx-auto  mb-4 flex justify-between items-center">
            <filter-search v-model="form.search" class="w-full max-w-md mr-4" @reset="reset">
                <div class="w-80 mt-2 px-4 py-6 shadow-xl bg-white rounded">
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
            <inertia-link class="btn btn-blue" v-if="can('members.create')" :href="route('members.create')">
                <span>Create </span>
                <span class="hidden md:inline">Member</span>
            </inertia-link>
        </div>
        <div class=" mx-auto">
            <div class="bg-white rounded shadow overflow-x-auto">
                <table class="w-full whitespace-no-wrap table-auto">
                    <thead class="bg-gray-50">
                    <tr class="text-left font-bold">
                        <th class="px-6 pt-4 pb-4 font-medium text-gray-500">ID</th>
                        <th class="px-6 pt-4 pb-4 font-medium text-gray-500">Name</th>
                        <th class="px-6 pt-4 pb-4 font-medium text-gray-500">Gender</th>
                        <th class="px-6 pt-4 pb-4 font-medium text-gray-500">Age</th>
                        <th class="px-6 pt-4 pb-4 font-medium text-gray-500">Mobile</th>
                        <th class="px-6 pt-4 pb-4 font-medium text-gray-500">Joining Date</th>
                        <th class="px-6 pt-4 pb-4 font-medium text-gray-500">Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-if="!members.data.length">
                        <td colspan="7" class="px-6 py-4 text-center">
                            No Members yet
                        </td>
                    </tr>
                    <tr v-for="member in members.data" :key="member.id"
                        class="hover:bg-gray-100 focus-within:bg-gray-100">
                        <td class="border-t">
                            <inertia-link class="px-6 py-4 flex items-center" :href="route('members.show', member.id)"
                                          tabindex="-1">
                                {{ member.id }}
                            </inertia-link>
                        </td>
                        <td class="border-t">
                            <inertia-link class="px-6 py-4 flex items-center focus:text-indigo-500"
                                          :href="route('members.show', member.id)">
                                <img v-if="member.profile_photo_url" class="block w-5 h-5 rounded-full mr-2 -my-2"
                                     :src="member.profile_photo_url">
                                {{ member.name }}
                                <icon v-if="member.deleted_at" name="trash"
                                      class="flex-shrink-0 w-3 h-3 fill-gray-400 ml-2"/>
                            </inertia-link>
                        </td>
                        <td class="border-t">
                            <inertia-link class="px-6 py-4 flex items-center" :href="route('members.show', member.id)"
                                          tabindex="-1">
                                <span v-if="member.gender=='male'">Male</span>
                                <span v-if="member.gender=='female'">Female</span>
                                <span v-if="member.gender=='unspecified'">Unspecified</span>
                            </inertia-link>
                        </td>
                        <td class="border-t">
                            <inertia-link class="px-6 py-4 flex items-center" :href="route('members.show', member.id)"
                                          tabindex="-1">
                                {{ member.age }}
                            </inertia-link>
                        </td>
                        <td class="border-t">
                            <inertia-link class="px-6 py-4 flex items-center" :href="route('members.show', member.id)"
                                          tabindex="-1">
                                {{ member.mobile }}
                            </inertia-link>
                        </td>

                        <td class="border-t">
                            <inertia-link class="px-6 py-4 flex items-center" :href="route('members.show', member.id)"
                                          tabindex="-1">
                                {{ member.join_date }}
                            </inertia-link>
                        </td>
                        <td class="border-t w-px pr-2">
                            <div class=" flex items-center gap-4">
                                <inertia-link :href="route('members.show', member.id)"
                                              tabindex="-1" class="text-green-600 hover:text-green-900" title="View">
                                    <font-awesome-icon icon="search"/>
                                </inertia-link>
                                <inertia-link v-if="can('members.update')"
                                              :href="route('members.edit', member.id)"
                                              tabindex="-1" class="text-indigo-600 hover:text-indigo-900" title="Edit">
                                    <font-awesome-icon icon="edit"/>
                                </inertia-link>
                                <a href="#" v-if="can('members.destroy')" @click="deleteAction(member.id)"
                                   class="text-red-600 hover:text-red-900" title="Delete">
                                    <font-awesome-icon icon="trash"/>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <tr v-if="members.length === 0">
                        <td class="border-t px-6 py-4" colspan="4">No members found.</td>
                    </tr>
                    </tbody>
                </table>

            </div>
            <pagination :links="members.links"/>
        </div>
        <jet-confirmation-modal :show="confirmingMemberDeletion" @close="confirmingMemberDeletion = false">
            <template #title>
                Delete Account
            </template>

            <template #content>
                Are you sure you want to delete your account? Once your account is deleted, all of its resources and
                data will be permanently deleted.
            </template>

            <template #footer>
                <jet-secondary-button @click.native="confirmingMemberDeletion = false">
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
        members: Object,
        filters: Object,
        roles: Object,

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
                gender: this.filters.gender,
                processing: false
            },
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
