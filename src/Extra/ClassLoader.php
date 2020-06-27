<?php

namespace Extra;

class ClassLoader
{
    private static $classes = [];
    private static $classDirs = [];
    private static $documentRoot = null;

    public static function init(string $documentRoot = null): void
    {
        self::$documentRoot = $documentRoot ?? $_SERVER['DOCUMENT_ROOT'];

        spl_autoload_register(function($className){
            self::autoLoad($className);
        });
    }

    /**
     * Example ['App\Http\Request' => '/app/App/Http/Request.php']
     *
     * @param array $data
     */
    public static function registerClass(array $data): void
    {
        foreach ($data as $className => $relPath){
            if($className[0] === '\\'){
                $className = substr($className, 1);
            }
            self::$classes[$className] = $relPath;
        }
    }

    public static function registerClassDir(array $data): void
    {
        foreach($data as $dir){
            self::$classDirs[] = $dir;
        }
    }

    public static function includeFile(string $filePath): bool
    {
        if(is_file($filePath) === true){
            require $filePath;
            return true;
        }
        return false;
    }

    private static function autoLoad(string $className): bool
    {
        if(!empty(self::$classes[$className])){
            return self::includeFile(self::$documentRoot . self::$classes[$className]);
        }


        $class = str_replace('\\', '/', $className);
        foreach(self::$classDirs as $dir){
            if(self::includeFile(self::$documentRoot . $dir . $class . '.php') === true){
                return true;
                break;
            }
        }

        return false;
    }
}