<template>
    <div class="md:flex mt-4 bg-white rounded mb-6 xl:mb-0 shadow-lg">
        <div class="box p-5">
            <div class="flex items-center">
                Appointments
            </div>
            <div class="mt-2 border-t broder-gray-200">
                <div class="-mb-1.5 -ml-2.5 mb-2">
                    <canvas id="monthlyAppointmentsChart" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>
</template>

<script>

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
            processing: false,

        }
    },
    mounted() {
        this.getAppointments();

    },
    methods: {
        getAppointments() {
            axios.get(this.route('dashboard.get_monthly_appointments_graph')).then(response => {
                this.appointments = response.data;
                let labels = [];
                let data = [];
                response.data.monthlyChartData.forEach(item => {
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
