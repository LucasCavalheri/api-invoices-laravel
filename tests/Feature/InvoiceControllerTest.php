<?php

namespace Tests\Feature;

use App\Http\Controllers\Api\V1\InvoiceController;
use App\Http\Resources\V1\InvoiceResource;
use App\Models\Invoice;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Mockery;
use Tests\TestCase;

class InvoiceControllerTest extends TestCase
{
    protected function tearDown(): void
    {
        parent::tearDown();
        Mockery::close();
    }

    public function testIndex()
    {
        $request = new Request();
        $invoice = new Invoice();
        $result = $invoice->filter($request);

        $this->assertEquals($result, (new InvoiceController())->index($request));
    }

    public function testStore()
    {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;
        $headers = ['Authorization' => 'Bearer ' . $token];

        $invoiceData = [
            'user_id' => $user->id,
            'type' => 'C',
            'paid' => 0,
            'payment_date' => null,
            'value' => 100.00,
        ];

        $response = $this->postJson('/api/v1/invoices', $invoiceData, $headers);

        $response->assertStatus(Response::HTTP_CREATED)
            ->assertJson([
                'message' => 'Invoice criado com sucesso',
            ])
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'user' => [
                        'id',
                        'firstName',
                        'lastName',
                        'fullName',
                        'email',
                    ],
                    'type',
                    'value',
                    'paid',
                    'paymentDate',
                    'paymentSince',
                ],
            ]);

        $this->assertDatabaseHas('invoices', $invoiceData);
    }

    public function testStoreWithInvalidRequestData()
    {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;
        $headers = ['Authorization' => 'Bearer ' . $token];

        $invoiceData = [
            'type' => 'A',
            'paid' => 3,
            'payment_date' => null,
        ];

        $response = $this->postJson('/api/v1/invoices', $invoiceData, $headers);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonStructure([
                'message',
                'errors' => [
                    'user_id',
                    'type',
                    'paid',
                    'value',
                ],
            ]);
    }

    public function testShow()
    {
        $invoice = Invoice::factory()->create();

        $response = $this->getJson('/api/v1/invoices/' . $invoice->id);

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'user' => [
                        'id',
                        'firstName',
                        'lastName',
                        'fullName',
                        'email',
                    ],
                    'type',
                    'value',
                    'paid',
                    'paymentDate',
                    'paymentSince',
                ],
            ])
            ->assertJson([
                'data' => (new InvoiceResource($invoice))->toArray(request()),
            ]);
    }

    public function testShowWithInvalidId()
    {
        $response = $this->getJson('/api/v1/invoices/9999');

        $response->assertStatus(Response::HTTP_NOT_FOUND)
            ->assertJson([
                'message' => 'Invoice não encontrado',
            ]);
    }

    public function testUpdate()
    {
        $user = User::factory()->create();
        $invoice = Invoice::factory()->create([
            'user_id' => $user->id,
        ]);
        $token = $user->createToken('test-token')->plainTextToken;
        $headers = ['Authorization' => 'Bearer ' . $token];

        $updatedInvoiceData = [
            'user_id' => $user->id,
            'type' => 'B',
            'paid' => 1,
            'value' => 150.00,
        ];

        $response = $this->putJson('/api/v1/invoices/' . $invoice->id, $updatedInvoiceData, $headers);

        $response->assertStatus(Response::HTTP_OK)
            ->assertJson([
                'message' => 'Invoice atualizado com sucesso',
            ])
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'user' => [
                        'id',
                        'firstName',
                        'lastName',
                        'fullName',
                        'email',
                    ],
                    'type',
                    'value',
                    'paid',
                    'paymentDate',
                    'paymentSince',
                ],
            ]);

        $this->assertDatabaseHas('invoices', $updatedInvoiceData);
    }

    public function testUpdateWithInvalidRequestData()
    {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;
        $headers = ['Authorization' => 'Bearer ' . $token];

        $invoiceData = [
            'user_id' => $user->id,
            'type' => 'B',
            'paid' => 1,
            'value' => 150.00,
        ];

        $response = $this->putJson('/api/v1/invoices/' . '999999', $invoiceData, $headers);

        $response->assertStatus(Response::HTTP_NOT_FOUND)
            ->assertJson([
                'message' => 'Invoice não encontrado',
            ])
            ->assertJsonStructure([
                'message',
                'status',
                'errors',
                'data'
            ]);
    }

    public function testUpdateWithInvalidId()
    {
        $user = User::factory()->create();
        $invoice = Invoice::factory()->create([
            'user_id' => $user->id,
        ]);
        $token = $user->createToken('test-token')->plainTextToken;
        $headers = ['Authorization' => 'Bearer ' . $token];

        $invoiceData = [
            'type' => 'A',
            'paid' => 3,
            'payment_date' => null,
        ];

        $response = $this->putJson('/api/v1/invoices/' . $invoice->id, $invoiceData, $headers);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJson([
                'message' => 'Erro de validação',
            ])
            ->assertJsonStructure([
                'message',
                'status',
                'errors' => [
                    'user_id',
                    'type',
                    'paid',
                    'value',
                ],
                'data'
            ]);
    }

    public function testUpdateInvoiceSetsPaymentDateToNullWhenPaidIsFalse()
    {

        $user = User::factory()->create();
        $invoice = Invoice::factory()->create([
            'user_id' => $user->id,
            'payment_date' => now(),
            'paid' => true,
        ]);
        $token = $user->createToken('test-token')->plainTextToken;
        $headers = ['Authorization' => 'Bearer ' . $token];

        $updateData = [
            'user_id' => $user->id,
            'type' => 'B',
            'paid' => 0,
            'value' => 150.00,
        ];

        $response = $this->putJson("/api/v1/invoices/{$invoice->id}", $updateData, $headers);

        $invoice->refresh();

        $response->assertStatus(200);

        $this->assertNull($invoice->payment_date);
    }

    public function testDestroy()
    {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;
        $headers = ['Authorization' => 'Bearer ' . $token];

        $invoice = Invoice::factory()->create();

        $response = $this->deleteJson('/api/v1/invoices/' . $invoice->id, [], $headers);

        $response->assertStatus(Response::HTTP_OK)
            ->assertJson([
                'message' => 'Invoice deletado com sucesso',
            ]);

        // Assert the invoice was deleted from the database
        $this->assertDatabaseMissing('invoices', ['id' => $invoice->id]);
    }

    public function testDestroyWithInvalidId()
    {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;
        $headers = ['Authorization' => 'Bearer ' . $token];

        $response = $this->deleteJson('/api/v1/invoices/' . '999999', [], $headers);

        $response->assertStatus(Response::HTTP_NOT_FOUND)
            ->assertJson([
                'message' => 'Invoice não encontrado',
            ]);
    }
}
