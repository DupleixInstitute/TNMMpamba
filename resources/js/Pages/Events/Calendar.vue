<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Events
            </h2>
        </template>
        <div class="mx-auto mb-4 md:flex justify-between">
            <filter-search v-model="form.search" class="w-full max-w-md mr-4" @reset="reset">
                <div class="w-80 mt-2 px-4 py-6 shadow-xl bg-white rounded">
                    <div class="mb-2">
                        <jet-label for="filter_status" value="Status"/>
                        <select v-model="form.status" id="filter_status"
                                class="mt-1 w-full border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm">
                            <option :value="null"/>
                            <option value="pending">Pending</option>
                            <option value="active">Active</option>
                            <option value="completed">Completed</option>
                        </select>
                    </div>
                    <div class="mb-2">
                            <jet-label for="category_id" value="Category"/>
                            <Multiselect
                                v-model="form.event_category_id"
                                mode="single"
                                :options="$page.props.categories"
                            />
                    </div>
                    <div class="mb-2">
                        <jet-label for="filter_date_range" value="Date Range"/>
                        <flat-pickr
                            v-model="form.date_range"
                            class="form-control w-full"
                            placeholder="Select date range"
                            :config="{mode:'range',dateFormat:'Y-m-d'}"
                            name="date_range">
                        </flat-pickr>
                    </div>
                </div>
            </filter-search>
            <div class="mt-4 md:mt-0">
                <inertia-link class="btn btn-blue"
                              :href="route('events.index')">
                    Table View
                </inertia-link>
                <button class="btn btn-blue ml-2" @click="showAddEventModal=true">
                    <span>Create </span>
                    <span class="hidden md:inline">Event</span>
                </button>
            </div>
        </div>
        <div class=" mx-auto">
            <div class="bg-white rounded shadow overflow-x-auto">
                <div class="p-5">
                    <FullCalendar :options="calendarOptions"/>
                </div>
            </div>
        </div>
        <jet-confirmation-modal :show="confirmingDeletion" @close="confirmingDeletion = false">
            <template #title>
                Delete Record
            </template>

            <template #content>
                Are you sure you want to delete record?
            </template>

            <template #footer>
                <jet-secondary-button @click.native="confirmingDeletion = false">
                    Nevermind
                </jet-secondary-button>

                <jet-danger-button class="ml-2" @click.native="destroy" :class="{ 'opacity-25': form.processing }"
                                   :disabled="form.processing">
                    Delete Record
                </jet-danger-button>
            </template>
        </jet-confirmation-modal>
        <jet-dialog-modal :show="showAddEventModal" @close="showAddEventModal = false">
            <template #title>
                Add Event
            </template>
            <template #content>
                <div class="grid grid-cols-1 gap-2">
                    <div class="mt-4">
                        <jet-label for="title" value="Title"/>
                        <jet-input id="title" type="text" class="mt-1 block w-full" v-model="event.title"
                                   required autofocus/>
                        <jet-input-error :message="errors.title" class="mt-2"/>
                    </div>
                    <div>
                        <jet-label for="event_category_id" value="Category"/>
                        <Multiselect
                            v-model="event.event_category_id"
                            mode="single"
                            :options="$page.props.categories"
                        />
                        <jet-input-error :message="errors.event_category_id" class="mt-2"/>
                    </div>
                    <div class="mt-4">
                        <jet-label for="location" value="Location"/>
                        <jet-input id="location" type="text" class="mt-1 block w-full" v-model="event.location"/>
                        <jet-input-error :message="errors.location" class="mt-2"/>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                        <div class="">
                            <jet-label for="latitude" value="Latitude"/>
                            <jet-input id="latitude" type="text" class="mt-1 block w-full" v-model="event.latitude"/>
                            <jet-input-error :message="errors.latitude" class="mt-2"/>
                        </div>
                        <div class="">
                            <jet-label for="longitude" value="Longitude"/>
                            <jet-input id="longitude" type="text" class="mt-1 block w-full"
                                       v-model="event.longitude"/>
                            <jet-input-error :message="errors.longitude" class="mt-2"/>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4 mt-4">
                        <div>
                            <jet-label for="start_date" value="Start Date"/>
                            <flat-pickr
                                v-model="event.start_date"
                                class="form-control w-full"
                                placeholder="Select date"
                                name="start_date">
                            </flat-pickr>
                            <jet-input-error :message="errors.start_date" class="mt-2"/>

                        </div>
                        <div>
                            <jet-label for="start_time" value="Start Time"/>
                            <flat-pickr
                                v-model="event.start_time"
                                class="form-control w-full"
                                placeholder="Select time"
                                :config="{time_24hr:true,noCalendar:true,allowInput: true,enableTime:true,dateFormat:'H:i'}"
                                name="start_time">
                            </flat-pickr>
                            <jet-input-error :message="errors.start_time" class="mt-2"/>

                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4 mt-4">
                        <div>
                            <jet-label for="end_date" value="End Date"/>
                            <flat-pickr
                                v-model="event.end_date"
                                class="form-control w-full"
                                placeholder="Select date"
                                :config="{allowInput: true}"
                                name="end_date">
                            </flat-pickr>
                            <jet-input-error :message="errors.end_date" class="mt-2"/>

                        </div>
                        <div>
                            <jet-label for="end_time" value="End Time"/>
                            <flat-pickr
                                v-model="event.end_time"
                                class="form-control w-full"
                                placeholder="Select time"
                                :config="{time_24hr:true,noCalendar:true,allowInput: true,enableTime:true,dateFormat:'H:i'}"
                                name="end_time">
                            </flat-pickr>
                            <jet-input-error :message="errors.end_time" class="mt-2"/>
                        </div>
                    </div>
                    <div class="mt-4">
                        <jet-label for="status" value="Status"/>
                        <select-input
                            name="status" v-model="event.status" id="status" required>
                            <option></option>
                            <option value="pending">Pending</option>
                            <option value="active">Active</option>

                        </select-input>
                        <jet-input-error :message="errors.status" class="mt-2"/>
                    </div>
                    <div class="mt-4">
                        <jet-label for="description" value="Description"/>
                        <editor
                            v-model="event.description"
                            api-key="no-api-key"
                            :init="editorConfig"
                        />
                        <jet-input-error :message="errors.description" class="mt-2"/>

                    </div>
                </div>
            </template>

            <template #footer>
                <jet-secondary-button @click.native="showAddEventModal = false">
                    Cancel
                </jet-secondary-button>

                <jet-secondary-button class="ml-2" @click.native="addEvent"
                                      :class="{ 'opacity-25': processing }"
                                      :disabled="processing">
                    Save
                </jet-secondary-button>
            </template>
        </jet-dialog-modal>
        <teleport to="head">
            <title>{{ pageTitle }}</title>
            <meta property="og:description" :content="pageDescription">
        </teleport>
    </app-layout>

