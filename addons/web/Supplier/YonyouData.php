<?php namespace Aike\Web\Supplier;

use Aike\Web\Index\BaseModel;

class YonyouData extends BaseModel
{
    protected $table = 'supplier_yonyou_data';
    
    public function yonyou()
    {
        return $this->belongsTo('Aike\Web\Supplier\Yonyou');
    }
}
