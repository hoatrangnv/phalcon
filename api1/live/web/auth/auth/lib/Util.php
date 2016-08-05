<?php
namespace TVN\Auth\Lib;

class Util
{
    /**
     * @author Cuong.Bui
     */
    public static function rotateHotline($hotline)
    {
        $week = date('W');
        $persons = count($hotline);

        if ($week % $persons == 0) {
            $index = $persons;
        } else {
            $index = $week % $persons;
        }

        $line = array();
        $line[1] = $hotline[$index];

        if ($index < count($hotline)) {
            for ($i = $index + 1; $i <= count($hotline); $i++) {
                $line[] = $hotline[$i];
            }
        }

        for ($i = 1; $i <= $index - 1; $i++) {
            $line[] = $hotline[$i];
        }

        return $line;
    }
}