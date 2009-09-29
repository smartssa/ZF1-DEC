<?php
/**
 * @author      Darryl E. Clarke <darryl.clarke@flatlinesystems.net>
 * @copyright   2009 Darryl E. Clarke
 * @version     $Id: Vimeo.php 55 2009-09-21 05:12:53Z dclarke $
 */

class DEC_View_Helper_CssMasher extends DEC_View_Helper_Helper
{
    public function cssMasher($startFile = '/css/base.css')
    {
        $css  = '';
        $root = '.';
        // TODO: start at the startFile and recurse through all @imports
        // build a big string
        //@import url("/css/reset.css");
        //@import url("/css/grid.css");
        //@import url("/css/type.css");
        //@import url("/css/widgets.css");
        $css .= $this->getIncludeContents($root . '/css/reset.css');
        $css .= $this->getIncludeContents($root . '/css/grid.css');
        $css .= $this->getIncludeContents($root . '/css/type.css');
        $css .= $this->getIncludeContents($root . '/css/widgets.css');
        // hackish for now
        // compress it into a mess
        /* remove comments */
        $css = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $css);
        /* remove tabs, spaces, newlines, etc. */
        $css = str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), '', $css);

        return $css;
    }

    private function getIncludeContents($filename) {
        //        if (is_file($filename)) {
        echo $filename;
        file_get_contents($filename);
        return $contents;
        //        }
        return false;
    }

}