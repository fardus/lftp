<?php

namespace Fardus\Lftp\Framework;

use Fardus\Lftp\Exception\LftpException;
use Symfony\Component\Process\Process;

class Connection
{
    const TYPE_FTP = 'ftp';
    const TYPE_FTPS = 'ftps';
    const PORT_21 = 21;
    const PORT_22 = 22;

    /**
     * @var string
     */
    protected $user;

    /**
     * @var string
     */
    protected $password;

    /**
     * @var string
     */
    protected $host;

    /**
     * @var string
     */
    protected $port = self::PORT_21;

    /**
     * @var string
     */
    protected $type = self::TYPE_FTP;

    /**
     * @return string
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param string $user
     * @return static
     */
    public function setUser( $user )
    {
        $this->user = $user;
        return $this;
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param string $password
     * @return static
     */
    public function setPassword( $password )
    {
        $this->password = $password;
        return $this;
    }

    /**
     * @return string
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * @param string $host
     * @return static
     */
    public function setHost( $host )
    {
        $this->host = $host;
        return $this;
    }

    /**
     * @return string
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * @param string $port
     * @return static
     */
    public function setPort( $port )
    {
        $this->port = (int) $port;
        return $this;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     * @return static
     */
    public function setType( $type )
    {
        $this->type = (string) $type;
        return $this;
    }

    public function useSecureFtp($use = true)
    {
        if ($use) {
            $this->setPort(self::PORT_22)
                ->setType(self::TYPE_FTPS);
        }
    }

    protected function getUrl( $directory = '/')
    {
        return strtr('%type%://%user%:%password%@%host%:%port%%directory%', array(
            '%type%' => $this->type,
            '%user%' => $this->user,
            '%password%' => $this->password,
            '%host%' => $this->host,
            '%port%' => $this->port,
            '%directory%' => $directory,
        ));
    }

    /**
     * @param string $command
     * @return string
     * @throws LftpException
     */
    public function runCommand( $command )
    {
        $url = $this->getUrl();
        $tmp = strtr('lftp %url% -e "%command%;quit"', array(
            '%url%' => $url,
            '%command%' => $command,
        ));

        return $this->runProcess($tmp);
    }

    /**
     * @param $command
     * @return string
     * @throws LftpException
     */
    protected function runProcess($command)
    {
        print_r(compact('command'));
        $process = new Process($command);
        $process->run();

        if(!$process->isSuccessful()) {
            throw new LftpException($process->getErrorOutput());
        }

        return $process->getOutput();
    }

    /**
     * @return string
     */
    public function checkConnection()
    {
        try {
            $result = (bool) $this->runProcess('lftp ' . $this->getUrl());
        } catch (LftpException $e) {
            $result = false;
        }

        return $result;
    }
}
