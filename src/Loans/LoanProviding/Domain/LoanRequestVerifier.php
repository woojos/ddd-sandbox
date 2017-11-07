<?php
namespace Loans\LoanProviding\Domain;


use Money\Money;

interface LoanRequestVerifier
{
    public function isEligible(Money $money, \DateTimeImmutable $dateTo): bool;
}