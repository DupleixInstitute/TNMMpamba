<template>
    <div class="md:flex mt-4 bg-white rounded mb-6 xl:mb-0 shadow-lg">
        <div class="w-full md:w-1/3">
            <div class="box p-5">
                <div class="flex items-center">
                    My Appointments
                </div>
                <div class="text-2xl font-medium mt-2">{{ totalAppointments }}</div>
                <div class="border-b border-gray-200 flex pb-2 mt-4">
                    <div class="text-gray-600 dark:text-gray-600 text-xs">Total Appointments</div>

                </div>
                <div class="mt-2 border-b broder-gray-200">
                    <div class="-mb-1.5 -ml-2.5 mb-2">
                        <canvas id="monthlyAppointmentsChart" height="200"></canvas>
                    </div>
                </div>
                <div class="text-gray-600 dark:text-gray-600 text-xs border-b border-gray-200 flex mb-2 pb-2 mt-4">
                    <div>By Status</div>

                </div>
                <div class="flex">
                    <div>Pending</div>
                    <div class="ml-auto">{{ totalPending }}</div>
                </div>
                <div class="flex mt-1.5">
                    <div>Approved</div>
                    <div class="ml-auto">{{ totalApproved }}</div>
                </div>
                <div class="flex mt-1.5">
                    <div>Cancelled</div>
                    <div class="ml-auto">{{ totalCancelled }}</div>
                </div>
                <div class="flex mt-1.5">
                    <div>Declined</div>
                    <div class="ml-auto">{{ totalDeclined }}</div>
                </div>
                <div class="flex mt-1.5">
                    <div>Completed</div>
                    <div class="ml-auto">{{ totalCompleted }}</div>
                </div>
                <div class="mt-4">
                    <inertia-link class="btn btn-outline-secondary border-dashed w-full py-1 px-2 mt-4">View
                        Appointments
                    </inertia-link>
                </div>
            </div>
        </div>
        <div class="w-full md:w-2/3 p-2">
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
            axios.get(this.route('appointments.get_appointments')).then(response => {
                this.appointments = response.data.appointments;
                this.appointmentsByStatus = response.data.appointmentsByStatus;
                this.totalAppointments = response.data.totalAppointments;
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
                this.appointmentsByStatus.forEach(item => {
                    if (item.status === 'pending') {
                        this.totalPending = item.count;
                    }
                    if (item.status === 'cancelled') {
                        this.totalCancelled = item.count;
                    }
                    if (item.status === 'approved') {
                        this.totalApproved = item.count;
                    }
                    if (item.status === 'declined') {
                        this.totalDeclined = item.count;
                    }
                    if (item.status === 'completed') {
                        this.totalCompleted = item.count;
                    }
                });
                let labels=[];
                let data=[];
                response.data.monthlyChartData.forEach(item=>{
                    labels.push(item.label);
                    data.push(item.value);
                })
                const ctx = document.getElementById('monthlyAppointmentsChart').getContext('2d');
                const monthlyAppointmentsChart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Monthly Appointments',
                            data: data,
                            backgroundColor: 'green',
                            borderColor: 'green',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });
            })
        }
    }
}

</script>

<style scoped>

</style>
