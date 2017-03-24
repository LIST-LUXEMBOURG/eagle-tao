<?php

/**
 * @copyright Luxembourg Institute of Science and Technology, 2015. All rights reserved. If you wish
 * to use this code for any purpose, please contact the author(s).
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR
 * IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND
 * FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR
 * CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL
 * DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY,
 * WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY
 * WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 */
/**
 *
 * @author Olivier Pedretti
 *        
 */
namespace itis\itemFeatureAnalysis\scripts;

use itis\itemFeatureAnalysis\models\ItemFeatureAnalysisService;

require_once dirname ( __FILE__ ) . '/../includes/raw_start.php';
function failed($msg) {
	die ( PHP_EOL . '!! FAILED !!' . PHP_EOL . $msg . PHP_EOL );
}
function message($msg) {
	echo $msg . PHP_EOL;
}
function warning($msg) {
	echo PHP_EOL . 'WARNING :' . PHP_EOL . $msg . PHP_EOL;
}

// define options
$OPT_HELP = 'h';
$OPT_HELP_LONG = 'help';
$OPT_RECURSIVE = 'r';
$OPT_TARGET = 'target';
$OPT_LANG = 'lang';
$OPT_OUTPUT_DIR = 'outputDir';
function help() {
	die ( "usage:
	-h or --help	this help
	-r		recursive search of xml file
	--target	file or directory to analyse
	--lang		language of analyse
	--outputDir	output directory for analyse log" );
}

// parse options
$options = getopt ( $OPT_RECURSIVE . $OPT_HELP, [ 
		$OPT_TARGET . ':',
		$OPT_LANG . ':',
		$OPT_OUTPUT_DIR . ':',
		$OPT_HELP_LONG 
] );
// help?
if (array_key_exists ( $OPT_HELP_LONG, $options ) || array_key_exists ( $OPT_HELP, $options )) {
	help ();
}
// option recursive
$recursive = array_key_exists ( $OPT_RECURSIVE, $options );
// option target
if (! array_key_exists ( $OPT_TARGET, $options )) {
	failed ( 'option not specified: ' . $OPT_TARGET );
}
$target = $options [$OPT_TARGET];
// option lang
if (! array_key_exists ( $OPT_LANG, $options )) {
	failed ( 'option not specified: ' . $OPT_LANG );
}
$lang = $options [$OPT_LANG];
// log dir
if (! array_key_exists ( $OPT_OUTPUT_DIR, $options )) {
	failed ( 'option not specified: ' . $OPT_OUTPUT_DIR );
}
$logDir = $options [$OPT_OUTPUT_DIR];

// some input check
// check lang
if (preg_match ( '/^[a-z]{2}-[A-Z]{2}$/', $lang ) !== 1) {
	failed ( 'bad lang format: ' . $lang );
}
// check target
$fs_rsc = realpath ( $target );
if (empty ( $fs_rsc )) {
	failed ( 'not a valid file-system resource for ' . $OPT_TARGET );
}
// check output directory
$fs_rsc_output_dir = realpath ( $logDir );
if (empty ( $fs_rsc_output_dir )) {
	failed ( 'not a valid file-system resource for ' . $OPT_OUTPUT_DIR );
}
$logDir = $fs_rsc_output_dir;
if (! is_dir ( $logDir )) {
	failed ( 'not a directory: ' . $logDir );
}
if (! is_writable ( $logDir )) {
	failed ( 'not readable: ' . $logDir );
}

// build list of items to be analysed
$toAnalyse = [ ];
if (is_dir ( $fs_rsc )) {
	// if directory
	message ( 'search in: ' . $fs_rsc );
	$recursiveDir = new \RecursiveDirectoryIterator ( $fs_rsc, \RecursiveIteratorIterator::LEAVES_ONLY );
	if ($recursive) {
		$recursiveDir = new \RecursiveIteratorIterator ( $recursiveDir, \RecursiveIteratorIterator::SELF_FIRST );
	}
	$files = new \RegexIterator ( $recursiveDir, '/.*\.xml$/i', \RecursiveRegexIterator::GET_MATCH );
	foreach ( $files as $filename => $data ) {
		if (! is_file ( $filename )) {
			continue;
		}
		if (! is_readable ( $filename )) {
			warning ( 'not readable: ' . $filename );
		}
		$xmlData = file_get_contents ( $filename, FILE_TEXT );
		if ($xmlData === false) {
			warning ( 'failed to read content of: ' . $filename );
		}
		message ( 'file to analyse: ' . $filename );
		$toAnalyse [] = [ 
				$filename,
				$xmlData 
		];
	}
} else if (is_file ( $fs_rsc )) {
	// if a file
	$filename = $fs_rsc;
	if (! is_readable ( $filename )) {
		failed ( 'not readable: ' . $filename );
	}
	$xmlData = file_get_contents ( $filename, FILE_TEXT );
	if ($xmlData === false) {
		warning ( 'failed to read content of: ' . $filename );
	}
	message ( 'file to analyse: ' . $filename );
	$toAnalyse [] = [ 
			$filename,
			$xmlData 
	];
} else {
	failed ( 'unsupported file-system resource' );
}

if (empty ( $toAnalyse )) {
	failed ( 'no file to analyse' );
}
$itemFeatureAnalysisService = ItemFeatureAnalysisService::singleton ();
foreach ( $toAnalyse as list ( $fileName, $data ) ) {
	message ( 'analyse: ' . $fileName );
	$report = $itemFeatureAnalysisService->analyse ( [ 
			$data 
	], false, $lang );
	
	$fileNameDir = dirname ( $fileName );
	
	$relativePath = substr ( $fileNameDir, strpos ( $fileNameDir, $fs_rsc ) + strlen ( $fs_rsc ) );
	$logPath = $logDir . $relativePath;
	$dirToCreate = $logPath;
	if (! file_exists ( $dirToCreate )) {
		if (! mkdir ( $dirToCreate, 0777, true )) {
			warning ( 'failed to create directory: ' . $dirToCreate );
			continue;
		}
	}
	$jsonString = json_encode ( $report, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES );
	$logFileName = $logPath . DIRECTORY_SEPARATOR . basename ( $fileName ) . '.json';
	message ( 'save result to: ' . $logFileName );
	if (file_put_contents ( $logFileName, $jsonString ) === false) {
		warning ( 'write error for: ' . $logFileName );
		continue;
	}
}







