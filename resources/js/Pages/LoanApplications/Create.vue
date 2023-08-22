<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <inertia-link class="text-indigo-400 hover:text-indigo-600" :href="route('loan_applications.index')">
                    Loan Applications
                </inertia-link>
                <span class="text-indigo-400 font-medium">/</span> Create
            </h2>
        </template>
        <div class=" mx-auto">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-4">
                <form @submit.prevent="submit" enctype="multipart/form-data">
                    <div class="grid grid-cols-1 md:grid-cols-1 gap-2 mt-4">
                        <div>
                            <jet-label for="client_id" value="Client"/>
                            <Multiselect
                                v-model="form.client_id"
                                v-bind="clientsMultiSelect"
                                :required="true"
                            />
                        </div>
                    </div>
                    <div class="mt-4">
                        <jet-label for="loan_product_id" value="Product"/>
                        <Multiselect
                            id="loan_product_id"
                            v-model="form.loan_product_id"
                            :options="availableProducts"
                        />
                        <jet-input-error :message="form.errors.loan_product_id" class="mt-2"/>
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
                        <div v-for="(group,parentIndex) in form.attributes" class="mb-4">
                            <h4 class="font-bold">{{ group.name }}</h4>
                            <div class="grid grid-cols-1 gap-4">
                                <div v-for="(field,index) in group.attributes">
                                    <div
                                        v-if="field.attribute.field_type==='text'||field.attribute.field_type==='number'">
                                        <jet-label :for="'field_'+parentIndex+'_'+index" :value="field.name"/>
                                        <jet-input :id="'field_'+parentIndex+'_'+index"
                                                   :type="field.attribute.field_type"
                                                   class=" block w-full" :required="field.attribute.required"
                                                   v-model="field.value"/>
                                    </div>
                                    <div v-if="field.attribute.field_type==='textarea'">
                                        <jet-label :for="'field_'+parentIndex+'_'+index" :value="field.name"/>
                                        <textarea-input :id="'field_'+parentIndex+'_'+index" class=" block w-full"
                                                        v-if="field.attribute.field_type==='textarea'"
                                                        v-model="field.value" :required="field.attribute.required"/>
                                    </div>
                                    <div v-if="field.attribute.field_type==='dropdown'">
                                        <jet-label :for="'field_'+parentIndex+'_'+index" :value="field.name"/>
                                        <select
                                            class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm w-full"
                                            v-model="field.value" :id="'field_'+parentIndex+'_'+index"
                                            v-if="field.attribute.field_type==='dropdown'"
                                            :required="field.attribute.required">
                                            <option v-for="option in field.attribute.options" :value="option.name">
                                                {{ option.name }}
                                            </option>
                                        </select>
                                    </div>
                                    <div v-if="field.attribute.field_type==='date'">
                                        <jet-label :for="'field_'+parentIndex+'_'+index" :value="field.name"/>
                                        <textarea-input :id="'field_'+parentIndex+'_'+index" class=" block w-full"
                                                        v-if="field.attribute.field_type==='textarea'"
                                                        v-model="field.value"/>
                                        <flat-pickr
                                            v-model="field.value"
                                            class="form-control w-full"
                                            placeholder="Select date"
                                            :required="field.attribute.required"
                                            :id="'field_'+parentIndex+'_'+index">
                                        </flat-pickr>
                                    </div>
                                    <div v-if="field.attribute.field_type==='radio'">
                                        <jet-label :for="'field_'+parentIndex+'_'+index" :value="field.name"/>
                                        <div v-for="option in field.attribute.options" class="flex items-center mb-4">
                                            <input v-model="field.value" :id="'field_'+parentIndex+'_'+index+'_'+option" type="radio" :value="option" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                                            <label :for="'field_'+parentIndex+'_'+index+'_'+option"  class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-300">{{ option }}</label>
                                        </div>
                                    </div>
                                    <div v-if="field.attribute.field_type==='checkbox'">
                                        <jet-label :for="'field_'+parentIndex+'_'+index" :value="field.name"/>
                                        <div v-for="option in field.attribute.options" class="flex items-center mb-4">
                                            <input v-model="field.value" :id="'field_'+parentIndex+'_'+index+'_'+option" type="checkbox" :value="option" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                                            <label :for="'field_'+parentIndex+'_'+index+'_'+option"  class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-300">{{ option }}</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
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

const fetchClients = async (query) => {
    let where = ''
    const response = await fetch(
        route('clients.search') + '?s=' + query,
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
        products: Object,
        branches: Object,
        provinces: Object,
        districts: Object,
        wards: Object,
        villages: Object,
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
                client_id: null,
                staff_id: null,
                loan_product_id: null,
                amount: null,
                description: null,
                date: moment().format("YYYY-MM-DD"),
                status: 'pending',
                attributes: []
            }),
            usersMultiSelect: {
                value: null,
                remark: null,
                placeholder: 'Search for Employee',
                filterResults: false,
                minChars: 2,
                resolveOnLoad: false,
                delay: 4,
                searchable: true,
                options: async (query) => {
                    return await fetchUsers(query)
                }
            },
            clientsMultiSelect: {
                valueProp: 'id',
                label: 'name',
                selected_patient: null,
                placeholder: 'Search for Client',
                filterResults: false,
                minChars: 2,
                resolveOnLoad: false,
                delay: 4,
                searchable: true,
                options: async (query) => {
                    return await fetchClients(query)
                }
            },
            fields: [],
            pageTitle: "Create Loan Application",
            pageDescription: "Create Loan Application",
        }

    },
    methods: {
        submit() {
            this.form.post(this.route('loan_applications.store'), {})
        },
    },
    computed: {
        availableProducts: function () {
            let products = []
            this.products.forEach(item => {
                products.push({
                    value: item.id,
                    label: item.name
                })
            })
            return products
        }
    },
    watch: {
        'form.loan_product_id': function (val) {
            if (val) {
                this.form.attributes = []
                this.products.forEach(item => {
                    if (item.id == val) {
                        this.fields = item.scoring_attributes
                        this.form.attributes= JSON.parse(JSON.stringify(item.scoring_attributes))
                    }
                })
            }
        }
    }
}
</script>
<style scoped>

</style>
