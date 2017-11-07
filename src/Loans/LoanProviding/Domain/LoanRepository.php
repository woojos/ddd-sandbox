<?php
namespace Loans\LoanProviding\Domain;

use Ramsey\Uuid\UuidInterface;

interface LoanRepository
{
    public function get(UuidInterface $id): Loan;

    public function save(Loan $loan): void;
}