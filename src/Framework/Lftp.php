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
     * @var string
     */
    protected $directory;

    /**
     * @var string
     */
    protected $in;

    /**
     * @param string $command
     * @return string
     * @throws LftpException
     */
    public function runCommand( $command )
    {
        if (!empty($this->in)) {
            $command = sprintf('cd %s; %s', escapeshellarg($this->in), $command);
        }

        return parent::runCommand($command);
    }

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
     * @param string $in
     * @return Lftp
     */
    public function setIn( $in )
    {
        $this->in = $in;
        return $this;
    }

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
    public function has( $pattern = '.')
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
        return $this->runCommand('cat '.escapeshellarg($file));
    }

    /**
     * @param $file
     * @return string
     */
    public function rm( $file)
    {
        try {
            $result = $this->runCommand('rm -rf ' . escapeshellarg($file));
        } catch (LftpException $e) {
            $result = false;
        }

        return !empty($result);
    }

}