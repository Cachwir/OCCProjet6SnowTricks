<?php
/**
 * Created by PhpStorm.
 * User: cachwir
 * Date: 27/06/17
 * Time: 18:10
 */

namespace AppBundle\Json;

trait JsonSerializableHandler
{
    // protected static $jsonFields = [];

    function jsonSerialize()
    {
        $json = [];

        foreach (self::$jsonFields as $field) {
            $method = "get" . ucfirst($field);
            $value = $this->$method();
            $json[$field] = is_string($value) ? htmlspecialchars($value) : $value;
        }

        return $json;
    }
}