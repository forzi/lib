<?
namespace stradivari\autoloader;

require_once(__DIR__ . '\..\..\functions\cut.php');
require_once(__DIR__ . '\..\singleton\TSingleton.php');
require_once(__DIR__ . '\..\event\interfaces\IEvent.php');
require_once(__DIR__ . '\..\event\interfaces\ISubscriber.php');
require_once(__DIR__ . '\..\event\Event.php');
require_once(__DIR__ . '\..\event\EventMediator.php');
require_once(__DIR__ . '\events\AutoloadSuccess.php');

use stradivari\autoloader\events\AutoloadSuccess;
use stradivari\singleton\TSingleton;

final class Autoloader {
    use TSingleton;

    public $vendor;
    public $extensions = array('php', 'inc');
    public $composerFileMap = array('files', 'classmap', 'psr0' => 'namespaces', 'psr4');
    public $environments = array(
        'files' => array(),
        'classmap' => array(),
        'psr0' => array(),
        'psr4' => array()
    );

    public function inheritComposer() {
        foreach ($this->composerFileMap as $key => $file) {
            if ( !is_string($key) ) {
                $key = $file;
            }
            $filename = $this->vendor . '/composer/autoload_' . $file . '.php';
            if ( file_exists($filename) ) {
                $this->environments[$key] += include($filename);
            }
        }
    }
    public function register() {
        $this->loadFiles();
        spl_autoload_register([$this, 'autoloader'], true, true);
    }
    public function unregister() {
        spl_autoload_unregister([$this, 'autoloader']);
    }
    private function autoloader($class) {
        if ( $this->loadClassmap($class) ) {
            $event = new AutoloadSuccess;
            $event->send($this, $class);
            return;
        }
        if ( $this->loadNamespace($class) ) {
            $event = new AutoloadSuccess;
            $event->send($this, $class);
            return;
        }
        throw new exceptions\NoSuchClass($class);
    }
    private  function loadFiles() {
        foreach ( $this->environments['files'] as $file ) {
            $file = $this->addVendor($file);
            if (is_file($file)) {
                require_once($file);
            }
        }
    }
    private function loadNamespace($class) {
        $path = explode('\\', $class);
        $last = array_pop($path);
        $last = explode('_', $last);
        $path = array_merge($path, $last);
        $path = implode('/', $path);
        return $this->isClassInFile($class, $this->searchPhpFile($path));
    }
    private function loadClassmap($class) {
        if ( array_key_exists($class, $this->environments['classmap']) && file_exists($this->environments['classmap'][$class]) ) {
            $file = $this->environments['classmap'][$class];
            $this->addVendor($file);
            return $this->isClassInFile($class, $file);
        }
        return false;
    }
    private function isClassInFile($class, $file) {
        if ( !is_readable($file) ) {
            return false;
        }
        require_once $file;
        return class_exists($class, false) || interface_exists($class, false) || trait_exists($class, false);
    }
    public function searchPhpFile($path) {
        foreach ($this->extensions as $extension) {
            $file = $this->searchFile("{$path}.{$extension}");
            if ($file) {
                return $file;
            }
        }
        return false;
    }
    public function searchFile($path) {
        $path = str_replace('\\', '/', $path);
        foreach (array('psr0', 'psr4') as $curMap) {
            $result = $this->psrSearch($path, $curMap);
            if ( $result ) {
                return $result;
            }
        }
        return false;
    }
    private function psrSearch($path, $map) {
        foreach ($this->environments[$map] as $prefix => $environments ) {
            $prefix = str_replace('\\', '/', $prefix);
            foreach ($environments as $environment) {
                $prefixPos = $prefix ? strpos($path, $prefix) : 0;
                if ( $prefixPos === 0 ) {
                    $method = "{$map}Search";
                    $file = $this->{$method}($prefix, $environment.'/', $path);
                    if ( $file ) {
                        return $file;
                    }
                }
            }
        }
        return false;
    }
    private function psr0Search($prefix, $environment, $path) {
        $realPath = "{$environment}/{$path}";
        $realPath = $this->addVendor($realPath);
        return realpath($realPath);
    }
    private function psr4Search($prefix, $environment, $path) {
        $realPath = str_replace($prefix, $environment, $path);
        $realPath = $this->addVendor($realPath);
        return realpath($realPath);
    }
    private function addVendor($path) {
        $vendor = $this->vendor;
        if (!$vendor) {
            return $path;
        }
        $vendor = rtrim($vendor, '/');
        $vendor = rtrim($vendor, '\\');
        $vendor .= '/';
        $path = str_replace('\\', '/', $path);
        $path = \stradivari\left_cut($path, $vendor);
        $path = $vendor . $path;
        return $path;
    }
}
