<?php
/**
 * Doofinder On-site Search osCommerce Module
 *
 * Author:  Carlos Escribano <carlos@doofinder.com>
 * Website: http://www.doofinder.com
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to
 * deal in the Software without restriction, including without limitation the
 * rights to use, copy, modify, merge, publish and distribute copies of the
 * Software, and to permit persons to whom the Software is furnished to do so,
 * subject to the following conditions:
 *
 *     - The above copyright notice and this permission notice shall be
 *       included in all copies or substantial portions of the Software.
 *     - The Software shall be used for Good, not Evil.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
 * FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS
 * IN THE SOFTWARE.
 *
 * This Software is licensed with a Creative Commons Attribution NonCommercial
 * ShareAlike 3.0 Unported license:
 *
 *       http://creativecommons.org/licenses/by-nc-sa/3.0/
 */

/**
 * Accepted parameters:
 *
 * - limit:      Max results in this request.
 * - offset:     Zero-based position to start getting results.
 * - chunk_size: The same as limit if limit is not present but for the
 *               "all results" mode
 * - language:   Language ISO code, like "es" or "en"
 * - currency:   Currency ISO code, like "EUR" or "GBP"
 *
 * - prices:     0 to hide prices (default: 1)
 * - taxes:      0 to not include taxes in prices (default: 1)
 */


include_once 'includes/application_top.php';

define('DF_TXT_SEPARATOR', '|');
define('DF_CATEGORY_SEPARATOR', '%%');
define('DF_CATEGORY_TREE_SEPARATOR', '>');

// =============================================================== CONFIGURATION

define('DOOFINDER_CURRENCY', DEFAULT_CURRENCY); // EUR, USD, GBP, ...
define('DOOFINDER_LANGUAGE', DEFAULT_LANGUAGE); // es, en, ...
define('DOOFINDER_SHOW_PRICES', true);
define('DOOFINDER_SHOW_FINAL_PRICES', true);

// ==================================================================== ADVANCED

define('DOOFINDER_CHUNK_SIZE', 100);

// =============================================================== DO NOT EDIT!!

if (! function_exists('tep_get_version'))
{
  function tep_get_version()
  {
    if (!defined('PROJECT_VERSION'))
      return "UNKNOWN";

    preg_match('/v((\d+\.\d+(\.\d+)?)(\s.*)?)$/', PROJECT_VERSION, $matches);

    if (!empty($matches[2]))
    {
      // RC versions are faked as 2.3.0
      if ($matches[2] == "2.2" && !empty($matches[4]) && strpos(trim($matches[4]), "RC") === 0)
        return "2.3.0";

      return $matches[2];
    }

    return "UNKNOWN";
  }
}

// ==================================================================== ENCODING
// https://github.com/neitanod/forceutf8/blob/master/src/ForceUTF8/Encoding.php
// -----------------------------------------------------------------------------

/*
Copyright (c) 2008 Sebastián Grignoli
All rights reserved.

Redistribution and use in source and binary forms, with or without
modification, are permitted provided that the following conditions
are met:
1. Redistributions of source code must retain the above copyright
   notice, this list of conditions and the following disclaimer.
2. Redistributions in binary form must reproduce the above copyright
   notice, this list of conditions and the following disclaimer in the
   documentation and/or other materials provided with the distribution.
3. Neither the name of copyright holders nor the names of its
   contributors may be used to endorse or promote products derived
   from this software without specific prior written permission.

THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
``AS IS'' AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED
TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR
PURPOSE ARE DISCLAIMED.  IN NO EVENT SHALL COPYRIGHT HOLDERS OR CONTRIBUTORS
BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
POSSIBILITY OF SUCH DAMAGE.
*/

/**
 * @author   "Sebastián Grignoli" <grignoli@framework2.com.ar>
 * @package  Encoding
 * @version  1.2
 * @link     https://github.com/neitanod/forceutf8
 * @example  https://github.com/neitanod/forceutf8
 * @license  Revised BSD
  */

class Encoding {

  protected static $win1252ToUtf8 = array(
        128 => "\xe2\x82\xac",

        130 => "\xe2\x80\x9a",
        131 => "\xc6\x92",
        132 => "\xe2\x80\x9e",
        133 => "\xe2\x80\xa6",
        134 => "\xe2\x80\xa0",
        135 => "\xe2\x80\xa1",
        136 => "\xcb\x86",
        137 => "\xe2\x80\xb0",
        138 => "\xc5\xa0",
        139 => "\xe2\x80\xb9",
        140 => "\xc5\x92",

        142 => "\xc5\xbd",


        145 => "\xe2\x80\x98",
        146 => "\xe2\x80\x99",
        147 => "\xe2\x80\x9c",
        148 => "\xe2\x80\x9d",
        149 => "\xe2\x80\xa2",
        150 => "\xe2\x80\x93",
        151 => "\xe2\x80\x94",
        152 => "\xcb\x9c",
        153 => "\xe2\x84\xa2",
        154 => "\xc5\xa1",
        155 => "\xe2\x80\xba",
        156 => "\xc5\x93",

        158 => "\xc5\xbe",
        159 => "\xc5\xb8"
  );

