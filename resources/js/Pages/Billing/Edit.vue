<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <inertia-link class="text-indigo-400 hover:text-indigo-600" :href="route('billing.invoices.index')">Billing
                </inertia-link>
                <span class="text-indigo-400 font-medium">/</span> Edit Invoice #{{ invoice.id }}
            </h2>
        </template>

        <div class="py-12">
            <div class=" mx-auto">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-4">
                    <form @submit.prevent="submit">
                        <div class="grid grid-cols-2 gap-2 ">
                            <div>
                                <jet-label for="date" value="Date"/>
                                <flat-pickr
                                    v-model="form.date"
                                    class="form-control w-full"
                                    placeholder="Select date"
                                    name="date" required>
                                </flat-pickr>
                                <jet-input-error :message="form.errors.date" class="mt-2"/>

                            </div>
                            <div>
                                <jet-label for="due_date" value="Due Date"/>
                                <flat-pickr
                                    v-model="form.due_date"
                                    class="form-control w-full"
                                    placeholder="Select date"
                                    name="due_date">
                                </flat-pickr>
                                <jet-input-error :message="form.errors.due_date" class="mt-2"/>

                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-2 mt-4">
                            <div>
                                <jet-label for="patient_id" value="Patient"/>
                                <Multiselect
                                    v-model="form.patient_id"
                                    v-bind="patientsMultiSelect"
                                    @change="changePatient"
                                />
                            </div>
                            <div>
                                <jet-label for="doctor_id" value="Doctor"/>
                                <Multiselect
                                    v-model="form.doctor_id"
                                    v-bind="doctorsMultiSelect"
                                />
                            </div>
                        </div>
                        <div class="grid grid-cols-3 gap-2 mt-4 mb-4">
                            <div>
                                <jet-label for="currency_id" value="Currency"/>
                                <Multiselect
                                    v-model="form.currency_id"
                                    mode="single"
                                    :required="true"
                                    :options="$page.props.currencies"
                                />
                                <jet-input-error :message="form.errors.currency_id" class="mt-2"/>
                            </div>
                            <div>
                                <jet-label for="sponsor" value="Sponsor"/>
                                <select
                                    class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm w-full"
                                    name="gender" v-model="form.sponsor" id="sponsor" required>
                                    <option value="cash">Cash</option>
                                    <option value="co_payer">CoPayer</option>
                                </select>
                                <jet-input-error :message="form.errors.sponsor" class="mt-2"/>
                            </div>
                            <div v-if="form.sponsor==='co_payer'">
                                <jet-label for="co_payer_id" value="CoPayer"/>
                                <Multiselect
                                    v-model="form.co_payer_id"
                                    mode="single"
                                    :required="true"
                                    :options="$page.props.coPayers"
                                />
                                <jet-input-error :message="form.errors.co_payer_id" class="mt-2"/>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-2 mt-4 mb-4">
                            <div>
                                <jet-label for="tariff" value="Tariff"/>
                                <Multiselect
                                    v-model="selectedTariff"
                                    v-bind="tariffsMultiSelect"
                                    :object="true"
                                    @select="addItem"
                                    ref="tariffsMultiselectField"
                                />
                            </div>
                        </div>
                        <div class="mt-4 mb-4">
                            <table class="w-full whitespace-no-wrap">
                                <thead class="bg-gray-50">
                                <tr class="text-left font-bold">
                                    <th class="px-6 pt-4 pb-4 font-medium text-gray-500">Name</th>
                                    <th class="px-6 pt-4 pb-4 font-medium text-gray-500">Qty</th>
                                    <th class="px-6 pt-4 pb-4 font-medium text-gray-500">Unit Cost</th>
                                    <th class="px-6 pt-4 pb-4 font-medium text-gray-500">Tax</th>
                                    <th class="px-6 pt-4 pb-4 font-medium text-gray-500">Cash</th>
                                    <th class="px-6 pt-4 pb-4 font-medium text-gray-500">Copayer</th>
                                    <th class="px-6 pt-4 pb-4 font-medium text-gray-500">Total</th>
                                    <th class="px-6 pt-4 pb-4 font-medium text-gray-500">Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr v-for="(item,index) in form.items">
                                    <td class="border-t">
                                        <jet-input type="text" class="mt-1 block w-full"
                                                   v-model="form.items[index].name"
                                                   required autocomplete="off"/>
                                    </td>
                                    <td class="border-t">
                                        <jet-input type="text" class="mt-1 block w-full"
                                                   v-model="form.items[index].qty"
                                                   required autocomplete="off" @input="updateItems"/>
                                    </td>
                                    <td class="border-t">
                                        <jet-input type="text" class="mt-1 block w-full"
                                                   v-model="form.items[index].unit_cost"
                                                   required autocomplete="off" @input="updateItems"/>
                                    </td>
                                    <td class="border-t">
                                        <select @change="updateItems"
                                                class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm w-full"
                                                name="tax_rate_id" v-model="form.items[index].tax_rate_id"
                                                id="tax_rate_id">
                                            <option v-for="tax_rate in taxRates" :value="tax_rate.id">{{
                                                    tax_rate.name
                                                }}
                                            </option>
                                        </select>
                                    </td>
                                    <td class="border-t">
                                        <jet-input type="text" class="mt-1 block w-full"
                                                   v-model="form.items[index].cash_amount" autocomplete="off" @input="updateItems"/>
                                    </td>
                                    <td class="border-t">
                                        <jet-input type="text" class="mt-1 block w-full"
                                                   v-model="form.items[index].co_payer_amount" autocomplete="off" @input="updateItems"/>
                                    </td>
                                    <td class="border-t">
                                        <jet-input type="text" class="mt-1 block w-full"
                                                   v-model="form.items[index].total"
                                                   required autocomplete="off" readonly/>
                                    </td>
                                    <td class="border-t  flex items-center justify-center">
                                        <div class="text-red-400 mt-2">
                                            <font-awesome-icon icon="trash" v-on:click="removeItem(index)"
                                                               class="w-4 h-4 mr-2"></font-awesome-icon>
                                        </div>
                                    </td>
                                </tr>
                                </tbody>
                                <tfoot>
                                <tr>
                                    <th colspan="4"></th>
                                    <th class="px-6 pt-4 pb-4 font-medium text-gray-500">{{ form.cash_amount }}</th>
                                    <th class="px-6 pt-4 pb-4 font-medium text-gray-500">{{ form.co_payer_amount }}</th>
                                    <th class="px-6 pt-4 pb-4 font-medium text-gray-500">{{ form.amount }}</th>
                                    <th></th>
                                </tr>
                                </tfoot>
                            </table>
                        </div>
                        <div class="grid grid-cols-1 gap-2">
                            <div>
                                <jet-label for="terms" value="Terms"/>
                                <textarea-input id="terms" class="mt-1 block w-full"
                                                v-model="form.terms"/>
                                <jet-input-error :message="form.errors.terms" class="mt-2"/>

                            </div>
                            <div>
                                <jet-label for="client_notes" value="Client Notes"/>
                                <textarea-input id="client_notes" class="mt-1 block w-full"
                                                v-model="form.client_notes"/>
                                <jet-input-error :message="form.errors.client_notes" class="mt-2"/>

                            </div>
                            <div>
                                <jet-label for="admin_notes" value="Admin Notes"/>
                                <textarea-input id="admin_notes" class="mt-1 block w-full"
                                                v-model="form.admin_notes"/>
                                <jet-input-error :message="form.errors.admin_notes" class="mt-2"/>

                            </div>
                            <div>
                                <jet-label for="description" value="Description"/>
                                <textarea-input id="description" class="mt-1 block w-full"
                                                v-model="form.description"/>
                                <jet-input-error :message="form.errors.description" class="mt-2"/>

                            </div>
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
        </div>
        <teleport to="head">
            <title>{{ pageTitle }}</title>
            <meta property="og:description" :content="pageDescription">
        </teleport>
    </app-layout>
