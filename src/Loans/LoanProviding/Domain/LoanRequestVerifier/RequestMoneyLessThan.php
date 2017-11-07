<?php
namespace Loans\LoanProviding\Domain\LoanRequestVerifier;

use Loans\LoanProviding\Domain\LoanRequestVerifier;
use Money\Money;

class RequestMoneyLessThan implements LoanRequestVerifier
{
    /** @var Money */
    private $threshold;

    /**
     * RequestMoneyLessThan constructor.
     * @param Money $threshold
     */
    public function __construct(Money $threshold)
    {
        $this->threshold = $threshold;
    }

    public function isEligible(Money $money, \DateTimeImmutable $dateTo): bool
    {
        return $this->threshold->greaterThan($money);
    }
}