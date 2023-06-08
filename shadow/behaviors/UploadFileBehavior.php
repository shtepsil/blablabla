<?php
/**
 * Created by PhpStorm.
 * User: lxShaDoWxl
 * Date: 31.07.15
 * Time: 14:27
 */
/*
public function behaviors(){
    return [
        [
            'class' => '\shadow\behaviors\UploadFileBehavior',
            'attributes' => ['img'],
        ],
    ];
}
*/
namespace shadow\behaviors;

use common\components\Debugger as d;
use shadow\helpers\StringHelper;
use shadow\SBehavior;
use Yii;
use yii\base\Exception;
use yii\base\InvalidCallException;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\helpers\FileHelper;
use yii\helpers\VarDumper;
use yii\web\UploadedFile;
use shadow\SResizeImg;

/**
 * Class UploadFileBehavior
 * @package shadow\behaviors
 * @property ActiveRecord $owner
 */

class UploadFileBehavior extends SBehavior
{
    const EVENT_AFTER_FILE_SAVE = 'afterFileSave';
    /** @var array Name of attributes which holds the attachment. */
    public $attributes = array('file');
    /** @var string Path template to use in storing files.5 */
    public $filePath = '@web_frontend/uploads/[[model]]/[[pk]]_[[attribute]].[[extension]]';
    /** @var string Path template to use cache in storing files */
    public $cacheFilePath = '@web_frontend/uploads/cache/[[model]]/[[size_image]]_[[pk]].[[extension]]';
    /** @var string Where to store images. */
    public $fileUrl = '/uploads/[[model]]/[[pk]]_[[attribute]].[[extension]]';
    /** @var string Where to store images in cache. */
    public $cacheFileUrl = '/uploads/cache/[[model]]/[[size_image]]_[[pk]].[[extension]]';
    /**
     * @var string Attribute used to link owner model with it's parent
     * @deprecated Use attribute_xxx placeholder instead
     */
    public $parentRelationAttribute;
    /** @var \yii\web\UploadedFile[] */
    protected $files;
    /**
     * @var boolean
     */
    protected $isValidate = false;
    /**
     * @var boolean
     */
    protected $id_key;

    public $path_return = '';
    public $stop_proccess = 0;

    /**
     * @inheritdoc
     */
    public function events()
    {
//        d::ajax('events');
        return [
            ActiveRecord::EVENT_BEFORE_VALIDATE => 'beforeValidate',
            ActiveRecord::EVENT_BEFORE_INSERT => 'beforeSave',
            ActiveRecord::EVENT_BEFORE_UPDATE => 'beforeSave',
            ActiveRecord::EVENT_AFTER_INSERT => 'afterSave',
            ActiveRecord::EVENT_AFTER_UPDATE => 'afterSave',
            ActiveRecord::EVENT_BEFORE_DELETE => 'beforeDelete',
        ];
    }

    public function beforeValidate()
    {
        $this->isValidate = true;
        foreach ($this->attributes as $attribute) {
            $this->addFiles($attribute);
        }
    }

    public function addFiles($attribute)
    {
        if ($this->owner->{$attribute} instanceof UploadedFile) {
            $this->files[$attribute] = $this->owner->{$attribute};
            return;
        }
        $this->files[$attribute] = UploadedFile::getInstance($this->owner, $attribute);
        if (!isset($this->files[$attribute]) || empty($this->files[$attribute])) {
            $this->files[$attribute] = UploadedFile::getInstanceByName($attribute);
        }
        if ($this->files[$attribute] instanceof UploadedFile) {
            $this->owner->{$attribute} = $this->files[$attribute];
        }
    }

