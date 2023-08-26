<?php

namespace Linsh\LakalaSdk\Model;
//http://open.lakala.com/#/home/document/detail?title=%E5%85%AC%E5%85%B1%E5%8F%82%E6%95%B0&id=115
//聚合扫码-关单
class CloseReq
{
    public $merchant_no;
    public $term_no;
    public $origin_out_trade_no;//原商户交易流水号	
    public $origin_trade_no;
    public $location_info;
}