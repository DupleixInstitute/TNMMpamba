<?php

namespace App\Http\Controllers;

use App\Events\InvoiceReconciled;
use App\Models\Branch;
use App\Models\CourseMaterial;
use App\Models\Currency;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\InvoicePayment;
use App\Models\PaymentType;
use App\Models\Tariff;
use App\Models\TaxRate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class BillingController extends Controller
{
    public function index()
    {
        $invoices = Invoice::with(['doctor', 'member', 'coPayer', 'invoiceItems'])->filter(\request()->only('search'))
            ->paginate(20);
        return Inertia::render('Billing/Index', [
            'filters' => \request()->all('search', 'status', 'co_payer_id', 'sponsor', 'doctor_id', 'member_id'),
            'invoices' => $invoices,
        ]);
    }

    public function show(Invoice $invoice)
    {
        $invoice->load(['doctor', 'member', 'coPayer', 'invoiceItems', 'invoicePayments', 'invoicePayments.paymentType', 'currency']);
        return Inertia::render('Billing/Show', [
            'invoice' => $invoice,
            'coPayers' => CourseMaterial::get()->map(function ($item) {
                return [
                    'value' => $item->id,
                    'label' => $item->name
                ];
            }),
            'paymentTypes' => PaymentType::where('active', 1)->get()->map(function ($item) {
                return [
                    'value' => $item->id,
                    'label' => $item->name
                ];
            }),
        ]);
    }

    public function create()
    {

        return Inertia::render('Billing/Create', [
            'taxRates' => TaxRate::get(),
            'branches' => Branch::get(),
            'currencies' => Currency::get()->transform(function ($currency) {
                return [
                    'value' => $currency->id,
                    'label' => $currency->name,
                ];
            }),
            'coPayers' => CourseMaterial::get()->transform(function ($coPayer) {
                return [
                    'value' => $coPayer->id,
                    'label' => $coPayer->name,
                ];
            }),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'date' => ['required', 'date'],
            'currency_id' => ['required'],
            'items' => ['required', 'array'],
            'sponsor' => ['required'],
            'co_payer_id' => ['required_if:sponsor,co_payer'],
        ]);
        $invoice = new Invoice();
        $invoice->created_by_id = Auth::id();
        $invoice->currency_id = $request->currency_id;
        $invoice->member_id = $request->member_id;
        $invoice->doctor_id = $request->doctor_id;
        $invoice->co_payer_id = $request->co_payer_id;
        $invoice->date = $request->date;
        $invoice->due_date = $request->due_date ?: null;
        $invoice->sponsor = $request->sponsor;
        $invoice->tax_total = $request->tax_total;
        $invoice->amount = $request->amount;
        $invoice->cash_amount = $request->cash_amount;
        $invoice->co_payer_amount = $request->co_payer_amount;
        $invoice->balance = $request->amount;
        $invoice->description = $request->description;
        $invoice->terms = $request->terms;
        $invoice->client_notes = $request->client_notes;
        $invoice->admin_notes = $request->admin_notes;
        $invoice->save();
        //invoice items
        foreach ($request->items as $key) {
            $tariff = Tariff::find($key);
            $invoiceItem = new InvoiceItem();
            $invoiceItem->invoice_id = $invoice->id;
            $invoiceItem->tariff_id = $key['tariff_id'];
            $invoiceItem->qty = $key['qty'];
            $invoiceItem->unit_cost = $key['unit_cost'];
            $invoiceItem->co_payer_amount = $key['co_payer_amount'];
            $invoiceItem->cash_amount = $key['cash_amount'];
            $invoiceItem->tax_total = $key['tax_total'];
            $invoiceItem->total = $key['total'];
            $invoiceItem->type = $key['type'];
            $invoiceItem->is_claimable = $key['is_claimable'] ? 1 : 0;
            $invoiceItem->name = $key['name'];
            $invoiceItem->save();
        }

        $invoice->save();
        activity()
            ->performedOn($invoice)
            ->log('Create Invoice');
        return redirect()->route('billing.show', $invoice->id)->with('success', 'Invoice created successfully.');

    }

    public function edit(Invoice $invoice)
    {
        $invoice->load(['doctor', 'member', 'coPayer', 'invoiceItems']);
        return Inertia::render('Billing/Edit', [
            'invoice' => $invoice,
            'taxRates' => TaxRate::get(),
            'branches' => Branch::get(),
            'currencies' => Currency::get()->transform(function ($currency) {
                return [
                    'value' => $currency->id,
                    'label' => $currency->name,
                ];
            }),
            'coPayers' => CourseMaterial::get()->transform(function ($coPayer) {
                return [
                    'value' => $coPayer->id,
                    'label' => $coPayer->name,
                ];
            }),
        ]);
    }

    public function update(Request $request, Invoice $invoice)
    {
        $request->validate([
            'date' => ['required', 'date'],
            'currency_id' => ['required'],
            'items' => ['required', 'array'],
            'sponsor' => ['required'],
            'co_payer_id' => ['required_if:sponsor,co_payer'],
        ]);
        $invoice->currency_id = $request->currency_id;
        $invoice->member_id = $request->member_id;
        $invoice->doctor_id = $request->doctor_id;
        $invoice->co_payer_id = $request->co_payer_id;
        $invoice->date = $request->date;
        $invoice->due_date = $request->due_date ?: null;
        $invoice->sponsor = $request->sponsor;
        $invoice->tax_total = $request->tax_total;
        $invoice->amount = $request->amount;
        $invoice->cash_amount = $request->cash_amount;
        $invoice->co_payer_amount = $request->co_payer_amount;
        $invoice->balance = $invoice->amount - $invoice->invoicePayments->sum('amount');
        $invoice->description = $request->description;
        $invoice->terms = $request->terms;
        $invoice->client_notes = $request->client_notes;
        $invoice->admin_notes = $request->admin_notes;
        if ($invoice->balance <= 0) {
            $invoice->status = 'paid';
        } elseif ($invoice->balance > 0 && $invoice->balance < $invoice->amount) {
            $invoice->status = 'partially_paid';
        } else {
            $invoice->status = 'unpaid';
        }
        $invoice->save();
        //invoice items
        $existing = $invoice->invoiceItems->pluck('id')->all();
        foreach ($request->items as $key) {
            if (in_array($key['id'], $existing)) {
                $invoiceItem = InvoiceItem::find($key['id']);
            } else {
                $invoiceItem = new InvoiceItem();
                $invoiceItem->tariff_id = $key['tariff_id'];
                $invoiceItem->invoice_id = $invoice->id;
            }
            $invoiceItem->qty = $key['qty'];
            $invoiceItem->unit_cost = $key['unit_cost'];
            $invoiceItem->co_payer_amount = $key['co_payer_amount'];
            $invoiceItem->cash_amount = $key['cash_amount'];
            $invoiceItem->tax_total = $key['tax_total'];
            $invoiceItem->total = $key['total'];
            $invoiceItem->type = $key['type'];
            $invoiceItem->is_claimable = $key['is_claimable'] ? 1 : 0;
            $invoiceItem->name = $key['name'];
            $invoiceItem->save();
            foreach ($existing as $k => $v) {
                if ($v == $key['id']) {
                    unset($existing[$k]);
                }
            }
        }
        foreach ($existing as $key) {
            InvoiceItem::destroy($key);
        }
        $invoice->save();
        activity()
            ->performedOn($invoice)
            ->log('Update Invoice');
        return redirect()->route('billing.show', $invoice->id)->with('success', 'Invoice updated successfully.');

    }

    public function destroy(Invoice $invoice)
    {
        if (App::environment('demo')) {
            return redirect()->back()->with('error', 'Deleting the demo invoice is not allowed.');
        }
        $invoice->invoiceItems->delete();
        $invoice->invoicePayments->delete();
        $invoice->delete();
        activity()
            ->performedOn($invoice)
            ->log('Delete Invoice');
        return redirect()->route('billing.index')->with('success', 'Invoice deleted successfully.');

    }

    public function reconcile(Invoice $invoice)
    {
        $invoice->load(['invoiceItems']);
        return Inertia::render('Billing/Reconcile', [
            'invoice' => $invoice,
            'paymentTypes' => PaymentType::get()->transform(function ($currency) {
                return [
                    'value' => $currency->id,
                    'label' => $currency->name,
                ];
            }),
            'currencies' => Currency::get()->transform(function ($currency) {
                return [
                    'value' => $currency->id,
                    'label' => $currency->name,
                ];
            }),
            'coPayers' => CourseMaterial::get()->transform(function ($coPayer) {
                return [
                    'value' => $coPayer->id,
                    'label' => $coPayer->name,
                ];
            }),
        ]);
    }

    public function updateReconcile(Request $request, Invoice $invoice)
    {
        $invoice->load(['invoiceItems']);
        if ($request->record_payment) {
            $request->validate([
                'date' => ['required', 'date'],
                'amount' => ['required'],
                'payment_type_id' => ['required'],
            ]);
            $invoicePayment = new InvoicePayment();
            $invoicePayment->created_by_id = Auth::id();
            $invoicePayment->invoice_id = $invoice->id;
            $invoicePayment->currency_id = $request->currency_id ?: $invoice->currency_id;
            $invoicePayment->member_id = $invoice->member_id;
            $invoicePayment->paid_by = 'co_payer';
            $invoicePayment->co_payer_id = $request->co_payer_id;
            $invoicePayment->date = $request->date;
            $invoicePayment->receipt = $request->receipt;
            $invoicePayment->payment_type_id = $request->payment_type_id;
            $invoicePayment->amount = $request->amount;
            $invoicePayment->description = $request->description;
            $invoicePayment->save();
            $invoice->refresh();
            $invoice->balance = $invoice->amount - $invoice->invoicePayments->sum('amount');
            if ($invoice->balance <= 0) {
                $invoice->status = 'paid';
            } elseif ($invoice->balance > 0 && $invoice->balance < $invoice->amount) {
                $invoice->status = 'partially_paid';
            } else {
                $invoice->status = 'unpaid';
            }
            $invoice->save();
        }
        foreach ($request->items as $item) {
            $invoiceItem = InvoiceItem::find($item['id']);
            $invoiceItem->award = $item['award'];
            $invoiceItem->shortfall = $item['shortfall'] ?? null;
            $invoiceItem->shortfall_reason = $item['shortfall_reason'] ?? null;
            $invoiceItem->save();
        }
        $invoice->reconciled = 1;
        $invoice->save();
        event(new InvoiceReconciled($invoice));
        activity()
            ->performedOn($invoice)
            ->log('Claimed Invoice');
        $params = ['invoice' => $invoice->id];
        return redirect()->route('billing.show', $params)->with('success', 'Invoice reconciled successfully.');

    }
}
