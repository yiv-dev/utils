<?php

namespace YIVDEV\FILEHANDLER;

/**
 * FileHandler class to handle the file operations
 */
class FileHandler
{
    /**
     * save_file function
     *
     * @param string $filePath
     * @param [type] $data
     * @return String
     */
    public static function save_file(string $filePath, $data): String
    {
        try {
            $fp = fopen($filePath, 'w');
            fwrite($fp, $data);
            fclose($fp);

            return $filePath;
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage);
        }
    }

    /**
     * clean_dir function
     * delete all the files in the directory
     *
     * @param String $dir_name
     * @return boolean
     */
    public static function clean_dir(String $dir_name): bool
    {
        try {
            array_map('unlink', array_filter((array) glob($dir_name . DIRECTORY_SEPARATOR . '*')));
            return true;
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage);
        }
    }

    /**
     * clean_older_then function
     * delete all the files older then $days parameter
     *
     * @param String $dir_name
     * @param Int $days
     * @return boolean
     */
    public static function clean_older_then(String $dir_name, Int $days): bool
    {
        try {
            $files = glob($dir_name . DIRECTORY_SEPARATOR . '*');
            $now   = time();

            foreach ($files as $file) {
                if (is_file($file)) {
                    if ($now - filemtime($file) >= 60 * 60 * 24 * $days) {
                        unlink($file);
                    }
                }
            }
            return true;
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage);
        }
    }

    /**
     * create_dir_if_not_exist function
     *
     * @param String $dir_name
     * @return boolean
     */
    public static function create_dir_if_not_exist(String $dir_name): bool
    {
        try {
            if (!file_exists($dir_name)) {
                mkdir($dir_name, 0777, true);
            }
            return true;
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage);
        }
    }

    /**
     * Zip the array of the files function
     *
     * @param array $files
     * @return String
     */
    public static function zip_files(array $files): String
    {
        try {
            $zip = new \ZipArchive();

            //make the temp dir
            $tempfile = tempnam(sys_get_temp_dir(), '');
            if (file_exists($tempfile)) {
                unlink($tempfile);
            }
            mkdir($tempfile);

            //let`s make the zip archive
            $dir = $tempfile;
            $filename = 'file-' . date('Y-m-d--H:i:s') . uniqid() . '.zip';
            $pathToZipFile = $dir . DIRECTORY_SEPARATOR . $filename;
            $zip->open($pathToZipFile, \ZipArchive::CREATE);
            foreach ($files as $file) {
                $zip->addFile($file, basename($file));
            }
            $zip->close();

            return $pathToZipFile;
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage);
        }
    }

    /**
     * Get file name without extention function
     *
     * @param [type] $filename
     * @return String
     */
    public static function getFileNameWOExtension(String $filename): string
    {
        $ext = self::getExtension($filename);
        return basename($filename, '.' . $ext);
    }

    /**
     * getExtension function
     * Get the extention of the filename
     *
     * @param String $filename
     * @return String
     */
    public static function getExtension(String $filename): String
    {
        return substr(strrchr($filename, '.'), 1);
    }
}
