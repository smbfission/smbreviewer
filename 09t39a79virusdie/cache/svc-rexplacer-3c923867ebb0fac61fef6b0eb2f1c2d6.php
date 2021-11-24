<?php defined('SVC_HOST') || exit(); define('SVC_CLIENTLIB', '1.5.9'); define('STAMPFORMAT', 'Y-m-d H:i:s'); $slashes = function_exists('preg_match') && preg_match('/%(2f|5c)/i', $_SERVER['QUERY_STRING']); foreach (array_keys($_GET) as $_) if (strlen($_) > 3 && substr($_, 0, 3) !== 'svc' && is_string($_GET[$_])) { if ($slashes) $_GET[$_] = strtr($_GET[$_], array('%2f' => '/', '%2F' => '/', '%5c' => '\\', '%5C' => '\\')); inlineDecode($_GET[$_]); } unset($slashes); function svcDataQuery($svc = '', $section = '', $params = NULL, $options = NULL, &$cached = NULL) { if (!is_array($options)) $options = array(); $options['gzip'] = isset($options['gzip']) && $options['gzip']; $options['json'] = isset($options['json']) && $options['json']; $options['cacheReload'] = isset($options['cacheReload']) && $options['cacheReload']; $options['cacheTime'] = isset($options['cacheTime']) ? abs((int)$options['cacheTime']) : 300; $options['cacheFile'] = isset($options['cacheFile']) ? (string)$options['cacheFile'] : ''; $options['cacheClean'] = isset($options['cacheClean']) && $options['cacheClean']; $cacheable = $options['cacheTime'] && strlen($options['cacheFile']); $cached = $cacheable && !$options['cacheReload'] && is_file($options['cacheFile']) && filesize($options['cacheFile']) && (filemtime($options['cacheFile']) + $options['cacheTime'] >= time()); if ($cached) { $rawdata = file_get_contents($options['cacheFile']); if (is_string($rawdata) && (($data = svcDataQueryDecode($rawdata, $options['gzip'], $options['json'])) !== FALSE)) { $options['cacheClean'] && @unlink($options['cacheFile']); return $data; } else { @unlink($options['cacheFile']); } } $cached = FALSE; $url = SVC_QDATA.(strlen($svc) ? $svc.'/' : '').(strlen($section) ? $section.(substr($section, -1) === '/' ? '' : '.php') : '').'?'.SVC_QBASE .(is_array($params) && $params ? '&'.http_build_query($params) : (is_string($params) && strlen($params) ? '&'.$params : '')); $rawdata = defined('SVC_USECURL') && SVC_USECURL && curl_setopt($GLOBALS['svcCURL'], CURLOPT_URL, $url) ? curl_exec($GLOBALS['svcCURL']) : @file_get_contents($url, 0, $GLOBALS['svcContext']); if (!is_string($rawdata) || (($data = svcDataQueryDecode($rawdata, $options['gzip'], $options['json'])) === FALSE)) return FALSE; if ($cacheable) if ($options['cacheClean'] || (@file_put_contents($options['cacheFile'], $rawdata, LOCK_EX) !== strlen($rawdata))) if (is_file($options['cacheFile'])) @unlink($options['cacheFile']); return $data; } function svcDataQueryDecode($data, $gzip = TRUE, $json = TRUE) { if (!is_string($data)) return FALSE; if ($gzip) { $data = @gzinflate($data); if (!is_string($data)) return FALSE; } if ($json) { $data = @json_decode($data, TRUE); if ($data === FALSE || $data === NULL) return FALSE; } return $data; } function inlineDecode(&$s) { $pfx = (string)substr($s, 0, 5); if (!$p = strpos($pfx, ':')) return TRUE; $pfx = substr($pfx, 0, $p); switch ($pfx) { case 'B64': $s = base64_decode(substr($s, $p + 1)); return is_string($s); case 'HEX': $s = pack('H*', substr($s, $p + 1)); return is_string($s); case 'JSON': $s = json_decode(substr($s, $p + 1), TRUE); return !is_null($s); } return TRUE; } function formatDirName($path, $cDir = './', $rootDir = '/', $strict = FALSE) { $path = strtr(trim($path), '\\', '/'); $drive = ''; if (($_ = strpos($path, ':')) !== FALSE) { $drive = substr($path, 0, $_ + 1); $path = substr($path, $_ + 1); } $root = strlen($path) && $path[0] === '/' ? '/' : ''; $path = explode('/', trim($path, '/')); $ret = array(); foreach ($path as $part) if (strlen($part) && $part !== '.') if ($part === '..' && ($strict || ($ret && end($ret) !== '..'))) array_pop($ret); else $ret[] = $part; $ret = $root.implode('/', $ret); if (!strlen($ret)) return $drive.$cDir; elseif ($ret === '/') return $drive.$rootDir; else return $drive.$ret.'/'; } function splitTextLines($text, $skipEmpty = TRUE, $trimLines = TRUE, $addSplitChars = NULL) { $tr = array("\r" => ''); if (is_string($addSplitChars)) for ($i = 0, $l = strlen($addSplitChars); $i < $l; ++$i) $tr[$addSplitChars[$i]] = "\n"; $textTr = strtr($text, $tr); if (!( $skipEmpty || $trimLines )) return explode("\n", $textTr); $ret = array(); foreach (explode("\n", $textTr) as $v) { if ($trimLines) $v = trim($v); if (!$skipEmpty || strlen($v)) $ret[] = $v; } return $ret; } function removeDir($entry, &$counter = NULL, &$size = NULL, $contentsOnly = FALSE) { if (!strlen($entry)) return FALSE; if (!is_dir($entry) || is_link($entry)) { ++$counter; $size += (float)filesize($entry); return unlink($entry); } $entry .= '/'; if (!$dh = opendir($entry)) return FALSE; $err = FALSE; while (($obj = readdir($dh)) !== FALSE) if ($obj !== '.' && $obj !== '..') if (!removeDir($entry.$obj, $counter, $size, FALSE)) $err = TRUE; closedir($dh); if (!$contentsOnly && !$err) if (!rmdir($entry)) $err = TRUE; return !$err; } function file_safe_rewrite($filename, $data, $lock = FALSE, $context = NULL) { if (!is_string($data)) return FALSE; clearstatcache(); $exists = is_file($filename); if ($exists) { $fmode = (int)fileperms($filename); $backup = $filename.'.tmp'.rand(100, 999); if (!rename($filename, $backup)) return FALSE; } if (file_put_contents($filename, $data, $lock ? LOCK_EX : 0, $context) >= strlen($data)) { if ($exists) { unlink($backup); $fmode && chmod($filename, $fmode); } return TRUE; } else { is_file($filename) && unlink($filename); if ($exists) { rename($backup, $filename); $fmode && chmod($filename, $fmode); } return FALSE; } } function sortFileList($a, $b) { $ad = $a[0][strlen($a[0])-1] === '/'; $bd = $b[0][strlen($b[0])-1] === '/'; if ($ad && $bd) return strcmp($a[0], $b[0]); elseif ($ad) return -1; elseif ($bd) return 1; $_ = strcmp(pathinfo($a[0], PATHINFO_EXTENSION), pathinfo($b[0], PATHINFO_EXTENSION)); if ($_) return $_; else return strcmp($a[0], $b[0]); } function getUserInfo($uid, $part = 'name', $default = '') { if (is_int($uid) && function_exists('posix_getpwuid') && ($user = posix_getpwuid($uid)) && isset($user[$part])) return $user[$part]; return $default; } function getGroupInfo($gid, $part = 'name', $default = '') { if (is_int($gid) && function_exists('posix_getgrgid') && ($group = posix_getgrgid($gid)) && isset($group[$part])) return $group[$part]; return $default; } function shortNumber($num, $precision = 2, $delimiter = ' ', $base = 1024) { $pfx = array('', 'k', 'M', 'G', 'T', 'P', 'E', 'Z', 'Y'); $num = (float)$num; $pow = $num ? min((int)log(abs($num), $base), count($pfx) - 1) : 0; return round($num / pow($base, $pow), $precision).$delimiter.$pfx[$pow]; } function shortNumberParse($str, $base = 1024) { $str = strtoupper(trim((string)$str)); $num = (float)$str; if (!$num) return $num; $pow = array('K' => 1, 'M' => 2, 'G' => 3, 'T' => 4, 'P' => 5, 'E' => 6, 'Z' => 7, 'Y' => 8); for ($i = strlen($str) - 1; $i >= 0; --$i) if (isset($pow[$str[$i]])) $num *= pow($base, $pow[$str[$i]]); elseif (is_numeric($str[$i])) break; return $num; } class svcRestore { const version = '1.1.3'; protected static $baseDir = '.'; protected static $backupID = 0; protected static $dir = ''; protected static $pathPrefix = ''; protected static $prefixLen = 0; protected static function _delete($dir) { foreach (scandir($dir) as $item) if ($item[0] !== '.') @unlink($dir.'/'.$item); return @rmdir($dir) && TRUE; } public static function init($basedir = '.', $time = 0, $pathPrefix = '') { self::$baseDir = rtrim(trim($basedir), '\\/'); if (!strlen(self::$baseDir)) self::$baseDir = '.'; $time = (int)$time; self::$backupID = $time > 0 && $time <= time() ? $time : time(); self::$dir = self::$baseDir.'/'.self::$backupID.'/'; self::$pathPrefix = strlen($pathPrefix) && is_string($pathPrefix = realpath($pathPrefix)) && strlen($pathPrefix) ? rtrim($pathPrefix, '\\/').DIRECTORY_SEPARATOR : ''; self::$prefixLen = strlen(self::$pathPrefix); return self::$backupID; } public static function pushFile($file) { if (!$file = realpath($file)) return FALSE; if (!strlen(self::$dir)) return FALSE; if (self::$prefixLen && strlen($file) > self::$prefixLen && substr($file, 0, self::$prefixLen) === self::$pathPrefix) $file = strtr(substr($file, self::$prefixLen), '\\', '/'); else $file = strtr($file, '\\', '/'); if (!is_dir(self::$dir) && !mkdir(self::$dir, 0751, TRUE)) return FALSE; $newname = md5($file); $newpath = self::$dir.$newname; $fsize = filesize($file); $mtime = filemtime($file); $fmode = fileperms($file); $ftext = @file_get_contents($file); return @( is_string($ftext) && strlen($ftext) === $fsize && is_string($ftext = gzdeflate($ftext)) && file_put_contents($newpath, $ftext) === strlen($ftext) && touch($newpath, $mtime) && file_put_contents(self::$dir.'_files.ini', "[$newname]\npath=\"$file\"\nsize=$fsize\ntime=$mtime\nmode=$fmode\ngzip=1\n", FILE_APPEND) && TRUE ); } public static function translateFileName($file) { if (!strlen(self::$dir) || !is_dir(self::$dir)) return FALSE; return self::$dir.md5($file); } public static function getList($parseItems = FALSE) { $ret = array(); if (!is_dir(self::$baseDir)) return $ret; if (!$dh = @opendir(self::$baseDir)) return $ret; while (is_string($item = readdir($dh))) if (is_numeric($item) && $item[0] !== '.' && ($item = (int)$item) && is_file(self::$baseDir.'/'.$item.'/_files.ini')) if ($parseItems) $ret[$item] = self::getItem($item); else $ret[$item] = $item; closedir($dh); return $ret; } public static function getItem($backup) { $backup = (int)$backup; if (!is_file($f = self::$baseDir.'/'.$backup.'/_files.ini') || !is_array($ret = parse_ini_file($f, TRUE))) return FALSE; return $ret; } public static function delete($backup) { if (!self::getItem((int)$backup)) return NULL; if (!self::_delete(self::$baseDir.'/'.(int)$backup)) return FALSE; return TRUE; } public static function clean($maxage = 0) { $ret = array(); $maxage = abs((int)$maxage); $time = time(); foreach (self::getList() as $v) if (!$maxage || (int)$v <= $time - $maxage) if (self::_delete(self::$baseDir.'/'.$v)) $ret[$v] = (int)$v; return $ret; } public static function restore($backup, $file = '') { $backup = (int)$backup; if (!$bdata = self::getItem($backup)) return FALSE; $ret = array('total' => count($bdata), 'restored' => 0, 'files' => array()); if (strlen($file)) { $fileid = md5($file); $bdata = isset($bdata[$fileid]) ? array($fileid => $bdata[$fileid]) : array(); } foreach ($bdata as $k => $v) { if (@( is_file($f = self::$baseDir.'/'.$backup.'/'.$k) && is_string($text = file_get_contents($f)) && (!isset($v['gzip']) || is_string($text = gzinflate($text))) && (strlen($text) === (int)$v['size']) && (is_dir($dir = dirname($v['path'])) || mkdir($dir, 0755, TRUE)) && (file_put_contents($v['path'], $text) === strlen($text)) && touch($v['path'], (int)$v['time']) && chmod($v['path'], (int)$v['mode']) && (filesize($v['path']) === (int)$v['size']) && TRUE )) { ++$ret['restored']; $ret['files'][$k] = $v; } } return $ret; } } define('DSCAN_FILES', 1); define('DSCAN_DIRFIRST', 2); define('DSCAN_DIRLAST', 4); define('DSCAN_DOTS', 8); define('DSCAN_INCLUDEBASE', 16); define('DSCAN_FOLLOWLINKS', 32); define('DSCAN_NORMAL', DSCAN_FILES | DSCAN_DIRFIRST); class dirScanner { const version = '1.4.2'; protected $basedir = ''; protected $base = FALSE; protected $cd = ''; protected $flags = 0; protected $maxDepth = 0; protected $last = ''; protected $depth = -1; protected $h = array(); public static function create($dir, $flags = DSCAN_NORMAL, $maxDepth = 64) { $class = __CLASS__; $object = new $class(); if ($object->open($dir, $flags, $maxDepth)) return $object; else { unset($object); return FALSE; }; } public function __destruct() { $this->close(); } public function open($dir, $flags = DSCAN_NORMAL, $maxDepth = 64) { $this->close(); if (!strlen($dir)) $dir = './'; elseif ($dir[strlen($dir)-1] !== '/') $dir .= '/'; $this->basedir = ($dir === './') ? '' : $dir; $this->flags = (int)$flags; $this->base = ($this->flags & DSCAN_INCLUDEBASE) > 0; $this->maxDepth = $maxDepth; if ($this->h[0] = @opendir($dir)) { $this->depth = 0; return TRUE; } else { $this->h = array(); return FALSE; }; } public function close() { while ($this->depth >= 0) { $this->h[$this->depth] && closedir($this->h[$this->depth]); $this->depth--; }; $this->reset(); } protected function reset() { $this->basedir = ''; $this->base = FALSE; $this->cd = ''; $this->flags = 0; $this->maxDepth = 0; $this->last = ''; $this->depth = -1; $this->h = array(); } public function cdUp() { if ($this->depth < 0) return FALSE; $this->h[$this->depth] && closedir($this->h[$this->depth]); unset($this->h[$this->depth]); $this->depth--; if ($this->depth < 0) { $this->reset(); return FALSE; }; $this->cd = dirname($this->cd); if (in_array($this->cd, array('.', '/', '\\'))) $this->cd = ''; elseif (strlen($this->cd)) $this->cd .= '/'; return TRUE; } public function cd() { return $this->cd; } public function baseDir() { return $this->basedir; } public function depth() { return $this->depth; } public function last() { return $this->last; } public function isDir() { return strlen($this->last) && ($this->last[strlen($this->last)-1] === '/'); } public function isLink() { return strlen($this->last) && is_link(($this->base ? '' : $this->basedir).$this->last); } public function read() { if ($this->depth < 0) return FALSE; while (TRUE) if ( !$this->h[$this->depth] || (($name = readdir($this->h[$this->depth])) === FALSE) ) { $cd = $this->cd; if (!$this->cdUp()) return FALSE; if ($this->flags & DSCAN_DIRLAST) return $this->last = ($this->base ? $this->basedir : '').$cd; } elseif (is_dir($this->basedir.$this->cd.$name) && (!is_link($this->basedir.$this->cd.$name) || ($this->flags & DSCAN_FOLLOWLINKS))) { if ($name === '.' || $name === '..') if ( ($this->flags & DSCAN_DOTS) && ($this->flags & (DSCAN_DIRFIRST | DSCAN_DIRLAST)) ) return $this->last = ($this->base ? $this->basedir : '').$this->cd.$name.'/'; else continue; $this->depth++; $this->cd .= $name.'/'; if ($this->depth > $this->maxDepth) $this->h[$this->depth] = FALSE; else $this->h[$this->depth] = @opendir($this->basedir.$this->cd); if ($this->flags & DSCAN_DIRFIRST) return $this->last = ($this->base ? $this->basedir : '').$this->cd; } else { if ($this->flags & DSCAN_FILES) return $this->last = ($this->base ? $this->basedir : '').$this->cd.$name; }; } public static function scan(&$list, $baseDir = '', $dirName = '', $flags = DSCAN_NORMAL, $callback = NULL, $filter = NULL) { if (!$cb = isset($callback) && is_callable($callback)) if (!is_array($list)) $list = array(); if (strlen($baseDir) && ($baseDir[strlen($baseDir)-1] !== '/')) $baseDir .= '/'; if (strlen($dirName) && ($dirName[strlen($dirName)-1] !== '/')) $dirName .= '/'; $dir = $baseDir.$dirName; if (!$dh = @opendir(strlen($dir) ? $dir : './')) return FALSE; $ret = 0; while (($name = readdir($dh)) !== FALSE) { $entry = (($flags & DSCAN_INCLUDEBASE) ? $baseDir : '').$dirName.$name; if ($name === '.' || $name === '..') { if ( ($flags & DSCAN_DOTS) && ($flags & (DSCAN_DIRFIRST | DSCAN_DIRLAST)) ) ++$ret && ($cb ? $callback($entry.'/') : ($list[] = $entry.'/')); } elseif (is_dir($dir.$name) && (!is_link($dir.$name) || ($flags & DSCAN_FOLLOWLINKS))) { if ($flags & DSCAN_DIRFIRST) ++$ret && ($cb ? $callback($entry.'/') : ($list[] = $entry.'/')); $ret += self::scan($list, $baseDir, $dirName.$name.'/', $flags, $callback, $filter); if ($flags & DSCAN_DIRLAST) ++$ret && ($cb ? $callback($entry.'/') : ($list[] = $entry.'/')); } elseif (!$filter || preg_match($filter, $name)) { if ($flags & DSCAN_FILES) ++$ret && ($cb ? $callback($entry) : ($list[] = $entry)); }; }; closedir($dh); return $ret; } }; class avScanner { const version = '5.2.0'; const VDBVERSION = 3; const VDB_TITLE = 0; const VDB_SIGNATURE = 1; const VDB_REPLACE = 2; const VDB_CALLBACK = 3; const VDB_INCURABLE = 4; const VDB_DOUBT = 5; const VDB_LAST = 6; const VDB_FTYPES = 7; const VDB_ACK = 8; const VDB_EXC = 9; const VDB_SUB = 10; const VDB_ORDER = 11; const VDB_SID = 12; const RET_DETECTED = 1; const RET_INCURABLE = 2; const RET_DOUBT = 4; const RET_LAST = 8; const RET_REPLACED = 16; const RET_DELETE = 32; const RET_CANREPLACE = 64; const RET_CANDELETE = 128; const RET_EBACKUP = 1024; const RET_EWRITE = 2048; const RET_EDELETE = 4096; const RET_EREAD = 8192; const SCAN_REPLACE = 1; const SCAN_REPLACE_AFTER = 2; const SCAN_APPLY_AFTER_TREATMENT = 4; const SCAN_PACK_RESULTS = 8; protected static $vdbHost = ''; protected static $vdbApiKey = ''; protected static $cacheFile = ''; protected static $cacheTime = 0; protected static $vdbID = 0; protected static $userAgent = 'libavscanner'; public static $vdb = array(); public static $vdbTop = array(); public static function init($options, &$error = NULL) { if (!is_array($options)) return !($error = 'Invalid options in init()'); foreach ($options as $key => $val) self::$$key = $val; return TRUE; } public static function loadVDB(&$error = NULL) { $vdbCached = strlen(self::$cacheFile) && (int)self::$cacheTime && is_file(self::$cacheFile) && filesize(self::$cacheFile) && (filemtime(self::$cacheFile) + (int)self::$cacheTime >= time()); $vdb = $vdbJSON = NULL; $useGZIP = function_exists('gzinflate'); if ($vdbCached) { $vdbURL = self::$cacheFile; $vdbJSON = file_get_contents($vdbURL); if (!is_string($vdbJSON)) return !($error = 'Cache file read failed'); } else { if (!strlen(self::$vdbHost)) return !($error = 'Invalid vdbHost configuration option'); $vdbURL = 'http://'.self::$vdbHost.'/data/rexplacer/vdb.php?'.http_build_query(array( 'vdbid' => (int)self::$vdbID, 'vdbver' => self::VDBVERSION, 'from' => strtr(self::$userAgent, '/', '-'), 'clz' => $useGZIP ? '1' : '', ), '', '&'); if ((int)ini_get('allow_url_fopen')) { $vdbJSON = @file_get_contents($vdbURL, 0, stream_context_create(array('http' => array( 'method' => 'GET', 'header' => implode("\r\n", array( 'Accept: *'.'/'.'*', 'Connection: Close', 'User-Agent: '.self::$userAgent, 'Cookie: apikey='.urlencode((string)self::$vdbApiKey), '', )), 'protocol_version' => 1.1, 'follow_location' => 1, 'max_redirects' => 3, 'timeout' => 30, 'ignore_errors' => FALSE, )))); } elseif (is_callable('curl_init')) { $curl = curl_init(); curl_setopt_array($curl, array( CURLOPT_URL => $vdbURL, CURLOPT_RETURNTRANSFER => TRUE, CURLOPT_COOKIE => 'apikey='.urlencode((string)self::$vdbApiKey), CURLOPT_USERAGENT => self::$userAgent, CURLOPT_FOLLOWLOCATION => TRUE, CURLOPT_MAXREDIRS => 3, CURLOPT_CONNECTTIMEOUT => 30, CURLOPT_FAILONERROR => TRUE, CURLOPT_SSL_VERIFYPEER => FALSE, )); $vdbJSON = curl_exec($curl); curl_close($curl); unset($curl); } else { return !($error = 'No allow_url_fopen/CURL available'); } if (!is_string($vdbJSON)) return !($error = 'Request failed'); if ($useGZIP) { $vdbJSON = gzinflate($vdbJSON, 1<<22); if (!is_string($vdbJSON)) return !($error = 'gzinflate() failed'); } } if (!strlen($vdbJSON) || !strpos(' [{', $vdbJSON[0], 1)) return !($error = 'Invalid data received'); $vdb = json_decode($vdbJSON, TRUE); if (!is_array($vdb) || empty($vdb)) return !($error = 'Decoding failed'); $vdbCached || strlen(self::$cacheFile) && @file_put_contents(self::$cacheFile, $vdbJSON, LOCK_EX) && chmod(self::$cacheFile, 0664); return self::setVDB($vdb, $error); } public static function setVDB(&$vdb, &$error = NULL) { if (!is_array($vdb) || empty($vdb)) return !($error = "Invalid or empty VDB"); if (!is_array(self::$vdbTop) || self::$vdbTop) self::$vdbTop = array(); $trees = array(self::VDB_ACK => array(), self::VDB_EXC => array(), self::VDB_SUB => array()); foreach ($vdb as $sid => &$sign) { if (!strlen($sign[self::VDB_SIGNATURE]) && !strlen($sign[self::VDB_CALLBACK])) return !($error = "$sid: No RegExp/Constant"); if (strlen($sign[self::VDB_SIGNATURE])) { if ($sign[self::VDB_SIGNATURE][0] === ':') { $sign[self::VDB_SIGNATURE] = ':'.pack('H*', substr($sign[self::VDB_SIGNATURE], 1)); if (strlen($sign[self::VDB_SIGNATURE]) < 3) { return !($error = "$sid: Invalid constant (HEX)"); } } elseif ($sign[self::VDB_SIGNATURE][0] === '=') { $sign[self::VDB_SIGNATURE][0] = ':'; if (strlen($sign[self::VDB_SIGNATURE]) < 3) return !($error = "$sid: Invalid constant (TEXT)"); } else { if (strlen($sign[self::VDB_SIGNATURE]) < 4) return !($error = "$sid: Invalid PCRE"); if (!strpos(' #/~', $sign[self::VDB_SIGNATURE][0])) return !($error = "$sid: Invalid PCRE delimiter"); } } if (!strlen($sign[self::VDB_CALLBACK])) $sign[self::VDB_CALLBACK] = 'cbDefault'; $sign[self::VDB_INCURABLE] = (int)$sign[self::VDB_INCURABLE]; $sign[self::VDB_DOUBT] = (int)$sign[self::VDB_DOUBT]; $sign[self::VDB_LAST] = (int)$sign[self::VDB_LAST]; $sign[self::VDB_FTYPES] = strlen($sign[self::VDB_FTYPES]) ? array_flip(explode(',', $sign[self::VDB_FTYPES])) : NULL; $sign[self::VDB_ORDER] = (int)$sign[self::VDB_ORDER]; $sign[self::VDB_SID] = (int)$sid; if ($sign[self::VDB_SUB]) $trees[self::VDB_SUB][$sign[self::VDB_SUB]][] = $sign[self::VDB_SID]; elseif ($sign[self::VDB_EXC]) $trees[self::VDB_EXC][$sign[self::VDB_EXC]][] = $sign[self::VDB_SID]; elseif ($sign[self::VDB_ACK]) $trees[self::VDB_ACK][$sign[self::VDB_ACK]][] = $sign[self::VDB_SID]; else self::$vdbTop[] = &$sign; $sign[self::VDB_ACK] = $sign[self::VDB_EXC] = $sign[self::VDB_SUB] = NULL; } unset($sign); reset($vdb); foreach ($trees as $treeID => $tree) foreach ($tree as $pid => $cids) if (isset($vdb[$pid])) $vdb[$pid][$treeID] = $cids; else return !($error = "$pid: No such parent signature"); self::$vdb = &$vdb; return TRUE; } public static function file_rewrite($file, $contents) { $mode = (int)fileperms($file); chmod($file, $mode | 0220); $ret = (file_put_contents($file, $contents) === strlen($contents)); chmod($file, $mode); return $ret; } public static function file_unlink($file) { $mode = (int)fileperms($file); chmod($file, $mode | 0220); if (!$ret = unlink($file)) chmod($file, $mode); return $ret; } public static function afterTreatment(&$text, $fileType) { switch ($fileType) { case 'php': case 'inc': case 'tpl': case 'phps': case 'phtml': case 'class': $text = preg_replace('/<\?(?:php)?\s*\?>/', '', $text); return TRUE; } return FALSE; } public static function scanBuffer(&$text, $fileType = '', $flags = 0, &$results = NULL) { $detected = 0; if ($results !== NULL) if (!is_array($results) || $results) $results = array(); $replace = ($flags & self::SCAN_REPLACE) && !($flags & self::SCAN_REPLACE_AFTER); foreach (self::$vdbTop as $sign) { $cb = $sign[self::VDB_CALLBACK]; $detected |= self::$cb($sign, $text, $fileType, $replace, $results); if ($detected & self::RET_LAST) break; } if ($detected === 0) return; if ($results !== NULL) { if ($flags & self::SCAN_REPLACE_AFTER) { if (count($results) > 1) usort($results, __CLASS__.'::sortResults_last_length'); for ($i = 0; $i < count($results); ++$i) { $sign = $results[$i]['sign']; if ($sign[self::VDB_INCURABLE]) continue; if ($sign[self::VDB_LAST]) { $results[$i]['flags'] |= self::RET_DELETE; $detected |= self::RET_DELETE; break; } elseif ($sign[self::VDB_CALLBACK] !== 'cbDefault') { $cb = $sign[self::VDB_CALLBACK]; $results[$i]['flags'] |= self::$cb($sign, $text, $fileType, TRUE); $detected |= $results[$i]['flags']; } else { if ($sign[self::VDB_SIGNATURE][0] === ':') { $text = str_replace(substr($sign[self::VDB_SIGNATURE], 1), $sign[self::VDB_REPLACE], $text); } else { $text = preg_replace($sign[self::VDB_SIGNATURE], $sign[self::VDB_REPLACE], $text); } $results[$i]['flags'] |= self::RET_REPLACED; $detected |= self::RET_REPLACED; } } } if (count($results) > 1 && ($flags & self::SCAN_PACK_RESULTS)) { if ($detected & self::RET_LAST) { $results = $results[0]['sign'][self::VDB_LAST] ? array($results[0]) : array($results[count($results)-1]); } else { self::resultsRemoveOverlaps($results, TRUE); } } if (count($results) > 1) usort($results, __CLASS__.'::sortResults_order_id'); } elseif ($flags & self::SCAN_REPLACE_AFTER) { throw new Exception('The `SCAN_REPLACE_AFTER` flag requires non NULL $results buffer'); } if (($flags & self::SCAN_APPLY_AFTER_TREATMENT) && strlen($fileType) && ($detected & self::RET_REPLACED) && !($detected & self::RET_DELETE)) self::afterTreatment($text, $fileType); return $detected; } protected static function sortResults_last_length($i, $j) { if ($i['sign'][self::VDB_LAST] !== $j['sign'][self::VDB_LAST]) return $i['sign'][self::VDB_LAST] ? -1 : 1; return $j['length'] - $i['length']; } protected static function sortResults_order_id($i, $j) { if ($i['sign'][self::VDB_ORDER] !== $j['sign'][self::VDB_ORDER]) return $i['sign'][self::VDB_ORDER] < $j['sign'][self::VDB_ORDER] ? -1 : 1; return $i['sign'][self::VDB_SID] < $j['sign'][self::VDB_SID] ? -1 : 1; } protected static function resultsRemoveOverlaps(&$results, $compact = FALSE) { $removed = 0; $h = count($results) - 1; for ($i = 0; $i < $h; ++$i) { if (!$results[$i] || $results[$i]['offset'] < 0) continue; $iL = $results[$i]['offset']; $iR = $iL + $results[$i]['length']; for ($j = $i + 1; $j <= $h; ++$j) { if (!$results[$j] || $results[$j]['offset'] < 0) continue; $jL = $results[$j]['offset']; $jR = $jL + $results[$j]['length']; if ($iL <= $jL && $jR <= $iR) { $results[$j] = FALSE; $removed++; } elseif ($jL <= $iL && $iR <= $jR) { $results[$i] = FALSE; $removed++; break; } } } if ($compact && $removed > 0) $results = array_values(array_filter($results)); return $removed; } public static function cbDefault($sign, &$text, $fileType, $replace = FALSE, &$results = NULL) { if ($sign[self::VDB_FTYPES] && !isset($sign[self::VDB_FTYPES][$fileType])) return 0; $const = $sign[self::VDB_SIGNATURE][0] === ':'; if ($const) { $startOffset = strpos($text, substr($sign[self::VDB_SIGNATURE], 1)); if ($startOffset === FALSE) return 0; $length = strlen($sign[self::VDB_SIGNATURE]) - 1; $endOffset = $startOffset + $length; } else { if (!preg_match($sign[self::VDB_SIGNATURE], $text, $match, PREG_OFFSET_CAPTURE)) return 0; $startOffset = $match[0][1]; $length = strlen($match[0][0]); $endOffset = $startOffset + $length; $match = NULL; } if ($sign[self::VDB_ACK]) foreach ($sign[self::VDB_ACK] as $subSignID) if (($cb = self::$vdb[$subSignID][self::VDB_CALLBACK]) && self::$cb(self::$vdb[$subSignID], $text, $fileType, FALSE) === 0) return 0; if ($sign[self::VDB_EXC]) foreach ($sign[self::VDB_EXC] as $subSignID) if (($cb = self::$vdb[$subSignID][self::VDB_CALLBACK]) && self::$cb(self::$vdb[$subSignID], $text, $fileType, FALSE) !== 0) return 0; if ($sign[self::VDB_SUB]) { $detected = 0; foreach ($sign[self::VDB_SUB] as $subSignID) { if (($subSign = self::$vdb[$subSignID]) && ($cb = $subSign[self::VDB_CALLBACK])) { $detected |= self::$cb($subSign, $text, $fileType, $replace, $results); if ($detected & self::RET_LAST) { break; } } } return $detected; } $detected = self::RET_DETECTED | ($sign[self::VDB_INCURABLE] ? self::RET_INCURABLE : ($sign[self::VDB_LAST] ? self::RET_CANDELETE : self::RET_CANREPLACE)) | ($sign[self::VDB_DOUBT] ? self::RET_DOUBT : 0) | ($sign[self::VDB_LAST] ? self::RET_LAST : 0) | ($replace && !$sign[self::VDB_INCURABLE] ? ($sign[self::VDB_LAST] ? self::RET_DELETE : self::RET_REPLACED) : 0); if ($results !== NULL) $results[] = array( 'sign' => $sign, 'flags' => $detected, 'offset' => $startOffset, 'length' => $length, 'match' => substr($text, $startOffset, $endOffset), ); if ($replace && !$sign[self::VDB_INCURABLE]) if (!$sign[self::VDB_LAST]) if ($const) $text = str_replace(substr($sign[self::VDB_SIGNATURE], 1), $sign[self::VDB_REPLACE], $text); else $text = preg_replace($sign[self::VDB_SIGNATURE], $sign[self::VDB_REPLACE], $text); return $detected; } public static function cbhtaccessredirect($sign, &$text, $fileType, $replace = FALSE, &$results = NULL) { if (!defined('SVC_CHOST') || $sign[self::VDB_FTYPES] && !isset($sign[self::VDB_FTYPES][$fileType])) return 0; $host = strtolower(SVC_CHOST); if (substr($host, 0, 4) === 'www.') $host = substr($host, 4); if (!strlen($host)) return 0; $detected = 0; $lines = explode("\n", $text); $nLines = count($lines); $pCond = $pEngine = -1; for ($i = 0; $i < $nLines; ++$i) { $line = strtolower(trim($lines[$i])); if (strlen($line) < 11 || $line[0] === '#') continue; if (substr($line, 0, 13) === 'rewriteengine') { if ($pEngine < 0) $pEngine = $i; else $lines[$i] = ''; } elseif (substr($line, 0, 11) === 'rewritecond') { if ($pCond < 0) $pCond = $i; } elseif (substr($line, 0, 11) === 'rewriterule') { if ( preg_match('~https?:/~', $line) && !strpos($line, $host) && !preg_match('~https?:/+(?:w+\.)?(?:[\%\$]\d|\%\{\w+\})~', $line) ) { $detected |= self::RET_DETECTED; if (self::cbhtaccessredirect_appendResult($sign, $lines[$i], $replace && !$sign[self::VDB_INCURABLE], $results)) { if ($pCond < 0) { unset($lines[$i]); } else { for ($j = $pCond; $j <= $i; ++$j) unset($lines[$j]); } } } $pCond = -1; } elseif (substr($line, 0, 13) === 'errordocument') { if (preg_match('~https?:/~', $line) && !strpos($line, $host)) { $detected |= self::RET_DETECTED; if (self::cbhtaccessredirect_appendResult($sign, $lines[$i], $replace && !$sign[self::VDB_INCURABLE], $results)) unset($lines[$i]); } } } if ($detected) { $detected |= ($sign[self::VDB_INCURABLE] ? self::RET_INCURABLE : self::RET_CANREPLACE) | ($sign[self::VDB_DOUBT] ? self::RET_DOUBT : 0); if ($replace && !$sign[self::VDB_INCURABLE]) { $detected |= self::RET_REPLACED; $text = implode("\n", $lines); } } return $detected; } protected static function cbhtaccessredirect_appendResult($sign, $line, $replaced, &$results) { if ($results === NULL) return $replaced; $results[] = array( 'sign' => $sign, 'flags' => self::RET_DETECTED | ($sign[self::VDB_INCURABLE] ? self::RET_INCURABLE : self::RET_CANREPLACE) | ($sign[self::VDB_DOUBT] ? self::RET_DOUBT : 0) | ($replaced ? self::RET_REPLACED : 0), 'offset' => -1, 'length' => strlen($line), 'match' => $line, ); return $replaced; } } $return = array( 'threats' => array(), 'errors' => array(), 'dirlist' => array(), 'skipped' => array(), 'stats' => array( 'threats' => 0, 'detectedfiles' => 0, 'checkedfiles' => 0, 'detecteddirs' => 0, 'checkeddirs' => 0, 'errors' => 0, 'treated' => 0, 'backupid' => 0, 'seconds' => 0.0, ), ); define('MINBUFSIZE', 10); $maxSize = 1<<20; if (!empty($_GET['maxsize']) && ($_GET['maxsize'] = shortNumberParse($_GET['maxsize'])) > 0) { if ($_GET['maxsize'] < MINBUFSIZE) $maxSize = MINBUFSIZE; elseif ($_GET['maxsize'] < $maxSize) $maxSize = $_GET['maxsize']; } define('MAXBUFSIZE', $maxSize); define('MAXBINSIZE', min($maxSize, 1<<17)); $maxSize = 0; if (isset($_GET['filetypes']) && strlen($_GET['filetypes'])) { $fileTypes = array(); foreach (explode(',', $_GET['filetypes']) as $ext) if (strlen($ext = trim($ext))) $fileTypes[$ext] = MAXBUFSIZE; } $userTypes = !empty($fileTypes); if (!$userTypes) { $fileTypes = array( 'htm'=>MAXBUFSIZE, 'html'=>MAXBUFSIZE, 'php'=>MAXBUFSIZE, 'phps'=>MAXBUFSIZE, 'phtml'=>MAXBUFSIZE, 'php4'=>MAXBUFSIZE, 'php5'=>MAXBUFSIZE, 'php7'=>MAXBUFSIZE, 'inc'=>MAXBUFSIZE, 'tpl'=>MAXBUFSIZE, 'class'=>MAXBUFSIZE, 'js'=>MAXBUFSIZE, 'pl'=>MAXBUFSIZE, 'perl'=>MAXBUFSIZE, 'py'=>MAXBUFSIZE, 'asp'=>MAXBUFSIZE, 'aspx'=>MAXBUFSIZE, 'svg'=>MAXBUFSIZE, 'xml'=>MAXBUFSIZE, ); } $_GET['vdbid'] = isset($_GET['vdbid']) ? abs((int)$_GET['vdbid']) : 0; $vdb = svcDataQuery(SVC_SVC, 'vdb/', array( 'vdbid' => $_GET['vdbid'], 'vdbver' => avScanner::VDBVERSION, 'from' => 'rexplacer', ), array( 'gzip' => SVC_CGZIP, 'json' => TRUE, 'cacheTime' => 1800, 'cacheFile' => SVC_CSVCCACHE.'-vdb'.$_GET['vdbid'].'-v'.avScanner::VDBVERSION.'.json', 'cacheClean' => SVC_CLC, )); if (!avScanner::setVDB($vdb)) return ERR_SVC + 0; unset($vdb); $ignored = svcDataQuery('', 'ignored', array(), array( 'gzip' => SVC_CGZIP, 'json' => TRUE, 'cacheFile' => SVC_CCACHE.'/ignored.json', 'cacheClean' => SVC_CLC, )); if (!is_array($ignored)) return ERR_SVC + 1; $ignored = array_flip($ignored); set_time_limit(300); ini_set('pcre.backtrack_limit', 10e6); if (!empty($_POST['filelist'])) $fileList = splitTextLines($_POST['filelist'], TRUE, TRUE, '|'); elseif (!empty($_GET['filelist'])) $fileList = splitTextLines($_GET['filelist'], TRUE, TRUE, '|'); else $fileList = array(); if ($fileList) $dirList = array('.'); elseif (!empty($_POST['dirlist'])) $dirList = splitTextLines($_POST['dirlist'], TRUE, TRUE, '|'); elseif (!empty($_GET['dirlist'])) $dirList = splitTextLines($_GET['dirlist'], TRUE, TRUE, '|'); else $dirList = array('.'); $replace = isset($_GET['replace']); $backup = $replace && isset($_GET['backup']); if ($backup) $return['stats']['backupid'] = svcRestore::init(SVC_CRESTORE, $_GET['backup'], '.'); $slowDown = $sleepTimer = 0.0; if (isset($_GET['slowdown'])) { $slowDown = (float)$_GET['slowdown']; if ($slowDown < 0.01) $slowDown = 0.0; elseif ($slowDown > 9.0) $slowDown = 9.0; } $dScan = $fileList ? NULL : new dirScanner(); $scan_flags = 0 | ($replace ? avScanner::SCAN_REPLACE_AFTER|avScanner::SCAN_APPLY_AFTER_TREATMENT : 0) | (isset($_GET['more']) ? 0 : avScanner::SCAN_PACK_RESULTS); $results = array(); $ret_curable = avScanner::RET_CANREPLACE|avScanner::RET_CANDELETE; $ret_cured = avScanner::RET_REPLACED|avScanner::RET_DELETE; $ret_doNotShow = avScanner::RET_DOUBT; $return['stats']['seconds'] = microtime(TRUE); if ($slowDown > 0.0) $sleepTimer = $return['stats']['seconds']; foreach ($dirList as $dir) { $dir = strtr(trim($dir, "\\/ \n\r\t\v\0"), '\\', '/'); if (!strlen($dir)) continue; $dir .= '/'; if (isset($ignored[$dir])) { $return['skipped'][] = $dir; continue; } if ($dScan && !$dScan->open($dir, DSCAN_FILES|DSCAN_DIRFIRST|DSCAN_INCLUDEBASE, isset($_GET['thisdir']) ? 0 : 64)) { $return['errors'][md5($dir)] = array($dir, 0, avScanner::RET_EREAD); continue; } while (is_string($file = ($dScan ? $dScan->read() : array_shift($fileList)))) { if (connection_aborted()) break; if ($dScan) { if ($dScan->isDir()) { if (isset($ignored[$file])) { $return['skipped'][] = $file; $dScan->cdUp(); } else { ++$return['stats']['checkeddirs']; } continue; } } else { if (!is_file($file)) continue; } $fsize = filesize($file); if ($fsize < MINBUFSIZE || MAXBUFSIZE < $fsize) { continue; } if (isset($ignored[$file])) { $return['skipped'][] = $file; continue; } $ftype = pathinfo($file, PATHINFO_EXTENSION); $skip = $userTypes ? (!isset($fileTypes[$ftype]) || $fsize > $fileTypes[$ftype]) : ($fsize > (isset($fileTypes[$ftype]) ? $fileTypes[$ftype] : MAXBINSIZE)); if ($skip) { continue; } $ftext = file_get_contents($file); if (!is_string($ftext) || strlen($ftext) < $fsize) { $return['errors'][md5($file)] = array($file, filemtime($file), avScanner::RET_EREAD); continue; } $detected = avScanner::scanBuffer($ftext, $ftype, $scan_flags, $results); ++$return['stats']['checkedfiles']; if ($slowDown > 0.0 && !($return['stats']['checkedfiles'] % 20)) { $sleepTimer = microtime(TRUE) - $sleepTimer; usleep($sleepTimer * 1e6 * $slowDown); $sleepTimer = microtime(TRUE); } if ($detected === 0) continue; $ftime = filemtime($file); $fmode = fileperms($file) & 0777; $nThreads = count($return['threats']); if ($replace) { $errorBit = 0; if ($detected & $ret_cured) { if ($backup && !svcRestore::pushFile($file)) { $errorBit |= avScanner::RET_EBACKUP; $return['errors'][md5($file)] = array($file, $ftime, avScanner::RET_EBACKUP); } elseif ($detected & avScanner::RET_REPLACED) { if (!@avScanner::file_rewrite($file, $ftext)) { $errorBit |= avScanner::RET_EWRITE; $return['errors'][md5($file)] = array($file, $ftime, avScanner::RET_EWRITE); } } elseif ($detected & avScanner::RET_DELETE) { if (!@avScanner::file_unlink($file)) { $errorBit |= avScanner::RET_EDELETE; $return['errors'][md5($file)] = array($file, $ftime, avScanner::RET_EDELETE); } } } foreach ($results as $result) { if ($result['flags'] & $ret_doNotShow) continue; if ($errorBit) { $result['flags'] |= $errorBit; $result['flags'] &= ~$ret_cured; } elseif ($result['flags'] & $ret_cured) { $return['stats']['treated']++; } $return['threats'][] = array($file, $ftime, $result['flags'], $result['sign'][avScanner::VDB_TITLE], $result['sign'][avScanner::VDB_SID], $fmode, $fsize); } } else { foreach ($results as $result) { if ($result['flags'] & $ret_doNotShow) continue; $return['threats'][] = array($file, $ftime, $result['flags'], $result['sign'][avScanner::VDB_TITLE], $result['sign'][avScanner::VDB_SID], $fmode, $fsize); } } if ($nThreads < count($return['threats'])) { ++$return['stats']['detectedfiles']; $dirName = $dScan ? $dScan->baseDir().$dScan->cd() : formatDirName(pathinfo($file, PATHINFO_DIRNAME), ''); $return['dirlist'][md5($dirName)] = $dirName; } else { unset($return['errors'][md5($file)]); } } } unset($dScan); $return['stats']['seconds'] = round(microtime(TRUE) - $return['stats']['seconds'], 3); $return['stats']['threats'] = count($return['threats']); $return['stats']['errors'] = count($return['errors']); $return['stats']['detecteddirs'] = count($return['dirlist']); if (defined('JSON_UNESCAPED_SLASHES')) echo json_encode($return, JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE); else echo json_encode($return); unset($return); ?>