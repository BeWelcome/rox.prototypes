<?php


//------------------------------------------------------------------------------------
/**
 * page showing latest images and albums of a user
 * 
 *
 */

class GalleryUserImagesPage extends GalleryUserPage
{

    protected function getSubmenuActiveItem()
    {
        return 'images';
    }

    protected function column_col3() {
        $statement = $this->statement;
        $words = $this->getWords();
        $this->thumbsize = 1;

        echo '<h2>'.$words->getFormatted('GalleryTitleLatest').'</h2>';        
        require SCRIPT_BASE . 'build/gallery/templates/imagefixedcolumns.list.php';
    }

}
