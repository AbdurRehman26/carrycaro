<?php

namespace App\Enums;

enum GeneralStatus: string
{
    const PENDING = 'pending';

    const APPROVED = 'approved';

    const REJECTED = 'rejected';
}
