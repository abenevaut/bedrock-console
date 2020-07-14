<?php

namespace abenevaut\BedrockConsole;

use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class WpCli
{

    /**
     * @var string
     */
    private $cmd;

    /**
     * @var
     */
    private $url;

    /**
     * @param string $cmd
     */
    public function __construct($cmd = 'vendor/bin/wp')
    {
        $this->cmd = $cmd;
    }

    /**
     * Set URL to use when calling wp-cli (--url parameter)
     *
     * @param string $url
     * @return self
     */
    public function setUrl($url = null)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Run wp-cli
     *
     * @param  string   $args
     * @param  integer  $url Override --url parameter with given URL
     * @param  string   $stdinInput Pipe content to stdin when argument string is too long for the shell
     *
     * @return \Symfony\Component\Process\Process
     */
    public function run($args = '', $url = null, $stdinInput = null)
    {
        $cmd = "{$this->cmd} ";

        if (!$url && $this->url) {
            $url = $this->url;
        }

        if ($url) {
            $cmd .= "--url={$url} ";
        }

        $cmd .= $args;

        $process = new Process($cmd, null, null, $stdinInput, 3600);
        $process->run(function ($type, $buffer) use ($cmd) {
            if (Process::ERR === $type) {
                throw new \Exception(sprintf('Runtime exception when executing command "%s" with the following buffer "%s"', $cmd, $buffer));
            }
        });

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        return $process;
    }

    /**
     * Get current available sites
     *
     * @return array Where keys are blog IDs and value are URLs.
     */
    public function getSites()
    {
        $process = $this->run('site list --fields=blog_id,url --format=json');
        $output = json_decode($process->getOutput(), true);
        if (!$output) {
            throw new \RuntimeException('Unable to decode output');
        }

        $sites = [];
        foreach ($output as $site) {
            $sites[$site['blog_id']] = $site['url'];
        }

        return $sites;
    }

    /**
     * Get blog ID corresponding to an URL.
     *
     * @param  string $url
     * @return integer|null If no blog has been found
     */
    public function getBlogIdFromUrl($url)
    {
        $url = rtrim($url, '/');

        $sites = $this->getSites();
        foreach ($sites as $blogId => $siteUrl) {
            if (rtrim($siteUrl, '/') === $url) {
                return $blogId;
            }
        }

        return null;
    }
}
