<?php

require_once('pug-helper.php');
class Twig_Helper extends Twig_Extension
{
    public function getFunctions()
    {
        return array(
          new Twig_Function('themeURI', 'themeURI'),
          new Twig_Function('currentENV', 'currentENV'),
          new Twig_Function('getThemeDir', 'getThemeDir'),
          new Twig_Function('themeURI', 'themeURI'),
          new Twig_Function('staticURI', 'staticURI'),
          new Twig_Function('menu', 'menu'),
          new Twig_Function('getPriceFilter', 'getPriceFilter'),
          new Twig_Function('hotline1', 'hotline1'),
          new Twig_Function('hotline2', 'hotline2'),
          new Twig_Function('meta_title_default', 'meta_title_default'),
          new Twig_Function('meta_description_default', 'meta_description_default'),
          new Twig_Function('facebook_pixel', 'facebook_pixel'),
          new Twig_Function('facebook_image', 'facebook_image'),
          new Twig_Function('name', 'name'),
          new Twig_Function('role', 'role'),
          new Twig_Function('currentHost', 'currentHost'),
          new Twig_Function('currentUrl', 'currentUrl'),
          new Twig_Function('livechat', 'livechat'),
          new Twig_Function('slider', 'slider'),
          new Twig_Function('collectionIndex', 'collectionIndex'),
          // new Twig_Function('canonical', 'canonical'),

        );
    }

    public function getFilters()
    {
        return array(
            new Twig_Filter('money', 'money'),
            new Twig_Filter('getFirstHistory', 'getFirstHistory'),
            new Twig_Filter('getLastHistory', 'getLastHistory'),
            new Twig_Filter('listArticles', 'listArticles'),
            new Twig_Filter('resize', 'resize'),
            new Twig_Filter('concatString', 'concatString'),
            new Twig_Filter('getPathname', 'getPathname'),
            new Twig_Filter('countArr', 'countArr'),
            new Twig_Filter('money', 'money'),
            new Twig_Filter('fullUrl', 'fullUrl'),
            new Twig_Filter('ddMMYYYY', 'ddMMYYYY'),
            new Twig_Filter('getHotArticle', 'getHotArticle'),
            new Twig_Filter('getRelatedArticle', 'getRelatedArticle'),
            new Twig_Filter('inInventory', 'inInventory'),
            new Twig_Filter('getCollectionChild', 'getCollectionChild'),
            new Twig_Filter('getMeta', 'getMeta')
        );
    }

    // ...
}
