<?php
namespace ITECH\Datasource\Lib;

use Phalcon\Assets\Manager as AssetManager;
use Phalcon\Tag;

class Manager extends AssetManager
{
    /**
     * @author Cuong.Bui
     */
    public function output($collection, $callback, $type = null)
    {
        if ($collection->getJoin()) {
            if (is_file($collection->getTargetPath())) {
                if ($type == 'css') {
                    echo Tag::stylesheetLink($collection->getTargetUri());
                }
                if ($type == 'js') {
                    echo Tag::javascriptInclude($collection->getTargetUri());
                }
            } else {
                parent::output($collection, $callback, $type);
            }
        } else {
            parent::output($collection, $callback, $type);
        }
    }
}