    protected static $brokenUtf8ToUtf8 = array(
        "\xc2\x80" => "\xe2\x82\xac",

        "\xc2\x82" => "\xe2\x80\x9a",
        "\xc2\x83" => "\xc6\x92",
        "\xc2\x84" => "\xe2\x80\x9e",
        "\xc2\x85" => "\xe2\x80\xa6",
        "\xc2\x86" => "\xe2\x80\xa0",
        "\xc2\x87" => "\xe2\x80\xa1",
        "\xc2\x88" => "\xcb\x86",
        "\xc2\x89" => "\xe2\x80\xb0",
        "\xc2\x8a" => "\xc5\xa0",
        "\xc2\x8b" => "\xe2\x80\xb9",
        "\xc2\x8c" => "\xc5\x92",

        "\xc2\x8e" => "\xc5\xbd",


        "\xc2\x91" => "\xe2\x80\x98",
        "\xc2\x92" => "\xe2\x80\x99",
        "\xc2\x93" => "\xe2\x80\x9c",
        "\xc2\x94" => "\xe2\x80\x9d",
        "\xc2\x95" => "\xe2\x80\xa2",
        "\xc2\x96" => "\xe2\x80\x93",
        "\xc2\x97" => "\xe2\x80\x94",
        "\xc2\x98" => "\xcb\x9c",
        "\xc2\x99" => "\xe2\x84\xa2",
        "\xc2\x9a" => "\xc5\xa1",
        "\xc2\x9b" => "\xe2\x80\xba",
        "\xc2\x9c" => "\xc5\x93",

        "\xc2\x9e" => "\xc5\xbe",
        "\xc2\x9f" => "\xc5\xb8"
  );

  protected static $utf8ToWin1252 = array(
       "\xe2\x82\xac" => "\x80",

       "\xe2\x80\x9a" => "\x82",
       "\xc6\x92"     => "\x83",
       "\xe2\x80\x9e" => "\x84",
       "\xe2\x80\xa6" => "\x85",
       "\xe2\x80\xa0" => "\x86",
       "\xe2\x80\xa1" => "\x87",
       "\xcb\x86"     => "\x88",
       "\xe2\x80\xb0" => "\x89",
       "\xc5\xa0"     => "\x8a",
       "\xe2\x80\xb9" => "\x8b",
       "\xc5\x92"     => "\x8c",

       "\xc5\xbd"     => "\x8e",


       "\xe2\x80\x98" => "\x91",
       "\xe2\x80\x99" => "\x92",
       "\xe2\x80\x9c" => "\x93",
       "\xe2\x80\x9d" => "\x94",
       "\xe2\x80\xa2" => "\x95",
       "\xe2\x80\x93" => "\x96",
       "\xe2\x80\x94" => "\x97",
       "\xcb\x9c"     => "\x98",
       "\xe2\x84\xa2" => "\x99",
       "\xc5\xa1"     => "\x9a",
       "\xe2\x80\xba" => "\x9b",
       "\xc5\x93"     => "\x9c",

       "\xc5\xbe"     => "\x9e",
       "\xc5\xb8"     => "\x9f"
    );

