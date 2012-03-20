<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Ken
 * Date: 2/4/12
 * Time: 7:08 PM
 * To change this template use File | Settings | File Templates.
 */
class HomeController
    extends BaseController
{
    protected $_as;

    public function __construct()
    {
        $this->_as = new AlbumService();
    }


    public function indexAction()
    {
        $featuredAlbum  = $this->_as->getAlbum(BlogConfiguration::$smugFavoritesAlbumId, BlogConfiguration::$smugFavoritesAlbumKey);

        shuffle($featuredAlbum);

        return $this->_view($featuredAlbum->images);
    }

    public function newIndexAction()
    {
        $featuredAlbum  = $this->_as->getAlbum(BlogConfiguration::$smugFavoritesAlbumId, BlogConfiguration::$smugFavoritesAlbumKey);

        shuffle($featuredAlbum);

        return $this->_view($featuredAlbum->images);
    }

    public function cvAction()
    {
        return $this->_view();
    }

    public function galleryAction()
    {
        $albums = $this->_as->getAlbums(true);

        return $this->_view($albums);
    }

    public function galleryViewAction($albumId, $albumKey)
    {
        $album = $this->_as->getAlbum($albumId, $albumKey);

        return $this->_view($album);
    }

    public function aboutAction()
    {
        return $this->_view();
    }
}
