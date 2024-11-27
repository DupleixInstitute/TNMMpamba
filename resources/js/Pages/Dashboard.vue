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
                <font-awesome-icon icon="list" class="mr-2 text-gray-600" />
                Loan Counts
            </h3>
            <div class="grid sm:grid-cols-1 md:grid-cols-4 gap-2">
                <div class="tooltip-wrapper">
                    <inertia-link :href="route('dashboard.filter', 'all')"
                        class="relative flex flex-col min-w-0 break-words bg-white rounded mb-6 xl:mb-0 shadow-lg"
                        title="Click to apply filter" >
                        <div class="flex-auto p-4">
                            <div class="flex flex-wrap">
                                <div class="relative w-full pr-4 max-w-full flex-grow flex-1">
                                    <h5 class="text-gray-500 uppercase font-bold text-xs"> LOANS APPLIED </h5>
                                    <span class="font-semibold text-xl text-gray-800"> {{ applicationsCount }} </span>
                                </div>
                                <div class="relative w-auto pl-4 flex-initial">
                                    <div
                                        class="text-white p-3 text-center inline-flex items-center justify-center w-12 h-12 shadow-lg rounded-full bg-yellow-500">
                                        <font-awesome-icon icon="money-bill" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </inertia-link>
                </div>
                <div class="tooltip-wrapper">
                    <inertia-link :href="route('dashboard.filter', 'pending')"
                        class="relative flex flex-col min-w-0 break-words bg-white rounded mb-6 xl:mb-0 shadow-lg"
                        title="Click to apply filter">

                        <div class="flex-auto p-4">
                            <div class="flex flex-wrap">
                                <div class="relative w-full pr-4 max-w-full flex-grow flex-1">
                                    <h5 class="text-gray-500 uppercase font-bold text-xs"> Loans Pending </h5>
                                    <span class="font-semibold text-xl text-gray-800"> {{ applicationsPending }} </span>
                                </div>
                                <div class="relative w-auto pl-4 flex-initial">
                                    <div
                                        class="text-white p-3 text-center inline-flex items-center justify-center w-12 h-12 shadow-lg rounded-full bg-blue-500">
                                        <font-awesome-icon icon="money-bill" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </inertia-link>
                </div>

                <div class="tooltip-wrapper">
                    <inertia-link :href="route('dashboard.filter', 'rejected')"
                        class="relative flex flex-col min-w-0 break-words bg-white rounded mb-6 xl:mb-0 shadow-lg"
                        title="Click to apply filter">

                        <div class="flex-auto p-4">
                            <div class="flex flex-wrap">
                                <div class="relative w-full pr-4 max-w-full flex-grow flex-1">
                                    <h5 class="text-gray-500 uppercase font-bold text-xs">Loans Rejected</h5>
                                    <span class="font-semibold text-xl text-gray-800"> {{ applicationsRejected }}
                                    </span>
                                </div>
                                <div class="relative w-auto pl-4 flex-initial">
                                    <div
                                        class="text-white p-3 text-center inline-flex items-center justify-center w-12 h-12 shadow-lg rounded-full bg-red-500">
                                        <font-awesome-icon icon="minus" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </inertia-link>
                </div>
                <div class="tooltip-wrapper">
                    <inertia-link :href="route('dashboard.filter', 'approved')"
                        class="relative flex flex-col min-w-0 break-words bg-white rounded mb-6 xl:mb-0 shadow-lg"
                        title="Click to apply filter">

                        <div class="flex-auto p-4">
                            <div class="flex flex-wrap">
                                <div class="relative w-full pr-4 max-w-full flex-grow flex-1">
                                    <h5 class="text-gray-500 uppercase font-bold text-xs"> Approved Loans </h5>
                                    <span class="font-semibold text-xl text-gray-800"> {{ applicationsApproved }}
                                    </span>
                                </div>
                                <div class="relative w-auto pl-4 flex-initial">
                                    <div
                                        class="text-white p-3 text-center inline-flex items-center justify-center w-12 h-12 shadow-lg rounded-full bg-green-500">
                                        <font-awesome-icon icon="check-circle" />
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
                Loan Amounts - Eswatini Currency (SZL)
            </h3>

            <div class="grid sm:grid-cols-1 md:grid-cols-4 gap-2">
                <!-- Loans Pending -->
                <div class="">
                    <div class="relative flex flex-col min-w-0 break-words bg-white rounded mb-6 xl:mb-0 shadow-lg">
                        <div class="flex-auto p-4">
                            <div class="flex flex-wrap">
                                <div class="relative w-full pr-4 max-w-full flex-grow flex-1">
                                    <h5 class="text-gray-500 uppercase font-bold text-xs">TOTAL LOANS APPLIED AMT</h5>
                                    <span class="font-semibold text-xl text-gray-800">{{
                                        $filters.formatNumber(totalAmountAppliedAmt) }}</span>
                                </div>
                                <div class="relative w-auto pl-4 flex-initial">
                                    <div
                                        class="text-white p-3 text-center inline-flex items-center justify-center w-12 h-12 shadow-lg rounded-full bg-yellow-500">
                                        <font-awesome-icon icon="hourglass-half" />
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
                                    <span class="font-semibold text-xl text-gray-800">{{
                                        $filters.formatNumber(totalPendingAmount) }}</span>
                                </div>
                                <div class="relative w-auto pl-4 flex-initial">
                                    <div
                                        class="text-white p-3 text-center inline-flex items-center justify-center w-12 h-12 shadow-lg rounded-full bg-blue-500">
                                        <font-awesome-icon icon="hand-holding-usd" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Loans Repaid -->
                <div class="">
                    <div class="relative flex flex-col min-w-0 break-words bg-white rounded mb-6 xl:mb-0 shadow-lg">
                        <div class="flex-auto p-4">
                            <div class="flex flex-wrap">
                                <div class="relative w-full pr-4 max-w-full flex-grow flex-1">
                                    <h5 class="text-gray-500 uppercase font-bold text-xs">LOANS REJECTED AMT</h5>
                                    <span class="font-semibold text-xl text-gray-800">{{
                                        $filters.formatNumber(totalRejectedAmt)
                                        }}</span>
                                </div>
                                <div class="relative w-auto pl-4 flex-initial">
                                    <div
                                        class="text-white p-3 text-center inline-flex items-center justify-center w-12 h-12 shadow-lg rounded-full bg-red-500">
                                        <font-awesome-icon icon="exclamation-triangle" />

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
                                    <span class="font-semibold text-xl text-gray-800">{{
                                        $filters.formatNumber(applicationsApprovedAmount) }}</span>
                                </div>
                                <div class="relative w-auto pl-4 flex-initial">
                                    <div
                                        class="text-white p-3 text-center inline-flex items-center justify-center w-12 h-12 shadow-lg rounded-full bg-green-500">
                                        <font-awesome-icon icon="check-circle" />

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Loans Applied Modal -->
        <div class="mx-auto mt-4">
            <h3 class="text-2xl font-semibold mb-4 flex items-center justify-center">
                My Workspace
            </h3>
            <div class="grid sm:grid-cols-1 md:grid-cols-4 gap-2">
                <div class="tooltip-wrapper">
                    <!-- Spinner shown when loading -->
                    <div v-if="loading" class="fixed inset-0 flex items-center justify-center bg-gray-900 bg-opacity-50 z-50">
                        <div class="loader ease-linear rounded-full border-8 border-t-8 border-gray-200 h-64 w-64"></div>
                        <p class="text-white text-xl font-semibold mt-4">Please wait...</p>
                    </div>

                    <!-- Link -->
                    <inertia-link @click.prevent="navigateToDashboard"
                        class="relative flex flex-col min-w-0 break-words bg-white rounded mb-6 xl:mb-0 shadow-lg"
                        title="Go to My Workspace">
                        <div class="flex-auto p-4">
                            <div class="flex flex-wrap">
                                <div class="relative w-full pr-4 max-w-full flex-grow flex-1">
                                    <h5 class="text-gray-500 uppercase font-bold text-xs">WORKSPACE</h5>
                                    <span class="font-semibold text-xl text-gray-800">My Workspace</span>
                                </div>
                                <div class="relative w-auto pl-4 flex-initial">
                                    <div class="text-white p-3 text-center inline-flex items-center justify-center w-12 h-12 shadow-lg rounded-full bg-purple-500">
                                        <font-awesome-icon icon="desktop" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </inertia-link>
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
        applicationsCount: Number,
        applicationsApproved: Number,
        applicationsRejected: Number,
        applicationsApprovedAmount: String,
        applicationsPending: Number,
        totalAmountAppliedAmt: Number,
        totalRecommendedAmt: Number,
        totalApprovedAmt: Number,
        totalRejectedAmt: Number,
        applicationsRecommended: Number,
        totalPendingAmount: Number


    },
    data() {
        return {

            pageTitle: "Dashboard",
            pageDescription: "Dashboard",
            showModal: null, // Initialize showModal property
            showAddWidgetModal: false,
            itemsLayout: this.layout,
            loading: false, // Ensure 'loading' is defined here

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
        showTooltip(event) {
            // Get the tooltip element
            const tooltip = event.target.getAttribute('title');
            // Show tooltip
            event.target.setAttribute('data-tooltip', tooltip);
        },
        hideTooltip(event) {
            // Remove tooltip when mouse out
            event.target.removeAttribute('data-tooltip');
        },
        navigateToDashboard() {
            this.loading = true;
            //show a spinner
            

            this.$inertia.visit(this.route('dashboard.my-workspace'), {
            onFinish: () => {
                this.loading = false;
            },
            onCancelToken: () => {
                this.loading = false; // In case of cancellation
            }
            });
        }

    },
    computed: {},
    mounted() {

    }
}
</script>
<style>
/* Style for the tooltip */
.tooltip-wrapper {
    position: relative;
}

