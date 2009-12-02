<?php


//------------------------------------------------------------------------------------
/**
 * GalleryImagePage shows a single image with the corresponding info
 *
 */


class GalleryImagePage extends GalleryBasePage
{

    protected function getStylesheets() {
        $stylesheets = parent::getStylesheets();
        $stylesheets[] = 'styles/css/minimal/screen/basemod_minimal_col3_75percent.css';
        return $stylesheets;
    }

    protected function getSubmenuActiveItem()
    {
        return 'overview';
    }

    protected function teaserHeadline() {
        return parent::teaserHeadline();
    }

    public function leftSidebar() {
        require SCRIPT_BASE . 'build/gallery/templates/galleryimage.leftsidebar.php';
    }

}
