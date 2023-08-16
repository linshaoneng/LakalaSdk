<?php

namespace Linshaoneng\LakalaSdk\Model;

/**
 * 主扫交易接口
 */
class TransPreorderReq
{
    public $merchant_no;
    public $term_no;
    public $out_trade_no;
    public $account_type;
    public $trans_type;
    public $total_amount;
    public $location_info;
    public $subject;
    public $notify_url;
    public $acc_busi_fields;
}