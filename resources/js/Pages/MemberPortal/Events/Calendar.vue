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
                              :href="route('portal.events.index')">
                    Table View
                </inertia-link>
            </div>
        </div>
        <div class=" mx-auto">
            <div class="bg-white rounded shadow overflow-x-auto">
                <div class="p-5">
                    <FullCalendar :options="calendarOptions"/>
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

import Editor from "@tinymce/tinymce-vue";


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
                this.$inertia.get(this.route('portal.events.calendar', Object.keys(query).length ? query : {}))
            }, 1000),
            deep: true,
        },
    },

    methods: {
        handleSelect: function (arg) {

        },
        reset() {
            this.form = mapValues(this.form, () => null)
        },
        deleteAction(id) {
            this.confirmingDeletion = true
            this.selectedRecord = id
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
                    url: route('portal.events.show', item.id),
                });
            });
        },
    },
}
</script>

<style scoped>

</style>
