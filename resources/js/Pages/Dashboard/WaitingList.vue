<template>
    <div class="w-full max-h-full">
        <div class="bg-white relative  overflow-auto h-64 ">
            <div class="fixed top-15 left-0 right-0 z-10 bg-white flex items-center p-5">
                <div class="mr-auto">
                    <div class="font-medium text-base">Waiting List</div>
                </div>
            </div>
            <div class="relative border-t border-gray-200 dark:border-dark-5 mt-16">
                <table class="w-full whitespace-no-wrap">
                    <thead class="bg-gray-50 ">
                    <tr class="text-left font-bold">
                        <th class="px-6 pt-4 pb-4 font-medium text-gray-500">Name</th>
                        <th class="px-6 pt-4 pb-4 font-medium text-gray-500">Age</th>
                        <th class="px-6 pt-4 pb-4 font-medium text-gray-500">Time Waiting</th>
                    </tr>
                    </thead>
                    <tbody class="">
                    <tr v-for="consultation in consultations" class="cursor-pointer"
                        @click="acknowledgeAction(consultation.id,$page.props.user.current_role)">
                        <td class="border-t px-6 py-4">
                            <span v-if="consultation.patient">{{ consultation.patient.name }}</span>
                        </td>
                        <td class="border-t px-6 py-4">
                            <span v-if="consultation.patient">{{ consultation.patient.age }}</span>
                        </td>
                        <td class="border-t px-6 py-4">
                            <span>{{ consultation.waiting_time }}</span>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <jet-confirmation-modal :show="confirmingAcknowledging" @close="confirmingAcknowledging = false">
        <template #title>
            Acknowledge Consultation
        </template>

        <template #content>
            Are you sure you want to acknowledge working on this consultation?
        </template>

        <template #footer>
            <jet-secondary-button @click.native="confirmingAcknowledging = false">
                Nevermind
            </jet-secondary-button>

            <jet-success-button class="ml-2" @click.native="acknowledge" :class="{ 'opacity-25': processing }"
                                :disabled="processing">
                Acknowledge
            </jet-success-button>
        </template>
    </jet-confirmation-modal>
</template>

<script>
import JetLabel from '@/Jetstream/Label.vue'
import SelectInput from '@/Jetstream/SelectInput.vue'
import JetConfirmationModal from '@/Jetstream/ConfirmationModal.vue'
import JetDialogModal from '@/Jetstream/DialogModal.vue'
import JetDangerButton from '@/Jetstream/DangerButton.vue'
import JetSuccessButton from '@/Jetstream/SuccessButton.vue'
import JetSecondaryButton from '@/Jetstream/SecondaryButton.vue'
import JetCheckbox from "@/Jetstream/Checkbox.vue";

export default {
    name: "WaitingList",
    components: {
        JetLabel,
        SelectInput,
        JetCheckbox,
        JetConfirmationModal,
        JetDialogModal,
        JetDangerButton,
        JetSecondaryButton,
        JetSuccessButton,
    },
    data() {
        return {
            consultations: [],
            confirmingAcknowledging: false,
            processing: false,
            selectedRecord: null,
            role: null,
        }
    },
    mounted() {
        this.getData();
        this.initializeChannels();
    },
    methods: {
        getData() {
            axios.get(this.route('dashboard.get_waiting_list')).then(response => {
                this.consultations = response.data;
            })
        },
        acknowledgeAction(id, role) {
            this.confirmingAcknowledging = true
            this.selectedRecord = id
            this.role = role
        },
        acknowledge() {
            this.processing = true
            this.$inertia.post(this.route('consultations.acknowledge', this.selectedRecord), {
                role: this.role
            }, {
                onSuccess: () => {
                    this.processing = false
                    this.$inertia.visit(route('patients.consultations.vitals.create', this.selectedRecord))
                }
            })
            this.confirmingAcknowledging = false
        },
        viewConsultation(id) {
            if (this.$page.props.user.current_role === 'doctor') {
                this.$inertia.visit(route('patients.consultations.show', id))
            }
            if (this.$page.props.user.current_role === 'nurse') {
                this.$inertia.visit(route('patients.consultations.vitals.create', id))
            }
            this.$inertia.visit(route('patients.consultations.show', id))
        },
        initializeChannels() {
            if (this.$page.props.user.current_role === 'nurse') {
                this.nurseConsultationChannel = Echo.private(`consultation-waiting-list-nurse.${this.$page.props.user.id}`)
                    .listen('ConsultationCreated', (e) => {
                        this.getData()
                    });
            }
            if (this.$page.props.user.current_role === 'doctor') {
                this.doctorConsultationChannel = Echo.private(`consultation-waiting-list-doctor.${this.$page.props.user.id}`)
                    .listen('ConsultationNurseCompleted', (e) => {
                        this.getData()
                    });
            }


        }
    }
}
</script>

<style scoped>

</style>
