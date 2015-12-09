<?php

/**
 * Базовый класс для всех классов, которые дают возможность пользователям загружать файлы на файловую систему.
 *
 * @author azazello
 */
abstract class FileUploader {

    /** Название класса */
    protected $CLASS;

    /** Тип для работы с базой */
    protected $DBTYPE;

    /** @var PsLoggerInterface */
    protected $LOGGER;

    /** @var SimpleDataCache */
    private $CACHE;

    /** @return DirItem */
    private function makeTmpDirItem() {
        $path = DirManager::uploads()->cdToHashFolder()->absDirPath();
        $tmpFilePath = tempnam($path, 'upload_');
        check_condition($tmpFilePath, 'Не удаётся создать временный файл');
        return DirItem::inst($tmpFilePath);
    }

    /**
     * Признак - сохраняется ли информация о загруженных файлах в базу данных?
     * Если да, то файлы после обработки не будут удаляться.
     */
    private function isStoreToDb() {
        return !empty($this->DBTYPE);
    }

    /**
     * Утверждение того, что класс работает с базой.
     */
    private function assertCanUseDb($__FUNCTION__) {
        check_condition($this->isStoreToDb(), 'Функиця ' . __CLASS__ . '#' . $__FUNCTION__ . ' не может быть вызвана, так как класс не работает в БД');
    }

    /**
     * Если в качестве $userId передан null, то будет возвращён код авторизованного
     * сейчас пользователя. Также будет проверен уровень доступа к работе с файлами.
     */
    private function checkUserId($userId) {
        AuthManager::checkAccess($this->getAuthType());
        return AuthManager::extractUserId4AuthType($userId, $this->getAuthType());
    }

    /**
     * Тип для хранения файлов в базе - должен ссылаться на константу UploadsBean::TYPE_XXX.
     * Если данный тип не задан, то работа с базой не ведётся.
     */
    protected abstract function getUploadType();

    /**
     * Уровень доступа, необходимый для загрузки файлов данного типа.
     */
    protected abstract function getAuthType();

    /**
     * Признак того, что класс самостоятельно может обработать загруженный файл.
     * 1. Это позволит его вызвать через ajax/FileUpload.
     * 2. Файл после обработки будет удалён (если только данный класс не работает с базой)
     */
    protected abstract function isAutonomous();

    /**
     * Утверждение того, что класс работает автономно.
     */
    public final function assertAutonomous($msg = 'файл не будет загружен') {
        check_condition($this->isAutonomous(), "Класс {$this->CLASS} не работает автономно, $msg");
    }

    /**
     * Метод вызывается после переноса файла во временную директорию и до его сохранения в базе,
     * позволяя пользователю провалидировать саму возможность загрузки файла.
     */
    protected abstract function onBeforeSave(DirItem $source, $userId, ArrayAdapter $params);

    /**
     * Метод вызывается уже после того, как все действия с файлом были произведены.
     */
    protected abstract function onAfterSave(DirItem $uploaded, $userId, ArrayAdapter $params);

    public final function getUploadedFilesIds($userId = null, $asc = true) {
        $this->assertCanUseDb(__FUNCTION__);
        $ids = UploadsBean::inst()->getFilesIds($this->DBTYPE, $userId, $asc);
        $this->LOGGER->info("Loaded {} file ids for user [$userId].", array_to_string($ids));
        return $ids;
    }

    public final function getUploadedFilesCount($userId = null) {
        $this->assertCanUseDb(__FUNCTION__);
        $cnt = UploadsBean::inst()->getFilesCount($this->DBTYPE, $userId);
        $this->LOGGER->info("Loaded [$cnt] files count for user [$userId].");
        return $cnt;
    }

    public final function hasUploadedFile($uploadId, $userId = null) {
        $this->assertCanUseDb(__FUNCTION__);
        $exists = UploadsBean::inst()->hasFile($this->DBTYPE, $uploadId, $userId);
        $this->LOGGER->info("Is file [$uploadId] exists for user [$userId] ? {}.", var_export($exists, true));
        return $exists;
    }

    /** @return DirItem */
    public final function getUploadedFileDi($uploadId, $userId = null) {
        if (!$this->CACHE->has($uploadId)) {
            $this->assertCanUseDb(__FUNCTION__);
            $this->assertAutonomous('файл не может быть загружен из БД');
            $file = UploadsBean::inst()->getFile($this->DBTYPE, $uploadId, $userId);
            $this->LOGGER->info("File [$uploadId] loaded from DB for user [$userId] ? {}.", var_export($file, true));
            $this->CACHE->set($uploadId, is_array($file) && array_key_exists('name', $file) ? DirItem::inst($file['name']) : null);
        }
        return $this->CACHE->get($uploadId);
    }

    public final function deleteUploadedFile($uploadId, $userId = null) {
        $this->assertCanUseDb(__FUNCTION__);
        //Удалять файл можно только для проверенного пользователя
        $userId = $this->checkUserId($userId);
        $deleted = UploadsBean::inst()->deleteFile($this->DBTYPE, $uploadId, $userId);
        $this->CACHE->remove($uploadId);
        $this->LOGGER->info("Delete file [$uploadId] for user [$userId]. Deleted ? {}.", var_export($deleted, true));
        return $deleted;
    }

    private static $saved = false;

