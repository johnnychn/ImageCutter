<?php

/**
 * Created by PhpStorm.
 * User: johnny
 * Date: 16/9/10
 * Time: 下午10:07
 */
class ImageCutter {
    var $image;
    var $image_url;
    var $ext;
    var $export_ext;
    var $thumbnail_name='_thumbnail';

    function __construct($image_url, $export = null) {
        if (!file_exists($image_url)) {
            return false;
        }

        @chmod($image_url, 0644);
        $this->image_url = $image_url;
        $this->ext = substr(strrchr($this->image_url, '.'), 1);
        if (!$export) {
            $this->export_ext = strtolower($this->ext);
        } else {
            $this->export_ext = strtolower($export);
        }
        $this->createImage();

    }

    private function createImage() {
        $ImgInfo = getimagesize($this->image_url);
        switch ($ImgInfo[2]) {
            case 1:
                $this->image = @imagecreatefromgif($this->image_url);
                break;
            case 2:
                $this->image = @imagecreatefromjpeg($this->image_url);
                break;
            case 3:
                $this->image = @imagecreatefrompng($this->image_url);
                break;
        }
    }


    private function browser($image, $quality = 75) {
        header('Content-Type: image/jpeg');

        switch ($this->export_ext) {
            case 'jpg':
            case 'jpeg':
                imagejpeg($image, null, $quality);
                break;
            case 'gif':
                imagegif($image);
                break;
            case 'png':
                imagepng($image);
                break;
        }

    }

    private function save($image, $url = null, $quality = 75) {
        if (is_int($url)) {
            $quality = $url;
            $url = null;
        }

        if (!$url) {
            $url = $this->image_url;
        }

        if (!is_writable($url)) {
            echo 'File(' . $url . ") is unwritable, please check file Permissions";
        }

        switch ($this->export_ext) {
            case 'jpg':
            case 'jpeg':
                imagejpeg($image, $url, $quality);
                break;
            case 'gif':
                imagegif($image, $url);
                break;
            case 'png':
                imagepng($image, $url);
                break;
        }

    }

    public function resize($new_width = 600, $new_height = 600, $quality = 75, $browser = false) {
        $w = imagesx($this->image);
        $h = imagesy($this->image);
        $width = $w;
        $height = $h;
        if ($width > $new_width) {
            $Par = $new_width / $width;
            $width = $new_width;
            $height = $height * $Par;
            if ($height > $new_height) {
                $Par = $new_height / $height;
                $height = $new_height;
                $width = $width * $Par;
            }
        } else if ($height > $new_height) {
            $Par = $new_height / $height;
            $height = $new_height;
            $width = $width * $Par;
            if ($width > $new_width) {
                $Par = $new_width / $width;
                $width = $new_width;
                $height = $height * $Par;
            }
        }

        $nImg = imagecreatetruecolor($width, $height);  //新建一个真彩色画布
        imagecopyresampled($nImg, $this->image, 0, 0, 0, 0, $width, $height, $w, $h);//重采样拷贝部分图像并调整大小

        if ($browser) {
            $this->browser($nImg, $quality);
        } else {
            $this->save($nImg, $quality);
        }
        return $this->image_url;

    }

    public function thumbnail($new_width = 200, $new_height = 200, $quality = 75, $browser = false) {

        $new_name = dirname($this->image_url) . '/' . basename($this->image_url, "." . $this->ext) . $this->thumbnail_name.'.' . $this->export_ext;
        copy($this->image_url, $new_name);

        if (empty($this->image)) {
            //如果是生成缩略图的时候出错,则需要删掉已经复制的文件
            unlink($new_name);
            return false;
        }
        $w = imagesx($this->image);
        $h = imagesy($this->image);

        $nImg = imagecreatetruecolor($new_width, $new_height);
        if ($h / $w > $new_height / $new_width) { //高比较大

            $height = $h * $new_width / $w;
            $IntNH = $height - $new_height;
            imagecopyresampled($nImg, $this->image, 0, -$IntNH / 2, 0, 0, $new_width, $height, $w, $h);
        } else {  //宽比较大

            $width = $w * $new_height / $h;
            $IntNW = $width - $new_width;
            imagecopyresampled($nImg, $this->image, -$IntNW / 2, 0, 0, 0, $width, $new_height, $w, $h);
        }
        if ($browser) {
            $this->browser($nImg, $quality);
        } else {
            $this->save($nImg, $new_name, $quality);
        }
        return $new_name;

    }

}