  static function toUTF8($text){
  /**
   * Function Encoding::toUTF8
   *
   * This function leaves UTF8 characters alone, while converting almost all non-UTF8 to UTF8.
   *
   * It assumes that the encoding of the original string is either Windows-1252 or ISO 8859-1.
   *
   * It may fail to convert characters to UTF-8 if they fall into one of these scenarios:
   *
   * 1) when any of these characters:   ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖ×ØÙÚÛÜÝÞß
   *    are followed by any of these:  ("group B")
   *                                    ¡¢£¤¥¦§¨©ª«¬­®¯°±²³´µ¶•¸¹º»¼½¾¿
   * For example:   %ABREPRESENT%C9%BB. «REPRESENTÉ»
   * The "«" (%AB) character will be converted, but the "É" followed by "»" (%C9%BB)
   * is also a valid unicode character, and will be left unchanged.
   *
   * 2) when any of these: àáâãäåæçèéêëìíîï  are followed by TWO chars from group B,
   * 3) when any of these: ðñòó  are followed by THREE chars from group B.
   *
   * @name toUTF8
   * @param string $text  Any string.
   * @return string  The same string, UTF8 encoded
   *
   */

    if(is_array($text))
    {
      foreach($text as $k => $v)
      {
        $text[$k] = self::toUTF8($v);
      }
      return $text;
    } elseif(is_string($text)) {

      $max = strlen($text);
      $buf = "";
      for($i = 0; $i < $max; $i++){
          $c1 = $text{$i};
          if($c1>="\xc0"){ //Should be converted to UTF8, if it's not UTF8 already
            $c2 = $i+1 >= $max? "\x00" : $text{$i+1};
            $c3 = $i+2 >= $max? "\x00" : $text{$i+2};
            $c4 = $i+3 >= $max? "\x00" : $text{$i+3};
              if($c1 >= "\xc0" & $c1 <= "\xdf"){ //looks like 2 bytes UTF8
                  if($c2 >= "\x80" && $c2 <= "\xbf"){ //yeah, almost sure it's UTF8 already
                      $buf .= $c1 . $c2;
                      $i++;
                  } else { //not valid UTF8.  Convert it.
                      $cc1 = (chr(ord($c1) / 64) | "\xc0");
                      $cc2 = ($c1 & "\x3f") | "\x80";
                      $buf .= $cc1 . $cc2;
                  }
              } elseif($c1 >= "\xe0" & $c1 <= "\xef"){ //looks like 3 bytes UTF8
                  if($c2 >= "\x80" && $c2 <= "\xbf" && $c3 >= "\x80" && $c3 <= "\xbf"){ //yeah, almost sure it's UTF8 already
                      $buf .= $c1 . $c2 . $c3;
                      $i = $i + 2;
                  } else { //not valid UTF8.  Convert it.
                      $cc1 = (chr(ord($c1) / 64) | "\xc0");
                      $cc2 = ($c1 & "\x3f") | "\x80";
                      $buf .= $cc1 . $cc2;
                  }
              } elseif($c1 >= "\xf0" & $c1 <= "\xf7"){ //looks like 4 bytes UTF8
                  if($c2 >= "\x80" && $c2 <= "\xbf" && $c3 >= "\x80" && $c3 <= "\xbf" && $c4 >= "\x80" && $c4 <= "\xbf"){ //yeah, almost sure it's UTF8 already
                      $buf .= $c1 . $c2 . $c3;
                      $i = $i + 2;
                  } else { //not valid UTF8.  Convert it.
                      $cc1 = (chr(ord($c1) / 64) | "\xc0");
                      $cc2 = ($c1 & "\x3f") | "\x80";
                      $buf .= $cc1 . $cc2;
                  }
              } else { //doesn't look like UTF8, but should be converted
                      $cc1 = (chr(ord($c1) / 64) | "\xc0");
                      $cc2 = (($c1 & "\x3f") | "\x80");
                      $buf .= $cc1 . $cc2;
              }
          } elseif(($c1 & "\xc0") == "\x80"){ // needs conversion
                if(isset(self::$win1252ToUtf8[ord($c1)])) { //found in Windows-1252 special cases
                    $buf .= self::$win1252ToUtf8[ord($c1)];
                } else {
                  $cc1 = (chr(ord($c1) / 64) | "\xc0");
                  $cc2 = (($c1 & "\x3f") | "\x80");
                  $buf .= $cc1 . $cc2;
                }
          } else { // it doesn't need convesion
              $buf .= $c1;
          }
      }
      return $buf;
    } else {
      return $text;
    }
  }

  static function toWin1252($text) {
    if(is_array($text)) {
      foreach($text as $k => $v) {
        $text[$k] = self::toWin1252($v);
      }
      return $text;
    } elseif(is_string($text)) {
      return utf8_decode(str_replace(array_keys(self::$utf8ToWin1252), array_values(self::$utf8ToWin1252), self::toUTF8($text)));
    } else {
      return $text;
    }
  }

  static function toISO8859($text) {
    return self::toWin1252($text);
  }

  static function toLatin1($text) {
    return self::toWin1252($text);
  }

  static function fixUTF8($text){
    if(is_array($text)) {
      foreach($text as $k => $v) {
        $text[$k] = self::fixUTF8($v);
      }
      return $text;
    }

    $last = "";
    while($last <> $text){
      $last = $text;
      $text = self::toUTF8(utf8_decode(str_replace(array_keys(self::$utf8ToWin1252), array_values(self::$utf8ToWin1252), $text)));
    }
    $text = self::toUTF8(utf8_decode(str_replace(array_keys(self::$utf8ToWin1252), array_values(self::$utf8ToWin1252), $text)));
    return $text;
  }

  static function UTF8FixWin1252Chars($text){
    // If you received an UTF-8 string that was converted from Windows-1252 as it was ISO8859-1
    // (ignoring Windows-1252 chars from 80 to 9F) use this function to fix it.
    // See: http://en.wikipedia.org/wiki/Windows-1252

    return str_replace(array_keys(self::$brokenUtf8ToUtf8), array_values(self::$brokenUtf8ToUtf8), $text);
  }

