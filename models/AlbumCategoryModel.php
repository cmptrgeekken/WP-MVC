<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Ken
 * Date: 2/5/12
 * Time: 4:30 PM
 * To change this template use File | Settings | File Templates.
 */
class AlbumCategoryModel
{
    public $id;

    public $name;

    public function __construct($id, $name)
    {
        $this->id   = $id;
        $this->name = $name;
    }
}
