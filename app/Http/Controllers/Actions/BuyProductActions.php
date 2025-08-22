<?php

namespace App\Http\Controllers\Actions;

use App\Http\Requests\BuyProductRequest;
use App\Models\BuyProductInvoice;
use App\Models\BuyProductInvoiceItems;
use App\Models\Companies;
use App\Models\Stock;
use Illuminate\Support\Facades\Auth;

class BuyProductActions
{
    public function store(BuyProductRequest $request)
    {
        // Validate and retrieve the data
        $data = $request->validated();
        $userId = auth()->id();
        $invoiceData = $data['invoiceData'];

        // Create the invoice with auto-incrementing `invoice_number`
        $invoice = BuyProductInvoice::create([
            'user_id' => $userId,
            'company_id' => $invoiceData['company_id'],
            'stock_type_id' => $invoiceData['stock_type_id'],
            'status_invoice_money' => $invoiceData['status_invoice_money'],
            'invoice_date' => $invoiceData['invoice_date'],
            'currency_price' => $invoiceData['currency_price'],
            'price_conversion' => $invoiceData['price_conversion'],
            'discount' => $invoiceData['discount'] ?? 0,
            'status_invoice' => $invoiceData['status_invoice'],
            'total_amount' => $invoiceData['total_amount'],
            'notes' => $invoiceData['notes']
        ]);

        // Iterate through the selected items
        foreach ($data['items'] as $item) {
            $unitPrice = $invoiceData['currency_price'] === 'USD'
                ? $item['buy_price_usd']
                : $item['buy_price_iqd'];

            // Find or create stock record
            $stock = Stock::firstOrCreate(
                [
                    'item_id' => $item['id'],
                    'type_stock_id' => $invoiceData['stock_type_id'],
                    'unit_price' => $unitPrice,
                ],
                [
                    'user_id' => $userId,
                    'quantity' => 0,
                    'unit_price' => $unitPrice,
                    'currency_price' => $invoiceData['currency_price']
                ]
            );

            // Increment stock
            $stock->increment('quantity', $item['quantity']);

            // Calculate subtotal
            $subtotal = $invoiceData['currency_price'] === 'USD'
                ? $item['quantity'] * $item['buy_price_usd']
                : $item['quantity'] * $item['buy_price_iqd'];

            // Create invoice item
            BuyProductInvoiceItems::create([
                'item_id' => $item['id'],
                'buy_product_invoice_id' => $invoice->id,
                'stock_id' => $stock->id,
                'quantity' => $item['quantity'],
                'unit_price' => $unitPrice,
                'subtotal' => $subtotal,
                'currency_price' => $invoiceData['currency_price']
            ]);
        }

        // Increment company debt if status_invoice_money is "1" (debt) and status_invoice is not "draft"
        if ($invoiceData['status_invoice_money'] === '1' && $invoiceData['status_invoice'] !== 'draft') {
            $company = Companies::find($invoiceData['company_id']);
            if ($company) {
                if ($invoiceData['currency_price'] === 'USD') {
                    $company->increment('debt_USD', $invoiceData['total_amount']);
                    $convertedAmount = $invoiceData['total_amount'] * $invoiceData['price_conversion']; // Convert USD to IQD
                    $company->increment('debt_IQD', $convertedAmount);
                } else if ($invoiceData['currency_price'] === 'IQD') {
                    $convertedAmount = $invoiceData['total_amount'] / $invoiceData['price_conversion']; // Convert IQD to USD
                    $company->increment('debt_IQD', $invoiceData['total_amount']);
                    $company->increment('debt_USD', $convertedAmount);
                }
            }
        }


        return redirect()->back()->with(['message' => 'کڕینەکە بە سەرکەوتوویی ئەنجام درا']);
    }

