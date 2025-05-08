<?php

namespace App\Traits;

use App\Models\GeneralSetting;
use App\Models\Setting;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Cache;

trait InstallerManager
{


    private function _isPurchased() :mixed{
         return true;
    }



     public function is_installed() :bool{


        try {
            $logFile   = storage_path(base64_decode(config('installer.cacheFile')));
            $tableName = 'general_settings';

            DB::connection()->getPdo();
            if(!DB::connection()->getDatabaseName() || !file_exists($logFile) || !Schema::hasTable($tableName) ){
                return false;
            }

            return true;
        } catch (\Exception $ex) {
            return false;
        }


     }


     public function checkRequirements(array $requirements) :array{


         $results = [];

         foreach ($requirements as $type => $requirement) {
             switch ($type) {

                 case 'php':
                     foreach ($requirements[$type] as $requirement) {
                         $results['requirements'][$type][$requirement] = true;

                         if (!extension_loaded($requirement)) {
                             $results['requirements'][$type][$requirement] = false;
                             $results['errors'] = true;
                         }
                     }
                     break;
                 case 'apache':
                     foreach ($requirements[$type] as $requirement) {
                         if (function_exists('apache_get_modules')) {
                             $results['requirements'][$type][$requirement] = true;

                             if (! in_array($requirement, apache_get_modules())) {
                                 $results['requirements'][$type][$requirement] = false;
                                 $results['errors'] = true;
                             }
                         }
                     }
                     break;
             }
         }



         return $results;

     }

     private function _setSystemRequirments(bool $flag) :void{
         session()->put('system_requirments',$flag);
     }





     /**
      * Get current Php version information.
      *
      * @return array
      */
     private static function getPhpVersionInfo()
     {
         $currentVersionFull = PHP_VERSION;
         preg_match("#^\d+(\.\d+)*#", $currentVersionFull, $filtered);
         $currentVersion = $filtered[0];

         return [
             'full' => $currentVersionFull,
             'version' => $currentVersion,
         ];
     }





     /**
      * Check PHP version requirement.
      *
      * @return array
      */
     public function checkPHPversion(string $minPhpVersion = null) :array
     {
         $minVersionPhp = $minPhpVersion;
         $currentPhpVersion = $this->getPhpVersionInfo();
         $supported = false;


         if (version_compare($currentPhpVersion['version'], $minVersionPhp) >= 0) {
             $supported = true;
         }

         $phpStatus = [
             'full' => $currentPhpVersion['full'],
             'current' => $currentPhpVersion['version'],
             'minimum' => $minVersionPhp,
             'supported' => $supported,
         ];

         return $phpStatus;
     }



     /** file permissions */


     public function permissionsCheck(array $folders) :array{


         foreach ($folders as $folder => $permission) {
             if (!($this->getPermission($folder) >= $permission)) {
                 $permissions [] =  $this->addFileAndSetErrors($folder, $permission, false);
             } else {
                 $permissions [] =  $this->addFile($folder, $permission, true);
             }
         }


         return $permissions;


     }


     /**
      * Get a folder permission.
      *
      * @param $folder
      * @return string
      */
     private function getPermission($folder)
     {
         return substr(sprintf('%o', fileperms(base_path($folder))), -4);
     }


     /**
      * Add the file and set the errors.
      *
      * @param $folder
      * @param $permission
      * @param $isSet
      */
     private function addFileAndSetErrors($folder, $permission, $isSet) :array
     {
         return $this->addFile($folder, $permission, $isSet);
     }


     /**
      * Add the file to the list of results.
      *
      * @param $folder
      * @param $permission
      * @param $isSet
      */
     private function addFile($folder, $permission, $isSet) :array
     {
         return [
             'folder' => $folder,
             'permission' => $permission,
             'isSet' => $isSet,
         ];

     }



     private function _envatoVerification(Request $request) : mixed {

         if($this->_registerDomain()){
            //  return $this->_validatePurchaseKey($request->input(base64_decode('cHVyY2hhc2VfY29kZQ==')));
         }
         return true;

     }

     private function _registerDomain() :mixed {

         try {
             $ch = curl_init();
             $postParams = [
                 base64_decode('YnV5ZXJfZG9tYWlu') => url('/'),
                 base64_decode('c29mdHdhcmVfaWQ=') => config('installer.software_id') ?? 'CRT0001=='
             ];

             $data = http_build_query($postParams);
             $postingData = base64_decode("aHR0cHM6Ly92ZXJpZnkua29kZXBpeGVsLmNvbQ==").'?'.$data;

             curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
             curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
             curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
             curl_setopt($ch, CURLOPT_URL, $postingData);
             curl_setopt($ch, CURLOPT_TIMEOUT, 80);
             $response = json_decode(curl_exec($ch));
             curl_close($ch);
             return $response->status;
         } catch (\Exception $ex) {
             return false;
         }


     }


