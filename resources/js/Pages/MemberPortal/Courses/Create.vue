<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <inertia-link class="text-indigo-400 hover:text-indigo-600" :href="route('courses.index')">Courses
                </inertia-link>
                <span class="text-indigo-400 font-medium">/</span> Create
            </h2>
        </template>
        <div class=" mx-auto">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-4">
                <form @submit.prevent="submit" enctype="multipart/form-data">
                    <div>
                        <jet-label for="name" value="Course Name"/>
                        <jet-input id="name" type="text" class="block w-full"
                                   v-model="form.name"
                                   required/>
                        <jet-input-error :message="form.errors.name" class="mt-2"/>
                    </div>
                    <div class="mt-4">
                        <jet-label for="course_category_id" value="Category"/>
                        <Multiselect
                            id="course_category_id"
                            v-model="form.course_category_id"
                            :options="categories"
                        />
                        <jet-input-error :message="form.errors.course_category_id" class="mt-2"/>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-2 mt-4">
                        <div>
                            <jet-label for="duration" value="Duration(Days)"/>
                            <jet-input id="duration" type="number" class="block w-full"
                                       v-model="form.duration"
                                       required/>
                            <jet-input-error :message="form.errors.duration" class="mt-2"/>
                        </div>
                        <div>
                            <jet-label for="featured_image" value="Course Image"/>
                            <file-input id="featured_image" v-model="form.featured_image"/>
                            <jet-input-error :message="form.errors.featured_image" class="mt-2"/>
                        </div>
                    </div>
                    <div class="mt-4">
                        <jet-label for="status" value="Status"/>
                        <select-input
                            name="status" v-model="form.status" id="status" required>
                            <option></option>
                            <option value="draft">Draft</option>
                            <option value="publish">Publish</option>
                            <option value="trash">Trash</option>
                        </select-input>
                        <jet-input-error :message="form.errors.status" class="mt-2"/>
                    </div>
                    <div class="grid grid-cols-1 mt-4 gap-2" v-if="form.status==='future'">
                        <jet-label for="publish_on" value="Publish On"/>
                        <flat-pickr
                            v-model="form.publish_on"
                            class="form-control"
                            placeholder="Select Publish Date"
                            id="publish_on"
                            name="publish_on" required>
                        </flat-pickr>
                        <jet-input-error :message="form.errors.publish_on" class="mt-2"/>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 mt-4">
                        <jet-label for="allow_comments">
                            <div class="flex items-center">
                                <jet-checkbox name="allow_comments" id="allow_comments"
                                              v-model:checked="form.allow_comments"/>
                                <div class="ml-2">
                                    Allow Comments
                                </div>
                            </div>
                        </jet-label>
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
                    <div class="flex items-center justify-end mt-4">

                        <jet-button class="ml-4" :class="{ 'opacity-25': form.processing }"
                                    :disabled="form.processing">
                            Save
                        </jet-button>
                    </div>
                </form>
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
import JetButton from "@/Jetstream/Button.vue";
import JetInput from "@/Jetstream/Input.vue";
import JetInputError from "@/Jetstream/InputError.vue";
import JetCheckbox from "@/Jetstream/Checkbox.vue";
import JetLabel from "@/Jetstream/Label.vue";
import Select from "@/Jetstream/Select.vue";
import FileInput from "@/Jetstream/FileInput.vue";
import TextareaInput from "@/Jetstream/TextareaInput.vue";
import Editor from '@tinymce/tinymce-vue'
import SelectInput from "@/Jetstream/SelectInput.vue"

const fetchMembers = async (query) => {
    let where = ''
    const response = await fetch(
        route('members.search') + '?s=' + query,
        {}
    );

    const data = await response.json(); // Here you have the data that you need
    return data.map((item) => {
        return item;
    })
}
const fetchUsers = async (query) => {
    let where = ''
    const response = await fetch(
        route('users.search') + '?type=employee&s=' + query,
        {}
    );

    const data = await response.json(); // Here you have the data that you need
    return data.map((item) => {
        return {value: item.id, label: item.name}
    })
}

export default {
    props: {
        categories: Object,
        branches: Object,
        provinces: Object,
        districts: Object,
        wards: Object,
        villages: Object,
    },
    components: {
        Select,
        AppLayout,
        JetButton,
        JetInput,
        JetCheckbox,
        JetLabel,
        JetInputError,
        FileInput,
        TextareaInput,
        Editor,
        SelectInput

    },
    data() {
        return {
            form: this.$inertia.form({
                tutor_id: null,
                course_category_id: null,
                name: null,
                duration: null,
                amount: null,
                featured_image: null,
                video_url: null,
                excerpt: null,
                description: null,
                allow_comments: true,
                active: true,
                status: 'draft',
            }),
            usersMultiSelect: {
                value: null,
                remark: null,
                placeholder: 'Search for Employee',
                filterResults: false,
                minChars: 2,
                resolveOnLoad: false,
                delay: 4,
                searchable: true,
                options: async (query) => {
                    return await fetchUsers(query)
                }
            },

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
            pageTitle: "Create Course",
            pageDescription: "Create Course",
        }

    },
    methods: {
        submit() {
            this.form.post(this.route('courses.store'), {})

        },
    },
    computed: {

    }
}
</script>
<style scoped>

</style>
