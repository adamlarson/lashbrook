<?php

namespace Lashbrook\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model {

    protected $connection = 'magento';
    
    protected $primaryKey = 'entity_id';
    protected $table = 'catalog_product_entity';
    public $timestamps = true;

    protected $fillable = [
        'attribute_set_id',
        'type_id',
        'sku',
        'has_options',
        'required_options'
    ];
}