  static function removeBOM($str=""){
    if(substr($str, 0,3) == pack("CCC",0xef,0xbb,0xbf)) {
      $str=substr($str, 3);
    }
    return $str;
  }

  public static function normalizeEncoding($encodingLabel)
  {
    $encoding = strtoupper($encodingLabel);
    $enc = preg_replace('/[^a-zA-Z0-9\s]/', '', $encoding);
    $equivalences = array(
        'ISO88591' => 'ISO-8859-1',
        'ISO8859'  => 'ISO-8859-1',
        'ISO'      => 'ISO-8859-1',
        'LATIN1'   => 'ISO-8859-1',
        'LATIN'    => 'ISO-8859-1',
        'UTF8'     => 'UTF-8',
        'UTF'      => 'UTF-8',
        'WIN1252'  => 'ISO-8859-1',
        'WINDOWS1252' => 'ISO-8859-1'
    );

    if(empty($equivalences[$encoding])){
      return 'UTF-8';
    }

    return $equivalences[$encoding];
  }

  public static function encode($encodingLabel, $text)
  {
    $encodingLabel = self::normalizeEncoding($encodingLabel);
    if($encodingLabel == 'UTF-8') return Encoding::toUTF8($text);
    if($encodingLabel == 'ISO-8859-1') return Encoding::toLatin1($text);
  }

}


// =================================================================== DOOFINDER

class DoofinderFeed
{
  const VERSION = "1.1.3";

  protected $_aLimit;
  protected $_iChunkSize;

  protected $_sSepField;
  protected $_sSepCategory;
  protected $_sSepSubcategory;

  protected $_iLanguageId;
  protected $_sCurrencyCode; // USD, EUR, GBP
  protected $_sProductURL;
  protected $_sProductURLParams;

  protected $_bShowPrices;
  protected $_bShowFinalPrices;

  protected $_aCategories;
  protected $_aCurrency; // array((float) rate, (int) decimal_places)


  /**
   * @param integer Language ID from db
   * @param string  Three letter currency code
   * @param integer Number of products obtained on each SQL query
   * @param boolean Display prices in the data feed
   * @param boolean Display final prices in the data feed (w. taxes)
   */
  public function __construct($languageCode = DOOFINDER_LANGUAGE,
                              $currencyCode = DOOFINDER_CURRENCY,
                              $chunkSize = DOOFINDER_CHUNK_SIZE,
                              $showPrices = DOOFINDER_SHOW_PRICES,
                              $showFinalPrices = DOOFINDER_SHOW_FINAL_PRICES)
  {
    $this->_iChunkSize = $chunkSize;

    $this->_sSepField = DF_TXT_SEPARATOR;
    $this->_sSepCategory = DF_CATEGORY_SEPARATOR;
    $this->_sSepSubcategory = DF_CATEGORY_TREE_SEPARATOR;

    $this->_iLanguageId = self::getLanguageIdFromCode($languageCode);
    $this->_sCurrencyCode = strtoupper($currencyCode);

    $this->_bShowPrices = $showPrices;
    $this->_bShowFinalPrices = $showFinalPrices;

    $this->_sProductURL = FILENAME_PRODUCT_INFO;
    $this->_sProductURLParams = "currency=" . $this->_sCurrencyCode . "&products_id=";
  }

  /**
   * If this function is called then the results will be limited to $limit
   * starting from the zero-based position indicated by $offset.
   * @param integet Max results
   * @param integer Starting from
   */
  public function setLimit($limit, $offset)
  {
    $this->_aLimit = array('limit' => $limit, 'offset' => $offset);
    $this->_iChunkSize = $limit;
  }

  //
  // Output
  //

