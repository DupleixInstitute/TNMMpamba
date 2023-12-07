<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <inertia-link class="text-indigo-400 hover:text-indigo-600" :href="route('loan_applications.index')">
                    Loan Applications
                </inertia-link>
                <span class="text-indigo-400 font-medium">/</span> #{{ application.id }}
            </h2>
        </template>
        <div class=" mx-auto">
            <div class="md:flex md:items-start">
                <div class="relative mb-4  w-full md:w-3/12">
                    <div class="intro-y bg-white mt-5 lg:mt-0">
                        <div class="relative flex items-center p-5">
                            <div class="ml-4 mr-auto">
                                <div class="font-medium text-base">#{{ application.id }}</div>
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
                                        <jet-dropdown-link v-if="can('loans.applications.update')"
                                                           :href="route('loan_applications.edit',application.id)">
                                            <font-awesome-icon icon="edit"/>
                                            Edit
                                        </jet-dropdown-link>

                                        <a href="#"
                                           class="block px-4 py-2 text-sm leading-5 text-gray-700 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 transition duration-150 ease-in-out"
                                           @click="confirmingDeletion=true"
                                           v-if="can('loans.applications.destroy')">
                                            <font-awesome-icon icon="trash"
                                                               class="text-red-600 hover:text-red-900"/>
                                            Delete
                                        </a>
                                    </div>
                                </template>
                            </jet-dropdown>
                        </div>
                        <div class="p-5 border-t border-gray-200 dark:border-dark-5">
                            <div class="flex justify-between">
                                <span class="font-medium">Client</span>
                                <span>
                                    <inertia-link class="px-6 py-4 flex items-center focus:text-indigo-500"
                                                  :href="route('clients.show', application.client_id)"
                                                  v-if="application.client">
                                {{ application.client.name }}
                            </inertia-link>
                                </span>
                            </div>
                            <div class="flex justify-between">
                                <span class="font-medium">Product</span>
                                <span>
                                    <inertia-link class="px-6 py-4 flex items-center focus:text-indigo-500"
                                                  :href="route('loan_products.show', application.loan_product_id)"
                                                  v-if="application.product">
                                {{ application.product.name }}
                            </inertia-link>
                                </span>
                            </div>
                            <div class="flex justify-between">
                                <span class="font-medium">Amount</span>
                                <span>{{ $filters.formatNumber(application.amount) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="font-medium">Score</span>
                                <span> {{
                                        $filters.formatNumber(application.score)
                                    }} ({{ $filters.formatNumber(application.score_percentage) }}%)</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="font-medium">Status</span>
                                <span class="">
                                     <span v-if="application.status=='pending'"
                                           class="px-2 rounded-full bg-yellow-100 text-yellow-800">
                                        pending
                                    </span>
                                    <span v-if="application.status=='in_progress'"
                                          class="px-2 rounded-full bg-blue-100 text-blue-800">
                                        in progress
                                    </span>
                                     <span v-if="application.status=='approved'"
                                           class="px-2 rounded-full bg-green-100 text-green-800">
                                        approved
                                    </span>
                                    <span v-if="application.status=='done'"
                                          class="px-2 rounded-full bg-green-100 text-green-800">
                                        done
                                    </span>
                                    <span v-if="application.status=='cancelled'"
                                          class="px-2 rounded-full bg-red-100 text-red-800">
                                        cancelled
                                    </span>
                                     <span v-if="application.status=='rejected'"
                                           class="px-2 rounded-full bg-red-100 text-red-800">
                                        rejected
                                    </span>
                                </span>
                            </div>
                        </div>
                        <div class="p-5 border-t border-gray-200 dark:border-dark-5 flex">
                            <button v-if="can('loans.applications.approve')"
                                    @click="showChangeStatusModal=true"
                                    type="button" class="btn btn-primary py-1 px-2">
                                Change Status
                            </button>
                        </div>
                    </div>
                    <div class="bg-white p-5 mt-5">
                        <div class="flex items-center">
                            <div class="font-medium text-lg">Description</div>
                        </div>

                        <div class="mt-4">{{ application.description }}</div>
                    </div>
                </div>
                <div class="w-full md:w-9/12 p-4 md:ml-4 bg-white">
                    <div class="flex justify-between mb-4">
                        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Scores</h2>
                    </div>
                    <div class="mb-4 relative" v-for="(group,parent_index) in application.product.scoring_attributes">
                        <div class="bg-gray-50 border p-4">
                            <h4 class="font-semibold text-xl text-gray-800 leading-tight">{{ group.name }}</h4>
                            <table class="w-full whitespace-no-wrap table-auto mt-4">
                                <thead>
                                <tr class="text-left font-bold">
                                    <th class="px-6 pt-4 pb-4 font-medium text-gray-500">Name</th>
                                    <th class="px-6 pt-4 pb-4 font-medium text-gray-500">Value</th>
                                    <th class="px-6 pt-4 pb-4 font-medium text-gray-500">Score</th>
                                    <th class="px-6 pt-4 pb-4 font-medium text-gray-500">Accepted</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr v-for="attribute in group.attributes"
                                    class="hover:bg-gray-100 focus-within:bg-gray-100">
                                    <td class="border-t px-6 py-4">
                                        {{ attribute.name }}
                                    </td>
                                    <td class="border-t px-6 py-4">
                                        <div v-if="attribute.attribute.field_type==='checkbox'">
                                            <span class="mr-2 bg-gray-100 p-1" v-for="value in attribute.value">{{value}}</span>
                                        </div>
                                        <div v-else>{{ attribute.value }}</div>

                                    </td>
                                    <td class="border-t px-6 py-4">
                                        {{ attribute.actual_score }}
                                    </td>
                                    <td class="border-t px-6 py-4">
                                        <span class="ml-2 text-green-600" v-if="attribute.accepted"><font-awesome-icon
                                            icon="check-circle"/></span>
                                        <span class="ml-2 text-red-600" v-else><font-awesome-icon icon="times-circle"/></span>
                                    </td>
                                </tr>
                                </tbody>
                                <tfoot>
                                <tr class="text-left font-bold">
                                    <th class="px-6 pt-4 pb-4 font-medium text-gray-500" colspan="2">Total</th>
                                    <th class="px-6 pt-4 pb-4 font-bold text-gray-800" colspan="2">
                                        {{ group.total_score }}
                                    </th>
                                </tr>
                                </tfoot>
                            </table>
                        </div>

                    </div>
                </div>
            </div>

        </div>
        <jet-confirmation-modal :show="confirmingDeletion" @close="confirmingDeletion = false">
            <template #title>
                Delete Record
            </template>

            <template #content>
                Are you sure you want to delete record?
            </template>

            <template #footer>
                <jet-secondary-button @click.native="confirmingDeletion = false">
                    Nevermind
                </jet-secondary-button>

                <jet-danger-button class="ml-2" @click.native="destroy" :class="{ 'opacity-25': processing }"
                                   :disabled="processing">
                    Delete Record
                </jet-danger-button>
            </template>
        </jet-confirmation-modal>
        <jet-dialog-modal :show="showChangeStatusModal" @close="showChangeStatusModal = false">
            <template #title>
                Change Status
            </template>
            <template #content>
                <div class="grid grid-cols-1 gap-2 mt-4">
                    <div>
                        <jet-label for="status" value="Status"/>
                        <select
                            class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm w-full"
                            name="status" v-model="form.status" id="status"
                            required>
                            <option value="pending">Pending</option>
                            <option value="approved">Approved</option>
                            <option value="rejected">Rejected</option>
                        </select>
                    </div>
                </div>
            </template>

            <template #footer>
                <jet-secondary-button @click.native="showChangeStatusModal = false">
                    Cancel
                </jet-secondary-button>

                <jet-success-button class="ml-2" @click.native="changeStatus"
                                    :class="{ 'opacity-25': form.processing }"
                                    :disabled="form.processing">
                    Save
                </jet-success-button>
            </template>
        </jet-dialog-modal>
        <teleport to="head">
            <title>{{ pageTitle }}</title>
            <meta property="og:description" :content="pageDescription">
        </teleport>
    </app-layout>
</template>
<script>
import AppLayout from '@/Layouts/AppLayout.vue'
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
import JetSuccessButton from '@/Jetstream/SuccessButton.vue'
import JetConfirmationModal from '@/Jetstream/ConfirmationModal.vue'
import JetDangerButton from '@/Jetstream/DangerButton.vue'
import Button from "../../Jetstream/Button.vue";
import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";

export default {
    props: {
        application: Object,
        groups: Object,
    },
    components: {
        FontAwesomeIcon,
        Button,
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
        JetSuccessButton,
        JetConfirmationModal,
        JetDangerButton,

    },
    data() {
        return {
            showChangeStatusModal: false,
            showAddAttributeModal: false,
            confirmItemDeletion: false,
            editingItem: false,
            showItemModal: false,
            group_id: '',
            attribute_id: '',
            form: this.$inertia.form({
                status: this.application.status,
            }),
            readyToSave: false,
            confirmingDeletion: false,
            selectedGroup: null,
            selectedAttribute: null,
            selectedRecord: null,
            processing: false,
            groupPercentagesTotal: 0,
            attributePercentagesTotal: 0,
            errors: [],
            pageTitle: "Loan Application",
            pageDescription: "Loan Application",
        }

    },
    mounted() {

    },
    methods: {
        changeStatus() {
            this.form.post(this.route('loan_applications.change_status', this.application.id), {
                preserveScroll: true,
                onSuccess: () => {
                    this.showChangeStatusModal = false
                    this.$inertia.reload()
                },
            })
        },
        deleteAction(id) {
            this.confirmingDeletion = true
            this.selectedRecord = id
        },
        destroy() {
            this.$inertia.delete(this.route('loan_applications.destroy', this.application.id), {
                preserveState: false,
                onSuccess: () => {
                    this.$inertia.visit(this.route('loan_applications.index'))
                }
            })
            this.confirmingDeletion = false
            window.location = route('loan_applications.index')
        },

    },
    computed: {},
    watch: {}
}
</script>
<style scoped>

</style>
