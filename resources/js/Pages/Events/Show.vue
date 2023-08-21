<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <inertia-link class="text-indigo-400 hover:text-indigo-600" :href="route('events.index')">
                    Events
                </inertia-link>
                <span class="text-indigo-400 font-medium">/</span> {{ event.title }}
            </h2>
        </template>

        <div class=" mx-auto">
            <div class="md:flex md:items-start">
                <div class="relative mb-4  w-full md:w-3/12">
                    <div class="intro-y bg-white mt-5 lg:mt-0">
                        <div class="relative flex items-center p-5">
                            <div class="ml-4 mr-auto">
                                <div class="font-medium text-base">{{ event.title }}</div>
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
                                        <jet-dropdown-link :href="route('events.edit',event.id)">
                                            <font-awesome-icon icon="edit"/>
                                            Edit
                                        </jet-dropdown-link>
                                        <a href="#"
                                           class="block px-4 py-2 text-sm leading-5 text-gray-700 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 transition duration-150 ease-in-out"
                                           @click="deleteAction(event.id)">
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
                                <span class="font-medium">Title</span>
                                <span>{{ event.title }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="font-medium">Category</span>
                                <span v-if="event.category">{{ event.category.name }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="font-medium">Status</span>
                                <span v-if="event.status==='pending'">Pending</span>
                                <span v-if="event.status==='active'">Active</span>
                                <span v-if="event.status==='inactive'">In-Active</span>
                                <span v-if="event.status==='published'">Published</span>
                                <span v-if="event.status==='completed'">Completed</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="font-medium">Start Date</span>
                                <span class="p">
                                {{ event.start_date }}
                                 <span v-if="event.start_time"> @  {{ event.start_time }}</span>
                            </span>
                            </div>
                            <div class="flex justify-between">
                                <span class="font-medium">End Date</span>
                                <span class="">
                                {{ event.end_date }}
                                 <span v-if="event.end_time"> @  {{ event.end_time }}</span>
                            </span>
                            </div>
                            <div class="flex justify-between">
                                <span class="font-medium">Location</span>
                                <span class="">
                                    {{ event.location }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="w-full md:w-9/12  md:ml-4">
                    <div class="bg-white p-5">
                        <div class="flex justify-between mt-4">
                            <h3>Description</h3>
                        </div>
                        <div v-html="event.description">

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
import SelectInput from "@/Jetstream/SelectInput.vue";
import FileInput from "@/Jetstream/FileInput.vue";
import TextareaInput from "@/Jetstream/TextareaInput.vue";
import JetDropdown from '@/Jetstream/Dropdown.vue'
import JetDropdownLink from '@/Jetstream/DropdownLink.vue'
import JetDialogModal from '@/Jetstream/DialogModal.vue'
import JetSecondaryButton from '@/Jetstream/SecondaryButton.vue'
import JetConfirmationModal from '@/Jetstream/ConfirmationModal.vue'
import JetDangerButton from '@/Jetstream/DangerButton.vue'

export default {
    props: {
        event: Object,
        printEventPayment: Boolean,
        eventPaymentID: Number,
    },
    components: {
        SelectInput,
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
            confirmSlideDeletion: false,
            confirmingDeletion: false,
            selectedSlideRecord: null,
            selectedRecord: null,
            processing: false,
            pageTitle: "Event Details",
            pageDescription: "Event Details",
        }

    },
    mounted() {
        if (this.printEventPayment) {
            this.printPayment(this.eventPaymentID)
        }
    },
    methods: {

        deleteAction(id) {
            this.confirmingDeletion = true
            this.selectedRecord = id
        },
        destroy() {
            this.$inertia.delete(this.route('events.destroy', this.selectedRecord))
            this.confirmingDeletion = false
            window.location = route('events.index')
        },

    }
}
</script>
<style scoped>

</style>
