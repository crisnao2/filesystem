<?php
namespace Crisnao2\FileSystem;

/**
* Está classe tem como objetivo, fornecer métodos que permitam:
*   obter arquivos e diretórios recursivamente a partir de um diretório base
*   buscar arquivos e diretórios recursivamente a partir de um diretório base
*
* @author Cristiano Soares <crisnao2@yahoo.com.br>
* @link http://comerciobr.com
*/
class Dir
{
    /*
    * Armazena a profundidade atual do scan
    *
    * @var integer
    */
    private static $currentDepth;
    
    /*
    * Armazena a profundidade máxima que o scan pode ir
    *
    * @var integer
    */
    private static $depth = -1;
    
    /*
    * Armazena o diretório base do scan
    *
    * @var string
    */
    private static $dirInit;

    /*
    * Este método é responsável por setar $depth e $dirInit fazer a chamada a
    * dirScan
    *
    * @param string $baseDir o diretório base para o scan
    * @param integer $depth (optional) o nível máximo de profundidade
    *
    * @return array
    */
    public static function scan($baseDir, $depth = -1)
    {
        self::$depth = $depth;
        self::$dirInit = $baseDir;
        self::$currentDepth = 0;

        return self::dirScan($baseDir);
    }

    /*
    * Este método é responsável por setar $depth e $dirInit fazer a chamada a
    * dirScan e aplicar a busca sobre os resultados retornados
    *
    * @param string $baseDir o diretório base para o scan
    * @param string $str o que se deseja procurar
    * @param integer $depth (optional) o nível máximo de profundidade
    * @param boolean $regex (optional) indica que se string de procura é uma expressão regular ou não
    *
    * @return array
    */
    public static function search($baseDir, $str, $depth = -1, $regex = false)
    {
        self::$depth = $depth;
        self::$dirInit = $baseDir;
        self::$currentDepth = 0;

        $path = array();
        self::dirScan($baseDir, function ($directory, $currentPath) use ($str, &$path, $regex) {
            if ($regex) {
                $result = preg_grep($str, $directory);
            } else {
                $result = preg_grep("#{$str}#i", $directory);
            }
            
            if ($result) {
                $result = array_map(function($arr) use ($currentPath) {
                    return $currentPath . $arr;
                }, $result);
                
                $path = array_merge($path, $result);
            }
        });

        return $path;
    }

    /*
    * Este método é responsável realizar o scan recursivo
    *
    * @param string $baseDir o diretório base para o scan
    * @param function $callback (optional) uma função que será chamada a cada nível do scan
    *
    * @return array|boolean
    */
    private function dirScan($baseDir, $callback = false)
    {
        if (self::$depth != -1 && self::$currentDepth >= self::$depth) {
            return false;
        }
        

        self::$currentDepth++;
        $directory = array();
        $exclude = array('.', '..');
        $relativePath = str_ireplace(self::$dirInit, '', $baseDir);
        $filesOrDir = array_diff(scandir($baseDir), $exclude);
        
        // callback deve retornar false para continuar ou true para parar
        if ($callback && $callback($filesOrDir, $relativePath)) {
            return false;
        }

        foreach ($filesOrDir as $dir) {
            if (is_dir($baseDir . $dir . '/')) {
                $directory[$relativePath . $dir] = self::dirScan(
                    $baseDir . $dir . '/',
                    $callback
                );
            } else {
                $directory['file'][] = $dir;
            }
        }

        return $directory;
    }

    /*
    * Este método é responsável remover arquivos e diretórios recursivamente
    *
    * @param string $baseDir o diretório base para o scan
    *
    * @return void
    */
    public static function delTree($dirOrFile = '')
    {
        clearstatcache();

        if (empty($dirOrFile)) {
            throw new \InvalidArgumentException("nome do diretório ou arquivo é obrigatório!");
        }

        if (!is_readable($dirOrFile) || !is_writable($dirOrFile)) {
            return;
        }

        if (is_file($dirOrFile)) {
            unlink($dirOrFile);
            return;
        }

        if (!is_dir($dirOrFile)) {
            throw new \InvalidArgumentException("{$dirOrFile} não é um diretório válido!");
        }

        $dirOrFile = rtrim($dirOrFile, '/') . '/';

        $files = glob($dirOrFile . '*', GLOB_MARK);
        foreach ($files as $file) {
            if (is_dir($file)) {
                self::delTree($file);
            } else {
                unlink($file);
            }
        }

        rmdir($dirOrFile);
    }
}

?>