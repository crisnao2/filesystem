<?php

namespace Test\Crisnao2\FileSystem;
use \Crisnao2\FileSystem\Dir;

/**
 * This class prepare the environment to the tests
 */
class BaseTestCase extends \PHPUnit_Framework_TestCase
{
    protected $baseDir;
    protected $dir;
    protected $dirFirstLevel = 'first_level/';
    protected $dirSecondLevel = 'first_level/second_level/';
    protected $fileBase = 'file_first_level.txt';
    protected $fileFirstLevel = 'first_level/file_first_level.txt';
    protected $fileSecondLevel = 'first_level/second_level/file_second_level.txt';
    protected $fileFirst = 'file_first_level.txt';
    protected $fileSecond = 'file_second_level.txt';

    public function __construct()
    {
        clearstatcache();

        $this->baseDir = str_replace('\\', '/', sys_get_temp_dir()) . '/';
        if (!is_readable($this->baseDir) || !is_writable($this->baseDir)) {
            $this->baseDir = str_replace('\\', '/', realpath(dirname(__FILE__) . '/') . '/');
        }

        $this->dir = $this->baseDir . md5(uniqid($this->baseDir)) . '/';

        if (!mkdir($this->dir, 0755, true)) {
            trigger_error('não foi possível criar o diretório para realizar os testes.');
            exit();
        }

        if (!is_readable($this->dir) || !is_writable($this->dir)) {
            trigger_error('não foi possível manipular o diretório de testes criado.');
            exit();
        }
        
        mkdir($this->dir . $this->dirFirstLevel, 0755, true);
        if (!is_readable($this->dir . $this->dirFirstLevel) ||
            !is_writable($this->dir . $this->dirFirstLevel)) {
                trigger_error('não foi possível criar o diretório para realizar os testes.');
                exit();
        }

        mkdir($this->dir . $this->dirSecondLevel, 0755, true);
        if (!is_readable($this->dir . $this->dirSecondLevel) ||
            !is_writable($this->dir . $this->dirSecondLevel)) {
                trigger_error('não foi possível criar o diretório para realizar os testes.');
                exit();
        }

        file_put_contents($this->dir . $this->fileFirstLevel, $this->dir);
        chmod($this->dir . $this->fileFirstLevel, 0644);
        if (!is_readable($this->dir . $this->fileFirstLevel) ||
            !is_writable($this->dir . $this->fileFirstLevel)) {
                trigger_error('não foi possível criar o arquivo para realizar os testes.');
                exit();
        }

        file_put_contents($this->dir . $this->fileSecondLevel, $this->dir);
        chmod($this->dir . $this->fileSecondLevel, 0644);
        if (!is_readable($this->dir . $this->fileSecondLevel) ||
            !is_writable($this->dir . $this->fileSecondLevel)) {
                trigger_error('não foi possível criar o arquivo para realizar os testes.');
                exit();
        }
    }

    public function __destruct()
    {
        Dir::delTree($this->dir);
    }
}


?>