</template>

<script>
const fetchDoctors = async (query) => {
    let where = ''
    const response = await fetch(
        route('users.search') + '?type=doctor&s=' + query,
        {}
    );

    const data = await response.json(); // Here you have the data that you need
    return data.map((item) => {
        return {value: item.id, label: item.name + '(' + item.practice_number + ')'}
    })
}
const fetchPatients = async (query) => {
    let where = ''

    const response = await fetch(
        route('patients.search') + '?s=' + query,
        {}
    );

    const data = await response.json(); // Here you have the data that you need
    return data.map((item) => {
        return {value: item.id, label: item.name + '(#' + item.id + ')', patient: item.patient}
    })
}
const fetchTariffs = async (query) => {
    let where = ''

    const response = await fetch(
        route('tariffs.search') + '?s=' + query,
        {}
    );

    const data = await response.json(); // Here you have the data that you need
    return data.map((item) => {
        return {value: item.id, label: item.name, item: item}
    })
}
import AppLayout from '@/Layouts/AppLayout.vue'
import JetButton from "@/Jetstream/Button.vue";
import JetInput from "@/Jetstream/Input.vue";
import JetInputError from "@/Jetstream/InputError.vue";
import JetCheckbox from "@/Jetstream/Checkbox.vue";
import JetLabel from "@/Jetstream/Label.vue";
import Select from "@/Jetstream/Select.vue";
import FileInput from "@/Jetstream/FileInput.vue";
import TextareaInput from "@/Jetstream/TextareaInput.vue";


