<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <inertia-link class="text-indigo-400 hover:text-indigo-600" :href="route('loans.index')">Loans
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
                    <div class="flex justify-between ">
                        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Edit Note</h2>
                        <inertia-link class="btn btn-blue" :href="route('loans.notes.index',loan.id)">
                            <span>Back </span>
                        </inertia-link>
                    </div>
                    <div class="mt-4">
                        <form @submit.prevent="submit" enctype="multipart/form-data">
                            <div class="grid grid-cols-1 space-y-4">

                                <div>
                                    <jet-label for="description" value="Description"/>
                                    <textarea-input id="description"  class="mt-1 block w-full"
                                                  v-model="form.description"/>
                                    <jet-input-error :message="form.errors.description" class="mt-2"/>

                                </div>
                            </div>
                            <div class="flex items-center justify-end mt-4">
                                <jet-button class="ml-4" :class="{ 'opacity-25': form.processing }"
                                            :disabled="form.processing">
                                    Save
                                </jet-button>
                            </div>
                        </form>
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

export default {
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
        TextareaInput
    },
    props: {
        loan: Object,
        note: Object,

    },
    data() {
        return {
            form: this.$inertia.form({
                description: this.note.description,
            }),
            pageTitle: "Edit Loan Notes",
            pageDescription: "Edit Loan Notes",

        }
    },

    methods: {
        submit() {
            this.form.put(this.route('loans.notes.update', this.note.id), {})
        },
    },
}
</script>

<style scoped>

</style>
