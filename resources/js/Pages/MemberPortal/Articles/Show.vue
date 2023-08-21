<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <inertia-link class="text-indigo-400 hover:text-indigo-600" :href="route('portal.articles.index')">
                    Articles
                </inertia-link>

                <span class="text-indigo-400 font-medium">/</span> {{ article.title }}
            </h2>
        </template>

        <div class=" mx-auto">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-4">
                <h4 class="font-semibold text-xl text-gray-800 leading-tight">{{ article.title }}</h4>
                <div class="flex mt-2 text-gray-400 mb-4">
                    <span v-if="article.category" class="mr-4"><font-awesome-icon
                        icon="folder"/> {{ article.category.name }}</span>
                    <span v-if="article.created_by" class="mr-4"><font-awesome-icon
                        icon="user"/> {{ article.created_by.name }}</span>
                    <span class="mr-4"><font-awesome-icon icon="clock"/> {{ $filters.time(article.created_at) }}</span>
                </div>
                <img v-if="article.featured_image" :src="'/storage/'+article.featured_image" class="w-full mb-4 border"
                     alt="Article Image"/>
                <div v-html="article.description" class="text-gray-600"></div>
            </div>
            <div v-if="article.allow_comments">
                <h3 class="font-bold text-lg mt-4 mb-5">Comments</h3>
                <div class="mt-4">
                    <div class="flex items-end mb-10 reply" v-for="comment in article.comments" :id="'comment-'+comment.id">
                        <img
                            :src="comment.created_by.profile_photo_url"
                            class="rounded-full h-10 w-10 mr-6 border hidden md:block flex-none"
                            v-if="comment.created_by">
                        <div class="w-full relative">
                            <div class="flex-grow rounded shadow p-6 mb-6 bg-white">
                                <div class="text-sm  text-gray-800  mb-2">
                                    <span v-if="comment.created_by">{{ comment.created_by.name }}</span>
                                </div>
                                <div class="break-words" v-html="comment.description"></div>
                            </div>
                            <div class="text-sm text-gray-700 mt-2 ">
                            <span class="tooltip">
                                {{ $filters.timeAgo(comment.created_at) }}
                            </span>
                            </div>
                            <div class="absolute right-0 bottom-0" v-if="can('articles.comments.destroy')">
                                <button type="button" class="text-red-600" @click="deleteAction(comment.id)">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 fill-red-600 text-red-600"
                                         viewBox="0 0 448 512">
                                        <path class="text-red-600"
                                              d="M135.2 17.7L128 32H32C14.3 32 0 46.3 0 64S14.3 96 32 96H416c17.7 0 32-14.3 32-32s-14.3-32-32-32H320l-7.2-14.3C307.4 6.8 296.3 0 284.2 0H163.8c-12.1 0-23.2 6.8-28.6 17.7zM416 128H32L53.2 467c1.6 25.3 22.6 45 47.9 45H346.9c25.3 0 46.3-19.7 47.9-45L416 128z"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <form class="bg-white p-3 mt-5 shadow mb-5 rounded-md" @submit.prevent="saveComment" method="post">
            <textarea class="w-full border border-neutral-300 p-3 rounded-md" name="comment" v-model="form.description"
                      placeholder="enter comment "></textarea>
                    <button
                        class="rounded-md bg-black text-white p-3 w-full disabled:bg-slate-50 disabled:text-slate-500 disabled:border-slate-200 disabled:shadow-none"
                        :class="{ 'opacity-25': form.processing }"
                        :disabled="form.processing">Send Comment
                    </button>
                </form>

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
import JetButton from "@/Jetstream/Button.vue";
import JetInput from "@/Jetstream/Input.vue";
import JetInputError from "@/Jetstream/InputError.vue";
import JetCheckbox from "@/Jetstream/Checkbox.vue";
import JetLabel from "@/Jetstream/Label.vue";
import SelectInput from "@/Jetstream/SelectInput.vue";
import FileInput from "@/Jetstream/FileInput.vue";
import TextareaInput from "@/Jetstream/TextareaInput.vue";
import JetConfirmationModal from '@/Jetstream/ConfirmationModal.vue'
import JetDangerButton from '@/Jetstream/DangerButton.vue'
import JetSecondaryButton from '@/Jetstream/SecondaryButton.vue'

const fetchCourses = async (query) => {
    let where = ''
    const response = await fetch(
        route('portal.courses.search') + '?s=' + query,
        {}
    );

    const data = await response.json(); // Here you have the data that you need
    return data.map((item) => {
        return item;
    })
}
export default {
    props: {
        article: Object,
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
        JetConfirmationModal,
        JetDangerButton,
        JetSecondaryButton,

    },
    data() {
        return {
            form: this.$inertia.form({
                description: '',

            }),
            confirmingDeletion: false,
            selectedRecord: null,
            pageTitle: "View Article",
            pageDescription: "View Article",
        }

    },
    methods: {
        saveComment() {
            this.form.post(this.route('portal.articles.comments.store', this.article.id), {
                preserveState: false,
                onSuccess: () => {
                    this.$inertia.reload();
                }
            })
        },

    }
}
</script>
<style scoped>

</style>
