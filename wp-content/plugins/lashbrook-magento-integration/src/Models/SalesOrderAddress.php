<?php

namespace Lashbrook\Models;

use Illuminate\Database\Eloquent\Model;

class SalesOrderAddress extends Model {

    protected $connection = 'magento';
    
    protected $primaryKey = 'entity_id';
    protected $table = 'sales_order_address';

    /**
     * specifies if model has primary key is auto incrementing
     * @var boolean
     */
    public $incrementing = true;

    /**
     * if model uses created_at and modified_at timestamp fields
     * @var boolean
     */
    public $timestamps = false;

    /**
     * list of fields to allow mass assignment
     * @var array
     */
    protected $fillable = [];

}