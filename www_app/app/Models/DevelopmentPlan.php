<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DevelopmentPlan extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'development_plan';

    /**
     * The attributes that are mass assignable.
     * @var array
     */
    protected $fillable = [
        'feature_en',
        'feature_ru',
        'feature_uk',
        'result_en',
        'result_ru',
        'result_uk',
        'technology_en',
        'technology_ru',
        'technology_uk',
        'sort_order',
        'status',
    ];

    /**
     * The timestamp attributes for the model.
     *
     * @var boolean
     */
    public $timestamps = true;

    /**
     * The attributes that should be cast to native types.
     *  - 'created_at'
     *  - 'updated_at'
     *
     * @var array
     */
    protected $dates = [
        'created_at',
        'updated_at',
    ];
}
