<?php
namespace Loans\LoanProviding\Domain\LoanRequestVerifier;

use Loans\LoanProviding\Domain\LoanRequestVerifier;
use Money\Money;

class AndX implements LoanRequestVerifier
{
    /** @var LoanRequestVerifier[] */
    private $verifiers;

    /**
     * AndX constructor.
     * @param LoanRequestVerifier[] $verifiers
     */
    public function __construct(array $verifiers)
    {
        $this->verifiers = $verifiers;
    }

    public function isEligible(Money $money, \DateTimeImmutable $dateTo): bool
    {
        foreach ($this->verifiers as $verifier) {
            if(!$verifier->isEligible($money, $dateTo)) {
                return false;
            }
        }
        return true;
    }
}