export default {
    props: {
        branches: Object,
        currencies: Object,
        taxRates: Object,
        coPayers: Object,
        invoice: Object,
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
                patient_id: this.invoice.patient_id,
                doctor_id: this.invoice.doctor_id,
                consultation_id: this.invoice.consultation_id,
                co_payer_id: this.invoice.co_payer_id,
                currency_id: this.invoice.currency_id,
                sponsor: this.invoice.sponsor,
                date: this.invoice.date,
                due_date: this.invoice.due_date,
                description: this.invoice.description,
                client_notes: this.invoice.client_notes,
                terms: this.invoice.terms,
                admin_notes: this.invoice.admin_notes,
                tax_total: this.invoice.tax_total,
                amount: this.invoice.amount,
                cash_amount: this.invoice.cash_amount,
                co_payer_amount: this.invoice.co_payer_amount,
                items: this.invoice.invoice_items,
            }),
            doctorsMultiSelect: {
                value: null,
                remark: null,
                placeholder: 'Search for Doctor',
                filterResults: false,
                minChars: 2,
                resolveOnLoad: true,
                delay: 4,
                searchable: true,
                options: async (query) => {
                    return await fetchDoctors(query||this.invoice.doctor_id)
                }
            },
            patientsMultiSelect: {
                remark: null,
                placeholder: 'Search for Patient',
                filterResults: false,
                minChars: 2,
                resolveOnLoad: true,
                delay: 4,
                searchable: true,
                options: async (query) => {
                    return await fetchPatients(query||this.invoice.patient_id)
                }
            },
            tariffsMultiSelect: {
                value: null,
                remark: null,
                placeholder: 'Search for Tariff',
                filterResults: false,
                minChars: 2,
                resolveOnLoad: false,
                delay: 4,
                searchable: true,
                options: async (query) => {
                    return await fetchTariffs(query)
                }
            },
            selectedTariff: null,
            pageTitle: "Edit Invoice",
            pageDescription: "Edit Invoice",
        }

    },
    mounted() {
        this.updateItems();
    },
    methods: {
        submit() {
            this.form.put(this.route('billing.invoices.update', this.invoice.id), {})

        },
        addItem() {
            if (this.selectedTariff) {
                let existing = false;
                this.form.items.forEach(item => {
                    if (item.tariff_id === this.selectedTariff.item.id) {
                        existing = true;
                        item.qty++;
                    }
                });
                if (!existing) {
                    this.form.items.push({
                        id:'',
                        tariff_id: this.selectedTariff.item.id,
                        qty: 1,
                        tax_rate_id: null,
                        name: this.selectedTariff.item.name,
                        cash_amount: parseFloat(this.selectedTariff.item.cash_amount || 0),
                        co_payer_amount: parseFloat(this.selectedTariff.item.co_payer_amount || 0),
                        co_payer_percent: parseFloat(this.selectedTariff.item.co_payer_percent || 0),
                        unit_cost: parseFloat(this.selectedTariff.item.amount || 0),
                        tax_total: 0,
                        total: parseFloat(this.selectedTariff.item.amount || 0),
                        maximum_quantity: this.selectedTariff.item.maximum_quantity,
                        type: this.selectedTariff.item.type,
                        is_claimable: this.selectedTariff.item.is_claimable,
                        needs_approval: this.selectedTariff.item.needs_approval,
                    })
                }
                this.updateItems();
            }
            this.$refs.tariffsMultiselectField.clear()
            this.$refs.tariffsMultiselectField.clearSearch()
            this.$refs.tariffsMultiselectField.refreshOptions()
            this.$refs.tariffsMultiselectField.deselect()
        },
        removeItem(index) {
            this.form.items.splice(index, 1);
            this.updateItems();
        },
        updateItems() {
            this.form.amount = 0;
            this.form.tax_total = 0;
            this.form.cash_amount = 0;
            this.form.co_payer_amount = 0;
            this.form.items.forEach(item => {
                if (item.tax_rate_id) {
                    this.taxRates.forEach(taxRate => {
                        if (taxRate.id === item.tax_rate_id) {
                            if (taxRate.type === 'fixed') {
                                item.tax_total = parseFloat(taxRate.amount);
                            } else {
                                item.tax_total = parseFloat(((item.unit_cost||0) * (item.qty||0) * parseFloat(taxRate.amount)) / 100);
                            }
                        }
                    })
                }
                item.total = parseFloat(item.tax_total||0) + (parseFloat(item.unit_cost||0) * (item.qty||0));
                item.cash_amount = parseFloat(item.total||0) - parseFloat(item.co_payer_amount||0);
                this.form.amount += parseFloat(item.total||0);
                this.form.tax_total += parseFloat(item.tax_total||0);
                this.form.cash_amount += parseFloat(item.cash_amount||0);
                this.form.co_payer_amount += parseFloat(item.co_payer_amount||0);

            });
        },
        changePatient() {

        },

    }
}
</script>
<style scoped>

</style>
