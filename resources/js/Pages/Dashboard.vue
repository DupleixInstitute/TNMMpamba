<template>
    <app-layout>
        <template #header>
            <div class="flex justify-between">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Dashboard
                </h2>
            </div>
        </template>
        <div class="mx-auto">
            <div class="grid sm:grid-cols-1 md:grid-cols-4 gap-2">
                <div class="">
                    <div
                        class="relative flex flex-col min-w-0 break-words bg-white rounded mb-6 xl:mb-0 shadow-lg">
                        <div class="flex-auto p-4">
                            <div class="flex flex-wrap">
                                <div class="relative w-full pr-4 max-w-full flex-grow flex-1"><h5
                                    class="text-gray-500 uppercase font-bold text-xs"> LOANS APPLIED </h5>
                                    <span
                                        class="font-semibold text-xl text-gray-800"> {{ applications }} </span>
                                </div>
                                <div class="relative w-auto pl-4 flex-initial">
                                    <div
                                        class="text-white p-3 text-center inline-flex items-center justify-center w-12 h-12 shadow-lg rounded-full bg-green-500">
                                        <font-awesome-icon icon="money-bill"/>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="">
                    <div
                        class="relative flex flex-col min-w-0 break-words bg-white rounded mb-6 xl:mb-0 shadow-lg">
                        <div class="flex-auto p-4">
                            <div class="flex flex-wrap">
                                <div class="relative w-full pr-4 max-w-full flex-grow flex-1"><h5
                                    class="text-gray-500 uppercase font-bold text-xs"> LOANS APPROVED </h5>
                                    <span
                                        class="font-semibold text-xl text-gray-800"> {{ applicationsApproved }} </span>
                                </div>
                                <div class="relative w-auto pl-4 flex-initial">
                                    <div
                                        class="text-white p-3 text-center inline-flex items-center justify-center w-12 h-12 shadow-lg rounded-full bg-green-500">
                                        <font-awesome-icon icon="money-bill"/>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="">
                    <div
                        class="relative flex flex-col min-w-0 break-words bg-white rounded mb-6 xl:mb-0 shadow-lg">
                        <div class="flex-auto p-4">
                            <div class="flex flex-wrap">
                                <div class="relative w-full pr-4 max-w-full flex-grow flex-1"><h5
                                    class="text-gray-500 uppercase font-bold text-xs">Loans Rejected</h5>
                                    <span
                                        class="font-semibold text-xl text-gray-800"> {{ applicationsRejected }} </span>
                                </div>
                                <div class="relative w-auto pl-4 flex-initial">
                                    <div
                                        class="text-white p-3 text-center inline-flex items-center justify-center w-12 h-12 shadow-lg rounded-full bg-red-500">
                                        <font-awesome-icon icon="minus"/>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="">
                    <div
                        class="relative flex flex-col min-w-0 break-words bg-white rounded mb-6 xl:mb-0 shadow-lg">
                        <div class="flex-auto p-4">
                            <div class="flex flex-wrap">
                                <div class="relative w-full pr-4 max-w-full flex-grow flex-1"><h5
                                    class="text-gray-500 uppercase font-bold text-xs"> Approved Loans Amount </h5>
                                    <span
                                        class="font-semibold text-xl text-gray-800"> {{ applicationsApprovedAmount }} </span>
                                </div>
                                <div class="relative w-auto pl-4 flex-initial">
                                    <div
                                        class="text-white p-3 text-center inline-flex items-center justify-center w-12 h-12 shadow-lg rounded-full bg-green-500">
                                        <font-awesome-icon icon="dollar-sign"/>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
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
import AppLayout from '@/Layouts/AppLayout.vue'
import JetDialogModal from '@/Jetstream/DialogModal.vue'
import JetDangerButton from '@/Jetstream/DangerButton.vue'
import JetSuccessButton from '@/Jetstream/SuccessButton.vue'
import JetSecondaryButton from '@/Jetstream/SecondaryButton.vue'
import JetInfoButton from '@/Jetstream/InfoButton.vue'
import JetLabel from '@/Jetstream/Label.vue'
import SelectInput from '@/Jetstream/SelectInput.vue'


export default {
    components: {
        AppLayout,
        JetDialogModal,
        JetDangerButton,
        JetSuccessButton,
        JetSecondaryButton,
        JetLabel,
        SelectInput,
        JetInfoButton,
    },
    props: {

        clients: Number,
        applications: Number,
        applicationsApproved: Number,
        applicationsRejected: Number,
        applicationsApprovedAmount: Number,
        applicationsPending: Number,


    },
    data() {
        return {

            pageTitle: "Dashboard",
            pageDescription: "Dashboard",
            showAddWidgetModal: false,
            itemsLayout: this.layout,
        }
    },
    methods: {

    },
    computed: {},
    mounted() {

    }
}
</script>
<style scoped>

</style>
