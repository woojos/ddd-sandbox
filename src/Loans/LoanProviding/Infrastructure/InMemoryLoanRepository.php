<?php
namespace Loans\LoanProviding\Infrastructure;

use Loans\LoanProviding\Domain\Loan;
use Loans\LoanProviding\Domain\LoanRepository;
use Ramsey\Uuid\UuidInterface;

class InMemoryLoanRepository implements LoanRepository
{

    private $loans = [];

    /**
     * @param UuidInterface $id
     * @return Loan
     */
    public function get(UuidInterface $id): Loan
    {
        if (!isset($this->loans[$id->toString()])) {
            throw new \InvalidArgumentException();
        }

        return $this->loans[$id->toString()];
    }

    /**
     * @param Loan $loan
     */
    public function save(Loan $loan): void
    {
        $this->loans[$loan->getId()->toString()] = $loan;
    }
}