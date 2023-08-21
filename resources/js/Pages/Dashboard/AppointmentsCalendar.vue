<template>
    <div class=" max-h-full md:flex mt-4 bg-white rounded mb-6 xl:mb-0 shadow-lg">
        <div class="p-2">
            <FullCalendar :options="calendarOptions"/>
        </div>
    </div>
</template>

<script>
import '@fullcalendar/core/vdom'
import FullCalendar from '@fullcalendar/vue3'
import dayGridPlugin from '@fullcalendar/daygrid'
import timeGridPlugin from '@fullcalendar/timegrid'
import listPlugin from '@fullcalendar/list'
import interactionPlugin from '@fullcalendar/interaction'
import JetDialogModal from '@/Jetstream/DialogModal.vue'
import Icon from "@/Jetstream/Icon.vue";
import JetLabel from "@/Jetstream/Label.vue";
import SelectInput from "@/Jetstream/SelectInput.vue";
import JetConfirmationModal from "@/Jetstream/ConfirmationModal.vue";
import JetDangerButton from "@/Jetstream/DangerButton.vue";
import JetSecondaryButton from "@/Jetstream/SecondaryButton.vue";
import JetInputError from "@/Jetstream/InputError.vue";
import JetCheckbox from "@/Jetstream/Checkbox.vue";
import TextareaInput from "@/Jetstream/TextareaInput.vue";
import JetInput from "@/Jetstream/Input.vue";
import {Chart, registerables} from "chart.js";

Chart.register(...registerables);
export default {
    components: {
        Icon,
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
        filters: Object,
        doctors: Object,
        errors: Object,
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
                initialView: 'dayGridMonth',
                selectable: true,
                unselectAuto: true,
                selectMirror: true,
                select: this.handleSelect,
                eventClassNames: function (arg) {
                    if (arg.event.extendedProps.status === 'pending') {
                        return ['px-2 rounded-full bg-yellow-100 text-yellow-800']
                    }
                    if (arg.event.extendedProps.status === 'in_progress') {
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
                search: null,
                doctor: null,
                status: null,
                start_date: null,
                end_date: null,
                end_time: null,
                processing: false
            },
            patientsMultiSelect: {
                value: null,
                remark: null,
                placeholder: 'Search for Patient',
                filterResults: false,
                minChars: 2,
                resolveOnLoad: false,
                delay: 4,
                searchable: true,
                options: async (query) => {
                    return await fetchPatients(query)
                }
            },
            appointments: [],
            appointmentsByStatus: [],
            monthlyChartData: {},
            totalPending: null,
            totalApproved: null,
            totalCancelled: null,
            totalDeclined: null,
            totalCompleted: null,
            totalAppointments: null,
            doctor_id: null,
            showAddAppointmentModal: false,
            tableView: false,
            processing: false,
            confirmingDeletion: false,
            selectedRecord: null,
            pageTitle: "Appointments",
            pageDescription: "Manage Appointments",

        }
    },
    mounted() {
        this.getAppointments();

    },
    methods: {
        getAppointments() {
            axios.get(this.route('dashboard.get_appointments')).then(response => {
                this.appointments = response.data;
                this.appointments.forEach(item => {
                    this.calendarOptions.events.push({
                        id: item.id,
                        title: item.patient.name || (item.patient_first_name + ' ' + item.last_name),
                        allDay: item.all_day,
                        start: item.appointment_start_date,
                        end: item.appointment_end_date,
                        url: route('appointments.show', item.id),
                        status: item.status,
                    });
                });
            })
        }
    }
}

</script>

<style scoped>

</style>
