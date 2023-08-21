<template>
    <div class="w-full max-h-full">
        <div
            class="relative flex flex-col min-w-0 break-words bg-white rounded mb-6 xl:mb-0 shadow-lg">
            <div class="flex-auto p-4">
                <div class="flex flex-wrap">
                    <div class="relative w-full pr-4 max-w-full flex-grow flex-1"><h5
                        class="text-gray-500 uppercase font-bold text-xs"> CONSULTATIONS </h5>
                        <span
                            class="font-semibold text-xl text-gray-800"> {{ consultations }} </span>
                    </div>
                    <div class="relative w-auto pl-4 flex-initial">
                        <div
                            class="text-white p-3 text-center inline-flex items-center justify-center w-12 h-12 shadow-lg rounded-full bg-blue-500">
                            <font-awesome-icon icon="stethoscope"/>
                        </div>
                    </div>
                </div>
                <p class="text-sm text-gray-500 mt-4"><span :class="consultationsChangeClass"
                                                            class="mr-2"><i
                    class="fas fa-arrow-up"></i> {{ consultationsChange }}% </span><span
                    class="whitespace-no-wrap">Since last month</span></p>
            </div>
        </div>
    </div>
</template>

<script>
import {Chart} from "chart.js";

export default {
    name: "TotalConsultationsCount",
    data() {
        return {
            consultations: String,
            consultationsChange: String,
            consultationsChangeClass: String,
        }
    },
    mounted() {
        this.getData();
    },
    methods: {
        getData() {
            axios.get(this.route('dashboard.get_total_consultations_count')).then(response => {
                this.consultations = response.data.consultations;
                this.consultationsChange = response.data.consultationsChange;
                this.consultationsChangeClass = response.data.consultationsChangeClass;
            })
        }
    }
}
</script>

<style scoped>

</style>
