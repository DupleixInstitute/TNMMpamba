<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <inertia-link class="text-indigo-400 hover:text-indigo-600" :href="route('clients.index')">Clients
                </inertia-link>
                <span class="text-indigo-400 font-medium">/</span> {{ client.name }}
            </h2>
        </template>
        <div class="mx-auto">
            <div class="md:flex md:items-start">
                <div class="bg-white relative shadow-xl mb-4 mt-20 w-full md:w-3/12">
                    <div class="col-span-12 lg:col-span-4 xxl:col-span-3 flex lg:block flex-col-reverse">
                        <div class="intro-y box mt-5 lg:mt-0">
                            <client-menu :client="client"></client-menu>
                        </div>
                    </div>

                </div>
                <div class="w-full md:w-9/12 p-4 md:ml-4 bg-white sm:mt-4">
                    <div class="flex justify-between ">
                        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Course Registrations</h2>
                        <inertia-link class="btn btn-blue" v-if="can('courses.registrations.create')"
                                      :href="route('registrations.create',{client_id:client.id})">
                            <span>Register </span>
                            <span class="hidden md:inline">Course</span>
                        </inertia-link>
                    </div>
                    <div class="mt-4 relative overflow-x-auto">
                        <table class="w-full whitespace-no-wrap table-auto">
                            <thead class="bg-gray-50">
                            <tr class="text-left font-bold">
                                <th class="px-6 pt-4 pb-4 font-medium text-gray-500">ID</th>
                                <th class="px-6 pt-4 pb-4 font-medium text-gray-500">Course Name</th>
                                <th class="px-6 pt-4 pb-4 font-medium text-gray-500">Tutor</th>
                                <th class="px-6 pt-4 pb-4 font-medium text-gray-500">Status</th>
                                <th class="px-6 pt-4 pb-4 font-medium text-gray-500">Date</th>
                                <th class="px-6 pt-4 pb-4 font-medium text-gray-500">Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-if="!registrations.data.length">
                                <td colspan="6" class="px-6 py-4 text-center">
                                    No Courses yet
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
                    <pagination v-if="registrations.data.length" :links="registrations.links"/>
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
import ClientMenu from '@/Pages/Clients/ClientMenu.vue'

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
        ClientMenu,
    },
    props: {
        client: Object,
        registrations: Object,

    },
    data() {
        return {
            form: {
                processing: false
            },
            confirmingDeletion: false,
            selectedRecord: null,
            pageTitle: "Client Courses",
            pageDescription: "Manage Clients",

        }
    },
    methods: {
        deleteAction(id) {
            this.confirmingDeletion = true
            this.selectedRecord = id
        },
        destroy() {

            this.$inertia.delete(this.route('registrations.destroy', this.selectedRecord))
            this.confirmingDeletion = false
        },
    },
}
</script>

<style scoped>

</style>
