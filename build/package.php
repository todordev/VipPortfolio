<?php

$source   = dirname( dirname(__FILE__) );
$dest     = $source.DIRECTORY_SEPARATOR."packages";

$modulesDir = $source.DIRECTORY_SEPARATOR."modules";
$pluginsDir = $source.DIRECTORY_SEPARATOR."plugins";

$package = new Package($source, $dest);
$package->setModulesDir($modulesDir);
$package->setPluginsDir($pluginsDir);

/*** Build Modules ***/
$package->buildModules();

/*** Build Plugins ***/
$package->buildPlugins();

/*** Build Component ***/
$comFiles = array(
    "install.php",
    "vipportfolio.xml",
    "site",
    "admin",
	"media",
);

$destFile = $dest.DIRECTORY_SEPARATOR."com_vipportfolio.zip";
$package->buildComponent($comFiles, $destFile);

/***** Build Package ***/
$pkgFiles = array(
    "pkg_vipportfolio.xml",
    "packages",
);

$destFile = $source.DIRECTORY_SEPARATOR."pkg_vipportfolio.zip";
$package->buildPackage($pkgFiles, $destFile);

class Package {
    
    private $source;
    private $dest;
    private $modulesDir;
    private $pluginsDir;
    
    public function __construct($source, $dest) {
        $this->source   = $source;
        $this->dest     = $dest;
    }

    public function setModulesDir($dir) {
        $this->modulesDir = $dir;
    }
    
    public function setPluginsDir($dir) {
        $this->pluginsDir = $dir;
    }
    
    public function buildModules() {
        
        $moduleFiles = array();
        
        $dir = new DirectoryIterator($this->modulesDir);
        foreach ($dir as $fileInfo) {
            if (!$fileInfo->isDot()) {
                
              $moduleFiles = array();
              // Get a module directory
              $sourceDir = $this->modulesDir.DIRECTORY_SEPARATOR.$fileInfo->getFilename();

               // Get the files of the module
              $moduleDirIterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($sourceDir));
               while($moduleDirIterator->valid()) {
                    if (!$moduleDirIterator->isDot()) {
                        $moduleFiles[] = $moduleDirIterator->getSubPathName(); 
                    }
                    $moduleDirIterator->next();
               }
               
               $destFile = $this->dest.DIRECTORY_SEPARATOR.$fileInfo->getFilename().".zip";
               
               // Build archive
               $this->createArchive($moduleFiles, $sourceDir, $destFile);
               
            }
        }

    }
    
    public function buildPlugins() {
        
        $types = array();
        $pluginFiles = array();
        
        // Get plugins types
        $pluginsDir = new DirectoryIterator($this->pluginsDir);
        foreach ($pluginsDir as $pluginTypeInfo) {
            if (!$pluginTypeInfo->isDot()) {
                $types[] = $pluginTypeInfo->getFilename();
            }
        }
        
        foreach( $types as $type ) {
            
            $typeDir = $this->pluginsDir.DIRECTORY_SEPARATOR.$type;
               
            // Get the plugin type
            $typeDirIterator = new DirectoryIterator($typeDir);
            foreach ($typeDirIterator as $pluginInfo) {
                
                if (!$pluginInfo->isDot()) {
                    $pluginFiles = array();
                   // Get a module directory
                   $sourceDir = $typeDir.DIRECTORY_SEPARATOR.$pluginInfo->getFilename();

                   // Get the files of the module
                   $pluginDirIterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($sourceDir));
                   while($pluginDirIterator->valid()) {
                        if (!$pluginDirIterator->isDot()) {
                            $pluginFiles[] = $pluginDirIterator->getSubPathName(); 
                        }
                        $pluginDirIterator->next();
                   }
                
                   $destFile = $this->dest.DIRECTORY_SEPARATOR.$pluginInfo->getFilename().".zip";
                   
                   // Build archive
                   $this->createArchive($pluginFiles, $sourceDir, $destFile);
                }
            }
            
        }

    }
    
    public function buildComponent( $files, $destFile ) {
        
        $componentFiles = array();
        foreach($files as $file) {
           $filePath = $this->source.DIRECTORY_SEPARATOR.$file;
           if(!is_dir($filePath)) {
               $componentFiles[] = $file;
           } else {
               $comDirIterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($filePath));
               while($comDirIterator->valid()) {
                    if (!$comDirIterator->isDot()) {
                        $componentFiles[] = $file.DIRECTORY_SEPARATOR.$comDirIterator->getSubPathName(); 
                    }
                    $comDirIterator->next();
               }
           }
        }
       // Build archive
       $this->createArchive($componentFiles, $this->source, $destFile);
                   
    }
    
    public function buildPackage( $files, $destFile) {
        
        $packageFiles = array();
        foreach($files as $file) {
           $filePath = $this->source.DIRECTORY_SEPARATOR.$file;
           if(!is_dir($filePath)) {
               $packageFiles[] = $file;
           } else {
               $pkgDirIterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($filePath));
               while($pkgDirIterator->valid()) {
                    if (!$pkgDirIterator->isDot()) {
                        $packageFiles[] = $file.DIRECTORY_SEPARATOR.$pkgDirIterator->getSubPathName(); 
                    }
                    $pkgDirIterator->next();
               }
           }
        }
       // Build archive
       $this->createArchive($packageFiles, $this->source, $destFile);
                   
    }
    
    /**
     * Archive the files of the extension
     */
    private function createArchive($files, $sourceDir, $destFile) {
        
        echo "DEST:".$destFile."\n";
//        var_dump($files);exit;
        $zip = new ZipArchive();
        
        if ($zip->open($destFile, ZIPARCHIVE::CREATE)!==TRUE) {
            exit("cannot open <$dest>\n");
        }
        
        foreach($files as $file) {
            $sourceFile = $sourceDir.DIRECTORY_SEPARATOR.$file;
            
            if(false === $zip->addFile($sourceFile, $file)) {
                echo "Error on: ".$file."\n";
            }
        }
        
        echo "numfiles: " . $zip->numFiles . "\n";
        echo "status:" . $zip->status . "\n";
        $zip->close();
        
    }
    
}