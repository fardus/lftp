<?php


namespace Fardus\Lftp\Test;


use PHPUnit\Framework\TestCase;

/**
 * Description of class LftpTest
 *
 * @package Fardus\Lftp\Test
 * @author fahari
 */
class LftpTest extends TestCase
{
    public function testLs(  )
    {
        $output = '';

        $result = preg_replace('#^.*\n(.*)\n.*$#', '$1',$output);
    }
}