  /**
   * Outputs the data feed
   */
  public function outputFeed()
  {
    list($error, $message, $hint) = $this->_checkConfig();

    if ($error !== true)
    {
      if (!$message)
        $message = "Unknown error";

      $this->outputError($error, $message, $hint);
    }

    $this->_prepareCategories();
    $this->_prepareCurrencyConversion();

    header("Content-Type: text/plain");

    // Header

    if (!$this->_aLimit || $this->_aLimit['offset'] === 0)
    {
      // If the query is not limited or, if so, we are retrieving the beginning

      echo implode($this->_sSepField, $this->_csvHeaderFields()).PHP_EOL;
      flush();ob_flush();
    }

    // Products

    if (!$this->_aLimit)
    {
      // All products

      $offset0 = 0;
      $nbRows = $this->_countProducts();
    }
    else
    {
      // Limit the number of rows and start from $offset.

      $offset0 = $this->_aLimit['offset'];
      $nbRows = $this->_aLimit['limit'];
    }

    $FS = $this->_sSepField;

    for ($offset = $offset0; $offset < $nbRows; $offset += $this->_iChunkSize)
    {
      $db_query = tep_db_query(self::sqlForProducts($this->_iLanguageId, false, $this->_iChunkSize, $offset));

      while ($product = self::tep_db_fetch_obj($db_query))
      {
        if ($product->status != 1)
          continue;

        // id
        echo $product->id.$FS;

        // title
        $productTitle = $this->clean($product->title);
        echo $productTitle.$FS;

        // link
        echo $this->clean($this->getProductURL($product->id), true).$FS;

        // description
        echo $this->clean($product->description).$FS;

        // image_link
        echo $this->clean($this->getProductImageUrl($product->image_url), true).$FS;

        // categories
        echo $this->clean(implode($this->_sSepCategory, $this->_productCategories($product->id))).$FS;

        // availability
        echo ($product->quantity > 0 ? "in stock" : "out of stock").$FS;

        // brand
        echo $this->clean($product->brand).$FS;

        // mpn
        echo $this->clean($product->mpn).$FS;

        if ($this->_bShowPrices)
        {
          // price
          $price = $this->getPrice($product->price, $this->clean($product->tax_rate));

          if ($price > 0)
            echo $price.$FS;
          else
            echo $FS;

          // sale_price
          $sale_price = $this->getPrice($product->sale_price, $this->clean($product->tax_rate));

          if ($sale_price > 0)
            echo $sale_price.$FS;
          else
            echo $FS;
        }

        // extra_title_1
        echo $this->cleanReferences($productTitle).$FS;

        // extra_title_2
        echo $this->splitReferences($productTitle);

        echo PHP_EOL;
        flush();ob_flush();
      }
    }

    exit();
  }

  /**
   * Outputs config for the Doofinder Installation Tube
   */
  public static function outputConfig()
  {
    header("Content-Type: application/json");

    $languages = array();
    $configurations = array();

    foreach (self::getAvailableLanguages() as $lang)
    {
      $lang = strtoupper($lang->code);
      $languages[] = $lang;
      $configurations[$lang] = array(
        'language' => $lang,
        'prices' => true, // TODO(@carlosescri): Make configurable.
        'taxes' => true,  // TODO(@carlosescri): Make configurable.
      );
    }

    echo json_encode(array(
      'platform' => array(
        'name' => 'osCommerce',
        'version' => tep_get_version()
      ),
      'module' => array(
        'version' => self::VERSION,
        'feed' => HTTP_SERVER . DIR_WS_CATALOG . basename(__FILE__),
        'options' => array (
          'language' => $languages,
          'currency' => self::getAvailableCurrencies(),
        ),
        'configurations' => $configurations,
      )
    ));

    exit();
  }

  /**
   * Outputs error info in JSON format
   *
   * @param string Error code
   * @param string Error message
   * @param string Help
   */
  public static function outputError($error, $message, $hint = false)
  {
    header("Content-Type: application/json; charset=utf-8");

    echo json_encode(array(
      'error' => $error,
      'message' => $message,
      'hint' => $hint
    ));

    exit();
  }

  //
  // Internal Methods
  //

  /**
   * Checks that the object is properly configured prior to generate the feed.
   * It checks:
   *
   * - Currency code
   * - Language
   *
   * @return array [0] => String error code or (bool) true
   *               [1] => String error message
   *               [2] => String help message
   */
  protected function _checkConfig()
  {
    $codes = self::getAvailableCurrencies();

    if (!in_array($this->_sCurrencyCode, $codes))
    {
      return array("ERR_CURRENCY", "Currency is not valid.", "Valid values are: ".implode(", ", $codes));
    }

    $langs = self::getAvailableLanguages();

    if (!in_array($this->_iLanguageId, array_keys($langs)))
    {
      $opts = array();
      foreach ($langs as $lang)
      {
        $opts[] = $lang->code . " (" . $lang->name . ")";
      }

      return array("ERR_LANGUAGE", "Language is not valid.", "Valid values are: ".implode(", ", $opts));
    }

    return array(true, "", "");
  }

  /**
   * Recursive function that gets the full path for the specified category.
   *
   * @param integer Category ID
   * @param array with category ID as key and parent category ID as value
   * @param array with category ID as key and category name as value
   * @param array with the category index
   * @return array with the updated category index
   */
  protected function _indexCategories($id, $parents, $names, $categories)
  {
    if (isset($parents[$id]) && $parents[$id] != 0)
    {
      if (!isset($categories[$parents[$id]]))
        $categories = $this->_indexCategories($parents[$id], $parents, $names, $categories);

      $temp = $categories[$parents[$id]];
    }

    if (isset($parents[$id]) && isset($names[$id]) && $parents[$id] == 0)
      $categories[$id] = $names[$id];
    else
      $categories[$id] = $temp . $this->_sSepSubcategory . $names[$id];

    return $categories;
  }

