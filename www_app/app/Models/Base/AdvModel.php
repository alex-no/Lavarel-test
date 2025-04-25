<?php

namespace App\Models\Base;

use Illuminate\Database\Eloquent\Model;
use App\Traits\LocalizedDbAttributeTrait;

class AdvModel extends Model
{
    use LocalizedDbAttributeTrait;

    /**
     * Overriding the magic method __get to support localized attributes.
     */
    public function __get($key)
    {
        $localized = $this->getLocalizedAttributeName($key);
        return parent::__get($localized);
    }

    /**
     * Overriding the magic method __set to support localized attributes.
     */
    public function __set($key, $value)
    {
        $localized = $this->getLocalizedAttributeName($key);
        return parent::__set($localized, $value);
    }
}
