<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Article Categories
            </h2>
        </template>
        <div class=" mx-auto mb-4 flex justify-between items-center">
            <div>
                <filter-search v-model="form.search" class="w-full max-w-md mr-4" @reset="reset">
                    <div class="w-80 mt-2 px-4 py-6 shadow-xl bg-white rounded">
                    </div>
                </filter-search>
            </div>
            <inertia-link v-if="can('articles.categories.create')" class="btn btn-blue"
                          :href="route('articles.categories.create')">
                <span>Add </span>
                <span class="hidden md:inline"> Category</span>
            </inertia-link>
        </div>
        <div class=" mx-auto">
            <div class="bg-white rounded shadow overflow-x-auto">
                <table class="w-full whitespace-no-wrap">
                    <thead class="bg-gray-50">
                    <tr class="text-left font-bold">
                        <th class="px-6 pt-4 pb-4 font-medium text-gray-500">Name</th>
                        <th class="px-6 pt-4 pb-4 font-medium text-gray-500">Description</th>
                        <th class="px-6 pt-4 pb-4 font-medium text-gray-500">Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="item in categories.data" :key="item.id"
                        class="hover:bg-gray-100 focus-within:bg-gray-100">
                        <td class="border-t">
                             <span class="px-6 py-4 flex items-center">
                                {{ item.name }}
                            </span>
                        </td>
                        <td class="border-t">
                             <span class="px-6 py-4 flex items-center">
                                {{ item.description }}
                            </span>
                        </td>
                        <td class="border-t w-px pr-2">
                            <div class=" flex items-center space-x-2">
                                <inertia-link v-if="can('articles.categories.update')"
                                              :href="route('articles.categories.edit', item.id)"
                                              tabindex="-1" class="text-indigo-600 hover:text-indigo-900">
                                    Edit
                                </inertia-link>
                                <a href="#" v-if="can('articles.categories.destroy')"
                                   @click="deleteAction(item.id)"
                                   class="text-red-600 hover:text-red-900">Delete</a>
                            </div>
                        </td>
                    </tr>
                    <tr v-if="categories.data.length === 0">
                        <td class="border-t px-6 py-4" colspan="3">No items found.</td>
                    </tr>
                    </tbody>
                </table>

            </div>
            <pagination :links="categories.links"/>
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

                <jet-danger-button class="ml-2" @click.native="destroy" :class="{ 'opacity-25': form.processing }"
                                   :disabled="form.processing">
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
import AppLayout from '@/Layouts/AppLayout.vue';
import Pagination from '@/Jetstream/Pagination.vue';
import FilterSearch from '@/Jetstream/FilterSearch.vue'
import mapValues from 'lodash/mapValues'
import pickBy from 'lodash/pickBy'
import throttle from 'lodash/throttle'
import JetLabel from '@/Jetstream/Label.vue'
import JetConfirmationModal from '@/Jetstream/ConfirmationModal.vue'
import JetDangerButton from '@/Jetstream/DangerButton.vue'
import JetSecondaryButton from '@/Jetstream/SecondaryButton.vue'

export default {
    components: {
        AppLayout,
        Pagination,
        FilterSearch,
        JetLabel,
        JetConfirmationModal,
        JetDangerButton,
        JetSecondaryButton,
    },
    props: {
        categories: Object,
        filters: Object,
    },
    mounted() {

    },
    data() {
        return {
            form: {
                search: this.filters.search,
                processing: false
            },
            confirmingDeletion: false,
            selectedRecord: null,
            pageTitle: "Article Categories",
            pageDescription: "Manage  Article Categories",

        }
    },
    watch: {
        form: {
            handler: _.debounce(function () {
                let query = pickBy(this.form)
                this.$inertia.get(this.route('articles.categories.index', Object.keys(query).length ? query : {}))
            }, 500),
            deep: true,
        },
    },
    methods: {
        reset() {
            this.form = mapValues(this.form, () => null)
        },
        deleteAction(id) {
            this.confirmingDeletion = true
            this.selectedRecord = id
        },
        destroy() {

            this.$inertia.delete(this.route('articles.categories.destroy', this.selectedRecord))
            this.confirmingDeletion = false
        },
    },
}
</script>

<style scoped>

</style>
