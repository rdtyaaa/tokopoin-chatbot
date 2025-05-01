<?php

namespace App\Traits;

use App\Models\GeneralSetting;
use App\Models\Setting;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Artisan;
use ZipArchive;
use Illuminate\Support\Facades\File;

use App\Traits\InstallerManager;
trait SystemUpdateManager
{


    use InstallerManager;

    /**
     * Store a new addon 
     *
     * @param Request $request
     * @return array
     */
     private function _addon(Request $request) : array {


        try {

            if(!$this->_validatePurchaseKey($request->purchase_key)) return [
                'status'  => false,
                'message' => translate('Invalid purchase key'),
            ];

            ini_set('memory_limit', '-1');
            ini_set('max_input_time', '300'); 
            ini_set('max_execution_time', '300');
            ini_set('upload_max_filesize', '1G'); 
            ini_set('post_max_size', '1G'); 


            $zipFile = $request->file('updateFile');
            $basePath = base_path('/storage/app/public/temp_update/');
            
            if (!file_exists($basePath)) {
                mkdir($basePath, 0777, true);
            }

            // Move the uploaded zip file to the temp directory
            $zipFile->move($basePath, $zipFile->getClientOriginalName());

            // Open the zip file
            $zip = new ZipArchive;
            $res = $zip->open($basePath . $zipFile->getClientOriginalName());
             
            if (!$res) {
                $this->removeDirectory($basePath );
                return [
                    'status'  => false,
                    'message' => translate('Error! Could not open File'),
                ];
            } 

            $zip->extractTo($basePath);
            $zip->close();

            // Read configuration file
            $configFilePath = $basePath.'config.json';
            $configJson = json_decode(file_get_contents($configFilePath), true);

            if (empty($configJson)) {
                $this->removeDirectory($basePath );
                return [
                    'status'  => false,
                    'message' => translate('Error! No Configuration file found'),
                ];
            }

            $src = storage_path('app/public/temp_update');
            $dst = dirname(base_path());

            if($this->copyDirectory($src, $dst)){

                // Copy files from temp directory to destination directory
                $this->copyDirectory($src, $dst);

                //Run migrations, seeders & shell commands
                $this->_runMigrations($configJson);
                $this->_runSeeder($configJson);
                
                optimize_clear();
                $this->removeDirectory($basePath );
                return [
                    'status'  => true,
                    'message' => translate('Addon added successfully'),
                ];

            }
            
        } catch (\Exception $ex) {
            $this->removeDirectory($basePath );
            return [
                'status'  => false,
                'message' => strip_tags($ex->getMessage()),
            ];
        }

        optimize_clear();
        $this->removeDirectory($basePath );
        return [
            'status'  => false,
            'message' => translate('Invalid addon'),
        ];

     }




    
    /**
     * Copy directory
     *
     * @param string $src
     * @param string $dst
     * @return boolean
     */
    public function copyDirectory(string $src, string $dst) :bool {

        try {
            $dir = opendir($src);
            @mkdir($dst);
            while (false !== ($file = readdir($dir))) {
                if (($file != '.') && ($file != '..')) {
                    if (is_dir($src . '/' . $file)) {
                        $this->copyDirectory($src . '/' . $file, $dst . '/' . $file);
                    } else {
                        copy($src . '/' . $file, $dst . '/' . $file);
                    }
                }
            }
            closedir($dir);
        } catch (\Exception $e) {
           return false;
        }

        return true;
    }


    
    /**
     * delete directory
     *
     * @param string $dirname
     * @return boolean
     */
    public function deleteDirectory(string $dirname) :bool {

        try{
            if (!is_dir($dirname)){
                return false;
            }
            $dir_handle = opendir($dirname);

            if (!$dir_handle)
                return false;
            while ($file = readdir($dir_handle)) {
                if ($file != "." && $file != "..") {
                    if (!is_dir($dirname . "/" . $file))
                        unlink($dirname . "/" . $file);
                    else
                        $this->deleteDirectory($dirname . '/' . $file);
                }
            }
            closedir($dir_handle);
            rmdir($dirname);
            return true;
        }
        catch (\Exception $e) {
            return false;
        }
    }




    

    /**
     * Run php shell commands
     *
     * @param array $json
     * @return void
     */
    private function _runShellcommands(array $json) :void {

        $commands = Arr::get($json , 'shell_commands' ,[]);
        if(count($commands) > 0){
            foreach ($commands as $command) {
                $res = shell_exec($command);
                sleep(1);
            }
        }
    }


    /**
     * Undocumented function
     *
     * @param string $basePath
     * @return void
     */
    public function removeDirectory(string $basePath) : void  {
        
        if (File::exists($basePath)) {
            File::deleteDirectory($basePath);
        }
    }



    /**
     * Run migration
     *
     * @param array $json
     * @return void
     */
    private function _runMigrations(array $json) :void {

        $migrations = Arr::get($json , 'migrations' ,[]);
        if(count($migrations) > 0){

            foreach ($migrations as $migration) {
                Artisan::call('migrate',
                    array(
                        '--path' => '2024_06_06_164227_create_countries_table',
                        '--force' => true));
            }
        }
    }


    /**
     * Run seeder
     *
     * @param array $json
     * @return void
     */
    private function _runSeeder(array $json) :void {

        $seeders = Arr::get($json , 'seeder' ,[]);

        if(count($seeders) > 0){
           
            foreach ($seeders  as $seeder) {
                Artisan::call('db:seed',
                    array(
                        '--class' => $seeder,
                        '--force' => true));
            }
        }
    }

    private function _getFormattedFiles (array $files) :array{

        $currentVersion  = (double) @site_settings("app_version")?? 1.0;
        $formattedFiles = [];
        foreach($files as $version => $file){
           if(version_compare($version, (string)$currentVersion, '>')){
              $formattedFiles [] =  $file;
           }
        }

        return array_unique(Arr::collapse($formattedFiles));

    }
   
}