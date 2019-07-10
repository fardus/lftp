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
    public function testRead(  )
    {
        $output = 'cd ok, cwd=/home/fahari.hamadasidi/wrapping_chain/alfresco                             
9c101e7cfa6815e94a666531fc0766be                                                   
9c101e7cfa6815e94a666531fc0766be                                                   
33 octets transférés';

        $result = preg_replace('#^.*\n(.*)\n.*$#', '$1',$output);
        var_dump($result);
    }
}