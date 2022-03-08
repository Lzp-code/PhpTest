<?php
/**
 * Created by PhpStorm.
 * User: Gkh
 * Date: 2020/2/10
 * Time: 16:29
 */

namespace app\common\constant;


abstract class SwitchReason
{
    public const ENTER_SCHOOL = 138;
    public const JOB_TRANSFER_YES = 139;
    public const JOB_TRANSFER_NO = 140;
    public const GO_ABROAD_PUBLIC = 141;
    public const GO_ABROAD_PRIVATE = 142;
    public const GO_ABROAD_STUDY = 143;
    public const NO_JOB = 144;
    public const OTHER_ORGANIZATION = 145;
    public const OTHER = 146;

    public const items = [
        ['id' => SwitchReason::ENTER_SCHOOL, 'name' => '升学/转学'],
        ['id' => SwitchReason::JOB_TRANSFER_YES, 'name' => '就业/工作调动（工作单位有团组织）'],
        ['id' => SwitchReason::JOB_TRANSFER_NO, 'name' => '就业/工作调动（工作单位无团组织）'],
        ['id' => SwitchReason::GO_ABROAD_PUBLIC, 'name' => '因公出国（境）'],
        ['id' => SwitchReason::GO_ABROAD_PRIVATE, 'name' => '因私出国（境）'],
        ['id' => SwitchReason::GO_ABROAD_STUDY, 'name' => '出国（境）学习研究'],
        ['id' => SwitchReason::NO_JOB, 'name' => '未就业'],
        ['id' => SwitchReason::OTHER_ORGANIZATION, 'name' => '转往特殊单位团组织'],
        ['id' => SwitchReason::OTHER, 'name' => '其他'],
    ];
}