.tooltip-wrapper:after {
    content: attr(data-tooltip);
    position: absolute;
    bottom: 100%;
    /* Tooltip position */
    left: 50%;
    transform: translateX(-50%);
    padding: 0.5em 1em;
    /* Tooltip padding */
    font-size: 0.8em;
    /* Tooltip font size */

    /* Tooltip background color */
    color: white;
    /* Tooltip text color */
    border-radius: 0.25em;
    /* Tooltip border radius */
    pointer-events: none;
    white-space: nowrap;
    /* Prevent line breaks */
    opacity: 0;
    /* Initially hidden */
    transition: opacity 0.3s ease;
    /* Smooth transition */
}

.tooltip-wrapper:hover:after {
    opacity: 1;
    /* Show tooltip on hover */
}

/* Add bounce animation to the card */
.tooltip-wrapper:hover .shadow-lg {
    animation: bounce 0.5s;
}

@keyframes bounce {
    0%, 100% {
        transform: translateY(0);
    }
    50% {
        transform: translateY(-5px);
    }
}
/* Spinner CSS */
.loader {
    border-top-color: #3490dc;
    -webkit-animation: spinner 1.5s linear infinite;
    animation: spinner 1.5s linear infinite;
  }

  @-webkit-keyframes spinner {
    0% {
      -webkit-transform: rotate(0deg);
    }
    100% {
      -webkit-transform: rotate(360deg);
    }
  }

  @keyframes spinner {
    0% {
      transform: rotate(0deg);
    }
    100% {
      transform: rotate(360deg);
    }
  }

</style>
