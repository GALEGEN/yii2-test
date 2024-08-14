<?php

namespace app\models;

use yii\base\Model;
use yii\web\UploadedFile;

class ImageUpload extends Model
{
    public $image;
    
    public function rules() {
        return [
            [['image'], 'required'],
            [['image'], 'file', 'extensions' => 'jpg,png']
        ];
    }
    
    public function uploadFile(UploadedFile $file, $currentImage) {
        $this->image = $file;
        
        if($this->validate()) {
            $this->deleteCurrentImage($currentImage);

            return $this->saveImage($currentImage);
        }
    }
    
    private function getFolder() {
        return \Yii::getAlias('@web') . 'uploads/';
    }
    
    private function generateFilename($currentImage)
    {
        do {
            $uniqFile = uniqid($this->image->baseName);
        } while($this->fileExists($currentImage));
        
        return strtolower($uniqFile . '.' . $this->image->extension);
    }
    
    public function deleteCurrentImage($currentImage) {
        if($this->fileExists($currentImage))
        {
            unlink($this->getFolder() . $currentImage);
        }
    }
    
    public function fileExists($currentImage) {
        if(!empty($currentImage) && $currentImage != null)
        {
            return file_exists($this->getFolder() . $currentImage);
        }
    }
    
    public function saveImage($currentImage) {
        $filename = $this->generateFilename($currentImage);

        $this->image->saveAs($this->getFolder() . $filename);
        
        return $filename;
    }
}