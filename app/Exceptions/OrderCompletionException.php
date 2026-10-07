<?php

namespace App\Exceptions;

use Illuminate\Contracts\Debug\ShouldntReport;
use RuntimeException;

class OrderCompletionException extends RuntimeException implements ShouldntReport {}