    /** @return DirItem */
    public final function saveUploadedFile($mandatory = true, $userId = null, array $params = array()) {
        check_condition(!self::$saved, 'Файл уже был обработан классом ' . self::$saved);
        self::$saved = $this->CLASS;

        $userId = $this->checkUserId($userId);
        $this->LOGGER->info("Uploading file for user [$userId]. Mandatory ? {}.", var_export($mandatory, true));

        $file = new FILEAdapter($mandatory);
        $this->LOGGER->info($file);
        if (!$file->isValid()) {
            $this->LOGGER->info('Upload file is not valid, skip saving.');
            return null;
        }

        $source = $file->moveUploadedFileTo($this->makeTmpDirItem());
        $this->LOGGER->info("Uploaded file moved to $source.");

        return $this->uploadFileImpl($source, $file, $userId, $params);
    }

    /** @return DirItem */
    public final function makeUploadedFile(DirItem $source, $userId = null, array $params = array()) {
        return $this->uploadFileImpl($source, null, $userId, $params);
    }

    private function uploadFileImpl(DirItem $source, FILEAdapter $file = null, $userId = null, array $params = array()) {
        $userId = $this->checkUserId($userId);
        $this->LOGGER->info("Processing file upload for user [$userId], source $source.");

        $aa = ArrayAdapter::inst($params);

        $uploaded = $file ? $source : null;
        $originalName = $file ? $file->getOriginalName() : $source->getName();

        $dbMsg = null;

        try {
            $this->LOGGER->info('Calling onBeforeSave...');
            $dbMsg = $this->onBeforeSave($source, $userId, $aa);
            $this->LOGGER->info("\tDone!");
        } catch (Exception $ex) {
            $this->LOGGER->info('Error occured in onBeforeSave method: ' . $ex->getMessage());
            $this->LOGGER->info('Source file will be deleted ? {}.', var_export(!!$uploaded, true));
            if ($uploaded) {
                $uploaded->remove();
            }
            throw $ex;
        }

        if ($uploaded) {
            //Это уже и так загруженный файл
            $this->LOGGER->info('Source file is uploaded file');
        } else {
            $this->LOGGER->info('Move source file to uploads dir');
            $uploaded = $this->makeTmpDirItem();
            $source->copyTo($uploaded);
        }

        if ($this->LOGGER->isEnabled()) {
            $this->LOGGER->info("\tUploaded file: $uploaded");
            $this->LOGGER->info("\tOriginal name: [$originalName]");
            $this->LOGGER->info("\tMime: [{$uploaded->getMime()}]");
            $this->LOGGER->info("\tParams: " . array_to_string($params, false));
        }

        $uploadId = null;

        if ($this->isStoreToDb()) {
            $this->LOGGER->info("Saving upload file into database. DbMsg: '$dbMsg'.");

            try {
                $uploadId = UploadsBean::inst()->saveFileUpload(
                        $this->DBTYPE, //
                        $uploaded->getAbsPath(), //
                        $originalName, //
                        $uploaded->getMime(), //
                        $userId, //
                        $dbMsg);

                //Почистим кеш, вдруг мы запрашивали информацию по данному файлу
                $this->CACHE->remove($uploadId);
                $this->LOGGER->info("\tFile successfully saved, uploadId = $uploadId.");
            } catch (Exception $ex) {
                $this->LOGGER->info('Error occured while saving file to DB: ' . $ex->getMessage());

                $this->LOGGER->info('Deleting upload file...');
                $uploaded->remove();
                $uploaded = null;
                throw $ex;
            }

            $uploaded->setData('id', $uploadId);
        }

        try {
            $this->LOGGER->info('Calling onAfterSave...');
            $this->onAfterSave($uploaded, $userId, $aa);
            $this->LOGGER->info("\tDone!");
        } catch (Exception $ex) {
            $this->LOGGER->info('Error occured in onAfterSave method: ' . $ex->getMessage());

            if (is_numeric($uploadId)) {
                $this->LOGGER->info('Deleting db record...');
                UploadsBean::inst()->clearFileUpload($uploadId);
                $uploadId = null;
            }

            $this->LOGGER->info('Deleting upload file...');
            $uploaded->remove();
            $uploaded = null;

            throw $ex;
        }

        /*
         * Если класс работает автономно и не работает с базой, то файл нужно удалить.
         */
        if ($this->isAutonomous() && !$this->isStoreToDb()) {
            $this->LOGGER->info('Class is auto clean, deleting uploaded file...');
            $uploaded->remove();
            $uploaded = null;
        }

        $this->LOGGER->info('');
        return $uploaded;
    }

    /**
     * СИНГЛТОНЫ
     */
    private static $insts = array();

    /** @return FileUploader */
    public static final function inst($classPrefix = null) {
        $class = null;
        if ($classPrefix) {
            $class = ensure_ends_with($classPrefix, 'Uploader');
            check_condition(PsUtil::isInstanceOf($class, __CLASS__), "Class $class is not instance of " . __CLASS__);
        } else {
            $class = get_called_class();
        }
        return array_key_exists($class, self::$insts) ? self::$insts[$class] : self::$insts[$class] = new $class();
    }

    private function __construct() {
        $this->CLASS = get_called_class();
        $this->CACHE = new SimpleDataCache();
        $this->DBTYPE = $this->getUploadType();
        $this->LOGGER = PsLogger::inst($this->CLASS);
        $this->LOGGER->info('Instance created. Work with db ? {}. Is autonomous ? {}.', var_export($this->isStoreToDb(), true), var_export($this->isAutonomous(), true));
        $this->LOGGER->info();
    }

}

?>
