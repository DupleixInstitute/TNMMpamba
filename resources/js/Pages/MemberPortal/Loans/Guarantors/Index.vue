<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <inertia-link class="text-indigo-400 hover:text-indigo-600" :href="route('portal.loans.index')">Loans
                </inertia-link>
                <span class="text-indigo-400 font-medium">/</span> {{ loan.id }}
            </h2>
        </template>
        <div class="mx-auto">
            <div class="md:flex md:items-start">
                <div class="bg-white relative shadow-xl mb-4 w-full md:w-3/12">
                    <div class="col-span-12 lg:col-span-4 xxl:col-span-3 flex lg:block flex-col-reverse">
                        <div class="intro-y box sm:mt-4 lg:mt-0">
                            <loan-menu :loan="loan"></loan-menu>
                        </div>
                    </div>

                </div>
                <div class="w-full md:w-9/12 p-4 md:ml-4 bg-white">
                    <div class="flex justify-between">
                        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Guarantors</h2>
                    </div>
                    <div class="mt-4 relative overflow-x-auto">
                        <form @submit.prevent="submit" v-if="can('portal.loans.guarantors.create')">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                                <div>
                                    <jet-label for="guarantor" value="Guarantor"/>
                                    <Multiselect
                                        v-model="form.member_id"
                                        v-bind="guarantorMultiSelect"
                                        :required="true"
                                    />
                                    <jet-input-error :message="form.errors.member_id" class="mt-2"/>
                                </div>
                                <div>
                                    <jet-label for="amount" value="Amount"/>
                                    <jet-input id="amount" type="text" class=" block w-full"
                                               v-model="form.amount"/>
                                    <jet-input-error :message="form.errors.amount" class="mt-2"/>
                                </div>
                            </div>

                            <div class="flex items-center justify-end mt-4 mb-4">
                                <jet-button class="ml-4" :class="{ 'opacity-25': form.processing }"
                                            :disabled="form.processing">
                                    Add Guarantor
                                </jet-button>
                            </div>
                        </form>
                        <table class="w-full whitespace-no-wrap table-auto">
                            <thead class="bg-gray-50">
                            <tr class="text-left font-bold">
                                <th class="px-6 pt-4 pb-4 font-medium text-gray-500">Name</th>
                                <th class="px-6 pt-4 pb-4 font-medium text-gray-500">Amount</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-if="!guarantors.data.length">
                                <td colspan="2" class="px-6 py-4 text-center">
                                    No Guarantors Yet
                                </td>
                            </tr>
                            <tr v-for="guarantor in guarantors.data" :key="guarantor.id"
                                class="hover:bg-gray-100 focus-within:bg-gray-100">
                                <td class="border-t">
                                    <span class="px-6 py-4 flex items-center focus:text-indigo-500"
                                                  v-if="guarantor.member">
                                        <img v-if="guarantor.member.profile_photo_url"
                                             class="block w-5 h-5 rounded-full mr-2 -my-2"
                                             :src="guarantor.member.profile_photo_url">
                                        {{ guarantor.member.name }}
                                    </span>
                                </td>
                                <td class="border-t">
                                    <span class="px-6 py-4 flex items-center">
                                         {{ $filters.formatNumber(guarantor.amount) }}
                                    </span>
                                </td>

                            </tr>
                            </tbody>
                        </table>
                    </div>
                    <pagination v-if="guarantors.data.length" :links="guarantors.links"/>
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
import FilterSearch from '@/Jetstream/FilterSearch.vue'
import mapValues from 'lodash/mapValues'
import pickBy from 'lodash/pickBy'
import throttle from 'lodash/throttle'
import JetLabel from '@/Jetstream/Label.vue'
import SelectInput from '@/Jetstream/SelectInput.vue'
import JetConfirmationModal from '@/Jetstream/ConfirmationModal.vue'
import JetDangerButton from '@/Jetstream/DangerButton.vue'
import JetSecondaryButton from '@/Jetstream/SecondaryButton.vue'
import LoanMenu from '@/Pages/MemberPortal/Loans/LoanMenu.vue'
import JetInput from "@/Jetstream/Input.vue";
import JetInputError from "@/Jetstream/InputError.vue";
import JetButton from "@/Jetstream/Button.vue";
import JetCheckbox from "@/Jetstream/Checkbox.vue";
import TextareaInput from "@/Jetstream/TextareaInput.vue";

const fetchGuarantors = async (query,branchID='') => {
    let where = ''
    const response = await fetch(
        route('members.search') + '?s=' + query+'&branch_id='+branchID,
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
        guarantors: Object,
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
            pageDescription: "View Guarantors",
            guarantorMultiSelect: {
                value: null,
                remark: null,
                placeholder: 'Search',
                filterResults: false,
                minChars: 2,
                resolveOnLoad: false,
                delay: 4,
                searchable: true,
                options: async (query) => {
                    return await fetchGuarantors(query,this.loan.member.branch_id)
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
