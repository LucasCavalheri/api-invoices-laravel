<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\InvoiceRequest;
use App\Http\Resources\V1\InvoiceResource;
use App\Models\Invoice;
use App\Traits\HttpResponses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class InvoiceController extends Controller
{
    use HttpResponses;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return InvoiceResource::collection(Invoice::with('user')->get());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $invoiceRequest = new InvoiceRequest();

        $validator = Validator::make($request->all(), $invoiceRequest->rules());

        if ($validator->fails()) {
            return $this->error('Erro de validação', 422, $validator->errors());
        }

        $invoice = Invoice::create($validator->validated());

        if (!$invoice) {
            return $this->error('Fatura não foi criada', 500);
        }

        return $this->response('Fatura criada com sucesso', 201, new InvoiceResource($invoice->load('user')));
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $invoice = Invoice::with('user')->where('id', $id)->first();

        if (!$invoice) {
            return response()->json([
                'message' => 'Invoice not found',
            ], 404);
        };

        return new InvoiceResource($invoice);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