  /**
   * Gets all the paths for all the categories
   */
  protected function _prepareCategories()
  {
    $db_query = tep_db_query(self::sqlForCategories($this->_iLanguageId));

    $names = array();
    $parents = array();

    while ($cat = self::tep_db_fetch_obj($db_query))
    {
      if (trim($cat->name) != "")
      {
        $names[$cat->id] = trim($cat->name);
        $parents[$cat->id] = $cat->parentId;
      }
    }

    $categories = array();

    foreach (array_keys($names) as $id)
      $categories = $this->_indexCategories($id, $parents, $names, $categories);

    $this->_aCategories = $categories;
  }

  /**
   * Configure the currency conversion saving an associative array with the
   * conversion rate and the number of decimal places of the currency.
   */
  protected function _prepareCurrencyConversion()
  {
    $db_query = tep_db_query(self::sqlForCurrencyCode($this->_sCurrencyCode));
    $currency = self::tep_db_fetch_obj($db_query);

    if (!currency)
      $this->_aCurrency = array('rate' => 1.0,
                                'decimals' => 2);
    else
      $this->_aCurrency = array('rate' => $currency->value,
                                'decimals' => $currency->decimal_places);
  }

  /**
   * Counts products based on the current object configuration.
   *
   * @return integer Number of products.
   */
  protected function _countProducts()
  {
    $db_query = tep_db_query(self::sqlForProducts($this->_iLanguageId, true));
    $result = self::tep_db_fetch_obj($db_query);

    if ($result)
      return $result->total;

    return 0;
  }

  /**
   * Returns product categories for a product ID.
   *
   * @param integer ID
   * @return array of category paths
   */
  protected function _productCategories($id)
  {
    $db_query = tep_db_query(self::sqlForProductCategories($id));

    $categories = array();

    while ($row = self::tep_db_fetch_obj($db_query))
      if (isset($this->_aCategories[$row->id]))
        $categories[] = $this->_aCategories[$row->id];

    sort($categories);

    $nbcategories = count($categories);
    $result = array();

    for ($i = 1; $i < $nbcategories; $i++)
    {
      if (strpos($categories[$i], $categories[$i - 1]) === 0)
        continue;
      $result[] = $categories[$i - 1];
    }
    $result[] = $categories[$i - 1];

    return $result;
  }

  /**
   * Returns an array with the CSV file header field names based on the current
   * object configuration.
   *
   * @return array
   */
  protected function _csvHeaderFields()
  {
    $header = array('id', 'title', 'link', 'description', 'image_link',
      'categories', 'availability', 'brand', 'mpn');

    if ($this->_bShowPrices)
    {
      $header[] = 'price';
      $header[] = 'sale_price';
    }

    $header[] = 'extra_title_1';
    $header[] = 'extra_title_2';

    return $header;
  }

  //
  // Public Methods
  //

  /**
   * Returns the permalink for a product from its ID.
   *
   * @param integer Product ID
   * @return string URL
   */
  public function getProductURL($id)
  {
    $url = tep_href_link($this->_sProductURL, $this->_sProductURLParams.$id, 'NONSSL', false);

    return html_entity_decode($url);
  }

  /**
   * Returns an image link from the relative path.
   *
   * @param string Relative path of the image.
   * @return string URL.
   */
  public function getProductImageUrl($imageUrl)
  {
    $url = tep_href_link(DIR_WS_IMAGES.$imageUrl, '', 'NONSSL', false);

    return html_entity_decode($url);
  }

  /**
   * Calculates a price (final or not) based on the object configuration.
   *
   * @param double Product price
   * @param double Product associated tax
   * @return string The price resulted
   */
  public function getPrice($price, $tax)
  {
    $price = doubleval($price);
    $tax = doubleval($tax);

    if ($this->_bShowFinalPrices)
      $price = $price * (1.0 + $tax/100);

    $price = $price * $this->_aCurrency['rate'];

    return number_format($price, $this->_aCurrency['decimals'], '.', '');
  }

  //
  // STATIC FUNCTIONS
  //

  // Querying

  /**
   * We use this function for convenience and to use the same name convention.
   */
  public static function tep_db_fetch_obj($db_query) {
    return mysql_fetch_object($db_query);
  }

  /**
   * Returns all available currency codes.
   *
   * @return array of currency codes.
   */
  public static function getAvailableCurrencies()
  {
    $codes = array();

    $db_query = tep_db_query(self::sqlForCurrencyCode());

    while ($row = self::tep_db_fetch_obj($db_query))
      $codes[] = strtoupper($row->code);

    return $codes;
  }

