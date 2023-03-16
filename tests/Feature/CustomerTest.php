<?php

namespace Tests\Feature;

use App\Models\SchedulePayment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CustomerTest extends TestCase
{
    use RefreshDatabase;
    /**
     * Customer can not create loan if not login.
     *
     * @return void
     */
    public function test_that_customer_can_not_access_creat_loan_if_not_login()
    {
        $response =  $this->withHeaders(['Accept' => 'application/json'])->post('/api/customer/createLoan', ['term' => 3, 'amount' => 10000]);

        $response->assertStatus(401);
        $response->assertExactJson(['message' => 'Unauthenticated.']);
    }

    /**
     * Customer can create loan with correct token.
     *
     * @return void
     */
    public function test_that_customer_can_creat_loan_with_corect_token()
    {
        $this->seed();
        $token = $this->getCustomerToken();


        $response =  $this->createLoan($token);
        $response
            ->assertStatus(200)
            ->assertJsonPath('message', 'Create Loan Successfully')
            ->assertJsonPath('loan.id', 1)
            ->assertJsonPath('loan.status', 'PENDING');

        $schedulePayments = SchedulePayment::where('loan_id', 1)->get();
        foreach ($schedulePayments as $schedulePayment) {
            $this->assertEquals('PENDING', $schedulePayment->status);
        }
    }

    public function test_that_customer_can_view_loan_belongs_to_him()
    {
        $this->seed();
        $token = $this->getCustomerToken();

        $response = $this->createLoan($token);
        $loan = json_decode($response->getContent(), true);
        $loanId = $loan['loan']['id'];

        $viewLoanResponse = $this->withHeaders(['Accept' => 'application/json', 'Bearer Token' => $token])
            ->get('api/customer/loan/'.$loanId);

        $viewLoanResponse->assertStatus(200);
    }

    private function getCustomerToken()
    {
        $response = $this->post('/api/auth/login', ['email' => 'customer@aspire.com', 'password' => 'customer@#$%']);
        $response = json_decode($response->getContent(), true);
        return $response['token'];
    }

    private function createLoan($token)
    {
        return $this->withHeaders(['Accept' => 'application/json', 'Bearer Token' => $token])
            ->post('/api/customer/createLoan', ['term' => 3, 'amount' => 10000]);
    }
}
