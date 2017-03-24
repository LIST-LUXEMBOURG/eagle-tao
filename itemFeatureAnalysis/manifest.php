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
return [ 
		'name' => 'itemFeatureAnalysis',
		'label' => 'Item Feature Analysis',
		'description' => 'Item Feature Analysis',
		'license' => 'GPL-2.0',
		'version' => '0.0.1',
		'author' => 'Luxembourg Institute of Science and Technology (LIST)',
		'requires' => [ 
				'tao' => '>=2.10.0',
				'taoItems' => '>=2.8.1' 
		],
		'models' => [ 
				'http://www.tao.lu/Ontologies/ItemMetrics.rdf' 
		],
		'install' => [ 
				'rdf' => [ 
						[ 
								'ns' => 'http://www.tao.lu/Ontologies/ItemFeatureAnalysis.rdf',
								'file' => dirname ( __FILE__ ) . '/models/ontology/ItemFeatureAnalysis.rdf' 
						] 
				],
				'php' => [ 
						dirname ( __FILE__ ) . '/install/scripts/addToAutoloader.php' 
				] 
		],
		'uninstall' => [ 
				'php' => [ 
						dirname ( __FILE__ ) . '/install/scripts/removeFromAutoloader.php' 
				] 
		],
		'managementRole' => 'http://www.tao.lu/Ontologies/ItemFeatureAnalysis.rdf#ItemFeatureAnalysisManagerRole',
		'acl' => [ 
				[ 
						'grant',
						'http://www.tao.lu/Ontologies/ItemFeatureAnalysis.rdf#ItemFeatureAnalysisManagerRole',
						[ 
								'ext' => 'itemFeatureAnalysis' 
						] 
				] 
		],
		'autoload' => [ 
				'psr-4' => [ 
						'itis\\itemFeatureAnalysis\\' => dirname ( __FILE__ ) . DIRECTORY_SEPARATOR 
				] 
		],
		'routes' => [ 
				'/itemFeatureAnalysis' => 'itis\\itemFeatureAnalysis\\controller' 
		],
		'constants' => [
				
				// extension name
				"EXT_NAME" => 'itemFeatureAnalysis',
				
				// default module name
				'DEFAULT_MODULE_NAME' => 'ItemFeatureAnalysis',
				
				// default action name
				'DEFAULT_ACTION_NAME' => 'index',
				
				// BASE WWW the web resources path
				'BASE_WWW' => ROOT_URL . 'itemFeatureAnalysis/views/',
				
				// BASE URL (usually the domain root)
				'BASE_URL' => ROOT_URL . 'itemFeatureAnalysis',
				
				// actions directory
				"DIR_ACTIONS" => dirname ( __FILE__ ) . DIRECTORY_SEPARATOR . "controller" . DIRECTORY_SEPARATOR,
				
				// models directory
				"DIR_MODELS" => dirname ( __FILE__ ) . DIRECTORY_SEPARATOR . "models" . DIRECTORY_SEPARATOR,
				
				// models directory
				"DIR_STOPWORDS" => dirname ( __FILE__ ) . DIRECTORY_SEPARATOR . 'models' . DIRECTORY_SEPARATOR. 'ontology' . DIRECTORY_SEPARATOR. 'stopwords' . DIRECTORY_SEPARATOR,
				
				// views directory
				"DIR_VIEWS" => dirname ( __FILE__ ) . DIRECTORY_SEPARATOR . "views" . DIRECTORY_SEPARATOR,
				
				// helpers directory
				"DIR_HELPERS" => dirname ( __FILE__ ) . DIRECTORY_SEPARATOR . "helpers" . DIRECTORY_SEPARATOR,
				
				// BASE PATH: the root path in the file system (usually the document root)
				'BASE_PATH' => dirname ( __FILE__ ) . DIRECTORY_SEPARATOR,
				
				'DOCS_PATH' => dirname ( __FILE__ ) . DIRECTORY_SEPARATOR . "views" . DIRECTORY_SEPARATOR . "data" . DIRECTORY_SEPARATOR,
				'DOCS_URL' => ROOT_URL . "itemFeatureAnalysis/views/data/" 
		],
		'extra' => [ 
				'structures' => dirname ( __FILE__ ) . DIRECTORY_SEPARATOR . 'controller' . DIRECTORY_SEPARATOR . 'structures.xml' 
		] 
];
