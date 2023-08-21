<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <inertia-link class="text-indigo-400 hover:text-indigo-600" :href="route('registrations.index')">Registration
                </inertia-link>
                <span class="text-indigo-400 font-medium">/</span> Edit
            </h2>
        </template>
        <div class=" mx-auto">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-4">
                <form @submit.prevent="submit" enctype="multipart/form-data">
                    <div class="grid grid-cols-1 md:grid-cols-1 gap-2 mt-4">
                        <div>
                            <jet-label for="member_id" value="Member"/>
                            <Multiselect
                                v-model="form.member_id"
                                v-bind="membersMultiSelect"
                                :required="true"
                            />
                            <jet-input-error :message="form.errors.member_id" class="mt-2"/>
                        </div>
                        <div>
                            <jet-label for="course_id" value="Course"/>
                            <Multiselect
                                v-model="form.course_id"
                                v-bind="coursesMultiSelect"
                                :required="true"
                            />
                            <jet-input-error :message="form.errors.course_id" class="mt-2"/>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-1 gap-2 mt-4">
                        <div>
                            <jet-label for="member_name" value="Name To Appear On Certificate (Leave blank to use selected member)"/>
                            <jet-input id="member_name" type="text" class="block w-full"
                                       v-model="form.member_name"/>
                            <jet-input-error :message="form.errors.member_name" class="mt-2"/>
                        </div>
                        <div>
                            <jet-label for="registration_date" value="Registration Date"/>
                            <flat-pickr
                                v-model="form.registration_date"
                                class="form-control w-full"
                                placeholder="Select date"
                                required
                                id="registration_date"
                                name="registration_date">
                            </flat-pickr>
                            <jet-input-error :message="form.errors.registration_date" class="mt-2"/>

                        </div>
                        <div>
                            <jet-label for="channel" value="Channel"/>
                            <select
                                class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm w-full"
                                name="channel" v-model="form.channel" id="channel">
                                <option value="mobile phone">mobile phone</option>
                                <option value="computer">computer</option>
                            </select>
                            <jet-input-error :message="form.errors.channel" class="mt-2"/>
                        </div>
                        <div>
                            <jet-label for="status" value="Status"/>
                            <select
                                class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm w-full"
                                name="status" v-model="form.status" id="status">
                                <option value="pending">pending</option>
                                <option value="in_progress">in-progress</option>
                                <option value="completed">completed</option>
                            </select>
                            <jet-input-error :message="form.errors.status" class="mt-2"/>
                        </div>
                    </div>
                    <div class="mt-4">
                        <jet-label for="description" value="Additional Notes"/>
                        <textarea-input id="description" class="mt-1 block w-full"
                                        v-model="form.description"/>
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
const fetchCourses = async (query) => {
    let where = ''
    const response = await fetch(
        route('courses.search') + '?s=' + query,
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
        registration: Object,
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

    },
    data() {
        return {
            form: this.$inertia.form({
                '_method': 'PUT',
                course_id: this.registration.course_id,
                member_id:this.registration.member_id,
                member_name: this.registration.member_name,
                channel: this.registration.channel,
                description: this.registration.description,
                registration_date: this.registration.registration_date,
                status: this.registration.status,
            }),
            usersMultiSelect: {
                value: null,
                remark: null,
                placeholder: 'Search for Employee',
                filterResults: false,
                minChars: 2,
                resolveOnLoad: true,
                delay: 4,
                searchable: true,
                options: async (query) => {
                    return await fetchUsers(query||this.registration.staff_id)
                }
            },
            membersMultiSelect: {
                valueProp: 'id',
                label: 'name',
                selected_patient: null,
                placeholder: 'Search for Member',
                filterResults: false,
                minChars: 2,
                resolveOnLoad: true,
                disabled:true,
                delay: 4,
                searchable: true,
                options: async (query) => {
                    return await fetchMembers(this.registration.member_id)
                }
            },
            coursesMultiSelect: {
                valueProp: 'id',
                label: 'name',
                selected_patient: null,
                placeholder: 'Search for Course',
                filterResults: false,
                minChars: 2,
                resolveOnLoad: true,
                disabled:true,
                delay: 4,
                searchable: true,
                options: async (query) => {
                    return await fetchCourses(this.registration.course_id)
                }
            },
            pageTitle: "Edit Registration",
            pageDescription: "Edit Registration",
        }

    },
    methods: {
        submit() {
            this.form.post(this.route('registrations.update', this.registration.id), {})
        },

    },
    computed: {

    }
}
</script>
<style scoped>

</style>