     private function _validatePurchaseKey(string $key) :mixed {

         $params[base64_decode('cHVyY2hhc2VkX2NvZGU=')] = $key;
         $params[base64_decode('YnV5ZXJfZG9tYWlu')]     = url('/');

         try {
             $ch = curl_init();
             $data = http_build_query($params);

             $postingData = base64_decode("aHR0cHM6Ly92ZXJpZnkua29kZXBpeGVsLmNvbQ==")."?".$data;


             curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
             curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
             curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
             curl_setopt($ch, CURLOPT_URL, $postingData);
             curl_setopt($ch, CURLOPT_TIMEOUT, 80);
             $response = curl_exec($ch);
             curl_close($ch);
             $response = json_decode( $response);
             return  $response->status;
         } catch (\Exception $e) {
             return false;
         }

     }

     private  function _chekcDbConnection(Request $request) :mixed {

         try {
             if (@mysqli_connect($request->input('db_host'), $request->input('db_username'),  $request->input('db_password'), $request->input('db_database') , $request->input('db_port'))) {
                 return true;
             }
             return false;
         }catch(\Exception $exception){
             return false;
         }
     }



     private  function _isDbEmpty() :mixed {

         try {
             $servername = env('DB_HOST');
             $username   = env('DB_USERNAME');
             $password   = env('DB_PASSWORD');
             $dbname     = env('DB_DATABASE');
             $conn       = new \mysqli($servername, $username, $password, $dbname);

             $result = $conn->query("SHOW TABLES");
             $conn->close();
             if ($result->num_rows > 0) {
                 return false;
             }
             return true;

         } catch (Exception $e) {
            return false;
         }
     }


     private  function _envConfig(Request $request) :mixed {


         try {

             $key = base64_encode(random_bytes(32));
             $appName = config('installer.app_name');
             $output =
                 'APP_NAME=' . $appName . PHP_EOL .
                 'APP_ENV=live' . PHP_EOL .
                 'APP_KEY=base64:' . $key . PHP_EOL .
                 'APP_DEBUG=false' . PHP_EOL .
                 'APP_INSTALL=true' . PHP_EOL .
                 'APP_LOG_LEVEL=debug' . PHP_EOL .
                 'APP_MODE=live' . PHP_EOL .
                 'APP_URL=' . URL::to('/') . PHP_EOL .

                 'DB_CONNECTION=mysql' . PHP_EOL .
                 'DB_HOST=' . $request->input("db_host") . PHP_EOL .
                 'DB_PORT=' . $request->input("db_port") . PHP_EOL .
                 'DB_DATABASE=' . $request->input("db_database") . PHP_EOL .
                 'DB_USERNAME=' . $request->input("db_username") . PHP_EOL .
                 'DB_PASSWORD=' . $request->input("db_password") . PHP_EOL .

                 'BROADCAST_DRIVER=log' . PHP_EOL .
                 'CACHE_DRIVER=file' . PHP_EOL .
                 'SESSION_DRIVER=file' . PHP_EOL .
                 'SESSION_LIFETIME=120' . PHP_EOL .
                 'QUEUE_DRIVER=sync' . PHP_EOL .

                 'REDIS_HOST=127.0.0.1' . PHP_EOL .
                 'REDIS_PASSWORD=null' . PHP_EOL .
                 'REDIS_PORT=6379' . PHP_EOL .

                 'PUSHER_APP_ID=' . PHP_EOL .
                 'PUSHER_APP_KEY=' . PHP_EOL .
                 'PUSHER_APP_SECRET=' . PHP_EOL .
                 'PUSHER_APP_CLUSTER=mt1' . PHP_EOL;

             $file = fopen(base_path('.env'), 'w');
             fwrite($file, $output);
             fclose($file);

             $path = base_path('.env');
             if (file_exists($path)) {
                 return true;
             }

             return false;

         } catch (\Throwable $th) {

             return false;
         }

     }

        private function _dbMigrate(bool $forceImport = false) :void{

            if($forceImport){
                Artisan::call('db:wipe', ['--force' => true]);
            }
            $sqlFile = resource_path('database/database.sql');
            DB::unprepared(file_get_contents($sqlFile));
        }
        private function _dbSeed() :void{

            Artisan::call('db:seed', ['--force' => true]);
        }


     private function _systemInstalled(string $purchaseKey = null ,string $envatoUsername = null ) :void {

         $this->_updateSetting();
         $message ="INSTALLED_AT:".Carbon::now();
         $logFile = storage_path(base64_decode(config('installer.cacheFile')));

         if (file_exists($logFile)) {
             unlink($logFile);
         }
         file_put_contents($logFile, $message);
         optimize_clear();

     }


     private function _updateSetting() :void {

        Setting::updateOrInsert(
            ['key'    => 'app_version'],
            ['value'  =>  Arr::get(config("installer.core"),'appVersion',1.0)]
        );
        Setting::updateOrInsert(
            ['key'    => 'system_installed_at'],
            ['value'  => Carbon::now()]
        );



     }
}
