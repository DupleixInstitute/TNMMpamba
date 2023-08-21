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
        </div>
        <div class="w-full mt-5">
            <inertia-link
                class="w-full border-t border-gray-100 font-medium text-gray-600 py-2 px-4 w-full block hover:bg-gray-100 transition duration-150"
                :class="{'bg-gray-100': route().current('portal.courses.show')}"
                :href="route('portal.courses.show',course.id)">
                <font-awesome-icon icon="user" class="w-4 h-4 mr-2"></font-awesome-icon>
                Course Details
            </inertia-link>
            <inertia-link v-if="course.registered_course"
                          class="w-full border-t border-gray-100 font-medium text-gray-600 py-2 px-4 w-full block hover:bg-gray-100 transition duration-150"
                          :class="{'bg-gray-100': route().current('portal.courses.materials.*')}"
                          :href="route('portal.courses.materials.index',course.id)">
                <font-awesome-icon icon="folder" class="w-4 h-4 mr-2"></font-awesome-icon>
                Course Material
            </inertia-link>
            <inertia-link v-if="course.registered_course"
                          class="w-full border-t border-gray-100 font-medium text-gray-600 py-2 px-4 w-full block hover:bg-gray-100 transition duration-150"
                          :class="{'bg-gray-100': route().current('portal.courses.articles.index')}"
                          :href="route('portal.courses.articles.index',course.id)">
                <font-awesome-icon icon="clipboard" class="w-4 h-4 mr-2"></font-awesome-icon>
                News & Updates
            </inertia-link>
        </div>
        <div class="p-5 border-t flex justify-between">
            <inertia-link v-if="!course.registered_course" :href="route('portal.registrations.create',{course_id:course.id})"
                          class="btn btn-primary py-1 px-2">Register
            </inertia-link>
        </div>
    </div>
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