  /**
   * Returns all available languages
   *
   * @return array
   */
  public static function getAvailableLanguages()
  {
    $langs = array();

    $db_query = tep_db_query(self::sqlForAvailableLanguages());

    while ($row = self::tep_db_fetch_obj($db_query))
      $langs[$row->id] = $row;

    return $langs;
  }

  public static function countAvailableProductsFor($languageCode)
  {
    $languageId = self::getLanguageIdFromCode($languageCode);
    $db_query = tep_db_query(self::sqlForProducts($languageId, true));
    $result = self::tep_db_fetch_obj($db_query);

    if ($result)
      return $result->total;

    return 0;
  }

  /**
   * Returns a language ID from a language ISO code.
   *
   * @param string Language code.
   * @return integer Language id.
   */
  public static function getLanguageIdFromCode($languageCode)
  {
    $db_query = tep_db_query(self::sqlForLanguageFromCode($languageCode));
    $language = self::tep_db_fetch_obj($db_query);

    if (!$language)
      return 0;

    return intval($language->id);
  }

  // Cleaning

  public function stripHtml($text)
  {
    $text = html_entity_decode($text, ENT_QUOTES, "ISO-8859-1");
    $text = preg_replace('/&#(\d+);/me',"chr(\\1)",$text);  // decimal notation
    $text = preg_replace('/&#x([a-f0-9]+);/mei',"chr(0x\\1)",$text);  // hex notation
    $text = str_replace("><", "> <", $text);
    $text = preg_replace('/\<br(\s*)?\/?\>/i', $blank, $text);
    $text = strip_tags($text);

    return $text;
  }

  public function clean($text, $is_link = false)
  {
    // http://stackoverflow.com/questions/4224141/php-removing-invalid-utf-8-characters-in-xml-using-filter
    $valid_utf8 = '/([\x09\x0A\x0D\x20-\x7E]|[\xC2-\xDF][\x80-\xBF]|\xE0[\xA0-\xBF][\x80-\xBF]|[\xE1-\xEC\xEE\xEF][\x80-\xBF]{2}|\xED[\x80-\x9F][\x80-\xBF]|\xF0[\x90-\xBF][\x80-\xBF]{2}|[\xF1-\xF3][\x80-\xBF]{3}|\xF4[\x80-\x8F][\x80-\xBF]{2})|./x';

    $blank = $is_link ? "" : " ";
    $sep_r = $is_link ? urlencode(TXT_SEPARATOR) : " - ";

    $text = str_replace(TXT_SEPARATOR, $sep_r, $text);
    $text = str_replace(array("\t", "\r", "\n", chr(9), chr(10)), $blank, $text);

    if ($is_link)
    {
      $text = str_replace(" ", $blank, $text);
    }
    else
    {
      $text = $this->stripHtml($text);
      $text = preg_replace('/\s+/', $blank, $text);
    }

    // return preg_replace($valid_utf8, '$1', trim($text));
    return Encoding::toUTF8($text);
  }

  public function cleanReferences($text)
  {
    $forbidden = array('-');
    return str_replace($forbidden, "", $text);
  }

  public function splitReferences($text)
  {
    return preg_replace("/([^\d\s])([\d])/", "$1 $2", $text);
  }

  // Request

  public static function req($name, $default, $length=null)
  {
    if (!isset($_GET[$name]) || ($length !== null && strlen($_GET[$name]) != $length))
      return $default;

    return tep_db_prepare_input($_GET[$name]);
  }

  public static function reqInt($name, $default)
  {
    if (!isset($_GET[$name]) || !strlen($_GET[$name]))
      return $default;

    return tep_db_prepare_input(intval($_GET[$name]));
  }

  public static function reqBool($name, $default=false)
  {
    if (!isset($_GET[$name]) || !strlen($_GET[$name]))
      return $default;

    switch (strtoupper($_GET[$name]))
    {
      case 'TRUE':
      case 'YES':
      case 'ON':
        return true;

      case 'FALSE':
      case 'NO':
      case 'OFF':
        return false;

      default:
        return (bool) $_GET[$name];
    }
  }

  //
  // SQL
  //

