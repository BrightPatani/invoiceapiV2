<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreInvoiceRequest;
use App\Http\Requests\UpdateInvoiceRequest;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Exception;

class InvoiceController extends Controller
{

    public function getAllInvoices(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'per_page' => 'integer|min:1|max:25',
            ]);

            if ($validator->fails()) {
                return $this->errorResponse(
                    'Validation failed',
                    422,
                    $validator->errors()->all()
                );
            }

            $perPage = $request->get('per_page', 15);
            $invoices = Invoice::with('items')
                              ->orderBy('created_at', 'desc')
                              ->paginate($perPage);

            return $this->successResponseWithData(
                $invoices,
                'Invoices retrieved successfully',
                200
            );
        } catch (Exception $e) {
            return $this->errorResponse('Failed to retrieve invoices', 500);
        }
    }

  
    public function createInvoice(StoreInvoiceRequest $request)
    {
        DB::beginTransaction();

        try {
            $invoice = Invoice::create($request->except('items'));

            foreach ($request->items as $itemData) {
                $item = new InvoiceItem($itemData);
                $invoice->items()->save($item);
            }

            $invoice->calculateTotals()->save();
            $invoice->load('items');

            DB::commit();

            return $this->successResponseWithData(
                $invoice,
                'Invoice created successfully',
                201
            );
        } catch (Exception $e) {
            DB::rollBack();
            return $this->errorResponse('Failed to create invoice', 500);
        }
    }

    
    public function getInvoiceById($id)
    {
        try {
            $invoice = Invoice::with('items')->find($id);

            if (!$invoice) {
                return $this->errorResponse('Invoice not found', 404);
            }

            return $this->successResponseWithData(
                $invoice,
                'Invoice retrieved successfully',
                200
            );
        } catch (Exception $e) {
            return $this->errorResponse('Failed to retrieve invoice', 500);
        }
    }

   
    public function editInvoice(UpdateInvoiceRequest $request, $id)
    {
        DB::beginTransaction();

        try {
            $invoice = Invoice::find($id);

            if (!$invoice) {
                return $this->errorResponse('Invoice not found', 404);
            }

            $invoice->update($request->except('items'));

            if ($request->has('items')) {
                $invoice->items()->delete();

                foreach ($request->items as $itemData) {
                    $item = new InvoiceItem($itemData);
                    $invoice->items()->save($item);
                }
            }

            $invoice->calculateTotals()->save();
            $invoice->load('items');

            DB::commit();

            return $this->successResponseWithData(
                $invoice,
                'Invoice updated successfully',
                200
            );
        } catch (Exception $e) {
            DB::rollBack();
            return $this->errorResponse('Failed to update invoice', 500);
        }
    }

    
    public function deleteInvoice($id)
    {
        try {
            $invoice = Invoice::find($id);

            if (!$invoice) {
                return $this->errorResponse('Invoice not found', 404);
            }

            $invoice->delete();

            return $this->successResponse('Invoice deleted successfully', 200);
        } catch (Exception $e) {
            return $this->errorResponse('Failed to delete invoice', 500);
        }
    }
}
