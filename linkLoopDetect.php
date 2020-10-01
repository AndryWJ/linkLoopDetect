<?php
/*
Plugin Name: linkLoopDetect-Andry-WJ
Description: linkLoop Detect and remove
Version: 1.0
Author: Ivanochko Andry 
*/

/*  Copyright 2019  Ivanochko Andry  (email: _andry__@ukr.net)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

defined('ABSPATH') or die("Hey! this not allowed");

class linkLoopDetect
{
  function __construct()
  {
    add_action( 'get_header', array( $this, 'func_head' ));
    add_action( 'wp_print_footer_scripts', array( $this, 'func_footer' ));

  }
  
  public function func_head(){
    ob_start(array($this,"callback_boofer"));
  }

  public function func_footer(){
    ob_end_flush();
  }

  private function callback_boofer($buffer)
    {
      $out = preg_replace_callback('/(<a.*href=("|\'))(.*)("|\')/U', array($this,"replace_callback_func"), $buffer);
      return $out;
    }

  private function replace_callback_func($matches){
    $link_out = $this->loopLinks($matches[3]);
    $out = $matches[1].$link_out.$matches[2];
    return $out;
  }

  private function loopLinks($in){

    if (!function_exists('url')) {
      function url()
      {
        return sprintf(
          "%s://%s%s",
          isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https' : 'http',
          $_SERVER['SERVER_NAME'],
          $_SERVER['REQUEST_URI']
        );
      }
    }
    $inTrim = trim($in, '/');
    $urlTrim = trim(url(), '/');
 
    if ($inTrim == $urlTrim || $inTrim == trim($_SERVER['REQUEST_URI'],'/')) {
      return "javascript:;";
    } else {
      return $in;
    }

  }

}

if(class_exists("linkLoopDetect")){
  $linkLoopDetect = new linkLoopDetect();
}
