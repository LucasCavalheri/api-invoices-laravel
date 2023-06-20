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

    public function __construct()
    {
        $this->middleware('auth:sanctum')->only(['store', 'update', 'destroy']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return (new Invoice())->filter($request);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // if (!auth()->user()->tokenCan('invoice-store')) {
        //     return $this->error('Você não tem autorização para criar este invoice', 403);
        // }

        $invoiceRequest = new InvoiceRequest();

        $validator = Validator::make($request->all(), $invoiceRequest->rules());

        if ($validator->fails()) {
            return $this->error('Erro de validação', 422, $validator->errors());
        }

        $invoice = Invoice::create($validator->validated());

        return $this->response('Invoice criado com sucesso', 201, new InvoiceResource($invoice->load('user')));
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
        // if (!auth()->user()->tokenCan('invoice-update')) {
        //     return $this->error('Você não tem autorização para atualizar este invoice', 403);
        // }
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
            $invoice->update($validated);

            return $this->response('Invoice atualizado com sucesso', 200, new InvoiceResource($invoice->load('user')));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // if (!auth()->user()->tokenCan('invoice-delete')) {
        //     return $this->error('Você não tem autorização para deletar este invoice', 403);
        // }

            $invoice = Invoice::find($id);

            if (!$invoice) {
                return $this->error('Invoice não encontrado', 404);
            }

            $invoice->delete();

            return $this->response('Invoice deletado com sucesso', 200);
    }
}
