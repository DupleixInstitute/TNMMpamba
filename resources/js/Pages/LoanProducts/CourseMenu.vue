<template>
    <div>
        <div class="">
            <h1 class="font-bold text-center text-xl text-gray-900"> {{ course.name }}</h1>
            <p class="text-center text-sm   text-gray-400 font-medium" v-if="course.category">
                {{ course.category.name }}
            </p>
            <p class="text-center text-sm text-gray-400 font-medium">
                {{ course.duration }} days
            </p>
            <p class="text-center text-sm text-gray-400 font-medium">
                Status: <span v-if="course.status=='draft'"
                              class="px-2 rounded-full bg-gray-100 text-gray-800">
                            Draft
                        </span>
                <span v-if="course.status=='publish'"
                      class="px-2 rounded-full bg-blue-100 text-blue-800">
                    Publish
                </span>
                <span v-if="course.status=='trash'"
                      class="px-2 rounded-full bg-red-100 text-red-800">
                    Trash
                </span>
            </p>
            <p class="text-center text-sm text-gray-400 font-medium">
                Approval Status: <span v-if="course.approval_status=='pending'"
                                       class="px-2 rounded-full bg-yellow-100 text-yellow-800">
                                        pending
                                    </span>
                <span v-if="course.approval_status=='approved'"
                      class="px-2 rounded-full bg-green-100 text-green-800">
                    approved
                </span>
                <span v-if="course.approval_status=='rejected'"
                      class="px-2 rounded-full bg-red-100 text-red-800">
                    rejected
                </span>
            </p>
        </div>
        <div class="w-full mt-5">
            <inertia-link
                class="w-full border-t border-gray-100 font-medium text-gray-600 py-2 px-4 w-full block hover:bg-gray-100 transition duration-150"
                :class="{'bg-gray-100': route().current('courses.show')}"
                :href="route('courses.show',course.id)">
                <font-awesome-icon icon="user" class="w-4 h-4 mr-2"></font-awesome-icon>
                Course Details
            </inertia-link>
            <inertia-link v-if="can('courses.materials.index')"
                          class="w-full border-t border-gray-100 font-medium text-gray-600 py-2 px-4 w-full block hover:bg-gray-100 transition duration-150"
                          :class="{'bg-gray-100': route().current('courses.materials.*')}"
                          :href="route('courses.materials.index',course.id)">
                <font-awesome-icon icon="folder" class="w-4 h-4 mr-2"></font-awesome-icon>
                Course Material
            </inertia-link>
            <inertia-link v-if="can('courses.registrations.index')"
                          class="w-full border-t border-gray-100 font-medium text-gray-600 py-2 px-4 w-full block hover:bg-gray-100 transition duration-150"
                          :class="{'bg-gray-100': route().current('courses.registrations.index')}"
                          :href="route('courses.registrations.index',course.id)">
                <font-awesome-icon icon="users" class="w-4 h-4 mr-2"></font-awesome-icon>
                Registrations
            </inertia-link>
            <inertia-link v-if="can('courses.index')"
                          class="w-full border-t border-gray-100 font-medium text-gray-600 py-2 px-4 w-full block hover:bg-gray-100 transition duration-150"
                          :class="{'bg-gray-100': route().current('courses.articles.index')}"
                          :href="route('courses.articles.index',course.id)">
                <font-awesome-icon icon="clipboard" class="w-4 h-4 mr-2"></font-awesome-icon>
                News & Updates
            </inertia-link>
        </div>
        <div class="p-5 border-t flex justify-between">
            <inertia-link v-if="can('courses.update')" :href="route('courses.edit',course.id)"
                          class="btn btn-primary py-1 px-2">Edit
            </inertia-link>
            <button type="button" v-if="can('courses.destroy')" @click="deleteAction"
                    class="btn btn-danger py-1 px-2 ml-auto">Delete
            </button>
            <button type="button" v-if="can('courses.approve')" @click="showChangeStatusModal=true"
                    class="btn btn-success py-1 px-2 ml-auto">
                Approve/Reject
            </button>
        </div>
    </div>
    <jet-confirmation-modal :show="confirmingCourseDeletion" @close="confirmingCourseDeletion = false">
        <template #title>
            Delete Record
        </template>

        <template #content>
            Are you sure you want to delete your account? Once the record is deleted, all of its resources and
            data will be permanently deleted.
        </template>

        <template #footer>
            <jet-secondary-button @click.native="confirmingCourseDeletion = false">
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
            Approve/Reject
        </template>
        <template #content>
            <div class="grid grid-cols-1 gap-2 mt-4">
                <div>
                    <jet-label for="status" value="Status"/>
                    <select
                        class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm w-full"
                        name="status" v-model="status" id="status"
                        required>
                        <option value="pending">Pending</option>
                        <option value="approved">Approved</option>
                        <option value="rejected">Rejected</option>
                    </select>
                </div>
                <div>
                    <jet-label for="description" value="Description"/>
                    <textarea-input id="description" class="mt-1 block w-full"
                                    v-model="description"/>
                </div>
            </div>
        </template>
        <template #footer>
            <jet-secondary-button @click.native="showChangeStatusModal = false">
                Cancel
            </jet-secondary-button>

            <jet-success-button class="ml-2" @click.native="changeStatus"
                                :class="{ 'opacity-25': processing }"
                                :disabled="processing">
                Save
            </jet-success-button>
        </template>
    </jet-dialog-modal>
</template>

<script>
import JetConfirmationModal from '@/Jetstream/ConfirmationModal.vue'
import JetDialogModal from '@/Jetstream/DialogModal.vue'
import JetDangerButton from '@/Jetstream/DangerButton.vue'
import JetSuccessButton from '@/Jetstream/SuccessButton.vue'
import JetSecondaryButton from '@/Jetstream/SecondaryButton.vue'
import Icon from "@/Jetstream/Icon.vue";
import JetLabel from "@/Jetstream/Label.vue";
import SelectInput from "@/Jetstream/SelectInput.vue";
import TextareaInput from "@/Jetstream/TextareaInput.vue";

export default {
    components: {
        Icon,
        JetLabel,
        SelectInput,
        JetConfirmationModal,
        JetDangerButton,
        JetDialogModal,
        JetSecondaryButton,
        JetSuccessButton,
        TextareaInput,
    },
    props: {
        course: Object,
    },
    data() {
        return {
            processing: false,
            confirmingCourseDeletion: false,
            showChangeStatusModal: false,
            status: this.course.approval_status,
            description: this.course.approval_status_notes,
        }
    },
    methods: {
        deleteAction() {
            this.confirmingCourseDeletion = true
        },
        destroy() {

            this.$inertia.delete(this.route('courses.destroy', this.course.id))
            this.confirmingCourseDeletion = false
        },
        changeStatus() {

            this.$inertia.post(this.route('courses.change_status', this.course.id), {
                status: this.status,
                description: this.description,
            }, {
                onSuccess: () => {
                    this.$inertia.reload();
                }
            })
            this.showChangeStatusModal = false
        },
    },
}
</script>

<style scoped>

</style>
