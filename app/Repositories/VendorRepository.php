<?php

namespace App\Repositories;

use App\Interfaces\VendorRepositoryInterface;
use App\Models\Vendor;

class VendorRepository extends BaseEloquentRepository implements VendorRepositoryInterface
{

    public function __construct(Vendor $vendor)
    {
        parent::__construct($vendor);
    }
}
