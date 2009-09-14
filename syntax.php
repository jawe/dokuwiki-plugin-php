<?php
/**
 * Plugin Include PHP: Includes an approved PHP file into a page.
 *
 * @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author     Jan Wessely <info@jawe.net>
 */

if(!defined('DOKU_INC')) {
	define('DOKU_INC',realpath(dirname(__FILE__).'/../../').'/');
}
if(!defined('DOKU_PLUGIN')) {
	define('DOKU_PLUGIN',DOKU_INC.'lib/plugins/');
}

require_once(DOKU_PLUGIN.'syntax.php');

/**
 * All DokuWiki plugins to extend the parser/rendering mechanism
 * need to inherit from this class
 */
class syntax_plugin_php extends DokuWiki_Syntax_Plugin {

	/**
	 * return some info
	 */
	function getInfo(){
		return array(
            'author' => 'Jan Wessely',
            'email'  => 'info@jawe.net',
            'date'   => '2009-09-13',
            'name'   => 'Include PHP',
            'desc'   => 'Includes an approved PHP file into a page.',
            'url'    => 'http://jawe.net/wiki/dev/dokuwiki/php',
		);
	}

	/**
	 * What kind of syntax are we?
	 */
	function getType(){
		return 'substition';
	}

	/**
	 * Just before build in links
	 * 
	 * @return sort position
	 */
	function getSort(){ return 299; }

	/**
	 * What about paragraphs?
	 */
	function getPType(){
		return 'block';
	}

	/**
	 * Add Lexer patterns
	 * @param $mode parser mode
	 */
	function connectTo($mode) {
		$this->Lexer->addSpecialPattern('\{\{php>[^\}]*?\}\}',$mode,'plugin_php');
	}


	/**
	 * Handle the match
	 */
	function handle($match, $state, $pos, &$handler){
		$match = preg_replace("%\\{\\{php(?:\\>(.*))?\\}\\}%u", "\\1", $match);
		return $match;
	}

	/**
	 * Create output
	 */
	function render($mode, &$renderer, $data) {
		if($mode == 'xhtml'){
			$text=$this->_include_php($data);
			$renderer->doc .= $text;
			return true;
		}
		return false;
	}

	/**
	 * Include the PHP file.
	 * 
	 * @param $phpfile 
	 * @return unknown_type
	 */
	function _include_php($phpfile) {
		global $ID;
		if(strlen($phpfile) < 1) {
			$phpfile = $ID;   // default to the current page ID
		}
		$path =  $this->_phpFN($phpfile);
		ob_start();
		echo "\n\t<!-- Begin '$phpfile' -->\n";
		include($path);
		echo "\n\t<!-- End '$phpfile' -->\n";
		$text = ob_get_contents();
		ob_end_clean();
		if (empty($text))
		$text = "\n\t<!-- No file found for '$phpfile'-->\n";
		return $text;
	} // _inc_form()

	/**
	 * Translate an ID into a PHP file name
	 * path is relative to the the plugin's directory.
	 * 
	 * @param $id page id
	 * @return PHP file name
	 */
	function _phpFN($id){
		global $conf;
		$id = cleanID($id);
		$id = str_replace(':','/',$id);
		$fn = 'php/'.utf8_encodeFN($id).'.php';
		return $fn;
	} // _phpFN()

} // syntax_plugin_php
?>
