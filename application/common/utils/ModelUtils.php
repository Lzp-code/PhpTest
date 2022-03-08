<?php
/**
 * Created by PhpStorm.
 * User: Gkh
 * Date: 2020/2/13
 * Time: 23:22
 */

namespace app\common\utils;


use think\Collection;
use think\Model;

abstract class ModelUtils
{
    public static function toArray($collection, array $attrs = []): ?array
    {
        if ($collection === null)
            return null;
        if ($collection instanceof Collection) {
            $result = [];
            foreach ($collection as $item) {
                ModelUtils::merge($item, $attrs, $result[]);
            }
            return $result;
        } else if ($collection instanceof Model) {
            return ModelUtils::merge($collection, $attrs);
        } else {
            return null;
        }
    }

    private static function merge(Model $array, array $attrs, array &$result = null): array
    {
        if ($result === null) {
            $result = $array->toArray();
        }
        foreach ($attrs as $key => $value) {
            if (is_string($key)) {
                $result[$key] = $value($array);
            } else {
                $result[$value] = $array->$value;
            }
        }
        return $result;
    }

}