<template>
    <app-layout>
        <template #header>
            <div class="flex justify-between">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    My Workspace
                </h2>
            </div>
        </template>

        <div class="mx-auto mt-4">
            <div class="tabs">
                <ul class="flex justify-center">
                    <li @click="activeTab = 'inbox'" :class="{'active': activeTab === 'inbox'}" class="cursor-pointer px-4 py-2 text-gray-700 relative">
                        Reminders <span class="badge">{{ inboxCount }}</span>
                    </li>
                    <li @click="activeTab = 'assigned'" :class="{'active': activeTab === 'assigned'}" class="cursor-pointer px-4 py-2 text-gray-700 relative">
                        Assigned To Me <span class="badge">{{ assignedCount }}</span>
                    </li>
                    <li @click="activeTab = 'approved'" :class="{'active': activeTab === 'approved'}" class="cursor-pointer px-4 py-2 text-gray-700 relative">
                        Approved By Me <span class="badge">{{ approvedCount }}</span>
                    </li>
                    <li @click="activeTab = 'pending'" :class="{'active': activeTab === 'pending'}" class="cursor-pointer px-4 py-2 text-gray-700 relative">
                        My Pending To Me <span class="badge">{{ pendingCount }}</span>
                    </li>
                </ul>
            </div>

            <div class="tab-content mt-4">
                <div v-if="activeTab === 'inbox'">
                    <h3 class="text-2xl font-semibold mb-4 flex items-center justify-center">Reminders</h3>
                    <div v-if="inboxCount === 0" class="flex flex-col items-center justify-center h-64">
                        <svg class="w-24 h-24 text-gray-400 animate-bounce mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15a4 4 0 014-4h1a4 4 0 018 0h1a4 4 0 014 4v2H3v-2zM7 10V7a3 3 0 013-3 3 3 0 013 3v3m6 10a3 3 0 01-6 0"></path>
                        </svg>
                        <p class="text-lg text-gray-600">Nothing to show yet!</p>
                    </div>
                    <div v-else>
                        <p class="text-center">Content for My Reminders</p>
                    </div>
                </div>
                <div v-if="activeTab === 'assigned'">
                    <h3 class="text-2xl font-semibold mb-4 flex items-center justify-center">Assigned</h3>
                   <div v-if="assignedCount === 0" class="flex flex-col items-center justify-center h-64">
                        <svg class="w-24 h-24 text-gray-400 animate-bounce mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15a4 4 0 014-4h1a4 4 0 018 0h1a4 4 0 014 4v2H3v-2zM7 10V7a3 3 0 013-3 3 3 0 013 3v3m6 10a3 3 0 01-6 0"></path>
                        </svg>
                        <p class="text-lg text-gray-600">Nothing to show yet!</p>
                    </div>
                    <div v-else>
                        <p class="text-center">Content for Assigned</p>
                    </div>
                </div>
                <div v-if="activeTab === 'approved'">
                    <h3 class="text-2xl font-semibold mb-4 flex items-center justify-center">Approved</h3>
                    <div v-if="approvedCount === 0" class="flex flex-col items-center justify-center h-64">
                        <svg class="w-24 h-24 text-gray-400 animate-bounce mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15a4 4 0 014-4h1a4 4 0 018 0h1a4 4 0 014 4v2H3v-2zM7 10V7a3 3 0 013-3 3 3 0 013 3v3m6 10a3 3 0 01-6 0"></path>
                        </svg>
                        <p class="text-lg text-gray-600">Nothing to show yet!</p>
                    </div>

                </div>
                <div v-if="activeTab === 'pending'">
                    <h3 class="text-2xl font-semibold mb-4 flex items-center justify-center">My Pending</h3>
                   <div v-if="pendingCount === 0" class="flex flex-col items-center justify-center h-64">
                        <svg class="w-24 h-24 text-gray-400 animate-bounce mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15a4 4 0 014-4h1a4 4 0 018 0h1a4 4 0 014 4v2H3v-2zM7 10V7a3 3 0 013-3 3 3 0 013 3v3m6 10a3 3 0 01-6 0"></path>
                        </svg>
                        <p class="text-lg text-gray-600">Nothing to show yet!</p>
                    </div>
                    <div v-else>
                        <p class="text-center">Content for My Pending</p>
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
import JetDialogModal from '@/Jetstream/DialogModal.vue'
import JetDangerButton from '@/Jetstream/DangerButton.vue'
import JetSuccessButton from '@/Jetstream/SuccessButton.vue'
import JetSecondaryButton from '@/Jetstream/SecondaryButton.vue'
import JetInfoButton from '@/Jetstream/InfoButton.vue'
import JetLabel from '@/Jetstream/Label.vue'
import SelectInput from '@/Jetstream/SelectInput.vue'




export default {
    components: {
        AppLayout,
        JetDialogModal,
        JetDangerButton,
        JetSuccessButton,
        JetSecondaryButton,
        JetLabel,
        SelectInput,
        JetInfoButton,

    },
    props: {
        applications: Object,
        filters: Object,
        products: Object,
        branches: Object,





    },
    data() {
        return {
            pageTitle: "My Workspace",
            pageDescription: "My Workspace",
            activeTab: 'inbox',  // Default active tab
            inboxCount: 0,      // Placeholder count for inbox
            assignedCount: 0,    // Placeholder count for assigned
            approvedCount: 0,    // Placeholder count for approved
            pendingCount: 0      // Placeholder count for pending
        }
    },
    methods: {


    },
    computed: {},
    mounted() {

    }
}
</script>

<style scoped>
.tabs ul {
    display: flex;
    border-bottom: 2px solid #e2e8f0;
}
.tabs ul li {
    margin-right: 16px; /* Increase the right margin to create more space between items */
    padding: 8px 16px;
    border: 1px solid #e2e8f0;
    border-bottom: none;
    cursor: pointer;
    border-radius: 4px 4px 0 0;
    position: relative; /* Ensure the badge is positioned relative to the tab */
}
.tabs ul li:last-child {
    margin-right: 0; /* Remove the right margin from the last item */
}
.tabs ul li.active {
    background-color: #fff;
    border-top: 2px solid #4299e1;
    border-left: 2px solid #4299e1;
    border-right: 2px solid #4299e1;
    border-bottom: none;
    color: #4299e1;
}
.tab-content {
    padding: 16px;
    border: 2px solid #e2e8f0;
    border-radius: 0 4px 4px 4px;
    background-color: #fff;
}
.badge {
    background-color: #ff0000;
    color: #fff;
    border-radius: 12px;
    padding: 2px 8px;
    font-size: 12px;
    font-weight: bold;
    position: absolute; /* Position the badge absolutely */
    top: 0;
    right: 0;
    transform: translate(50%, -50%); /* Adjust position to look better */
}
</style>


