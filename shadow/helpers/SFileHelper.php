<?php
/**
 * Created by PhpStorm.
 * User: lxShaDoWxl
 * Date: 21.01.16
 * Time: 14:18
 */
namespace shadow\helpers;

use common\components\Debugger as d;
use yii\helpers\FileHelper;
use Yii;

class SFileHelper extends FileHelper
{
    public static function fileSize($path)
    {
        $bytes = filesize($path);
        if ($bytes >= 1073741824) {
            $bytes = number_format($bytes / 1073741824, 2) . ' Gb';
        } elseif ($bytes >= 1048576) {
            $bytes = number_format($bytes / 1048576, 2) . ' Mb';
        } elseif ($bytes >= 1024) {
            $bytes = number_format($bytes / 1024, 2) . ' Kb';
        } elseif ($bytes > 1) {
            $bytes = $bytes . ' bytes';
        } elseif ($bytes == 1) {
            $bytes = $bytes . ' byte';
        } else {
            $bytes = '0 bytes';
        }
        return $bytes;
    }

    public static function downloadFile($filePath = '')
    {
        // нужен для Internet Explorer, иначе Content-Disposition игнорируется
        if(ini_get('zlib.output_compression'))
            ini_set('zlib.output_compression', 'Off');

        if($filePath[0] == '@'){
            $filePath = Yii::getAlias($filePath);
        }

        if( $filePath == '' )
        {
            return false;
//            echo "ОШИБКА: не указано имя файла.";
//            exit;
        } elseif ( ! is_file( $filePath ) ) // проверяем существует ли указанный файл
        {
            return false;
//            echo "ОШИБКА: данного файла не существует.";
//            exit;
        };

        $file_extension = strtolower(substr(strrchr($filePath,'.'),1));

        // Разрешённые расширения фалов
        $permitted_extensions = [
            'pdf' => 'application/pdf',
//            'exe' => 'application/octet-stream',
//            'zip' => 'application/zip',
//            'doc' => 'application/msword',
//            'xls' => 'application/vnd.ms-excel',
//            'ppt' => 'application/vnd.ms-powerpoint',
//            'mp4' => 'video/mp4',
//            'mp3' => 'audio/mp3',
//            'gif' => 'image/gif',
            'png' => 'image/png',
            'jpeg' => 'image/jpg',
            'jpg' => 'image/jpg',
        ];

        $ctype = 'application/force-download';
        foreach($permitted_extensions as $ext => $type){
            if($ext == $file_extension){
                $ctype = $type;
            }
        }
        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Cache-Control: private",false); // нужен для некоторых браузеров
        header("Content-Type: $ctype");
        header("Content-Disposition: attachment; filename=\"" . basename($filePath) . "\";" );
        header("Content-Transfer-Encoding: binary");
        header("Content-Length: ".filesize($filePath)); // необходимо доделать подсчет размера файла по абсолютному пути
        readfile("$filePath");
        d::ajax('dd');
        return true;
    }

}//Class