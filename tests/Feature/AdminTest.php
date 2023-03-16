<?php

namespace Tests\Feature;

use App\Models\Loan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AdminTest extends TestCase
{
    /**
     * Admin change the pending loans to state APPROVED.
     *
     * @return void
     */
    public function test_that_admin_can_approve_loan()
    {
        $this->seed();
        $customer = User::where('email', 'customer@aspire.com')->where('role', 'customer')->first();
        // create a test loan
        $loan = Loan::create(
            [
                'status' => Loan::STATUS_PENDING,
                'user_id' => $customer->id,
                'amount' => 1000,
                'term' => 3,
            ]
        );

        $emailAdmin = 'admin@aspire.com';
        $passwordAdmin = 'admin@#$%';
        $tokenAdmin = $this->getToken($emailAdmin, $passwordAdmin);

        $response = $this->withHeaders(['Accept' => 'application/json', 'Bearer Token' => $tokenAdmin])
            ->post('/api/admin/approveLoan', ['loanId' => $loan->id]);

        $response
            ->assertStatus(200)
            ->assertJsonPath('message', 'Approve Loan Successfully')
            ->assertJsonPath('loan.id', $loan->id)
            ->assertJsonPath('loan.status', 'APPROVED');

    }

    private function getToken($email, $password)
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