    /**
     * Before save event.
     *
     * @throws \yii\base\InvalidConfigException
     */
    public function beforeSave()
    {
        if($this->stop_proccess == 1) return false;
        $oldModel = false;
        foreach ($this->attributes as $attribute) {
            if (!$this->isValidate) {
                $this->addFiles($attribute);
            }
            if ($this->files[$attribute] instanceof UploadedFile) {
                if (!$this->owner->isNewRecord) {
                    /** @var static $oldModel */
                    if (!$oldModel) {
                        $oldModel = $this->owner->findOne($this->owner->primaryKey);
                    }
                    $oldModel->cleanFile($attribute);
                    $oldModel->cleanCacheFiles($attribute);
                }else{
                    $this->id_key = uniqid();
                }
                $this->owner->{$attribute} = $this->resolvePath($this->fileUrl, $attribute);
            } else { // Fix html forms bug, when we have empty file field
                if (!$this->owner->isNewRecord && empty($this->owner->{$attribute})) {
                    $this->owner->{$attribute} = ArrayHelper::getValue($this->owner->oldAttributes, $attribute, null);
                }
            }
        }
    }

    /**
     * Removes files associated with attributes
     */
    public function cleanFiles()
    {
        foreach ($this->attributes as $attribute) {
            $this->cleanFile($attribute);
        }
    }

    /**
     * Removes file associated with attribute
     * @param string $attribute
     */
    public function cleanFile($attribute)
    {
        $path = $this->resolvePath($this->filePath, $attribute);
        @unlink($path);
    }

    /**
     * @param $attribute
	 * Этот метод больше ни где не испльзуется
	 * в связи с доработками он стал работать не правильно
     */
    public function cleanCacheFiles_old($attribute)
    {
        $sizes = SResizeImg::$_size_img_a;
        $cache_sizes = Yii::$app->seo->resizes_imgs;
        if((isset($sizes) AND count($sizes)) AND (isset($cache_sizes) AND count($cache_sizes))){
            foreach($cache_sizes as $с_size){
                $path = $this->resolvePath($this->cacheFilePath, $attribute,$с_size);
                @unlink($path);
            }
        }
    }
	
    /**
     * @param $attribute
     */
    public function cleanCacheFiles($attribute)
    {
        $sizes = array_keys(SResizeImg::$_size_img_a);
        if(isset($sizes) AND count($sizes)){
            foreach($sizes as $с_size){
                $path = $this->resolvePath($this->cacheFilePath, $attribute, $с_size);
                @unlink($path);
            }
        }
    }
	
    /**
     * Replaces all placeholders in path variable with corresponding values
     *
     * @param string $path
     * @param string $attribute
     * @return string
     */
    public function resolvePath($path, $attribute, $size = '')
    {
        $path = Yii::getAlias($path);
        $pi = pathinfo($this->owner->{$attribute});
        $fileName = ArrayHelper::getValue($pi, 'filename');
        $extension = strtolower(ArrayHelper::getValue($pi, 'extension'));
        return preg_replace_callback('|\[\[([\w\_/]+)\]\]|', function ($matches) use ($fileName, $extension, $attribute, $size) {
            $name = $matches[1];
            switch ($name) {
                case 'size_image':
                    $sizes = SResizeImg::$_size_img_a;
                    return $sizes[$size]['width'].'x'.$sizes[$size]['height'];
                case 'extension':
                    return $extension;
                case 'filename':
                    return $fileName;
                case 'basename':
                    return $fileName . '.' . $extension;
                case 'app_root':
                    return Yii::getAlias('@app');
                case 'web_root':
                    return Yii::getAlias('@web_frontend');
                case 'base_url':
                    return Yii::getAlias('@web');
                case 'model':
                    $r = new \ReflectionClass($this->owner->className());
                    return lcfirst($r->getShortName());
                case 'attribute':
                    return lcfirst($attribute);
                case 'id':
                case 'pk':
                    if (!$this->id_key) {
                        $pk = implode('_', $this->owner->getPrimaryKey(true));
                        return lcfirst($pk);
                    }else{
                        return $this->id_key;
                    }
                case 'id_path':
                    return static::makeIdPath($this->owner->getPrimaryKey());
                case 'parent_id':
                    return $this->owner->{$this->parentRelationAttribute};
            }
            if (preg_match('|^attribute_(\w+)$|', $name, $am)) {
                $attribute = $am[1];
                return $this->owner->{$attribute};
            }
            return '[[' . $name . ']]';
        }, $path);
    }

