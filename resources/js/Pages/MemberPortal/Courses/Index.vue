<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Courses
            </h2>
        </template>
        <div class=" mx-auto  mb-4 flex justify-between items-center">
            <filter-search v-model="form.search" class="w-full max-w-md mr-4" @reset="reset">
                <div class="w-80 mt-2 px-4 py-6 shadow-xl bg-white rounded">
                    <div class="mb-2">
                        <jet-label for="course_category_id" value="Category"/>
                        <Multiselect
                            id="course_category_id"
                            v-model="form.course_category_id"
                            :options="categories"
                        />
                    </div>
                </div>
            </filter-search>
            <inertia-link class="btn btn-blue" :href="route('portal.registrations.create')">
                <span>Register </span>
                <span class="hidden md:inline">Course</span>
            </inertia-link>
        </div>
        <div class=" mx-auto">
            <div class="bg-white rounded shadow overflow-x-auto">
                <table class="w-full whitespace-no-wrap table-auto">
                    <thead class="bg-gray-50">
                    <tr class="text-left font-bold">
                        <th class="px-6 pt-4 pb-4 font-medium text-gray-500">ID</th>
                        <th class="px-6 pt-4 pb-4 font-medium text-gray-500">Name</th>
                        <th class="px-6 pt-4 pb-4 font-medium text-gray-500">Category</th>
                        <th class="px-6 pt-4 pb-4 font-medium text-gray-500">Tutor</th>
                        <th class="px-6 pt-4 pb-4 font-medium text-gray-500">Duration</th>
                        <th class="px-6 pt-4 pb-4 font-medium text-gray-500">Date</th>
                        <th class="px-6 pt-4 pb-4 font-medium text-gray-500">Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-if="!courses.data.length">
                        <td colspan="7" class="px-6 py-4 text-center">
                            No Courses yet
                        </td>
                    </tr>
                    <tr v-for="course in courses.data" :key="course.id"
                        class="hover:bg-gray-100 focus-within:bg-gray-100">
                        <td class="border-t">
                            <inertia-link class="px-6 py-4 flex items-center"
                                          :href="route('portal.courses.show', course.id)"
                                          tabindex="-1">
                                {{ course.id }}
                            </inertia-link>
                        </td>
                        <td class="border-t">
                            <inertia-link class="px-6 py-4 flex items-center"
                                          :href="route('portal.courses.show', course.id)"
                                          tabindex="-1">
                                {{ course.name }}
                            </inertia-link>
                        </td>
                        <td class="border-t">
                            <span class="px-6 py-4 flex items-center" v-if="course.category">
                                {{ course.category.name }}
                            </span>
                        </td>
                        <td class="border-t">
                            <span class="px-6 py-4 flex items-center focus:text-indigo-500" v-if="course.tutor">
                                <img v-if="course.tutor.profile_photo_url"
                                     class="block w-5 h-5 rounded-full mr-2 -my-2"
                                     :src="course.tutor.profile_photo_url">
                                {{ course.tutor.name }}
                            </span>
                        </td>
                        <td class="border-t">
                            <span class="px-6 py-4 flex items-center">
                                {{ course.duration }} days
                            </span>
                        </td>
                        <td class="border-t">
                            <span class="px-6 py-4 flex items-center">
                                {{ $filters.date(course.created_at) }}
                            </span>
                        </td>

                        <td class="border-t w-px pr-2">
                            <div class=" flex items-center gap-4">
                                <inertia-link :href="route('portal.courses.show', course.id)"
                                              tabindex="-1" class="text-blue-600 hover:text-blue-900" title="View">
                                    View
                                </inertia-link>
                                <inertia-link :href="route('portal.registrations.create',{course_id:course.id})"
                                              tabindex="-1" class="text-green-600 hover:text-green-900" title="View">
                                    Register
                                </inertia-link>
                            </div>
                        </td>
                    </tr>
                    </tbody>
                </table>

            </div>
            <pagination :links="courses.links"/>
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
        courses: Object,
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
                course_category_id: this.filters.course_category_id,
                tutor_id: this.filters.tutor_id,
                status: this.filters.status,
                approval_status: this.filters.approval_status,
                processing: false
            },
            confirmingCourseDeletion: false,
            selectedRecord: null,
            pageTitle: "Courses",
            pageDescription: "Manage Courses",

        }
    },
    watch: {
        form: {
            handler: _.debounce(function () {
                let query = pickBy(this.form)
                this.$inertia.get(this.route('portal.courses.index', Object.keys(query).length ? query : {}))
            }, 500),
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
