<?php

namespace App\Models;

use Illuminate\Support\Facades\Schema;

trait Columns
{
    public function getFillable()
    {
        return Schema::getColumnListing($this->getTable());
    }
}
