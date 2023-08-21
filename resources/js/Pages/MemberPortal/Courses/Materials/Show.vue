<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <inertia-link class="text-indigo-400 hover:text-indigo-600" :href="route('portal.courses.index')">Courses
                </inertia-link>
                <span class="text-indigo-400 font-medium">/</span> {{ course.name }}
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
                        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ material.title }}</h2>
                        <inertia-link class="btn btn-blue" :href="route('portal.courses.materials.index',course.id)">
                            <span>Back </span>
                        </inertia-link>
                    </div>
                    <div class="mt-4">
                        <a  :href="route('files.download', material.file.id)" target="_blank" class="btn btn-blue" v-if="material.type==='document'">
                            Download File <font-awesome-icon icon="download"/>
                        </a>
                        <a  :href="material.external_link" target="_blank" class="btn btn-blue" v-if="material.type==='external_link'">
                            Download File <font-awesome-icon icon="download"/>
                        </a>
                        <div v-html="material.description" class="mt-2"></div>
                        <div v-if="material.type==='video'" class="mt-2">
                                <video-player
                                    :src="'/storage/'+material.file.link"
                                    class="w-full"
                                    controls
                                    :volume="0.6"
                                />
                        </div>
                        <div v-if="material.type==='external_video'" class="mt-2">
                            <div v-html="material.external_link"></div>
                        </div>
                    </div>
                </div>
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
import CourseMenu from '@/Pages/MemberPortal/Courses/CourseMenu.vue'
import JetInput from "@/Jetstream/Input.vue";
import JetInputError from "@/Jetstream/InputError.vue";
import JetButton from "@/Jetstream/Button.vue";
import JetCheckbox from "@/Jetstream/Checkbox.vue";
import FileInput from "@/Jetstream/FileInput.vue";
import TextareaInput from "@/Jetstream/TextareaInput.vue";
import Button from "@/Jetstream/Button.vue";
import { VideoPlayer } from '@videojs-player/vue'
import 'video.js/dist/video-js.css'

export default {
    components: {
        Button,
        AppLayout,
        Icon,
        TextareaInput,
        JetLabel,
        JetInput,
        JetInputError,
        SelectInput,
        JetConfirmationModal,
        JetDangerButton,
        JetSecondaryButton,
        CourseMenu,
        JetButton,
        JetCheckbox,
        FileInput,
        VideoPlayer
    },
    props: {
        course: Object,
        material: Object,

    },
    data() {
        return {

            pageTitle: "View Course Material",
            pageDescription: "View Course Material",

        }
    },

    methods: {

    },
}
</script>

<style scoped>

</style>
