<?php
namespace Loans\LoanProviding\Domain;

use MyCLabs\Enum\Enum;

class LoanStatus extends Enum
{
    const ACTIVE = 1;
    const CANCELED = 2;
    const PAID = 3;
}