</template>

<script>

import Editor from "@tinymce/tinymce-vue";

const fetchDoctors = async (query) => {
    let where = ''
    const response = await fetch(
        route('users.search') + '?type=doctor&s=' + query,
        {}
    );

    const data = await response.json(); // Here you have the data that you need
    return data.map((item) => {
        return {value: item.id, label: item.name}
    })
}
const fetchPatients = async (query) => {
    let where = ''
    const response = await fetch(
        route('patients.search') + '?s=' + query,
        {}
    );

    const data = await response.json(); // Here you have the data that you need
    return data.map((item) => {
        return {value: item.id, label: item.name + '(#' + item.id + ')', patient: item.patient}
    })
}
import '@fullcalendar/core/vdom'
import JetInput from "@/Jetstream/Input.vue";
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
import FullCalendar from '@fullcalendar/vue3'
import dayGridPlugin from '@fullcalendar/daygrid'
import timeGridPlugin from '@fullcalendar/timegrid'
import listPlugin from '@fullcalendar/list'
import interactionPlugin from '@fullcalendar/interaction'
import JetDialogModal from '@/Jetstream/DialogModal.vue'
import JetInputError from "@/Jetstream/InputError.vue";
import JetCheckbox from "@/Jetstream/Checkbox.vue";
import TextareaInput from "@/Jetstream/TextareaInput.vue";

