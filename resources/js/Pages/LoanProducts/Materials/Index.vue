<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <inertia-link class="text-indigo-400 hover:text-indigo-600" :href="route('courses.index')">Courses
                </inertia-link>
                <span class="text-indigo-400 font-medium">/</span> {{ course.id }}
            </h2>
        </template>
        <div class="mx-auto">
            <div class="md:flex md:items-start">
                <div class="bg-white relative shadow-xl mb-4 w-full md:w-3/12">
                    <div class="col-span-12 lg:col-span-4 xxl:col-span-3 flex lg:block flex-col-reverse">
                        <div class="intro-y box sm:mt-4 lg:mt-0">
                            <course-menu :course="course"></course-menu>
                        </div>
                    </div>

                </div>
                <div class="w-full md:w-9/12 p-4 md:ml-4 bg-white">
                    <div class="flex justify-between ">
                        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Materials</h2>
                        <inertia-link class="btn btn-blue" v-if="can('courses.materials.create')"
                                      :href="route('courses.materials.create',course.id)">
                            <span>Create </span>
                            <span class="hidden md:inline">Material</span>
                        </inertia-link>
                    </div>
                    <div class="mt-4 relative overflow-x-auto">
                        <table class="w-full whitespace-no-wrap table-auto">
                            <thead class="bg-gray-50">
                            <tr class="text-left font-bold">
                                <th class="px-6 pt-4 pb-4 font-medium text-gray-500">Title</th>
                                <th class="px-6 pt-4 pb-4 font-medium text-gray-500">Type</th>
                                <th class="px-6 pt-4 pb-4 font-medium text-gray-500">Date</th>
                                <th class="px-6 pt-4 pb-4 font-medium text-gray-500">Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-if="!materials.data.length">
                                <td colspan="5" class="px-6 py-4 text-center">
                                    No Materials Yet
                                </td>
                            </tr>
                            <tr v-for="material in materials.data" :key="material.id"
                                class="hover:bg-gray-100 focus-within:bg-gray-100">
                                <td class="border-t">
                                    <inertia-link v-if="can('courses.materials.index')"
                                                  :href="route('courses.materials.show', material.id)"
                                                  tabindex="-1" class="px-6 py-4 text-indigo-600 hover:text-indigo-900">
                                        {{ material.title }}
                                    </inertia-link>
                                </td>
                                <td class="border-t">
                                    <span class="px-6 py-4 flex items-center">
                                        <span v-if="material.type==='document'">Document</span>
                                        <span v-if="material.type==='video'">Video</span>
                                        <span v-if="material.type==='external_video'">External Video</span>
                                        <span v-if="material.type==='external_link'">External Link</span>
                                    </span>
                                </td>
                                <td class="border-t">
                                    <span class="px-6 py-4 flex items-center">
                                    {{ $filters.date(material.created_at) }}
                                    </span>
                                </td>

                                <td class="border-t w-px pr-2">
                                    <div class=" flex items-center space-x-2">
                                        <inertia-link v-if="can('courses.materials.index')"
                                                      :href="route('courses.materials.show', material.id)"
                                                      tabindex="-1" class="text-indigo-600 hover:text-indigo-900">
                                            View
                                        </inertia-link>
                                        <inertia-link v-if="can('courses.materials.update')"
                                                      :href="route('courses.materials.edit', material.id)"
                                                      tabindex="-1" class="text-indigo-600 hover:text-indigo-900">
                                            Edit
                                        </inertia-link>
                                        <a href="#" v-if="can('courses.materials.destroy')" @click="deleteAction(material.id)"
                                           class="text-red-600 hover:text-red-900">Delete</a>
                                    </div>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                    <pagination v-if="materials.data.length" :links="materials.links"/>
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
import CourseMenu from '@/Pages/Courses/CourseMenu.vue'

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
        CourseMenu,
    },
    props: {
        course: Object,
        materials: Object,

    },
    data() {
        return {
            form: {
                processing: false
            },
            confirmingDeletion: false,
            selectedRecord: null,
            pageTitle: "Course Materials",
            pageDescription: "Course Materials",

        }
    },
    methods: {
        deleteAction(id) {
            this.confirmingDeletion = true
            this.selectedRecord = id
        },
        destroy() {

            this.$inertia.delete(this.route('courses.materials.destroy', this.selectedRecord))
            this.confirmingDeletion = false
        },
    },
}
</script>

<style scoped>

</style>
