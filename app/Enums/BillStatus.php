<?php

namespace App\Enums;

enum BillStatus: int
{
    const UNPAID = 0;
    const PAID = 1;
}