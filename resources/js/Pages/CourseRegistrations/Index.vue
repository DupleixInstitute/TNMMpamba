<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Registrations
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
                        <jet-label for="registration_category_id" value="Category"/>
                        <Multiselect
                            id="registration_category_id"
                            v-model="form.registration_category_id"
                            :options="categories"
                        />
                    </div>
                </div>
            </filter-search>
            <inertia-link class="btn btn-blue" v-if="can('courses.registrations.create')" :href="route('registrations.create')">
                <span>Create </span>
                <span class="hidden md:inline">Registration</span>
            </inertia-link>
        </div>
        <div class=" mx-auto">
            <div class="bg-white rounded shadow overflow-x-auto">
                <table class="w-full whitespace-no-wrap table-auto">
                    <thead class="bg-gray-50">
                    <tr class="text-left font-bold">
                        <th class="px-6 pt-4 pb-4 font-medium text-gray-500">ID</th>
                        <th class="px-6 pt-4 pb-4 font-medium text-gray-500">Member</th>
                        <th class="px-6 pt-4 pb-4 font-medium text-gray-500">Course</th>
                        <th class="px-6 pt-4 pb-4 font-medium text-gray-500">Tutor</th>
                        <th class="px-6 pt-4 pb-4 font-medium text-gray-500">Status</th>
                        <th class="px-6 pt-4 pb-4 font-medium text-gray-500">Date</th>
                        <th class="px-6 pt-4 pb-4 font-medium text-gray-500">Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-if="!registrations.data.length">
                        <td colspan="7" class="px-6 py-4 text-center">
                            No registrations yet
                        </td>
                    </tr>
                    <tr v-for="registration in registrations.data" :key="registration.id"
                        class="hover:bg-gray-100 focus-within:bg-gray-100">
                        <td class="border-t">
                            <inertia-link class="px-6 py-4 flex items-center" :href="route('registrations.show', registration.id)"
                                          tabindex="-1">
                                {{ registration.id }}
                            </inertia-link>
                        </td>
                        <td class="border-t">
                            <inertia-link class="px-6 py-4 flex items-center focus:text-indigo-500"
                                          :href="route('members.show', registration.member_id)" v-if="registration.member">
                                <img v-if="registration.member.profile_photo_url" class="block w-5 h-5 rounded-full mr-2 -my-2"
                                     :src="registration.member.profile_photo_url">
                                {{ registration.member.name }}
                            </inertia-link>
                        </td>
                        <td class="border-t">
                            <inertia-link class="px-6 py-4 flex items-center" :href="route('courses.show', registration.course_id)"
                                          tabindex="-1" v-if="registration.course">
                                {{ registration.course.name }}
                            </inertia-link>
                        </td>
                        <td class="border-t">
                            <inertia-link class="px-6 py-4 flex items-center" :href="route('users.show', registration.tutor_id)"
                                          tabindex="-1" v-if="registration.tutor">
                                {{ registration.tutor.name }}
                            </inertia-link>
                        </td>
                        <td class="border-t">
                                 <span class="px-6 py-4 flex items-center">
                                    <span v-if="registration.status=='pending'"
                                          class="px-2 rounded-full bg-yellow-100 text-yellow-800">
                                        pending
                                    </span>
                                    <span v-if="registration.status=='in_progress'"
                                          class="px-2 rounded-full bg-blue-100 text-blue-800">
                                        in progress
                                    </span>
                                     <span v-if="registration.status=='completed'"
                                           class="px-2 rounded-full bg-green-100 text-green-800">
                                        completed
                                    </span>
                                    <span v-if="registration.status=='done'"
                                          class="px-2 rounded-full bg-green-100 text-green-800">
                                        done
                                    </span>
                                    <span v-if="registration.status=='cancelled'"
                                          class="px-2 rounded-full bg-red-100 text-red-800">
                                        cancelled
                                    </span>
                                     <span v-if="registration.status=='rejected'"
                                           class="px-2 rounded-full bg-red-100 text-red-800">
                                        rejected
                                    </span>
                                </span>
                        </td>
                        <td class="border-t">
                            <span class="px-6 py-4 flex items-center">
                                 {{ registration.registration_date }}
                            </span>
                        </td>

                        <td class="border-t w-px pr-2">
                            <div class=" flex items-center gap-4">
                                <inertia-link :href="route('registrations.show', registration.id)"
                                              tabindex="-1" class="text-green-600 hover:text-green-900" title="View">
                                    <font-awesome-icon icon="search"/>
                                </inertia-link>
                                <inertia-link v-if="can('courses.registrations.update')"
                                              :href="route('registrations.edit', registration.id)"
                                              tabindex="-1" class="text-indigo-600 hover:text-indigo-900" title="Edit">
                                    <font-awesome-icon icon="edit"/>
                                </inertia-link>
                                <a href="#" v-if="can('courses.registrations.destroy')" @click="deleteAction(registration.id)"
                                   class="text-red-600 hover:text-red-900" title="Delete">
                                    <font-awesome-icon icon="trash"/>
                                </a>
                            </div>
                        </td>
                    </tr>
                    </tbody>
                </table>

            </div>
            <pagination :links="registrations.links"/>
        </div>
        <jet-confirmation-modal :show="confirmingRegistrationDeletion" @close="confirmingRegistrationDeletion = false">
            <template #title>
                Delete Record
            </template>

            <template #content>
                Are you sure you want to delete your account? Once record is deleted, all of its resources and
                data will be permanently deleted.
            </template>

            <template #footer>
                <jet-secondary-button @click.native="confirmingRegistrationDeletion = false">
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
        registrations: Object,
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
                registration_category_id: this.filters.registration_category_id,
                member_id: this.filters.member_id,
                status: this.filters.status,
                processing: false
            },
            confirmingRegistrationDeletion: false,
            selectedRecord: null,
            pageTitle: "Registrations",
            pageDescription: "Manage Registrations",

        }
    },
    watch: {
        form: {
            handler: _.debounce(function () {
                let query = pickBy(this.form)
                this.$inertia.get(this.route('registrations.index', Object.keys(query).length ? query : {}))
            }, 500),
            deep: true,
        },
    },
    methods: {
        reset() {
            this.form = mapValues(this.form, () => null)
        },
        deleteAction(id) {
            this.confirmingRegistrationDeletion = true
            this.selectedRecord = id
        },
        destroy() {
            this.$inertia.delete(this.route('registrations.destroy', this.selectedRecord))
            this.confirmingRegistrationDeletion = false
        },
    },
}
</script>

<style scoped>

</style>
