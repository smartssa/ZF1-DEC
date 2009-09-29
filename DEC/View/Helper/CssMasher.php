<?php
/**
 * @author      Darryl E. Clarke <darryl.clarke@flatlinesystems.net>
 * @copyright   2009 Darryl E. Clarke
 * @version     $Id$
 */

class DEC_View_Helper_CssMasher extends DEC_View_Helper_Helper
{
    public function cssMasher($startFile = '/css/base.css')
    {
        $css  = '';
        $root = getCwd(); // should be documet_root
        // TODO: start at the startFile and recurse through all @imports
        // build a big string
        //@import url("/css/reset.css");
        //@import url("/css/grid.css");
        //@import url("/css/type.css");
        //@import url("/css/widgets.css");
        $css .= file_get_contents($root . '/css/reset.css');
        $css .= file_get_contents($root . '/css/grid.css');
        $css .= file_get_contents($root . '/css/type.css');
        $css .= file_get_contents($root . '/css/widgets.css');
        // hackish for now
        
        // TODO: Cache this
        // compress it into a mess
        /* remove comments */
        $css = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $css);
        /* remove tabs, spaces, newlines, etc. */
        $css = str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), '', $css);

        return $css;
    }


}