  public static function sqlForProducts($languageId, $count = false, $chunkSize = 0, $offset = 0)
  {
    if ($count)
    {
      $limit = "";
      $fields = "COUNT(pr.products_id) AS total";
      $extraJoins = "";
      $groupBy = "";
    }
    else
    {
      if ($chunkSize > 0)
        $limit = "LIMIT " . $chunkSize . " OFFSET " . $offset;
      else
        $limit = "";

      $fields = "
        pr.products_id AS id,
        pd.products_name AS title,
        pd.products_description AS description,
        mf.manufacturers_name as brand,
        pr.products_quantity AS quantity,
        pr.products_status AS status,
        pr.products_price AS price,
        ss.specials_new_products_price AS sale_price,
        tr.tax_rate AS tax_rate,
        pr.products_model as mpn,
        pr.products_image AS image_url
      ";

      $extraJoins = "
        LEFT JOIN " . TABLE_TAX_RATES . " tr
          ON pr.products_tax_class_id = tr.tax_class_id
        LEFT JOIN " . TABLE_ZONES_TO_GEO_ZONES . " gz
          ON
            tr.tax_zone_id = gz.geo_zone_id
            AND (
              gz.zone_country_id is null
              OR gz.zone_country_id = '0'
              OR gz.zone_country_id = '" . STORE_COUNTRY . "'
            )
            AND (
              gz.zone_id is null
              OR gz.zone_id = '0'
              OR gz.zone_id = '" . STORE_ZONE . "'
            )
      ";

      $groupBy = "
        GROUP BY
          pr.products_id,
          tr.tax_priority
      ";
    }

    $sql = "
      SELECT DISTINCT
        " . $fields . "
      FROM (
        " . TABLE_PRODUCTS . " pr,
        " . TABLE_PRODUCTS_DESCRIPTION . " pd,
        " . TABLE_PRODUCTS_TO_CATEGORIES . " pc
      )
      LEFT JOIN
        " . TABLE_MANUFACTURERS . " mf ON ( mf.manufacturers_id = pr.manufacturers_id )
      LEFT JOIN
        " . TABLE_SPECIALS . " ss ON (
          ss.products_id = pr.products_id
          AND ss.status = 1
          AND (
            ss.expires_date > CURRENT_DATE
            OR ss.expires_date is NULL
            OR ss.expires_date = 0
          )
        )
      " . $extraJoins . "
      WHERE
        pr.products_id=pd.products_id
        AND pr.products_id=pc.products_id
        AND pd.language_id = " . $languageId . "
      " . $groupBy . "
      ORDER BY
        pr.products_id ASC
      " . $limit . "
    ";

    return $sql;
  }

  public static function sqlForProductCategories($id)
  {
    $sql = "
      SELECT
        categories_id AS id
      FROM
        products_to_categories
      WHERE
        products_id = " . $id . "
    ";

    return $sql;
  }

  public static function sqlForCategories($languageId)
  {
    $sql = "
      SELECT
        cs.categories_id AS id,
        cs.parent_id AS parentId,
        cd.categories_name AS name
      FROM
        " . TABLE_CATEGORIES . " cs,
        " . TABLE_CATEGORIES_DESCRIPTION . " cd
      WHERE
        cs.categories_id = cd.categories_id
        AND cd.language_id = " . $languageId . "
    ";

    return $sql;
  }

  public static function sqlForCurrencyCode($currencyCode = null)
  {
    $currentCurrencySQL = "1 = 1";

    if ($currencyCode !== null)
      $currentCurrencySQL = "cs.code = '" . $currencyCode . "'";

    $sql = "
      SELECT
        cs.code,
        cs.value,
        cs.decimal_places
      FROM
        " . TABLE_CURRENCIES . " cs
      WHERE
        " . $currentCurrencySQL . "
    ";

    return $sql;
  }

  public static function sqlForAvailableLanguages()
  {
    $sql = "
      SELECT
        ls.languages_id AS id,
        ls.name,
        ls.code
      FROM
        " . TABLE_LANGUAGES . " ls
      ORDER BY
        ls.sort_order
    ";

    return $sql;
  }

  public static function sqlForLanguageFromCode($languageCode)
  {
    $sql = "
      SELECT
        ls.languages_id AS id,
        ls.name
      FROM
        " . TABLE_LANGUAGES . " ls
      WHERE
        ls.code = '" . $languageCode . "'
    ";

    return $sql;
  }
}

// doofinder.php?config=1

if (DoofinderFeed::reqBool('config', false))
  DoofinderFeed::outputConfig();

// doofinder.php?prices=1&taxes=0&language=en&currency=eur

$feed = new DoofinderFeed(
  DoofinderFeed::req('language', DOOFINDER_LANGUAGE),
  DoofinderFeed::req('currency', DOOFINDER_CURRENCY),
  DoofinderFeed::reqInt('chunk_size', DOOFINDER_CHUNK_SIZE),
  DoofinderFeed::reqBool('prices', DOOFINDER_SHOW_PRICES),
  DoofinderFeed::reqBool('taxes', DOOFINDER_SHOW_FINAL_PRICES)
);

$limit = DoofinderFeed::reqInt('limit', 0);
$offset = DoofinderFeed::reqInt('offset', 0);

if ($limit > 0)
  $feed->setLimit($limit, $offset);

$feed->outputFeed();
