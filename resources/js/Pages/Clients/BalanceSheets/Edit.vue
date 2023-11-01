<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <inertia-link class="text-indigo-400 hover:text-indigo-600" :href="route('clients.index')">Clients
                </inertia-link>
                <span class="text-indigo-400 font-medium">/</span> {{ client.name }}
            </h2>
        </template>
        <div class="mx-auto">
            <div class="md:flex md:items-start">
                <div class="bg-white relative shadow-xl mb-4 mt-20 w-full md:w-3/12">
                    <div class="col-span-12 lg:col-span-4 xxl:col-span-3 flex lg:block flex-col-reverse">
                        <div class="intro-y box mt-5 lg:mt-0">
                            <client-menu :client="client"></client-menu>
                        </div>
                    </div>

                </div>
                <div class="w-full md:w-9/12 p-4 md:ml-4 bg-white sm:mt-4">
                    <div class="flex justify-between ">
                        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Edit Balance Sheet</h2>
                        <inertia-link class="btn btn-blue" :href="route('clients.balance_sheets.index',client.id)">
                            <span>Back </span>
                        </inertia-link>
                    </div>
                    <div class="mt-4">
                        <form @submit.prevent="submit" enctype="multipart/form-data">
                            <div>
                                <jet-label for="year" value="Year(4 digits)"/>
                                <jet-input id="year" type="number" required class="block w-full" v-model="form.year"/>
                                <jet-input-error :message="form.errors.year" class="mt-2"/>
                            </div>
                            <table class="mt-4 w-full whitespace-no-wrap table-auto">
                                <tbody>
                                <tr>
                                    <td colspan="2" class="px-6 py-4">
                                        <h1 class="font-bold">Sales</h1>
                                    </td>
                                </tr>
                                <tr v-for="(item,index) in form.charts.sales" :key="index"
                                    class="hover:bg-gray-100 focus-within:bg-gray-100">
                                    <td class="border-t px-6 py-4">
                                    <span class=" flex items-center">
                                    {{ item.name }}
                                    </span>
                                    </td>
                                    <td class="border-t px-6 py-4">
                                        <jet-input id="name" @keyup="updateTotal" type="number"
                                                   class="mt-1 block w-full"
                                                   v-model="item.amount"/>
                                    </td>
                                </tr>
                                <tr
                                    class="hover:bg-gray-100 focus-within:bg-gray-100">
                                    <td class="border-t px-6 py-4">
                                    <span class="font-bold">
                                   Net Sales
                                    </span>
                                    </td>
                                    <td class="border-t px-6 py-4 text-right">
                                        <span class="font-bold">{{ $filters.currency(form.total_sales) }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2" class="px-6 py-4">
                                        <h1 class="font-bold">Cost of goods sold</h1>
                                    </td>
                                </tr>
                                <tr v-for="(item,index) in form.charts.cost_of_goods_sold" :key="index"
                                    class="hover:bg-gray-100 focus-within:bg-gray-100">
                                    <td class="border-t px-8 py-4">
                                    <span class=" flex items-center">
                                    {{ item.name }}
                                    </span>
                                    </td>
                                    <td class="border-t px-6 py-4">
                                        <jet-input id="name" @keyup="updateTotal" type="number"
                                                   class="mt-1 block w-full"
                                                   v-model="item.amount"/>
                                    </td>
                                </tr>
                                <tr
                                    class="hover:bg-gray-100 focus-within:bg-gray-100">
                                    <td class="border-t px-6 py-4">
                                    <span class="font-bold">
                                   Gross Margin
                                    </span>
                                    </td>
                                    <td class="border-t px-6 py-4 text-right">
                                        <span class="font-bold">{{ $filters.currency(form.total_gross_margin) }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2" class="px-6 py-4">
                                        <h1 class="font-bold">Operating expenses</h1>
                                    </td>
                                </tr>
                                <tr v-for="(item,index) in form.charts.operating_expenses" :key="index"
                                    class="hover:bg-gray-100 focus-within:bg-gray-100">
                                    <td class="border-t px-8 py-4">
                                    <span class=" flex items-center">
                                    {{ item.name }}
                                    </span>
                                    </td>
                                    <td class="border-t px-6 py-4">
                                        <jet-input id="name" @keyup="updateTotal" type="number"
                                                   class="mt-1 block w-full"
                                                   v-model="item.amount"/>
                                    </td>
                                </tr>
                                <tr
                                    class="hover:bg-gray-100 focus-within:bg-gray-100">
                                    <td class="border-t px-6 py-4">
                                    <span class="font-bold">
                                   Operating Income
                                    </span>
                                    </td>
                                    <td class="border-t px-6 py-4 text-right">
                                        <span class="font-bold">{{
                                                $filters.currency(form.total_operating_profit)
                                            }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2" class="px-6 py-4">
                                        <h1 class="font-bold">Other income and expenses</h1>
                                    </td>
                                </tr>
                                <tr v-for="(item,index) in form.charts.other_expenses" :key="index"
                                    class="hover:bg-gray-100 focus-within:bg-gray-100">
                                    <td class="border-t px-8 py-4">
                                    <span class=" flex items-center">
                                    {{ item.name }}
                                    </span>
                                    </td>
                                    <td class="border-t px-6 py-4">
                                        <jet-input id="name" @keyup="updateTotal" type="number"
                                                   class="mt-1 block w-full"
                                                   v-model="item.amount"/>
                                    </td>
                                </tr>
                                <tr v-for="(item,index) in form.charts.other_income" :key="index"
                                    class="hover:bg-gray-100 focus-within:bg-gray-100">
                                    <td class="border-t px-8 py-4">
                                    <span class=" flex items-center">
                                    {{ item.name }}
                                    </span>
                                    </td>
                                    <td class="border-t px-6 py-4">
                                        <jet-input id="name" @keyup="updateTotal" type="number"
                                                   class="mt-1 block w-full"
                                                   v-model="item.amount"/>
                                    </td>
                                </tr>
                                <tr
                                    class="hover:bg-gray-100 focus-within:bg-gray-100">
                                    <td class="border-t px-6 py-4">
                                    <span class="font-bold">
                                        Income before tax
                                    </span>
                                    </td>
                                    <td class="border-t px-6 py-4 text-right">
                                        <span class="font-bold">{{
                                                $filters.currency(form.total_income_before_tax)
                                            }}</span>
                                    </td>
                                </tr>
                                <tr v-for="(item,index) in form.charts.income_tax" :key="index"
                                    class="hover:bg-gray-100 focus-within:bg-gray-100">
                                    <td class="border-t px-8 py-4">
                                    <span class=" flex items-center">
                                    {{ item.name }}
                                    </span>
                                    </td>
                                    <td class="border-t px-6 py-4">
                                        <jet-input id="name" @keyup="updateTotal" type="number"
                                                   class="mt-1 block w-full"
                                                   v-model="item.amount"/>
                                    </td>
                                </tr>
                                <tr
                                    class="hover:bg-gray-100 focus-within:bg-gray-100">
                                    <td class="border-t px-6 py-4">
                                    <span class="font-bold">
                                        Net Profit/(Loss)
                                    </span>
                                    </td>
                                    <td class="border-t px-6 py-4 text-right">
                                        <span class="font-bold">{{ $filters.currency(form.net_profit) }}</span>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
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
        </div>
        <teleport to="head">
            <title>{{ pageTitle }}</title>
            <meta property="og:description" :content="pageDescription">
        </teleport>
    </app-layout>

</template>

<script>
import AppLayout from '@/Layouts/AppLayout.vue'
import Icon from '@/Jetstream/Icon.vue'
import mapValues from 'lodash/mapValues'
import pickBy from 'lodash/pickBy'
import throttle from 'lodash/throttle'
import JetLabel from '@/Jetstream/Label.vue'
import SelectInput from '@/Jetstream/SelectInput.vue'
import JetConfirmationModal from '@/Jetstream/ConfirmationModal.vue'
import JetDangerButton from '@/Jetstream/DangerButton.vue'
import JetSecondaryButton from '@/Jetstream/SecondaryButton.vue'
import ClientMenu from '@/Pages/Clients/ClientMenu.vue'
import JetInput from "@/Jetstream/Input.vue";
import JetInputError from "@/Jetstream/InputError.vue";
import JetButton from "@/Jetstream/Button.vue";
import JetCheckbox from "@/Jetstream/Checkbox.vue";
import TextareaInput from "@/Jetstream/TextareaInput.vue";

export default {
    components: {
        AppLayout,
        Icon,
        TextareaInput,
        JetLabel,
        JetInput,
        JetInputError,
        SelectInput,
        JetConfirmationModal,
        JetDangerButton,
        JetSecondaryButton,
        ClientMenu,
        JetButton,
        JetCheckbox,
    },
    props: {
        client: Object,
        sheet: Object,
        sales: Object,
        costsOfGoodsSold: Object,
        expenses: Object,
        otherExpenses: Object,
        otherIncome: Object,
        incomeTax: Object,

    },
    data() {
        return {
            form: this.$inertia.form({
                '_method': 'PUT',
                year: this.sheet.year,
                description: this.sheet.description,
                as_at_date: this.sheet.as_at_date,
                total_sales: 0,
                total_operating_expenses: 0,
                total_gross_margin: 0,
                total_other_income: 0,
                total_other_expenses: 0,
                total_income_before_tax: 0,
                net_profit: 0,
                total_operating_profit: 0,
                total_cost_of_goods_sold: 0,
                total_income_tax: 0,
                charts:this.sheet.charts
            }),
            pageTitle: "Edit Statement",
            pageDescription: "Edit Statement",

        }
    },
    mounted() {
        this.updateTotal()
    },
    methods: {
        submit() {
            this.form.post(this.route('clients.balance_sheets.update', this.sheet.id), {})
        },
        updateTotal() {
            this.form.total_sales = 0;
            this.form.total_operating_expenses = 0;
            this.form.total_gross_margin = 0;
            this.form.total_other_income = 0;
            this.form.total_other_expenses = 0;
            this.form.total_income_before_tax = 0;
            this.form.net_profit = 0;
            this.form.total_operating_profit = 0;
            this.form.total_cost_of_goods_sold = 0;
            this.form.total_income_tax = 0;
            this.form.charts.sales.forEach(item => {
                this.form.total_sales += parseFloat(item.amount || '0')
            })
            this.form.charts.cost_of_goods_sold.forEach(item => {
                this.form.total_cost_of_goods_sold += parseFloat(item.amount || '0')
            })
            this.form.total_gross_margin = this.form.total_sales - this.form.total_cost_of_goods_sold;
            this.form.charts.operating_expenses.forEach(item => {
                this.form.total_operating_expenses += parseFloat(item.amount || '0')
            })
            this.form.total_operating_profit = this.form.total_gross_margin - this.form.total_operating_expenses;
            this.form.charts.other_income.forEach(item => {
                this.form.total_other_income += parseFloat(item.amount || '0')
            })
            this.form.total_income_before_tax = this.form.total_operating_profit + this.form.total_other_income;
            this.form.charts.other_expenses.forEach(item => {
                this.form.total_other_expenses += parseFloat(item.amount || '0')
            })
            this.form.total_income_before_tax -= this.form.total_other_expenses;
            this.form.charts.income_tax.forEach(item => {
                this.form.total_income_tax += parseFloat(item.amount || '0')
            })
            this.form.net_profit = this.form.total_income_before_tax - this.form.total_income_tax;
        }
    },
}
</script>

<style scoped>

</style>
