<?php
/**
 * Created by PhpStorm.
 * User: Isham
 * Date: 3/17/2017
 * Time: 11:28 AM
 */

namespace Aboutgcc\Test2Bundle\Controller;


class Dev
{
    public function back($request){
        exit(\Doctrine\Common\Util\Debug::dump($request));
    }


}