<template>
    <div class="w-full max-h-full">
        <div
            class="relative flex flex-col min-w-0 break-words bg-white rounded mb-6 xl:mb-0 shadow-lg">
            <div class="flex-auto p-4">
                <div class="flex flex-wrap">
                    <div class="relative w-full pr-4 max-w-full flex-grow flex-1"><h5
                        class="text-gray-500 uppercase font-bold text-xs"> Payments </h5>
                        <span
                            class="font-semibold text-xl text-gray-800"> {{ payments }} </span>
                    </div>
                    <div class="relative w-auto pl-4 flex-initial">
                        <div
                            class="text-white p-3 text-center inline-flex items-center justify-center w-12 h-12 shadow-lg rounded-full bg-green-500">
                            <font-awesome-icon icon="dollar-sign"/>
                        </div>
                    </div>
                </div>
                <p class="text-sm text-gray-500 mt-4"><span :class="paymentsChangeClass"
                                                            class="mr-2"><i
                    class="fas fa-arrow-up"></i> {{ paymentsChange }}% </span><span
                    class="whitespace-no-wrap">Since last month</span></p></div>
        </div>
    </div>
</template>

<script>
import {Chart} from "chart.js";

export default {
    name: "TotalPaymentsAmount",
    data() {
        return {
            payments: String,
            paymentsChange: String,
            paymentsChangeClass: String,
        }
    },
    mounted() {
        this.getData();
    },
    methods: {
        getData() {
            axios.get(this.route('dashboard.get_total_payments_amount')).then(response => {
                this.payments = response.data.payments;
                this.paymentsChange = response.data.paymentsChange;
                this.paymentsChangeClass = response.data.paymentsChangeClass;
            })
        }
    }
}
</script>

<style scoped>

</style>
