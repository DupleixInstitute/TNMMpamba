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
            <h3 class="text-2xl font-semibold mb-4 flex items-center justify-center">
                <font-awesome-icon icon="list" class="mr-2 text-gray-600"/>
                Loan Counts
            </h3>
            <div class="grid sm:grid-cols-1 md:grid-cols-4 gap-2">
                  <inertia-link :href="route('dashboard.filter','all')" class="relative flex flex-col min-w-0 break-words bg-white rounded mb-6 xl:mb-0 shadow-lg">
                    <div class="flex-auto p-4">
                      <div class="flex flex-wrap">
                        <div class="relative w-full pr-4 max-w-full flex-grow flex-1">
                          <h5 class="text-gray-500 uppercase font-bold text-xs"> LOANS APPLIED  </h5>
                          <span class="font-semibold text-xl text-gray-800"> {{ applicationsCount }} </span>
                        </div>
                        <div class="relative w-auto pl-4 flex-initial">
                          <div class="text-white p-3 text-center inline-flex items-center justify-center w-12 h-12 shadow-lg rounded-full bg-yellow-500">
                            <font-awesome-icon icon="money-bill"/>
                          </div>
                        </div>
                      </div>
                    </div>
                  </inertia-link>
                <div class="">
                    <inertia-link  :href="route('dashboard.filter','pending')" class="relative flex flex-col min-w-0 break-words bg-white rounded mb-6 xl:mb-0 shadow-lg">

                        <div class="flex-auto p-4">
                            <div class="flex flex-wrap">
                                <div class="relative w-full pr-4 max-w-full flex-grow flex-1"><h5
                                    class="text-gray-500 uppercase font-bold text-xs"> Loans Pending </h5>
                                    <span
                                        class="font-semibold text-xl text-gray-800"> {{ applicationsPending }} </span>
                                </div>
                                <div class="relative w-auto pl-4 flex-initial">
                                    <div
                                        class="text-white p-3 text-center inline-flex items-center justify-center w-12 h-12 shadow-lg rounded-full bg-blue-500">
                                        <font-awesome-icon icon="money-bill"/>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </inertia-link>
                </div>

                <div class="">
                    <inertia-link :href="route('dashboard.filter','rejected')" class="relative flex flex-col min-w-0 break-words bg-white rounded mb-6 xl:mb-0 shadow-lg">

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
                    </inertia-link>
                </div>
                <div class="">
                    <inertia-link :href="route('dashboard.filter','approved')" class="relative flex flex-col min-w-0 break-words bg-white rounded mb-6 xl:mb-0 shadow-lg">

                        <div class="flex-auto p-4">
                            <div class="flex flex-wrap">
                                <div class="relative w-full pr-4 max-w-full flex-grow flex-1"><h5
                                    class="text-gray-500 uppercase font-bold text-xs"> Approved Loans </h5>
                                    <span
                                        class="font-semibold text-xl text-gray-800"> {{ applicationsApproved }} </span>
                                </div>
                                <div class="relative w-auto pl-4 flex-initial">
                                    <div
                                        class="text-white p-3 text-center inline-flex items-center justify-center w-12 h-12 shadow-lg rounded-full bg-green-500">
                                        <font-awesome-icon icon="dollar-sign"/>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </inertia-link>
                </div>
            </div>
        </div>

        <!-- Loan Amounts Section -->
        <div class="mx-auto mt-4">
            <h3 class="text-2xl font-semibold mb-4 flex items-center justify-center">
                <font-awesome-icon icon="dollar-sign" class="mr-2 text-gray-600"/>
                Loan Amounts
            </h3>
            <div class="grid sm:grid-cols-1 md:grid-cols-4 gap-2">
                <!-- Loans Pending -->
                <div class="">
                    <div class="relative flex flex-col min-w-0 break-words bg-white rounded mb-6 xl:mb-0 shadow-lg">
                        <div class="flex-auto p-4">
                            <div class="flex flex-wrap">
                                <div class="relative w-full pr-4 max-w-full flex-grow flex-1">
                                    <h5 class="text-gray-500 uppercase font-bold text-xs">TOTAL LOANS APPLIED AMT</h5>
                                    <span class="font-semibold text-xl text-gray-800">{{ totalAmountAppliedAmt }}</span>
                                </div>
                                <div class="relative w-auto pl-4 flex-initial">
                                    <div class="text-white p-3 text-center inline-flex items-center justify-center w-12 h-12 shadow-lg rounded-full bg-yellow-500">
                                        <font-awesome-icon icon="hourglass-half"/>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Loans Disbursed -->
                <div class="">
                    <div class="relative flex flex-col min-w-0 break-words bg-white rounded mb-6 xl:mb-0 shadow-lg">
                        <div class="flex-auto p-4">
                            <div class="flex flex-wrap">
                                <div class="relative w-full pr-4 max-w-full flex-grow flex-1">
                                    <h5 class="text-gray-500 uppercase font-bold text-xs">LOANS PENDING AMT</h5>
                                    <span class="font-semibold text-xl text-gray-800">{{ totalPendingAmount }}</span>
                                </div>
                                <div class="relative w-auto pl-4 flex-initial">
                                    <div class="text-white p-3 text-center inline-flex items-center justify-center w-12 h-12 shadow-lg rounded-full bg-blue-500">
                                        <font-awesome-icon icon="hand-holding-usd"/>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Loans Repaid -->
                <div class="mt-2">
                    <div class="relative flex flex-col min-w-0 break-words bg-white rounded mb-6 xl:mb-0 shadow-lg">
                        <div class="flex-auto p-4">
                            <div class="flex flex-wrap">
                                <div class="relative w-full pr-4 max-w-full flex-grow flex-1">
                                    <h5 class="text-gray-500 uppercase font-bold text-xs">LOANS REJECTED AMT</h5>
                                    <span class="font-semibold text-xl text-gray-800">{{ totalRejectedAmt }}</span>
                                </div>
                                <div class="relative w-auto pl-4 flex-initial">
                                    <div class="text-white p-3 text-center inline-flex items-center justify-center w-12 h-12 shadow-lg rounded-full bg-red-500">
                                        <font-awesome-icon icon="exclamation-triangle"/>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                 <!-- Loans Defaulted -->
                 <div class="">
                    <div class="relative flex flex-col min-w-0 break-words bg-white rounded mb-6 xl:mb-0 shadow-lg">
                        <div class="flex-auto p-4">
                            <div class="flex flex-wrap">
                                <div class="relative w-full pr-4 max-w-full flex-grow flex-1">
                                    <h5 class="text-gray-500 uppercase font-bold text-xs">LOANS APPROVED AMT</h5>
                                    <span class="font-semibold text-xl text-gray-800">{{ applicationsApprovedAmount }}</span>
                                </div>
                                <div class="relative w-auto pl-4 flex-initial">
                                    <div class="text-white p-3 text-center inline-flex items-center justify-center w-12 h-12 shadow-lg rounded-full bg-green-500">
                                        <font-awesome-icon icon="check-circle"/>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Loans Applied Modal -->
        <!-- <jet-dialog-modal v-if="showModal === 'loansApplied'" @close="closeModal">
            <div class="p-6 justify-center">
              <h2 class="text-lg font-semibold mb-4">Loan Applications</h2>
              <p>This is the content of the Loans Applied modal. You can display detailed information about loan applications here.</p>
            </div>
        </jet-dialog-modal> -->


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
        applicationsCount: Number,
        applicationsApproved: Number,
        applicationsRejected: Number,
        applicationsApprovedAmount: Number,
        applicationsPending: Number,
        totalAmountAppliedAmt: Number,
        totalRecommendedAmt: Number,
        totalApprovedAmt: Number,
        totalRejectedAmt: Number,
        applicationsRecommended : Number,
        totalPendingAmount : Number


    },
    data() {
        return {

            pageTitle: "Dashboard",
            pageDescription: "Dashboard",
            showModal: null, // Initialize showModal property
            showAddWidgetModal: false,
            itemsLayout: this.layout,

        }
    },
    methods: {
        openModal(modalName) {
            console.log("Opening modal:", modalName);
            this.showModal = modalName;
            console.log("showModal:", this.showModal);
        },
        closeModal(modalName) {
            this.showModal = null;
        },

    },
    computed: {},
    mounted() {

    }
}
</script>
<style scoped>

</style>
