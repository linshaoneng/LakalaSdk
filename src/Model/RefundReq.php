<?php

namespace Linsh\LakalaSdk\Model;
//http://open.lakala.com/#/home/document/detail?title=%E5%85%AC%E5%85%B1%E5%8F%82%E6%95%B0&id=113
//扫码-退款交易
class RefundReq
{
    public $merchant_no;
    public $term_no;
    public $out_trade_no;
    public $origin_out_trade_no;
    public $refund_amount;//退款金额
    public $refund_reason;//退款原因
    public $location_info;
}