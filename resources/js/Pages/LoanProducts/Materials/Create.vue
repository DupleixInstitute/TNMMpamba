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
                        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Create Material</h2>
                        <inertia-link class="btn btn-blue" :href="route('courses.materials.index',course.id)">
                            <span>Back </span>
                        </inertia-link>
                    </div>
                    <div class="mt-4">
                        <form @submit.prevent="submit" enctype="multipart/form-data">
                            <div class="grid grid-cols-1 gap-4">
                                <div>
                                    <jet-label for="title" value="Title"/>
                                    <jet-input id="title" type="text" class="mt-1 block w-full"
                                               v-model="form.title"/>
                                    <jet-input-error :message="form.errors.title" class="mt-2"/>

                                </div>
                                <div>
                                    <jet-label for="type" value="Type"/>
                                    <select-input
                                        name="type" v-model="form.type" id="type" required>
                                        <option></option>
                                        <option value="document">Document</option>
                                        <option value="video">Video</option>
                                        <option value="external_video">External Video</option>
                                        <option value="external_link">External Link</option>
                                    </select-input>
                                    <jet-input-error :message="form.errors.type" class="mt-2"/>
                                </div>
                                <div v-if="form.type==='document'||form.type==='video'">
                                    <jet-label for="file" value="File"/>
                                    <file-input v-model="form.file" class="mt-1 block w-full" id="file" type="file" required/>
                                    <jet-input-error :message="form.errors.file" class="mt-2"/>
                                </div>
                                <div v-if="form.type==='external_video'||form.type==='external_link'">
                                    <jet-label for="external_link" value="Link"/>
                                    <jet-input id="external_link" type="text" class="mt-1 block w-full"
                                               v-model="form.external_link" required/>
                                    <jet-input-error :message="form.errors.external_link" class="mt-2"/>
                                </div>
                                <div>
                                    <jet-label for="position" value="Position"/>
                                    <jet-input id="position" type="number" class="mt-1 block w-full"
                                               v-model="form.position"/>
                                    <jet-input-error :message="form.errors.position" class="mt-2"/>
                                </div>
                                <div class="mt-4">
                                    <jet-label for="description" value="Description"/>
                                    <editor
                                        v-model="form.description"
                                        api-key="no-api-key"
                                        :init="editorConfig"
                                    />
                                    <jet-input-error :message="form.errors.description" class="mt-2"/>

                                </div>
                            </div>
                            <div class="flex items-center justify-end mt-4">
                                <jet-button class="ml-4" :class="{ 'opacity-25': form.processing }"
                                            :disabled="form.processing">
                                    Save
                                </jet-button>
                            </div>
                        </form>
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
import CourseMenu from '@/Pages/Courses/CourseMenu.vue'
import JetInput from "@/Jetstream/Input.vue";
import JetInputError from "@/Jetstream/InputError.vue";
import JetButton from "@/Jetstream/Button.vue";
import JetCheckbox from "@/Jetstream/Checkbox.vue";
import FileInput from "@/Jetstream/FileInput.vue";
import TextareaInput from "@/Jetstream/TextareaInput.vue";
import Editor from "@tinymce/tinymce-vue";

export default {
    components: {
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
        Editor,
    },
    props: {
        course: Object,

    },
    data() {
        return {
            form: this.$inertia.form({
                title: null,
                type: 'document',
                external_link: null,
                position: null,
                description: null,
                file: null,
            }),
            editorConfig: {
                menubar: 'file edit view insert format tools table help',
                plugins: 'lists link  preview image media insertdatetime help   importcss emoticons code advlist anchor autolink autoresize charmap codesample fullscreen  pagebreak quickbars save table',
                toolbar: 'styleselect | bold italic | format alignleft aligncenter alignright alignjustify | bullist numlist  table paste | anchor charmap pagebreak link image media insertdatetime code codesample emoticons save preview print fullscreen',
                images_upload_url: this.route('files.upload'),
                images_upload_base_path: '/storage',
                relative_urls: false,
                remove_script_host: false,
                convert_urls: true,
                height: "480"
            },
            pageTitle: "Create Course Material",
            pageDescription: "Create Course Material",

        }
    },

    methods: {
        submit() {
            this.form.post(this.route('courses.materials.store', this.course.id), {})
        },
    },
}
</script>

<style scoped>

</style>
