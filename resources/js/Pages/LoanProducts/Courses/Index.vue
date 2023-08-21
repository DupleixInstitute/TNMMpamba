<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <inertia-link class="text-indigo-400 hover:text-indigo-600" :href="route('courses.index')">Courses
                </inertia-link>
                <span class="text-indigo-400 font-medium">/</span> {{ course.name }}
            </h2>
        </template>
        <div class="mx-auto">
            <div class="md:flex md:items-start">
                <div class="bg-white relative shadow-xl mb-4 mt-20 w-full md:w-3/12">
                    <div class="col-span-12 lg:col-span-4 xxl:col-span-3 flex lg:block flex-col-reverse">
                        <div class="intro-y box mt-5 lg:mt-0">
                            <course-menu :course="course"></course-menu>
                        </div>
                    </div>

                </div>
                <div class="w-full md:w-9/12 p-4 md:ml-4 bg-white sm:mt-4">
                    <div class="flex justify-between ">
                        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Courses</h2>
                        <inertia-link class="btn btn-blue" v-if="can('courses.registrations.create')"
                                      :href="route('courses.registrations.create',{course_id:course.id})">
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
                                <th class="px-6 pt-4 pb-4 font-medium text-gray-500">Course ID</th>
                                <th class="px-6 pt-4 pb-4 font-medium text-gray-500">Tutor</th>
                                <th class="px-6 pt-4 pb-4 font-medium text-gray-500">Status</th>
                                <th class="px-6 pt-4 pb-4 font-medium text-gray-500">Date</th>
                                <th class="px-6 pt-4 pb-4 font-medium text-gray-500">Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-if="!courses.data.length">
                                <td colspan="6" class="px-6 py-4 text-center">
                                    No Courses yet
                                </td>
                            </tr>
                            <tr v-for="courses in courses.data" :key="course.id"
                                class="hover:bg-gray-100 focus-within:bg-gray-100">
                                <td class="border-t">
                                    <inertia-link :href="route('courses.courses.show', course.id)"
                                                  tabindex="-1" class="px-6 py-4 flex items-center text-blue-600 hover:text-blue-900">
                                        {{ course.id }}
                                    </inertia-link>
                                </td>
                                <td class="border-t">
                                    <span class="px-6 py-4 flex items-center"
                                          v-if="course.course_type==='in_clinic'">
                                        In-Clinic
                                    </span>
                                    <span class="px-6 py-4 flex items-center"
                                          v-if="course.course_type==='text'">
                                        Text
                                    </span>
                                    <span class="px-6 py-4 flex items-center"
                                          v-if="course.course_type==='video'">
                                        Video <font-awesome-icon icon="video" class="w-4 h-4 ml-2"></font-awesome-icon>
                                    </span>
                                </td>
                                <td class="border-t">
                                    <span class="px-6 py-4 flex items-center" v-if="course.doctor">
                                    {{ course.doctor.name }}
                                    </span>
                                </td>
                                <td class="border-t">
                                    <span class="px-6 py-4 flex items-center">
                                        <span v-if="course.stage==='with_receptionist'">
                                            At Reception
                                        </span>
                                        <span v-if="course.stage==='waiting_for_nurse'">
                                            Waiting For Nurse
                                        </span>
                                        <span v-if="course.stage==='with_nurse'">
                                            With Nurse
                                        </span>
                                        <span v-if="course.stage==='waiting_for_doctor'">
                                            Waiting For Doctor
                                        </span>
                                        <span v-if="course.stage==='with_doctor'">
                                            With Doctor
                                        </span>
                                        <span v-if="course.stage==='finalizing'">
                                            Finalizing
                                        </span>
                                        <span v-if="course.stage==='complete'"
                                              class="px-2 rounded-full bg-green-100 text-green-800">
                                            Complete
                                        </span>
                                    </span>
                                </td>
                                <td class="border-t">
                                    <span class="px-6 py-4 flex items-center">
                                    {{ course.date }}
                                    </span>
                                </td>
                                <td class="border-t w-px pr-2">
                                    <div class=" flex items-center space-x-2">
                                        <inertia-link :href="route('courses.registrations.show', course.id)"
                                                      tabindex="-1" class="text-blue-600 hover:text-blue-900">
                                            View
                                        </inertia-link>
                                        <inertia-link v-if="can('courses.registrations.update')"
                                                      :href="route('courses.courses.edit', course.id)"
                                                      tabindex="-1" class="text-indigo-600 hover:text-indigo-900">
                                            Edit
                                        </inertia-link>
                                        <a href="#" v-if="can('courses.registrations.destroy')"
                                           @click="deleteAction(course.id)"
                                           class="text-red-600 hover:text-red-900">Delete</a>
                                    </div>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                    <pagination v-if="courses.data.length" :links="courses.links"/>
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
import CourseMenu from '@/Pages/Courses/CourseMenu.vue'

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
        CourseMenu,
    },
    props: {
        course: Object,
        courses: Object,

    },
    data() {
        return {
            form: {
                processing: false
            },
            confirmingDeletion: false,
            selectedRecord: null,
            pageTitle: "Course Courses",
            pageDescription: "Manage Courses",

        }
    },
    methods: {
        deleteAction(id) {
            this.confirmingDeletion = true
            this.selectedRecord = id
        },
        destroy() {

            this.$inertia.delete(this.route('courses.registrations.destroy', this.selectedRecord))
            this.confirmingDeletion = false
        },
    },
}
</script>

<style scoped>

</style>
