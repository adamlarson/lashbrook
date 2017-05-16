<?php

namespace Lashbrook\Models;

use Illuminate\Database\Eloquent\Model;

use Lashbrook\Models\SalesOrder;

class SalesOrderEauthToken extends Model {

    protected $primaryKey = 'entity_id';
    protected $table = 'sales_order_eauth_tokens';

    protected $connection = 'magento';

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
    protected $fillable = [
    	'order_id',
    	'sales_order_id',
    	'eauth_token',
        'signature_image_data'
    ];

    public function order(){
        return $this->belongsTo(SalesOrder::class,"sales_order_id");
    }

}