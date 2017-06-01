<?php

namespace Lashbrook\Models;

use Illuminate\Database\Eloquent\Model;

use Lashbrook\Models\SalesOrderAddress;
use Lashbrook\Models\SalesOrderItem;
use Lashbrook\Models\SalesOrderEauthToken;
use Lashbrook\Models\SalesOrderPayment;

class SalesOrder extends Model {

    protected $connection = 'magento';

    protected $primaryKey = 'entity_id';
    protected $table = 'sales_order';

    /**
     * specifies if model has primary key is auto incrementing
     * @var boolean
     */
    public $incrementing = true;

    /**
     * if model uses created_at and modified_at timestamp fields
     * @var boolean
     */
    public $timestamps = true;

    /**
     * list of fields to allow mass assignment
     * @var array
     */
    protected $fillable = [];

    /**
     * Sales Order Addresses
     * @return Collection list of addresses 
     */
    public function addresses(){
        return $this->hasMany(SalesOrderAddress::class,'parent_id')->where(['address_type'=>'billing']);
    }

    public function items(){
        return $this->hasMany(SalesOrderItem::class,'order_id');
    }
    
    public function payment(){
        return $this->hasOne(SalesOrderPayment::class,'parent_id');
    }

    public function token(){
        return $this->hasOne(SalesOrderEauthToken::class,'sales_order_id','entity_id');
    }
}