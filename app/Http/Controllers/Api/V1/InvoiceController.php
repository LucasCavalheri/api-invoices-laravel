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
            return $this->error('Invoice não encontrado', 404);
        };

        return new InvoiceResource($invoice);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $invoiceRequest = new InvoiceRequest();

        $validator = Validator::make($request->all(), $invoiceRequest->rules());

        if ($validator->fails()) {
            return $this->error('Erro de validação', 422, $validator->errors());
        }

        $invoice = Invoice::find($id);

        if (!$invoice) {
            return $this->error('Invoice não encontrado', 404);
        }

        $validated = $validator->validated();

        if (isset($validated['paid']) && !$validated['paid']) {
            $validated['payment_date'] = null;
        }

        $updated = $invoice->update($validated);

        if (!$updated) {
            return $this->error('Invoice não foi atualizado', 500);
        }

        return $this->response('Invoice atualizado com sucesso', 200, new InvoiceResource($invoice->load('user')));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $invoice = Invoice::find($id);

        if (!$invoice) {
            return $this->error('Invoice não encontrado', 404);
        }

        $deleted = $invoice->delete();

        if (!$deleted) {
            return $this->error('Invoice não foi deletado', 500);
        }

        return $this->response('Invoice deletado com sucesso', 200);
    }
}
