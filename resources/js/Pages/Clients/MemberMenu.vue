<template>
    <div>
        <div class="flex justify-center">
            <img v-if="member.profile_photo_url" :src="member.profile_photo_url" alt=""
                 class="rounded-full mx-auto absolute -top-20 w-32 h-32 shadow-2xl border-4 border-white">
        </div>
        <div class="mt-16">
            <h1 class="font-bold text-center text-3xl text-gray-900">{{ member.name }}</h1>
            <p class="text-center text-sm text-gray-400 font-medium">
                {{ member.gender }}
            </p>
            <p class="text-center text-sm text-gray-400 font-medium">
                {{ member.age }}
            </p>
            <p class="text-center text-sm text-gray-400 font-medium">
                {{ member.mobile }}
            </p>
            <p class="text-center text-sm text-gray-400 font-medium">
                {{ member.email }}
            </p>
            <div class="flex justify-center">
                <inertia-link v-if="can('communication.campaigns.create')" :href="route('communication.campaigns.create',{member_id:member.id,campaign_type:'sms'})"
                              class="btn btn-success  mr-2" title="SMS">
                    <font-awesome-icon icon="sms" class="w-4 h-4"></font-awesome-icon>
                </inertia-link>
                <inertia-link v-if="can('communication.campaigns.create')" :href="route('communication.campaigns.create',{member_id:member.id,campaign_type:'email'})"
                              class="btn btn-success" title="Email">
                    <font-awesome-icon icon="envelope" class="w-4 h-4"></font-awesome-icon>
                </inertia-link>
            </div>
        </div>
        <div class="w-full mt-5">
            <inertia-link
                class="w-full border-t border-gray-100 font-medium text-gray-600 py-2 px-4 w-full block hover:bg-gray-100 transition duration-150"
                :class="{'bg-gray-100': route().current('members.show')}"
                :href="route('members.show',member.id)">
                <font-awesome-icon icon="user" class="w-4 h-4 mr-2"></font-awesome-icon>
                Basic Profile
            </inertia-link>
            <inertia-link v-if="can('loans.index')"
                          class="w-full border-t border-gray-100 font-medium text-gray-600 py-2 px-4 w-full block hover:bg-gray-100 transition duration-150"
                          :class="{'bg-gray-100': route().current('members.loans.index')}"
                          :href="route('members.loans.index',member.id)">
                <font-awesome-icon icon="database" class="w-4 h-4 mr-2"></font-awesome-icon>
                Loans
            </inertia-link>
            <inertia-link v-if="can('courses.registrations.index')"
                          class="w-full border-t border-gray-100 font-medium text-gray-600 py-2 px-4 w-full block hover:bg-gray-100 transition duration-150"
                          :class="{'bg-gray-100': route().current('members.courses.index')}"
                          :href="route('members.courses.index',member.id)">
                <font-awesome-icon icon="graduation-cap" class="w-4 h-4 mr-2"></font-awesome-icon>Courses
            </inertia-link>
            <inertia-link v-if="can('members.notes')"
                          class="w-full border-t border-gray-100 font-medium text-gray-600 py-2 px-4 w-full block hover:bg-gray-100 transition duration-150"
                          :class="{'bg-gray-100': route().current('members.notes.index')}"
                          :href="route('members.notes.index',member.id)">
                <font-awesome-icon icon="bookmark" class="w-4 h-4 mr-2"></font-awesome-icon>
                Notes
            </inertia-link>
            <inertia-link v-if="can('members.files.index')"
                          class="w-full border-t border-gray-100 font-medium text-gray-600 py-2 px-4 w-full block hover:bg-gray-100 transition duration-150"
                          :class="{'bg-gray-100': route().current('members.files.*')}"
                          :href="route('members.files.index',member.id)">
                <font-awesome-icon icon="folder" class="w-4 h-4 mr-2"></font-awesome-icon>
                Files
            </inertia-link>
            <inertia-link v-if="can('members.create')"
                          class="w-full border-t border-gray-100 font-medium text-gray-600 py-2 px-4 w-full block hover:bg-gray-100 transition duration-150"
                          :class="{'bg-gray-100': route().current('members.login_details.*')}"
                          :href="route('members.login_details.index',member.id)">
                <font-awesome-icon icon="user-lock" class="w-4 h-4 mr-2"></font-awesome-icon>
                Login Details
            </inertia-link>
        </div>
        <div class="p-5 border-t flex">
            <inertia-link v-if="can('members.update')" :href="route('members.edit',member.id)"
                          class="btn btn-primary py-1 px-2">Edit
            </inertia-link>
            <button type="button" v-if="can('members.destroy')" @click="deleteAction" class="btn btn-danger py-1 px-2 ml-auto">Delete
            </button>
        </div>
    </div>
    <jet-confirmation-modal :show="confirmingMemberDeletion" @close="confirmingMemberDeletion = false">
        <template #title>
            Delete Record
        </template>

        <template #content>
            Are you sure you want to delete your account? Once the record is deleted, all of its resources and
            data will be permanently deleted.
        </template>

        <template #footer>
            <jet-secondary-button @click.native="confirmingMemberDeletion = false">
                Nevermind
            </jet-secondary-button>

            <jet-danger-button class="ml-2" @click.native="destroy" :class="{ 'opacity-25': processing }"
                               :disabled="processing">
                Delete Record
            </jet-danger-button>
        </template>
    </jet-confirmation-modal>
</template>

<script>
import JetConfirmationModal from '@/Jetstream/ConfirmationModal.vue'
import JetDangerButton from '@/Jetstream/DangerButton.vue'
import JetSecondaryButton from '@/Jetstream/SecondaryButton.vue'
import Icon from "@/Jetstream/Icon.vue";
import JetLabel from "@/Jetstream/Label.vue";
import SelectInput from "@/Jetstream/SelectInput.vue";

export default {
    components: {
        Icon,
        JetLabel,
        SelectInput,
        JetConfirmationModal,
        JetDangerButton,
        JetSecondaryButton,
    },
    props: {
        member: Object,
    },
    data() {
        return {
            processing: false,
            confirmingMemberDeletion: false,

        }
    },
    methods: {
        deleteAction() {
            this.confirmingMemberDeletion = true
        },
        destroy() {

            this.$inertia.delete(this.route('members.destroy', this.member.id))
            this.confirmingMemberDeletion = false
        },
    },
}
</script>

<style scoped>

</style>
