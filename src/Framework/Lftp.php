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
    const REGEX_LS_LINE = '#(?<right>[-rwx]+) +([\d ]+ +[\d]+) +(?<size>[\d]+) (?<date>[a-zA-Z]{3}.*[\d]+) (?<file>.*)$#i';
    /**
     * @return string
     */
    public function getDirectory()
    {
        return $this->directory;
    }

    /**
     * @param string $directory
     * @return Lftp
     */
    public function setDirectory( $directory )
    {
        $this->directory = $directory;
        return $this;
    }

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
     * @param null|string $pattern
     * @param null|int $maxResult
     * @return array
     * @throws LftpException
     */
    public function files($pattern = null, $maxResult = null)
    {
        $list = array();
        $result = $this->runCommand('ls -U ' . $pattern);

        if(!empty($result)) {
            $result = explode("\n", $result, $maxResult);
            foreach ($result as $item) {
                $item = trim($item);
                if (!empty($item)) {
                    $list[] = preg_replace(self::REGEX_LS_LINE, '$5', $item);
                }
            }
        }

        return $list;
    }

    /**
     * @param $pattern
     * @return bool
     * @throws LftpException
     */
    public function has( $pattern )
    {
        $result = $this->runCommand('ls -alU ' . $pattern);
        return !empty($result);
    }

    /**
     * @param $file
     * @return string
     * @throws LftpException
     */
    public function get( $file )
    {
        $result = $this->runCommand('get '.escapeshellarg($file));
        return !empty($result);
    }

    /**
     * @param $file
     * @return bool
     * @throws LftpException
     */
    public function read( $file )
    {
        $output = $this->runCommand('read '.escapeshellarg($file));
        $output = explode("\n", $output);
        $output = array_slice($output, -1);
        $output = array_slice($output, 1);

        return implode("\n", $output);
    }

    /**
     * @param $file
     * @return string
     * @throws LftpException
     */
    public function rm( $file )
    {
        $result = $this->runCommand('rm -rf '.escapeshellarg($file));
        return !empty($result);
    }

}