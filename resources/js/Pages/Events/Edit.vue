<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <inertia-link class="text-indigo-400 hover:text-indigo-600" :href="route('events.index')">
                    Events
                </inertia-link>
                <span class="text-indigo-400 font-medium">/</span> Edit
            </h2>
        </template>

        <div class=" mx-auto">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-4">
                <form @submit.prevent="submit" enctype="multipart/form-data">
                    <div class="grid grid-cols-1 gap-2">
                        <div class="mt-4">
                            <jet-label for="title" value="Title"/>
                            <jet-input id="title" type="text" class="mt-1 block w-full" v-model="form.title"
                                       required autofocus/>
                            <jet-input-error :message="form.errors.title" class="mt-2"/>
                        </div>
                        <div>
                            <jet-label for="event_category_id" value="Category"/>
                            <Multiselect
                                v-model="form.event_category_id"
                                mode="single"
                                :options="$page.props.categories"
                            />
                            <jet-input-error :message="form.errors.event_category_id" class="mt-2"/>
                        </div>
                        <div class="mt-4">
                            <jet-label for="location" value="Location"/>
                            <jet-input id="location" type="text" class="mt-1 block w-full" v-model="form.location"/>
                            <jet-input-error :message="form.errors.location" class="mt-2"/>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                            <div class="">
                                <jet-label for="latitude" value="Latitude"/>
                                <jet-input id="latitude" type="text" class="mt-1 block w-full" v-model="form.latitude"/>
                                <jet-input-error :message="form.errors.latitude" class="mt-2"/>
                            </div>
                            <div class="">
                                <jet-label for="longitude" value="Longitude"/>
                                <jet-input id="longitude" type="text" class="mt-1 block w-full"
                                           v-model="form.longitude"/>
                                <jet-input-error :message="form.errors.longitude" class="mt-2"/>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-4 mt-4">
                            <div>
                                <jet-label for="start_date" value="Start Date"/>
                                <flat-pickr
                                    v-model="form.start_date"
                                    class="form-control w-full"
                                    placeholder="Select date"
                                    name="start_date">
                                </flat-pickr>
                                <jet-input-error :message="form.errors.start_date" class="mt-2"/>

                            </div>
                            <div>
                                <jet-label for="start_time" value="Start Time"/>
                                <flat-pickr
                                    v-model="form.start_time"
                                    class="form-control w-full"
                                    placeholder="Select time"
                                    :config="{time_24hr:true,noCalendar:true,allowInput: true,enableTime:true,dateFormat:'H:i'}"
                                    name="start_time">
                                </flat-pickr>
                                <jet-input-error :message="form.errors.start_time" class="mt-2"/>

                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-4 mt-4">
                            <div>
                                <jet-label for="end_date" value="End Date"/>
                                <flat-pickr
                                    v-model="form.end_date"
                                    class="form-control w-full"
                                    placeholder="Select date"
                                    :config="{allowInput: true}"
                                    name="end_date">
                                </flat-pickr>
                                <jet-input-error :message="form.errors.end_date" class="mt-2"/>

                            </div>
                            <div>
                                <jet-label for="end_time" value="End Time"/>
                                <flat-pickr
                                    v-model="form.end_time"
                                    class="form-control w-full"
                                    placeholder="Select time"
                                    :config="{time_24hr:true,noCalendar:true,allowInput: true,enableTime:true,dateFormat:'H:i'}"
                                    name="end_time">
                                </flat-pickr>
                                <jet-input-error :message="form.errors.end_time" class="mt-2"/>
                            </div>
                        </div>
                        <div class="mt-4">
                            <jet-label for="status" value="Status"/>
                            <select-input
                                name="status" v-model="form.status" id="status" required>
                                <option></option>
                                <option value="pending">Pending</option>
                                <option value="active">Active</option>

                            </select-input>
                            <jet-input-error :message="form.errors.status" class="mt-2"/>
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
import SelectInput from "@/Jetstream/SelectInput.vue";
import FileInput from "@/Jetstream/FileInput.vue";
import TextareaInput from "@/Jetstream/TextareaInput.vue";
import Editor from '@tinymce/tinymce-vue'


export default {
    props: {
        event: Object,
        selectedCategories: Array,
    },
    components: {
        SelectInput,
        AppLayout,
        JetButton,
        JetInput,
        JetCheckbox,
        JetLabel,
        JetInputError,
        FileInput,
        TextareaInput,
        Editor,

    },
    data() {
        return {
            form: this.$inertia.form({
                title: this.event.title,
                event_category_id: this.event.event_category_id,
                location: this.event.location,
                latitude: this.event.latitude,
                longitude: this.event.longitude,
                start_date: this.event.start_date,
                start_time: this.event.start_time,
                end_date: this.event.end_date,
                end_time: this.event.end_time,
                max_attendance: this.event.max_attendance,
                description: this.event.description,
                status: this.event.status,
                recurring: this.event.recurring,
                recur_frequency: this.event.recur_frequency,
                recur_start_date: this.event.recur_start_date,
                recur_end_date: this.event.recur_end_date,
                recur_type: this.event.recur_type,
                selected_days:this.event.selected_days,
            }),
            editorConfig: {
                menubar: false,
                plugins: 'lists link image emoticons code',
                toolbar: 'styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist | link image code emoticons',
                images_upload_url: this.route('files.upload'),
                images_upload_base_path: '/storage',
                relative_urls: false,
                remove_script_host: false,
                convert_urls: true,
                height : "480"
            },
            pageTitle: "Edit Event",
            pageDescription: "Edit Event",
        }

    },
    methods: {
        submit() {
            this.form.put(this.route('events.update',this.event.id), {})
        },

    }
}
</script>
<style scoped>

</style>
