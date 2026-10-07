<?php

namespace App\Exceptions;

use Illuminate\Contracts\Debug\ShouldntReport;
use LogicException;

class UnbalancedJournalEntryException extends LogicException implements ShouldntReport {}
