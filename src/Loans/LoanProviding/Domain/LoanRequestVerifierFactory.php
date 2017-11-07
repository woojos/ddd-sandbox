<?php
namespace Loans\LoanProviding\Domain;

use Loans\LoanProviding\Domain\LoanRequestVerifier\Always;
use Ramsey\Uuid\UuidInterface;

class LoanRequestVerifierFactory
{
    public function createFor(UuidInterface $userId): LoanRequestVerifier
    {
        return new Always();
    }
}