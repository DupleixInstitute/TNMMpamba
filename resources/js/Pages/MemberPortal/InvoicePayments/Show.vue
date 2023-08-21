<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <inertia-link class="text-indigo-400 hover:text-indigo-600" :href="route('portal.payments.index')">
                    Payments
                </inertia-link>
                <span class="text-indigo-400 font-medium">/</span> Invoice Payment #{{ invoicePayment.id }}
            </h2>
        </template>

        <div class="py-12">
            <div class=" mx-auto">
                <div class="grid grid-cols-12 gap-6">
                    <div class="col-span-12 lg:col-span-4 xxl:col-span-3 flex lg:block">
                        <div class="intro-y bg-white mt-5 lg:mt-0">
                            <div class="relative flex items-center p-5">
                                <div class="ml-4 mr-auto">
                                    <div class="font-medium text-base">InvoicePayment #{{ invoicePayment.id }}</div>
                                    <div class="text-gray-600">{{ invoicePayment.receipt }}</div>
                                </div>
                                <jet-dropdown align="right" width="60">
                                    <template #trigger>
                                        <span class="inline-flex rounded-md">
                                            <button type="button"
                                                    class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:bg-gray-50 hover:text-gray-700 focus:outline-none focus:bg-gray-50 active:bg-gray-50 transition ease-in-out duration-150">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                     viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                     stroke-width="1.5"
                                                     stroke-linecap="round" stroke-linejoin="round"
                                                     class="feather feather-more-horizontal w-5 h-5 text-gray-600 dark:text-gray-300">
                                            <circle cx="12" cy="12" r="1"></circle>
                                            <circle cx="19" cy="12" r="1"></circle>
                                            <circle cx="5" cy="12" r="1"></circle>
                                        </svg>
                                            </button>
                                        </span>
                                    </template>

                                    <template #content>
                                        <div class="w-60 shadow-xl bg-white rounded">
                                            <a :href="route('portal.payments.pdf',invoicePayment.id)"
                                               class="block px-4 py-2 text-sm leading-5 text-gray-700 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 transition duration-150 ease-in-out"
                                               target="_blank">
                                                <font-awesome-icon icon="file-pdf"/>
                                                Download Pdf
                                            </a>
                                            <a @click.prevent="printPayment" href="#"
                                               class="block px-4 py-2 text-sm leading-5 text-gray-700 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 transition duration-150 ease-in-out">
                                                <font-awesome-icon icon="print"/>
                                                Print Receipt
                                            </a>
                                        </div>
                                    </template>
                                </jet-dropdown>
                            </div>
                            <div class="p-5 border-t border-gray-200 dark:border-dark-5">
                                <div class="flex justify-between">
                                    <span class="font-medium">Date</span>
                                    <span>{{ invoicePayment.date }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="font-medium">Invoice</span>
                                    <span v-if="invoicePayment.invoice">
                                             <inertia-link :href="route('billing.invoices.show', invoicePayment.invoice_id)"
                                                           tabindex="-1" class="text-indigo-600 hover:text-indigo-900">
                                                {{ invoicePayment.invoice.reference }}
                                            </inertia-link>
                                    </span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="font-medium">Member</span>
                                    <span v-if="invoicePayment.member">
                                                {{ invoicePayment.member.name }}
                                    </span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="font-medium">Paid By</span>
                                    <span v-if="invoicePayment.paid_by=='member' && invoicePayment.member">
                                         <inertia-link :href="route('members.show', invoicePayment.member_id)"
                                                       tabindex="-1" class="text-indigo-600 hover:text-indigo-900">
                                    {{ invoicePayment.member.name }}
                                </inertia-link>
                                    </span>
                                    <span v-if="invoicePayment.paid_by=='co_payer' && invoicePayment.co_payer">

                                    {{ invoicePayment.co_payer.name }}
                                    </span>
                                </div>

                            </div>
                            <div class="p-5 border-t border-gray-200 dark:border-dark-5">
                                <div class="flex justify-between">
                                    <span class="font-medium">Currency</span>
                                    <span v-if="invoicePayment.currency">{{ invoicePayment.currency.name }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="font-medium">Amount</span>
                                    <span>{{ invoicePayment.amount }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="font-medium">Payment Type</span>
                                    <span v-if="invoicePayment.payment_type">{{ invoicePayment.payment_type.name }}</span>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="col-span-12 lg:col-span-8 xxl:col-span-9">
                        <div class="bg-white p-5">
                            <div class="flex items-center">
                                <div class="font-medium text-lg">Notes</div>
                            </div>

                            <div class="mt-4">{{ invoicePayment.description }}</div>
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
import AppLayout from '@/Pages/MemberPortal/Layouts/AppLayout.vue'
import JetButton from "@/Jetstream/Button.vue";
import JetInput from "@/Jetstream/Input.vue";
import JetInputError from "@/Jetstream/InputError.vue";
import JetCheckbox from "@/Jetstream/Checkbox.vue";
import JetLabel from "@/Jetstream/Label.vue";
import Select from "@/Jetstream/Select.vue";
import FileInput from "@/Jetstream/FileInput.vue";
import TextareaInput from "@/Jetstream/TextareaInput.vue";
import JetDropdown from '@/Jetstream/Dropdown.vue'
import JetDropdownLink from '@/Jetstream/DropdownLink.vue'
import JetDialogModal from '@/Jetstream/DialogModal.vue'
import JetSecondaryButton from '@/Jetstream/SecondaryButton.vue'
import JetConfirmationModal from '@/Jetstream/ConfirmationModal.vue'
import JetDangerButton from '@/Jetstream/DangerButton.vue'
import print from 'print-js'

export default {
    props: {
        invoicePayment: Object,
        printInvoicePayment: Boolean,
    },
    components: {
        Select,
        AppLayout,
        JetButton,
        JetInput,
        JetCheckbox,
        JetLabel,
        JetInputError,
        FileInput,
        TextareaInput,
        JetDropdown,
        JetDropdownLink,
        JetDialogModal,
        JetSecondaryButton,
        JetConfirmationModal,
        JetDangerButton,

    },
    data() {
        return {
            confirmClaim: false,
            confirmPaymentDeletion: false,
            confirmingDeletion: false,
            selectedPaymentRecord: null,
            selectedRecord: null,
            processing: false,
            pageTitle: "Invoice Details",
            pageDescription: "Invoice Details",
        }

    },
    mounted() {
        if (this.printInvoicePayment) {
            this.printPayment(this.invoicePayment.id)
        }
    },
    methods: {

        printPayment() {
            axios.get(this.route('portal.payments.print', this.invoicePayment.id)).then(response => {
                print({printable: response.data, type: 'raw-html'})
            })

        },
    }
}
</script>
<style scoped>

</style>
