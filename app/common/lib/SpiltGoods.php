<?php
namespace app\common\lib;


/**
 * 优惠 分摊算法
 */
class SpiltGoods
{
	/**
     * useuserpoint 拆购物金分摊
     * usediscount 拆代金卷分摊
     * use_offer 除去购物金和折价券之外的优惠分摊，目前来说就是满减的分摊
     * 按照不同活动类型来计算此次参与活动商品总金额，再按参与商品金额goods_total的占比来分配分摊的优惠
     * $type就是来对应相关的优惠分摊类型
     * 
     * @DateTime 2022-10-26
     * @return   [type]
     */
    public function _splitPrice(&$goods_info, $tmprice, $type)
    {
        $goods_num = count($goods_info);
        $total     = 0;
        foreach($goods_info as $k => $v) {
            if($type=='use_offer'){
                if($v['is_own_shop'] == 1) {
                    if($goods_num>1){
                        if(!$v['yihe_supplier_deliver']){   //第三方发货的不要摊到满减等优惠
                            if(!$v['more_goods_discount_info']){  //多件N折商品跳过满减优惠分摊
                                $total += $v['goods_total'];
                            }
                        }    
                    }else{
                        $total += $v['goods_total'];
                    }
                }   
            }else{
                if($v['is_own_shop'] == 1) {
                    $total += $v['goods_total'];
                }
            }
        }

        //表面看很不理解，但是其他2个$type对应的值写出来就很明白就是一个根据不同优惠分摊类型经行计算总价格的，
        //下面的也是不过是计算分摊具体优惠转换成存打数据库的字段
        //        foreach($goods_info as $k => $v) {
        //            if($type=='use_offer'){
        //                if($v['is_own_shop'] == 1) {
        //                    if($goods_num>1){
        //                        if(!$v['yihe_supplier_deliver']){   //第三方发货的不要摊到满减等优惠
        //                                     if(!$v['more_goods_discount_info']){  //多件N折商品跳过满减优惠分摊
        //                                 $total += $v['goods_total'];
        //                                     }
        //                        }    
        //                    }else{
        //                        $total += $v['goods_total'];
        //                    }
        //                }   
        //            }elseif($type=='usediscount'){
        //                if($v['is_own_shop'] == 1) {
        //                    $total += $v['goods_total'];
        //                }
        //            }elseif($type=='useuserpoint'){
        //                        if($v['is_own_shop'] == 1) {
        //                    $total += $v['goods_total'];
        //                }
        //                        
        //                    }
        //        }

        foreach($goods_info as $k => $v) {
            //第三方发货的跳过
            //多件N折商品跳过满减优惠分摊

            if($type == 'use_offer'&&$goods_num>1&&$v['yihe_supplier_deliver']){
                continue;
            }

            if($type == 'use_offer'&&$goods_num>1&&$v['more_goods_discount_info']){
                continue;
            }

            if($v['is_own_shop'] == 1) {
                $splitVal = intval($v['goods_total']/$total*$tmprice) + 1;
                if($splitVal > $tmp) {
                    $splitVal = $tmp;
                }

                $tmp -= $splitVal;
                if($type == 'useuserpoint') {
                    $goods_info[$k]['goods_splituserpoint'] = $splitVal;
                } elseif($type == 'usediscount') {
                    $goods_info[$k]['goods_splitusediscount'] = $splitVal;
                }elseif($type=='use_offer'){
                    $goods_info[$k]['goods_split_use_offer'] = $splitVal;
                }

                if($tmp == 0) {
                    break;
                }
            }
        }

        var_dump($goods_info);
    }

}