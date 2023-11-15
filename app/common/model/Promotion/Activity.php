<?php
declare (strict_types = 1);

namespace app\common\model\Promotion;

use think\Model;
use think\model\concern\SoftDelete;

/**
 * Notes：促销首页信息
 * {2023/11/13}
 * Class PromotionActivity
 * @package app\common\model\Promotion
 */
class PromotionActivity extends Model
{
    use SoftDelete;
    protected $deleteTime = 'delete_time';



}