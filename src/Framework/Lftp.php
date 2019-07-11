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
    public function files($pattern = null, $maxResult = 1, $useCache = false)
    {
        $list = array();
        $function = $useCache ? 'ls' : 'rels';
        $output = $this->runCommand(sprintf('%s -U %s | head -%d', $function, $pattern, $maxResult));

        if(!empty($output)) {
            $result = explode("\n", $output);
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
        $result = $this->runCommand(sprintf('rels -alU %s', $pattern));
        return !empty($result);
    }

    /**
     * @param $file
     * @param bool $delete
     * @return string
     * @throws LftpException
     */
    public function get( $file, $delete = false)
    {
        $optDelete = $delete ? '-E' : null;
        $result = $this->runCommand(sprintf('mget %s %s', $optDelete, escapeshellarg($file)));
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