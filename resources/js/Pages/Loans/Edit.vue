<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <inertia-link class="text-indigo-400 hover:text-indigo-600" :href="route('loans.index')">Loans
                </inertia-link>
                <span class="text-indigo-400 font-medium">/</span> Edit
            </h2>
        </template>
        <div class=" mx-auto">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-4">
                <form @submit.prevent="submit" enctype="multipart/form-data">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-2 mt-4">
                        <div>
                            <jet-label for="member_id" value="Member"/>
                            <Multiselect
                                v-model="form.member_id"
                                v-bind="membersMultiSelect"
                                :required="true"
                                :disabled="true"
                            />
                        </div>
                        <div>
                            <jet-label for="staff_id" value="Staff"/>
                            <Multiselect
                                v-model="form.staff_id"
                                v-bind="usersMultiSelect"
                            />
                        </div>
                    </div>
                    <div class="mt-4">
                        <jet-label for="loan_category_id" value="Category"/>
                        <Multiselect
                            id="loan_category_id"
                            v-model="form.loan_category_id"
                            :options="categories"
                        />
                        <jet-input-error :message="form.errors.loan_category_id" class="mt-2"/>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-2 mt-4">
                        <div>
                            <jet-label for="amount" value="Amount"/>
                            <jet-input id="amount" type="text" class="block w-full"
                                       v-model="form.amount"
                                       required/>
                            <jet-input-error :message="form.errors.amount" class="mt-2"/>
                        </div>
                        <div>
                            <jet-label for="date" value="Received Date"/>
                            <flat-pickr
                                v-model="form.date"
                                class="form-control w-full"
                                placeholder="Select date"
                                required
                                id="date"
                                name="date">
                            </flat-pickr>
                            <jet-input-error :message="form.errors.date" class="mt-2"/>

                        </div>
                    </div>
                    <div class="mt-4">
                        <jet-label for="description" value="Description"/>
                        <textarea-input id="description" class="mt-1 block w-full"
                                        v-model="form.description"/>
                        <jet-input-error :message="form.errors.description" class="mt-2"/>

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
import Select from "@/Jetstream/Select.vue";
import FileInput from "@/Jetstream/FileInput.vue";
import TextareaInput from "@/Jetstream/TextareaInput.vue";
const fetchMembers = async (query) => {
    let where = ''
    const response = await fetch(
        route('members.search') + '?s=' + query,
        {}
    );

    const data = await response.json(); // Here you have the data that you need
    return data.map((item) => {
        return item;
    })
}
const fetchUsers = async (query) => {
    let where = ''
    const response = await fetch(
        route('users.search') + '?type=employee&s=' + query,
        {}
    );

    const data = await response.json(); // Here you have the data that you need
    return data.map((item) => {
        return {value: item.id, label: item.name}
    })
}

export default {
    props: {
        categories: Object,
        loan: Object,
    },
    components: {
        Select,
        AppLayout,
        JetButton,
        JetInput,
        JetCheckbox,
        JetLabel,
        JetInputError,
        FileInput,
        TextareaInput,

    },
    data() {
        return {
            form: this.$inertia.form({
                '_method': 'PUT',
                member_id: this.loan.member_id,
                staff_id: this.loan.staff_id,
                loan_category_id: this.loan.loan_category_id,
                amount: this.loan.amount,
                description: this.loan.description,
                date: this.loan.date,
                status: this.loan.status,
            }),
            usersMultiSelect: {
                value: null,
                remark: null,
                placeholder: 'Search for Employee',
                filterResults: false,
                minChars: 2,
                resolveOnLoad: true,
                delay: 4,
                searchable: true,
                options: async (query) => {
                    return await fetchUsers(query||this.loan.staff_id)
                }
            },
            membersMultiSelect: {
                valueProp: 'id',
                label: 'name',
                selected_patient: null,
                placeholder: 'Search for Member',
                filterResults: false,
                minChars: 2,
                resolveOnLoad: true,
                delay: 4,
                searchable: true,
                options: async (query) => {
                    return await fetchMembers(query || this.loan.member_id)
                }
            },
            pageTitle: "Edit Loan",
            pageDescription: "Edit Loan",
        }

    },
    methods: {
        submit() {
            this.form.post(this.route('loans.update', this.loan.id), {})
        },

    },
    computed: {

    }
}
</script>
<style scoped>

</style>
