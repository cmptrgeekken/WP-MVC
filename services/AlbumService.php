<?php
require_once TEMPLATEPATH . '/lib/phpSmug/phpSmug.php';

class AlbumService

{
    protected $_smug;

    public function __construct()
    {
        $this->_smug = new phpSmug(
            'APIKey='      . BlogConfiguration::$smugApiKey
        );

        $this->_smug->login();
    }

    public function getAlbum($id, $key)
    {
        $smugAlbum = $this->_smug->albums_getInfo(
            "AlbumID=$id",
            "AlbumKey=$key",
            "Heavy=1"
        );

        $album = $this->_loadAlbum($smugAlbum);
        $album->images = $this->getAlbumImages($id, $key);

        return $album;
    }

    public function getAlbums($loadImages = false)
    {
        $transient = get_transient('smug_getAlbums_' . $loadImages);
        if ($transient !== false) {
            return $transient;
        }

        $albums = array();
        $smugAlbums = $this->_smug->albums_get(
            'NickName=' . BlogConfiguration::$smugNickname,
            'Heavy=1',
            'Empty=false'
        );

        foreach($smugAlbums as $smugAlbum) {
            $album = $this->_loadAlbum($smugAlbum, $loadImages);
            if ($loadImages) {
                $album->images = $this->getAlbumImages($album->id, $album->key);
            }

            $category = $album->category;
            $subcategory = $album->subCategory;

            if (!empty($category)) {
                if (!empty($subcategory)) {
                    $albums[$category->name][$subcategory->name][$album->title] = $album;
                } else {
                    $albums[$category->name]['_Global'][$album->title] = $album;
                }
            } else {
                $albums['_Global'][] = $album;
            }
        }


        set_transient('smug_getAlbums_' . $loadImages, $albums, 600);

        return $albums;
    }

    public function getFeaturedAlbums()
    {
        $albums = array();
        $smugFeaturedAlbums = $this->_smug->featured_albums_get(
            'NickName=' . BlogConfiguration::$smugNickname
        );

        foreach($smugFeaturedAlbums['Albums'] as $smugFeaturedAlbum) {
            $albums[] = $this->getAlbum(
                $smugFeaturedAlbum['id'],
                $smugFeaturedAlbum['Key']
            );
        }

        return $albums;
    }

    public function getAlbumImages($id, $key)
    {
        $images = array();
        $smugImages = $this->_smug->images_get(
            "AlbumID=$id",
            "AlbumKey=$key",
            "Heavy=1"
        );

        foreach($smugImages['Images'] as $smugImage) {
            $images[] = $this->_loadImage($smugImage);
        }

        return $images;
    }

    protected function _loadAlbum($smugAlbum)
    {
        $album              = new AlbumModel();
        $album->id          = $smugAlbum['id'];
        $album->key         = $smugAlbum['Key'];
        $album->title       = $smugAlbum['Title'];
        $album->imageCount  = $smugAlbum['ImageCount'];
        $album->description = $smugAlbum['Description'];
        if (isset($smugAlbum['Category'])) {
            $album->category = new AlbumCategoryModel(
                $smugAlbum['Category']['id'],
                $smugAlbum['Category']['Name']
            );
        }

        if (isset($smugAlbum['SubCategory'])) {
            $album->subCategory = new AlbumCategoryModel(
                $smugAlbum['SubCategory']['id'],
                $smugAlbum['SubCategory']['Name']
            );
        }

        return $album;
    }

    protected function _loadImage($smugImage)
    {
        $image              = new ImageModel();
        $image->id          = $smugImage['id'];
        $image->key         = $smugImage['Key'];
        $image->caption     = $smugImage['Caption'];
        $image->width       = $smugImage['Width'];
        $image->height      = $smugImage['Height'];
        $image->thumbUrl    = $smugImage['ThumbURL'];
        $image->tinyUrl     = $smugImage['TinyURL'];
        $image->smallUrl    = $smugImage['SmallURL'];
        $image->mediumUrl   = $smugImage['MediumURL'];
        $image->largeUrl    = $smugImage['LargeURL'];
        $image->xLargeUrl   = isset($smugImage['XLargeURL'])   ? $smugImage['XLargeURL']   : null;
        $image->xxLargeUrl  = isset($smugImage['X2LargeURL'])  ? $smugImage['X2LargeURL']  : null;
        $image->xxxLargeURL = isset($smugImage['X3LargeURL'])  ? $smugImage['X3LargeURL']  : null;
        $image->originalUrl = isset($smugImage['OriginalURL']) ? $smugImage['OriginalURL'] : null;
        $image->oneUpUrl    = $smugImage['URL'];

        return $image;
    }
}
