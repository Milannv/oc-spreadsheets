<?php
namespace OCA\Spreadsheets;

class AuthorStorage {

    private $storage;

    public function __construct($storage){
        $this->storage = $storage;
    }

    public function getContent($id) {
        try {
            $file = $this->storage->getById($id);
            if($file instanceof \OCP\Files\File) {
                return $file->getContent();
            } else {
                throw new StorageException('Can not read from folder');
            }
        } catch(\OCP\Files\NotFoundException $e) {
            throw new StorageException('File does not exist');
        }
    }
}