<?php


namespace Fardus\Lftp\Framework;


use Fardus\Lftp\Exception\LftpException;

/**
 * Description of class Lftp
 *
 * @package Fardus\Lftp
 * @author fahari
 */
class Lftp extends Connection
{
    /**
     * @var string
     */
    protected $directory;

    protected function getUrl($directory = null)
    {
        $directory = !empty($directory) ? $directory : $this->directory;
        return parent::getUrl($directory);
    }

    /**
     * @param null $regexp
     * @return array
     * @throws LftpException
     */
    public function files($regexp = null, $maxResult = null)
    {
        $list = array();
        $result = $this->runCommand('ls ' . $regexp);

        if(!empty($result)) {
            $list = explode("\n", $result);
        }

        return $list;
    }

}