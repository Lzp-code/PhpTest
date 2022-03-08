<?php
/**
 * Created by PhpStorm.
 * User: Gkh
 * Date: 2020/2/10
 * Time: 16:29
 */

namespace app\common\constant;


abstract class Work
{
    public const DOCTOR = 0;
    public const TEACHER = 1;
    public const EXPRESS = 2;
    public const CIVIL_SERVANT = 3;
    public const STUDENT = 4;
    public const COMPANY_STAFF = 5;
    public const OTHER = 6;

    public const items = [
        ['id' => Work::DOCTOR, 'name' => '医护人员'],
        ['id' => Work::TEACHER, 'name' => '老师'],
        ['id' => Work::EXPRESS, 'name' => '快递'],
        ['id' => Work::CIVIL_SERVANT, 'name' => '公务员'],
        ['id' => Work::STUDENT, 'name' => '学生'],
        ['id' => Work::COMPANY_STAFF, 'name' => '企业人员'],
        ['id' => Work::OTHER, 'name' => '其他'],
    ];
}