export default {
    components: {
        Editor,
        AppLayout,
        Icon,
        Pagination,
        FilterSearch,
        JetLabel,
        SelectInput,
        JetConfirmationModal,
        JetDangerButton,
        JetSecondaryButton,
        FullCalendar,
        JetDialogModal,
        JetInputError,
        JetCheckbox,
        TextareaInput,
        JetInput,
    },
    props: {
        events: Object,
        filters: Object,
        doctors: Object,
        errors: Object,
    },
    mounted() {
        this.processEvents();
    },
    data() {
        return {
            calendarOptions: {
                plugins: [dayGridPlugin, interactionPlugin, timeGridPlugin, listPlugin],
                headerToolbar: {
                    right: 'dayGridMonth,timeGridWeek,timeGridDay,list',
                    left: 'prev,next,today',
                    center: 'title',
                },
                eventColor: 'green',
                initialView: 'dayGridMonth',
                selectable: true,
                unselectAuto: true,
                selectMirror: true,
                select: this.handleSelect,
                eventClassNames: function (arg) {
                    if (arg.event.extendedProps.status === 'pending') {
                        return ['px-2 rounded-full bg-yellow-100 text-yellow-800']
                    }
                    if (arg.event.extendedProps.status === 'active') {
                        return ['px-2 rounded-full bg-blue-100 text-blue-800']
                    }
                    if (arg.event.extendedProps.status === 'approved') {
                        return ['px-2 rounded-full bg-blue-100 text-blue-800']
                    }
                    if (arg.event.extendedProps.status === 'done') {
                        return ['px-2 rounded-full bg-green-100 text-green-800']
                    }
                    if (arg.event.extendedProps.status === 'completed') {
                        return ['px-2 rounded-full bg-green-100 text-green-800']
                    }
                    if (arg.event.extendedProps.status === 'cancelled') {
                        return ['px-2 rounded-full bg-red-100 text-red-800']
                    }
                    if (arg.event.extendedProps.status === 'declined') {
                        return ['px-2 rounded-full bg-red-100 text-red-800']
                    }
                },
                events: []
            },
            form: {
                search: this.filters.search,
                created_by_type: this.filters.created_by_type,
                patient_id: this.filters.patient_id,
                doctor_id: this.filters.doctor_id,
                event_type: this.filters.event_type,
                date_range: this.filters.date_range,
                branch_id: this.filters.branch_id,
                status: this.filters.status,
                calendar_view: true,
                processing: false
            },
            event: {
                title: null,
                event_category_id: null,
                location: null,
                latitude: null,
                longitude: null,
                start_date: moment().format("YYYY-MM-DD"),
                start_time: null,
                end_date: moment().format("YYYY-MM-DD"),
                end_time: null,
                max_attendance: null,
                description: ``,
                status: 'active',
                recurring: false,
                recur_frequency: null,
                recur_start_date: null,
                recur_end_date: null,
                recur_type: null,
                selected_days: [],
            },
            editorConfig: {
                menubar: false,
                plugins: 'lists link image emoticons code',
                toolbar: 'styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist | link image code emoticons',
                images_upload_url: this.route('files.upload'),
                images_upload_base_path: '/storage',
                relative_urls: false,
                remove_script_host: false,
                convert_urls: true,
                height: "480"
            },
            doctor_id: null,
            showAddEventModal: false,
            table_view: false,
            processing: false,
            confirmingDeletion: false,
            selectedRecord: null,
            pageTitle: "Events",
            pageDescription: "Manage Events",

        }
    },
    watch: {
        form: {
            handler: _.debounce(function () {
                let query = pickBy(this.form)
                this.$inertia.get(this.route('events.calendar', Object.keys(query).length ? query : {}))
            }, 1000),
            deep: true,
        },
    },

    methods: {
        handleSelect: function (arg) {
            let startTime = moment(arg.startStr);
            let endTime = moment(arg.endStr);
            this.event.start_date = startTime.format('YYYY-MM-DD');
            this.event.end_date = endTime.format('YYYY-MM-DD');
            if (!arg.allDay) {
                this.event.start_time = startTime.format('H:i');
                this.event.end_time = endTime.format('H:i');
            }
            if (startTime.isBefore(moment())) {
                alert('You cannot add Events to Past dates.')
                return false;
            } else {
                this.showAddEventModal = true;
            }
        },
        reset() {
            this.form = mapValues(this.form, () => null)
        },
        deleteAction(id) {
            this.confirmingDeletion = true
            this.selectedRecord = id
        },
        destroy() {

            this.$inertia.delete(this.route('events.destroy', this.selectedRecord))
            this.confirmingDeletion = false
        },
        addEvent() {
            this.processing = true
            this.$inertia.post(this.route('events.store'), this.event, {
                    onSuccess: () => {
                        this.showAddEventModal = false
                        this.processing = false
                        this.$inertia.reload();
                    },
                    onError: (errors) => {
                        this.processing = false
                    },
                preserveState:false
                }
            )
        },
        processEvents() {
            this.calendarOptions.events = [];
            this.events.forEach(item => {
                this.calendarOptions.events.push({
                    id: item.id,
                    title: item.title,
                    allDay: item.all_day,
                    start: item.event_start_date,
                    end: item.event_end_date,
                    status: item.status,
                    url: route('events.show', item.id),
                });
            });
        },
    },
}
</script>

<style scoped>

</style>
