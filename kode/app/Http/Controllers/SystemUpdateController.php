<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\GeneralSetting;
use App\Models\Settings;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\View\View;
use ZipArchive;
use Illuminate\Support\Facades\Artisan;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;

class SystemUpdateController extends Controller
{
    

    public function __construct(){

    }


    public function init() :View {


        return view('admin.system_update',[
            "title" => translate("Update System")
        ]);
    }


    /**
     * update the system
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function update(Request $request) :RedirectResponse | array {

        ini_set('memory_limit', '-1');
        ini_set('max_input_time', '300'); 
        ini_set('max_execution_time', '300');
        ini_set('upload_max_filesize', '1G'); 
        ini_set('post_max_size', '1G'); 
    
        $request->validate([
            'updateFile' => ['required', 'mimes:zip'],
        ], [
            'updateFile.required' => translate('File field is required')
        ]);

        $basePath = base_path('/storage/app/public/temp_update/');

        try {
            if ($request->hasFile('updateFile')) {
                $zipFile = $request->file('updateFile');
           
                
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

                if (empty($configJson) || empty($configJson['version'])) {
                    $this->removeDirectory($basePath );
                    return [
                        'status'  => false,
                        'message' => translate('Error! No Configuration file found'),
                    ];
                }


                $newVersion      = (double) $configJson['version'];
                $currentVersion  = (double) @site_settings("app_version")?? 1.1;

                $src = storage_path('app/public/temp_update');
                $dst = dirname(base_path());



                if($newVersion  > $currentVersion){
                    if($this->copyDirectory($src, $dst)){

                        // Copy files from temp directory to destination directory
                        $this->copyDirectory($src, $dst);

                        //Run migrations, seeders & shell commands
                        $this->_runMigrations($configJson);
                        $this->_runSeeder($configJson);
                        $this->_runShellcommands($configJson);

                        // update setting
                        DB::table('settings')->upsert([
                            ['key' => 'app_version', 'value' => $newVersion],
                            ['key' => 'system_installed_at', 'value' =>  Carbon::now()],
                        ], ['key'], ['value']);

                        optimize_clear();
                        $this->removeDirectory($basePath );
                        return [
                            'status'  => true,
                            'message' => translate('Your system updated successfully'),
                        ];

                    }
                }
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
            'message' => translate('Your system is currently running the latest version.'),
        ];
    }


    private function _runShellcommands(array $json) :void{

        $commands = Arr::get($json , 'shell_commands' ,[]);
        if(count($commands) > 0){
            foreach ($commands as $command) {
                $res = shell_exec($command);
                sleep(1);
            }
        }
    }

    public function removeDirectory($basePath) {
        
        if (File::exists($basePath)) {
            File::deleteDirectory($basePath);
        }
    }


    private function _runMigrations(array $json) :void{

        $migrations = Arr::get($json , 'migrations' ,[]);
        if(count($migrations) > 0){
            $migrationFiles = $this->_getFormattedFiles($migrations);
            foreach ($migrationFiles as $migration) {
                Artisan::call('migrate',
                    array(
                        '--path' => $migration,
                        '--force' => true));
            }
        }
    }

    private function _runSeeder(array $json) :void{

        $seeders = Arr::get($json , 'seeder' ,[]);

        if(count($seeders) > 0){
            $seederFiles = $this->_getFormattedFiles($seeders);
            foreach ($seederFiles as $seeder) {
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
    



    /** Manual update process */

    

    /**
     * Manual update view
     *
     * @return View
     */
    public function manualUpdate() : View | RedirectResponse{
   

        if(!$this->_checkManualUpdate()){
            return redirect()->route('home')->with('success', translate('Your system is currently running the latest version.'));
        }

        return view('admin.manual_update',[
            "title" => translate("Update Application")
        ]);
    }

    


    /**
     * Manual update process
     *
     * @return RedirectResponse
     */
    public function manualUpdateApplication() : RedirectResponse{

        if(!$this->_checkManualUpdate()){
            return redirect()->route('home')->with('success', translate('Your system is currently running the latest version.'));
        }

        try {
            $baseURL = url('/');

            $configFilePath = $baseURL.'/'.'manual.json';
            $configJson     = json_decode(file_get_contents($configFilePath), true);
    
            $newVersion = (double) $configJson['version'];
    
            $this->_runMigrations($configJson);
            $this->_runSeeder($configJson);
            $this->_runShellcommands($configJson);
    
    
            // update setting
            DB::table('settings')->upsert([
                ['key' => 'app_version', 'value' => $newVersion],
                ['key' => 'system_installed_at', 'value' =>  Carbon::now()],
            ], ['key'], ['value']);

    
            return redirect()->route('home')->with('success',translate('Application updated'));
        } catch (\Exception $ex) {
            return back()->with('error',strip_tags( $ex->getMessage()));
        }
        




    }


    private function _checkManualUpdate() : bool{

        $baseURL = url('/');

        $configFilePath = $baseURL.'/'.'manual.json';
        $configJson     = json_decode(file_get_contents($configFilePath), true);


        if(is_array($configJson) && Arr::get($configJson , 'is_manual' , false) && (double)(Arr::get($configJson , 'version' , 1.0)) >(double)site_settings("app_version") ?? 1.0 ){
            return true;
        }

        return false;
    }


}
