<?php

namespace Tests\Feature;

use App\Models\Loan;
use App\Models\SchedulePayment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
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
    public function test_that_customer_can_create_loan_with_corect_token()
    {
        $this->seed();
        $emailCustomer = 'customer@aspire.com';
        $password = 'customer@#$%';
        $token = $this->getCustomerToken($emailCustomer, $password);


        $response =  $this->createLoan($token);
        $loan = json_decode($response->getContent(), true);
        $loanId = $loan['loan']['id'];
        $response
            ->assertStatus(200)
            ->assertJsonPath('message', 'Create Loan Successfully')
            ->assertJsonPath('loan.id', $loanId)
            ->assertJsonPath('loan.status', 'PENDING');

        $schedulePayments = SchedulePayment::where('loan_id', $loanId)->get();
        foreach ($schedulePayments as $schedulePayment) {
            $this->assertEquals('PENDING', $schedulePayment->status);
        }
    }

    /**
     * Customer can view his loan
     *
     * @return void
     */
    public function test_that_customer_can_view_loan_belongs_to_him()
    {
        $this->seed();
        $emailCustomer = 'customer@aspire.com';
        $password = 'customer@#$%';
        $token = $this->getCustomerToken($emailCustomer, $password);

        $response = $this->createLoan($token);
        $loan = json_decode($response->getContent(), true);
        $loanId = $loan['loan']['id'];

        $viewLoanResponse = $this->withHeaders(['Accept' => 'application/json', 'Bearer Token' => $token])
            ->get('api/customer/loan/'.$loanId);

        $viewLoanResponse->assertStatus(200);
    }

    /**
     * Customer can not view loan, which he does now own
     *
     * @return void
     */
    public function test_that_customer_can_not_see_loan_he_not_own()
    {
        $this->seed();
        $emailFake = 'fake@abc.com';
        $password = 'customer@#$%';
        // create a fake customer
        $customerFake = User::create(
            [
                'name'     => 'customerFake',
                'email' => $emailFake,
                'role'  => 'customer',
                'password' => Hash::make($password)
            ]
        );
        // create a test loan belongs to fake customer
        $loanTest = Loan::create(
            [
                'status' => Loan::STATUS_PENDING,
                'user_id' => $customerFake->id,
                'amount' => 1000,
                'term' => 3,
            ]
        );

        $emailCustomer = 'customer@aspire.com';
        $token = $this->getCustomerToken($emailCustomer, $password);
        // Try to view test loan with real customer
        $viewLoanResponse = $this->withHeaders(['Accept' => 'application/json', 'Bearer Token' => $token])
            ->get('api/customer/loan/'.$loanTest->id);

        $viewLoanResponse->assertStatus(401);
        $viewLoanResponse->assertExactJson(['message' => 'You do not have permission to view this loan.']);
    }

    public function test_that_customer_can_add_repayment_with_amount_greater_or_equal_scheduled_repayment()
    {
        $this->seed();
        $emailCustomer = 'customer@aspire.com';
        $password = 'customer@#$%';
        $token = $this->getCustomerToken($emailCustomer, $password);

        $responseCreateLoan =  $this->createLoan($token);
        $loan = json_decode($responseCreateLoan->getContent(), true);
        $loanId = $loan['loan']['id'];


        $schedulePayments = SchedulePayment::where('loan_id', $loanId)->get();
        foreach ($schedulePayments as $schedulePayment) {
            $responseAddRepayment = $this->withHeaders(['Accept' => 'application/json', 'Bearer Token' => $token])
                ->post('/api/customer/payment/'.$schedulePayment->id.'/pay', ['amount' => 3337.34]);
            $responseAddRepayment->assertStatus(200);
            $responseAddRepayment->assertExactJson(['message' => 'Add Repayment Successfully.', 'status' => true]);
        }

        //Query again to check status after paying
        $schedulePayments = SchedulePayment::where('loan_id', $loanId)->get();
        foreach ($schedulePayments as $schedulePayment) {
            $this->assertEquals('PAID', $schedulePayment->status);
        }

        //Check loan status after all schedule repayments are paid
        $loan = Loan::find($loanId);
        $this->assertEquals('PAID', $loan->status);
    }

    public function test_that_customer_can_not_add_repayment_with_amount_lower_scheduled_repayment()
    {
        $this->seed();
        $emailCustomer = 'customer@aspire.com';
        $password = 'customer@#$%';
        $token = $this->getCustomerToken($emailCustomer, $password);

        $responseCreateLoan =  $this->createLoan($token);
        $loan = json_decode($responseCreateLoan->getContent(), true);
        $loanId = $loan['loan']['id'];


        $schedulePayments = SchedulePayment::where('loan_id', $loanId)->get();
        foreach ($schedulePayments as $schedulePayment) {
            $responseAddRepayment = $this->withHeaders(['Accept' => 'application/json', 'Bearer Token' => $token])
                ->post('/api/customer/payment/'.$schedulePayment->id.'/pay', ['amount' => 123]);
            $responseAddRepayment->assertStatus(401);
            $responseAddRepayment->assertExactJson(['message' => 'You should add a repayment with amount greater or equal to the scheduled repayment'.'('.$schedulePayment->amount.')']);
        }

    }


    private function getCustomerToken($email, $password)
    {
        $response = $this->post('/api/auth/login', ['email' => $email, 'password' => $password]);
        $response = json_decode($response->getContent(), true);
        return $response['token'];
    }

    private function createLoan($token)
    {
        return $this->withHeaders(['Accept' => 'application/json', 'Bearer Token' => $token])
            ->post('/api/customer/createLoan', ['term' => 3, 'amount' => 10000]);
    }
}
