<?php
namespace Loans\LoanProviding\Domain\LoanRequestVerifier;

use Loans\LoanProviding\Domain\LoanRequestVerifier;
use Money\Money;

class Never implements LoanRequestVerifier
{

    public function isEligible(Money $money, \DateTimeImmutable $dateTo): bool
    {
        return false;
    }

}