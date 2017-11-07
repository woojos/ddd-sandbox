<?php
namespace Loans\LoanProviding\Infrastructure;

use Loans\LoanProviding\Domain\Loan;
use Loans\LoanProviding\Domain\LoanRepository;
use Prooph\EventSourcing\Aggregate\AggregateRepository;
use Ramsey\Uuid\UuidInterface;

/**
 * Class EventBasedLoanRepository
 * @package LoanFlow\LoanProviding\Infrastructure
 */
class EventBasedLoanRepository implements LoanRepository
{
    /**
     * @var AggregateRepository
     */
    private $aggregateRepository;

    /**
     * EventBasedLoanRepository constructor.
     * @param AggregateRepository $aggregateRepository
     */
    public function __construct(AggregateRepository $aggregateRepository)
    {
        $this->aggregateRepository = $aggregateRepository;
    }

    /**
     * @param UuidInterface $id
     * @return Loan
     */
    public function get(UuidInterface $id): Loan
    {
        return $this->aggregateRepository->getAggregateRoot($id->toString());
    }

    public function save(Loan $loan): void
    {
        $this->aggregateRepository->saveAggregateRoot($loan);
    }
}