    /**
     * @param integer $id
     * @return string
     */
    protected static function makeIdPath($id)
    {
        $id = is_array($id) ? implode('', $id) : $id;
        $length = 10;
        $id = str_pad($id, $length, '0', STR_PAD_RIGHT);
        $result = [];
        for ($i = 0; $i < $length; $i++) {
            $result[] = substr($id, $i, 1);
        }
        return implode('/', $result);
    }

    /**
     * @return void
     */
    public function saveFileAjax()
    {
        foreach ($this->attributes as $attribute) {
            $this->addFiles($attribute);
        }
        $this->id_key = $this->owner->id;
        $this->saveFiles();
    }

    /**
     * @return void
     */
    public function saveFiles()
    {
        foreach ($this->attributes as $attribute) {
            if ($this->files[$attribute] instanceof UploadedFile) {
                $path = $this->getUploadedFilePath($attribute);
                FileHelper::createDirectory(pathinfo($path, PATHINFO_DIRNAME), 0775, true);
                if (!$this->files[$attribute]->saveAs($path)) {
                    throw new Exception('File saving error.2');
                }
                if(method_exists($this->owner, 'afterFileSave')){
                    $this->path_return = StringHelper::getPartStrByCharacter($path, 'uploads', 'string_all_from_first');
                    $this->owner->on(static::EVENT_AFTER_FILE_SAVE, [$this->owner, 'afterFileSave'], $this->path_return);
                }
                // Сбросим массив, чтобы не было попыток повторной загрузки.
                $this->files = [];
                /*
                 * Для последующих событий ставим флаг, что для текущего запроса
                 * не нужно больше ничего делать.
                 */
                $this->stop_proccess = 1;
                $this->owner->trigger(static::EVENT_AFTER_FILE_SAVE);
            }
        }
    }

    /**
     * After save event.
     */
    public function afterSave()
    {
        foreach ($this->attributes as $attribute) {
            if (isset($this->files[$attribute]) AND $this->files[$attribute] instanceof UploadedFile) {
                $path = $this->getUploadedFilePath($attribute);
                FileHelper::createDirectory(pathinfo($path, PATHINFO_DIRNAME), 0775, true);
                if (!$this->files[$attribute]->saveAs($path)) {
                    throw new Exception('File saving error.2');
                }
                $this->owner->trigger(static::EVENT_AFTER_FILE_SAVE);
            }
        }
    }

    /**
     * @return void
     */
    public function deleteFileAjax($filePath)
    {
        $record = $this->owner;
        $filePath = Yii::getAlias('@web_frontend/' . $filePath);
        @unlink($filePath);
        if(method_exists($record, 'afterFileDelete')){
            $record->on($record::EVENT_AFTER_FILE_DELETE, [$record, 'afterFileDelete']);
        }
        $record->trigger($record::EVENT_AFTER_FILE_DELETE);
    }

    /**
     * Returns file path for attribute.
     *
     * @param string $attribute
     * @return string
     */
    public function getUploadedFilePath($attribute)
    {
        $behavior = static::getInstance($this->owner, $attribute);
        if (!$this->owner->{$attribute}) {
            return '';
        }
        return $behavior->resolvePath($behavior->filePath, $attribute);
    }

    /**
     * Returns behavior instance for specified class and attribute
     *
     * @param ActiveRecord $model
     * @param string $attribute
     * @return static
     */
    public static function getInstance(ActiveRecord $model, $attribute)
    {
        foreach ($model->behaviors as $behavior) {
            if ($behavior instanceof self && in_array($attribute, $behavior->attributes)) {
                return $behavior;
            }
        }
        throw new InvalidCallException('Missing behavior for attribute ' . VarDumper::dumpAsString($attribute));
    }

    /**
     * Before delete event.
     */
    public function beforeDelete()
    {
        $this->cleanFiles();
    }

    /**
     * Returns file url for the attribute.
     *
     * @param string $attribute
     * @return string|null
     */
    public function getUploadedFileUrl($attribute)
    {
        if (!$this->owner->{$attribute}) {
            return null;
        }
        $behavior = static::getInstance($this->owner, $attribute);
        return $behavior->resolvePath($behavior->fileUrl,$attribute);
    }
}