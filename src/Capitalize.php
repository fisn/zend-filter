<?php
namespace Zend\Filter;

class Capitalize implements FilterInterface
{
    public function filter($val)
    {
        $toUpper = new StringToUpper('UTF-8');
        $replaced = preg_replace_callback('/\b\w/u', function ($m) use ($toUpper) {
            return $toUpper->filter($m[0]);
        }, $val);
        return $replaced;
    }
}
