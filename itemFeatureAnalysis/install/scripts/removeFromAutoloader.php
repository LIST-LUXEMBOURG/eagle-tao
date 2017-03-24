<?php
/**
 * @copyright Luxembourg Institute of Science and Technology, 2016. All rights reserved. If you wish
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
 * @author Olivier Pedretti olivier.pedretti@list.lu
 */

/**
 * Remove namespaces to TAO autoloader file
 */
$autoloaderFile = VENDOR_PATH . 'composer/autoload_psr4.php';
$content = file_get_contents ( $autoloaderFile );
$arrayStartInFile = 'return array(';
if (strpos ( $content, $arrayStartInFile ) === false) {
	throw new common_exception_PreConditionFailure ( 'Bad content in: ' . $autoloaderFile );
}

$extension = common_ext_ExtensionsManager::singleton ()->getExtensionById ( EXT_NAME );

$composerFile = $extension->getDir () . DIRECTORY_SEPARATOR . 'composer.json';
$composerFileContent = file_get_contents ( $composerFile );
$composerJson = json_decode ( $composerFileContent, true );

$namespaces = array_keys ( $composerJson ['autoload'] ['psr-4'] );
if (empty ( $namespaces )) {
	common_Logger::w ( 'no namespace to remove found' );
	return;
}
$modifyFile = false;
foreach ( $namespaces as $namespace ) {
	$safeForRegEx = preg_quote ( '\'' . str_replace ( '\\', '\\\\', $namespace ) . '\' => array($baseDir . \'/' . EXT_NAME . '\'', '/' );
	if (preg_match ( "/\s*" . $safeForRegEx . ".*/", $content ) !== 1) {
		common_Logger::w ( 'no existing namespace: ' . $namespace );
		continue;
	}
	$content = preg_replace ( "/\s*" . $safeForRegEx . ".*/", '', $content );
	$modifyFile = true;
	common_Logger::d ( 'namespace removed: ' . $namespace );
}
if ($modifyFile) {
	$content = file_put_contents ( $autoloaderFile, $content );
}
