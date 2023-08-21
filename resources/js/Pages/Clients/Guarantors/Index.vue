<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <inertia-link class="text-indigo-400 hover:text-indigo-600" :href="route('loans.index')">Loans
                </inertia-link>
                <span class="text-indigo-400 font-medium">/</span> {{ loan.name }}
            </h2>
        </template>
        <div class="mx-auto">
            <div class="md:flex md:items-start">
                <div class="bg-white relative shadow-xl mb-4 mt-20 w-full md:w-3/12">
                    <div class="col-span-12 lg:col-span-4 xxl:col-span-3 flex lg:block flex-col-reverse">
                        <div class="intro-y box mt-5 lg:mt-0">
                            <loan-menu :loan="loan" :loan="loan"></loan-menu>
                        </div>
                    </div>

                </div>
                <div class="w-full md:w-9/12 p-4 md:ml-4 bg-white sm:mt-4">
                    <div class="flex justify-between">
                        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Investigation\Procedures</h2>
                        <inertia-link class="btn btn-blue" :href="route('loans.loans.index',loan.id)">
                            <span>Back </span>
                        </inertia-link>
                    </div>
                    <div class="mt-4 relative overflow-x-auto">
                        <form @submit.prevent="submit" v-if="can('loans.procedures.create')">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                                <div>
                                    <jet-label for="procedure" value="Procedure"/>
                                    <Multiselect
                                        v-model="form.tariff_id"
                                        v-bind="procedureMultiSelect"
                                        :required="true"
                                    />
                                </div>
                                <div>
                                    <jet-label for="description" value="Remark"/>
                                    <jet-input id="description" type="text" class=" block w-full"
                                               v-model="form.description"/>
                                </div>
                            </div>

                            <div class="flex items-center justify-end mt-4 mb-4">
                                <jet-button class="ml-4" :class="{ 'opacity-25': form.processing }"
                                            :disabled="form.processing">
                                    Add Procedure
                                </jet-button>
                            </div>
                        </form>
                        <table class="w-full whitespace-no-wrap table-auto">
                            <thead class="bg-gray-50">
                            <tr class="text-left font-bold">
                                <th class="px-6 pt-4 pb-4 font-medium text-gray-500">Procedure</th>
                                <th class="px-6 pt-4 pb-4 font-medium text-gray-500">Status</th>
                                <th class="px-6 pt-4 pb-4 font-medium text-gray-500">Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-if="!loan.procedures.length">
                                <td colspan="3" class="px-6 py-4 text-center">
                                    No Procedures Yet
                                </td>
                            </tr>
                            <tr v-for="procedure in loan.procedures" :key="procedure.id"
                                class="hover:bg-gray-100 focus-within:bg-gray-100">
                                <td class="border-t">
                                    <span class="px-6 py-4 flex items-center">
                                    {{ procedure.name }}
                                    </span>
                                </td>
                                <td class="border-t">
                                    <span class="px-6 py-4 flex items-center" v-if="procedure.status=='ordered'">
                                    Ordered
                                    </span>
                                    <span class="px-6 py-4 flex items-center" v-if="procedure.status=='collected'">
                                    Collected
                                    </span>
                                    <span class="px-6 py-4 flex items-center" v-if="procedure.status=='dispatched'">
                                    Dispatched
                                    </span>
                                    <span class="px-6 py-4 flex items-center" v-if="procedure.status=='result_entered'">
                                    Result Entered
                                    </span>
                                    <span class="px-6 py-4 flex items-center"
                                          v-if="procedure.status=='result_finalised'">
                                    Result Finalised
                                    </span>
                                    <span class="px-6 py-4 flex items-center" v-if="procedure.status=='completed'">
                                    Completed
                                    </span>
                                    <span class="px-6 py-4 flex items-center" v-if="procedure.status=='processing'">
                                    Processing
                                    </span>
                                </td>
                                <td class="border-t w-px pr-2">
                                    <div class=" flex items-center space-x-2">
                                        <a href="#" v-if="can('loans.procedures.destroy')"
                                           @click="deleteAction(procedure.id)"
                                           class="text-red-600 hover:text-red-900">Delete</a>
                                    </div>
                                </td>
                            </tr>
                            </tbody>
                        </table>
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

                            <jet-danger-button class="ml-2" @click.native="destroy"
                                               :class="{ 'opacity-25': form.processing }"
                                               :disabled="form.processing">
                                Delete Record
                            </jet-danger-button>
                        </template>
                    </jet-confirmation-modal>
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
import Icon from '@/Jetstream/Icon.vue'
import Pagination from '@/Jetstream/Pagination.vue'
import SearchFilter from '@/Jetstream/SearchFilter.vue'
import FilterSearch from '@/Jetstream/FilterSearch.vue'
import mapValues from 'lodash/mapValues'
import pickBy from 'lodash/pickBy'
import throttle from 'lodash/throttle'
import JetLabel from '@/Jetstream/Label.vue'
import SelectInput from '@/Jetstream/SelectInput.vue'
import JetConfirmationModal from '@/Jetstream/ConfirmationModal.vue'
import JetDangerButton from '@/Jetstream/DangerButton.vue'
import JetSecondaryButton from '@/Jetstream/SecondaryButton.vue'
import LoanMenu from '@/Pages/Loans/LoanMenu.vue'
import JetInput from "@/Jetstream/Input.vue";
import JetInputError from "@/Jetstream/InputError.vue";
import JetButton from "@/Jetstream/Button.vue";
import JetCheckbox from "@/Jetstream/Checkbox.vue";
import TextareaInput from "@/Jetstream/TextareaInput.vue";

const fetchProcedures = async (query) => {
    let where = ''
    const response = await fetch(
        route('tariffs.search') + '?s=' + query,
        {}
    );

    const data = await response.json(); // Here you have the data that you need
    return data.map((item) => {
        return {value: item.id, label: item.name}
    })
}
export default {
    metaInfo: {title: 'Loans'},
    components: {
        AppLayout,
        Icon,
        Pagination,
        SearchFilter,
        FilterSearch,
        JetLabel,
        JetInput,
        JetInputError,
        SelectInput,
        JetConfirmationModal,
        JetDangerButton,
        JetSecondaryButton,
        LoanMenu,
        JetButton,
        JetCheckbox,
        TextareaInput,
    },
    props: {
        loan: Object,

    },
    data() {
        return {
            form: this.$inertia.form({
                member_id: null,
                amount: null,
                description: null,

            }),
            selectedRecord: null,
            confirmingDeletion: false,
            pageTitle: "Loans",
            pageDescription: "View Procedures",
            procedureMultiSelect: {
                value: null,
                remark: null,
                placeholder: 'Search',
                filterResults: false,
                minChars: 2,
                resolveOnLoad: false,
                delay: 4,
                searchable: true,
                options: async (query) => {
                    return await fetchProcedures(query)
                }
            }

        }
    },

    methods: {
        submit() {
            this.form.post(this.route('loans.guarantors.store', this.loan.id), {
                onSuccess: () => {
                    this.form.reset()
                }
            })
        },
        deleteAction(id) {
            this.confirmingDeletion = true
            this.selectedRecord = id
        },
        destroy() {
            this.$inertia.delete(this.route('loans.guarantors.destroy', this.selectedRecord))
            this.confirmingDeletion = false
        },
    },

}

</script>

<style scoped>

</style>
