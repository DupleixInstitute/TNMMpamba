<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Articles
            </h2>
        </template>
        <div class=" mx-auto mb-4 flex justify-between items-center">
            <filter-search v-model="form.search" class="w-full max-w-md mr-4" @reset="reset">
                <div class="w-80 mt-2 px-4 py-6 shadow-xl bg-white rounded">
                </div>
            </filter-search>
        </div>
        <div class=" mx-auto">
            <div class="bg-white rounded shadow overflow-x-auto">
                <table class="w-full whitespace-no-wrap">
                    <thead class="bg-gray-50">
                    <tr class="text-left font-bold">
                        <th class="px-6 pt-4 pb-4 font-medium text-gray-500">Title</th>
                        <th class="px-6 pt-4 pb-4 font-medium text-gray-500">Author</th>
                        <th class="px-6 pt-4 pb-4 font-medium text-gray-500">Category</th>
                        <th class="px-6 pt-4 pb-4 font-medium text-gray-500">Date</th>
                        <th class="px-6 pt-4 pb-4 font-medium text-gray-500">Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="article in articles.data" :key="article.id"
                        class="hover:bg-gray-100 focus-within:bg-gray-100">
                        <td class="border-t">
                            <inertia-link
                                :href="route('portal.articles.show', article.id)"
                                tabindex="-1" class="px-6 py-4 text-indigo-600 hover:text-indigo-900">
                                {{ article.title }}
                            </inertia-link>
                        </td>
                        <td class="border-t">
                            <span v-if="article.created_by"
                                tabindex="-1" class="px-6 py-4 flex items-center">
                                <span>{{ article.created_by.name }}</span>
                            </span>
                        </td>
                        <td class="border-t">
                           <span class="px-6 py-4 flex items-center">
                               <span v-if="article.category">
                                   {{ article.category.name }}
                               </span>
                            </span>
                        </td>
                        <td class="border-t">
                             <span class="px-6 py-4 flex items-center">
                                {{ $filters.time(article.created_at) }}
                            </span>
                        </td>
                        <td class="border-t w-px pr-2">
                            <div class=" flex items-center space-x-2">
                                <inertia-link
                                              :href="route('portal.articles.show', article.id)"
                                              tabindex="-1" class="text-indigo-600 hover:text-indigo-900">
                                    View
                                </inertia-link>
                            </div>
                        </td>
                    </tr>
                    <tr v-if="articles.data.length === 0">
                        <td class="border-t px-6 py-4 text-center" colspan="5">No Articles found.</td>
                    </tr>
                    </tbody>
                </table>

            </div>
            <pagination :links="articles.links"/>
        </div>
        <teleport to="head">
            <title>{{ pageTitle }}</title>
            <meta property="og:description" :content="pageDescription">
        </teleport>
    </app-layout>

</template>

<script>
import AppLayout from '@/Layouts/AppLayout.vue'
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

export default {
    components: {
        AppLayout,
        Pagination,
        FilterSearch,
        JetLabel,
        SelectInput,
        JetConfirmationModal,
        JetDangerButton,
        JetSecondaryButton,
    },
    props: {
        articles: Object,
        filters: Object,

    },
    data() {
        return {
            form: {
                search: this.filters.search,
                processing: false
            },
            confirmingDeletion: false,
            selectedRecord: null,
            pageTitle: "Articles",
            pageDescription: "Manage Articles",

        }
    },
    watch: {
        form: {
            handler: _.debounce(function () {
                let query = pickBy(this.form)
                this.$inertia.get(this.route('portal.articles.index', Object.keys(query).length ? query : {}))
            }, 500),
            deep: true,
        },
    },
    methods: {
        reset() {
            this.form = mapValues(this.form, () => null)
        },
    },
}
</script>

<style scoped>

</style>
