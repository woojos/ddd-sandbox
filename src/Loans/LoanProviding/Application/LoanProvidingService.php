<?php
namespace Loans\LoanProviding\Application;

use Loans\LoanProviding\Application\Command\LoanRequest;
use Loans\LoanProviding\Domain\Loan;
use Loans\LoanProviding\Domain\LoanRepository;
use Loans\LoanProviding\Domain\LoanRequestVerifierFactory;
use Prooph\ServiceBus\EventBus;

class LoanProvidingService
{
    /** @var LoanRepository */
    private $repository;
    /** @var EventBus */
    private $eventBus;
    /** @var LoanRequestVerifierFactory */
    private $verifierFactory;
    /**
     * LoanProvidingService constructor.
     * @param LoanRepository $repository
     * @param EventBus $eventBus
     */
    public function __construct(LoanRepository $repository, EventBus $eventBus, LoanRequestVerifierFactory $verifierFactory)
    {
        $this->repository = $repository;
        $this->eventBus = $eventBus;
        $this->verifierFactory = $verifierFactory;
    }

    public function requestForLoan(LoanRequest $loanRequest): ?Loan
    {
        $loanRequestVerifier = $this->verifierFactory->createFor($loanRequest->customerId());

        if ($loanRequestVerifier->isEligible($loanRequest->money(), $loanRequest->dateTo())) {
            $loan = Loan::init(
                $loanRequest->loanId(),
                $loanRequest->customerId(),
                $loanRequest->money(),
                $loanRequest->dateTo()
            );
            $this->repository->save($loan);
            return $loan;
        }

        return null;
        /*$this->eventBus->dispatch(

        );*/

    }

    public function repayLoan(): void
    {

    }

/*
    public function repayLoanWithLoan()
    {

    }
*/

    public function cancel(): void
    {

    }

}