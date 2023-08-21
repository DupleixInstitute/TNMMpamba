<template>
    <div class=" max-h-full w-full bg-white rounded mb-6 xl:mb-0 shadow-lg">

        <div class="flex justify-between items-center border-b p-2">
            <h3>Payments By Period</h3>
            <jet-dropdown width="70"
                          class="">
                <template #trigger>
                    <button type="button"
                            class="inline-flex items-center px-3 py-4 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:bg-gray-50 hover:text-gray-700 focus:outline-none focus:bg-gray-50 active:bg-gray-50 transition ease-in-out duration-150">
                        <span class="text-gray-700  md:inline">Filter</span>
                        <svg class="w-2 h-2 fill-gray-700 md:ml-2"
                             xmlns="http://www.w3.org/2000/svg"
                             viewBox="0 0 961.243 599.998">
                            <path
                                d="M239.998 239.999L0 0h961.243L721.246 240c-131.999 132-240.28 240-240.624 239.999-.345-.001-108.625-108.001-240.624-240z"/>
                        </svg>
                    </button>
                </template>
                <template #content>
                    <div class="w-80 mt-2 px-4 py-6 shadow-xl bg-white rounded">
                        <div class="mb-2">
                            <jet-label for="filter_period" value="Period"/>
                            <select-input placeholder="Select Period" id="filter_period"
                                          v-model="form.period">
                                <option value="week">Week</option>
                                <option value="month">Month</option>
                                <option value="year">Year</option>
                            </select-input>
                        </div>
                        <div class="mb-2">
                            <jet-label for="filter_currency_id" value="Currency"/>
                            <Multiselect
                                id="filter_currency_id"
                                v-model="form.currency_id"
                                mode="single"
                                :searchable="true"
                                :options="$page.props.currencies"
                            />
                        </div>
                        <div class="mb-2">
                            <jet-label for="filter_payment_type_id" value="Payment Type"/>
                            <Multiselect
                                id="filter_payment_type_id"
                                v-model="form.payment_type_id"
                                mode="single"
                                :searchable="true"
                                :options="$page.props.paymentTypes"
                            />
                        </div>
                        <div class="mb-2">
                            <jet-label for="filter_co_payer_id" value="CoPayer"/>
                            <Multiselect
                                id="filter_co_payer_id"
                                v-model="form.co_payer_id"
                                mode="single"
                                :searchable="true"
                                :options="$page.props.coPayers"
                            />
                        </div>
                        <div class="mb-2">
                            <jet-label for="filter_paid_by" value="Paid By"/>
                            <select-input placeholder="Select Paid By" id="filter_paid_by"
                                          v-model="form.paid_by">
                                <option :value="null">All</option>
                                <option value="patient">Patient</option>
                                <option value="co_payer">CoPayer</option>
                            </select-input>
                        </div>
                        <div class="mb-2">
                            <button
                                class="ml-3 text-sm text-gray-500 hover:text-gray-700 focus:text-indigo-500"
                                type="button"
                                @click="reset">Reset
                            </button>
                        </div>
                    </div>
                </template>
            </jet-dropdown>

        </div>
        <div class="mb-2  p-2">
            <h4>Total: {{ $filters.currency(totalPayments) }}</h4>
            <bar :chart-data="paymentsByPeriodData" ref="paymentsByPeriodChart"
                 :chart-options="{responsive:true,maintainAspectRatio: false}" :height="0" />
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
import {Bar} from 'vue-chartjs'
import pickBy from "lodash/pickBy";
import JetDropdown from "@/Jetstream/Dropdown.vue";
import mapValues from "lodash/mapValues";

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
        JetDropdown,
        Bar,
    },
    props: {
        filters: Object,
        doctors: Object,
        errors: Object,
    },
    data() {
        return {
            form: {
                start_date: '',
                end_date: '',
                doctor_id: '',
                currency_id: '',
                period: null,
            },
            paymentsByPeriodData: [],
            totalPayments: 0,
            processing: false,

        }
    },
    mounted() {
        this.getPaymentsByPeriod();

    },
    methods: {
        getPaymentsByPeriod() {
            let query = pickBy(this.form)
            this.paymentsByPeriodTotal = 0;
            axios.get(this.route('dashboard.get_payments_by_period_graph', Object.keys(query).length ? query : {})).then(response => {
                let labels = [];
                let data = [];
                response.data.forEach(item => {
                    labels.push(item.label);
                    data.push(item.value);
                    this.totalPayments += parseFloat(item.value);
                })
                this.paymentsByPeriodData = {
                    labels: labels,
                    datasets: [{
                        label: 'Payments',
                        data: data,
                        backgroundColor: 'green',
                        borderColor: 'green',
                        borderWidth: 1
                    }]
                }
            })
        },
        reset() {
            this.form = mapValues(this.form, () => null)
        },
    },
    watch: {
        form: {
            handler: _.debounce(function () {
                this.getPaymentsByPeriod();
            }, 1500),
            deep: true,
        },
    }
}

</script>

<style scoped>

</style>