    public function update(BuyProductRequest $request, $id)
    {
        $data = $request->validated();
        $userId = auth()->id();
        $invoice = BuyProductInvoice::findOrFail($id);
        $existingItems = $invoice->items ? $invoice->items->keyBy('item_id') : collect();

        // Revert old debts if the invoice was a debt and not a draft
        if ($invoice->status_invoice_money === '1' && $invoice->status_invoice !== 'draft') {
            $company = Companies::find($invoice->company_id);
            if ($company) {
                if ($invoice->currency_price === 'USD') {
                    $company->decrement('debt_USD', $invoice->total_amount);
                    $company->decrement('debt_IQD', $invoice->total_amount * $invoice->price_conversion);
                } elseif ($invoice->currency_price === 'IQD') {
                    $convertedAmount = $invoice->total_amount / $invoice->price_conversion;
                    $company->decrement('debt_IQD', $invoice->total_amount);
                    $company->decrement('debt_USD', $convertedAmount);
                }
            }
        }

        // Update the invoice data
        $invoice->update([
            'company_id' => $data['invoiceData']['company_id'],
            'stock_type_id' => $data['invoiceData']['stock_type_id'],
            'status_invoice_money' => $data['invoiceData']['status_invoice_money'],
            'invoice_date' => $data['invoiceData']['invoice_date'],
            'currency_price' => $data['invoiceData']['currency_price'],
            'price_conversion' => $data['invoiceData']['price_conversion'],
            'discount' => $data['invoiceData']['discount'] ?? 0,
            'status_invoice' => $data['invoiceData']['status_invoice'],
            'total_amount' => $data['invoiceData']['total_amount'],
            'notes' => $data['invoiceData']['notes']
        ]);

        // Iterate through items in the request
        foreach ($data['items'] as $item) {
            if (empty($item['id']) || empty($item['quantity'])) {
                \Log::error('Invalid item data:', $item);
                continue;
            }

            $unitPrice = $data['invoiceData']['currency_price'] === 'USD' ? $item['buy_price_usd'] : $item['buy_price_iqd'];

            $invoiceItem = BuyProductInvoiceItems::updateOrCreate(
                [
                    'buy_product_invoice_id' => $invoice->id,
                    'item_id' => $item['id'],
                ],
                [
                    'stock_id' => $invoice['stock_type_id'], // Ensure stock_id is set
                    'quantity' => $item['quantity'],
                    'unit_price' => $unitPrice,
                    'subtotal' => $data['invoiceData']['currency_price'] === 'USD' ? $item['quantity'] * $item['buy_price_usd'] : $item['quantity'] * $item['buy_price_iqd'],
                    'currency_price' => $data['invoiceData']['currency_price'],
                ]
            );


            $stock = Stock::firstOrCreate(
                [
                    'item_id' => $item['id'],
                    'type_stock_id' => $data['invoiceData']['stock_type_id'],
                    'unit_price' => $unitPrice,
                ],
                [
                    'user_id' => $userId,
                    'quantity' => 0,
                    'currency_price' => $data['invoiceData']['currency_price'],
                ]
            );

            $previousQuantity = $existingItems[$item['id']]['quantity'] ?? 0;
            $stock->decrement('quantity', $previousQuantity);
            $stock->increment('quantity', $item['quantity']);
        }

        // Adjust quantities for items not in the request (set to 0)
        $requestItemIds = collect($data['items'])->pluck('id');
        $itemsNotInRequest = $existingItems->whereNotIn('item_id', $requestItemIds);

        foreach ($itemsNotInRequest as $item) {
            $invoiceItem = BuyProductInvoiceItems::where('buy_product_invoice_id', $invoice->id)->where('item_id', $item['item_id'])->first();
            if ($invoiceItem) {
                $invoiceItem->update(['quantity' => 0]);
                $stock = Stock::where('item_id', $item['item_id'])->where('type_stock_id', $invoice->stock_type_id)->first();
                if ($stock) {
                    $stock->decrement('quantity', $invoiceItem->quantity);
                }
            }
        }

        // Recalculate debts
        if ($data['invoiceData']['status_invoice_money'] === '1' && $data['invoiceData']['status_invoice'] !== 'draft') {
            $company = Companies::find($data['invoiceData']['company_id']);
            if ($company) {
                if ($data['invoiceData']['currency_price'] === 'USD') {
                    $company->increment('debt_USD', $data['invoiceData']['total_amount']);
                    $convertedAmount = $data['invoiceData']['total_amount'] * $data['invoiceData']['price_conversion'];
                    $company->increment('debt_IQD', $convertedAmount);
                } elseif ($data['invoiceData']['currency_price'] === 'IQD') {
                    $convertedAmount = $data['invoiceData']['total_amount'] / $data['invoiceData']['price_conversion'];
                    $company->increment('debt_IQD', $data['invoiceData']['total_amount']);
                    $company->increment('debt_USD', $convertedAmount);
                }
            }
        }

        return redirect()->back()->with(['message' => 'نوێکردنەوەی کڕینەکە بە سەرکەوتوویی ئەنجام درا']);
    }
}
