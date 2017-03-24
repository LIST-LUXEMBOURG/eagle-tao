<?php
/**  
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 * 
 * Copyright (c) 2016 (original work) Luxembourg Institute of Science and Technology (LIST);
 *               
 * 
 */
$todefine = [
		// Metrics
		'ITEMFEATUREANALYSIS_DISTRACTORS_JAROWINKLER_AVERAGE' => 'http://www.tao.lu/Ontologies/ItemFeatureAnalysis.rdf#DistractorsJaroWinklerAverage',
		'ITEMFEATUREANALYSIS_DISTRACTORS_JAROWINKLER_STD_DEV' => 'http://www.tao.lu/Ontologies/ItemFeatureAnalysis.rdf#DistractorsJaroWinklerStdDev',
		'ITEMFEATUREANALYSIS_DISTRACTORS_DISCO_AVERAGE' => 'http://www.tao.lu/Ontologies/ItemFeatureAnalysis.rdf#DistractorsDiscoAverage',
		'ITEMFEATUREANALYSIS_DISTRACTORS_DISCO_STD_DEV' => 'http://www.tao.lu/Ontologies/ItemFeatureAnalysis.rdf#DistractorsDiscoStdDev',
		'ITEMFEATUREANALYSIS_DISTRACTORS_WORDNET_AVERAGE' => 'http://www.tao.lu/Ontologies/ItemFeatureAnalysis.rdf#DistractorsWordNetAverage',
		'ITEMFEATUREANALYSIS_DISTRACTORS_WORDNET_STD_DEV' => 'http://www.tao.lu/Ontologies/ItemFeatureAnalysis.rdf#DistractorsWordNetStdDev',
		'ITEMFEATUREANALYSIS_DISTRACTORS_SEMSIM_AVERAGE' => 'http://www.tao.lu/Ontologies/ItemFeatureAnalysis.rdf#DistractorsSemSimAverage',
		'ITEMFEATUREANALYSIS_DISTRACTORS_SEMSIM_STD_DEV' => 'http://www.tao.lu/Ontologies/ItemFeatureAnalysis.rdf#DistractorsSemSimStdDev',
		'ITEMFEATUREANALYSIS_ITEM_TEXT_DIFFICULTY_NUMBER_OF_SENTENCES' => 'http://www.tao.lu/Ontologies/ItemFeatureAnalysis.rdf#ItemTextDifficultyNumberOfSentences',
		'ITEMFEATUREANALYSIS_ITEM_TEXT_DIFFICULTY_NUMBER_OF_WORDS' => 'http://www.tao.lu/Ontologies/ItemFeatureAnalysis.rdf#ItemTextDifficultyNumberOfWords',
		'ITEMFEATUREANALYSIS_ITEM_TEXT_DIFFICULTY_NUMBER_OF_SYLLABLES' => 'http://www.tao.lu/Ontologies/ItemFeatureAnalysis.rdf#ItemTextDifficultyNumberOfSyllables',
		'ITEMFEATUREANALYSIS_ITEM_TEXT_DIFFICULTY_NUMBER_OF_PROPOSITIONS' => 'http://www.tao.lu/Ontologies/ItemFeatureAnalysis.rdf#ItemTextDifficultyNumberOfPropositions',
		'ITEMFEATUREANALYSIS_ITEM_TEXT_DIFFICULTY_AVERAGE_SENTENCE_LENGTH' => 'http://www.tao.lu/Ontologies/ItemFeatureAnalysis.rdf#ItemTextDifficultyAverageSentenceLength',
		'ITEMFEATUREANALYSIS_ITEM_TEXT_DIFFICULTY_AVERAGE_WORD_LENGTH' => 'http://www.tao.lu/Ontologies/ItemFeatureAnalysis.rdf#ItemTextDifficultyAverageWordLength',
		'ITEMFEATUREANALYSIS_ITEM_TEXT_DIFFICULTY_AVERAGE_NUMBER_OF_SYLLABLES_PER_WORD' => 'http://www.tao.lu/Ontologies/ItemFeatureAnalysis.rdf#ItemTextDifficultyAverageNumberOfSyllablesPerWord',
		'ITEMFEATUREANALYSIS_ITEM_TEXT_DIFFICULTY_AVERAGE_NUMBER_OF_PROPOSITIONS' => 'http://www.tao.lu/Ontologies/ItemFeatureAnalysis.rdf#ItemTextDifficultyAverageNumberOfPropositions',
		'ITEMFEATUREANALYSIS_ITEM_TEXT_DIFFICULTY_TYPE_TOKEN_RATIO' => 'http://www.tao.lu/Ontologies/ItemFeatureAnalysis.rdf#ItemTextDifficultyTypeTokenRatio',
		'ITEMFEATUREANALYSIS_ITEM_TEXT_DIFFICULTY_REFERENCES' => 'http://www.tao.lu/Ontologies/ItemFeatureAnalysis.rdf#ItemTextDifficultyReferences',
		'ITEMFEATUREANALYSIS_ITEM_TEXT_DIFFICULTY_IDENTIFICATIONS' => 'http://www.tao.lu/Ontologies/ItemFeatureAnalysis.rdf#ItemTextDifficultyIdentifications',
		'ITEMFEATUREANALYSIS_ITEM_TEXT_DIFFICULTY_FLESCH_KINCAID' => 'http://www.tao.lu/Ontologies/ItemFeatureAnalysis.rdf#ItemTextDifficultyFleschKincaid',
		'ITEMFEATUREANALYSIS_ITEM_TEXT_DIFFICULTY_DALE' => 'http://www.tao.lu/Ontologies/ItemFeatureAnalysis.rdf#ItemTextDifficultyDale',
		'ITEMFEATUREANALYSIS_ITEM_TEXT_DIFFICULTY_DICTIONARY_COCA' => 'http://www.tao.lu/Ontologies/ItemFeatureAnalysis.rdf#ItemTextDifficultyDictionaryCoca',
		'ITEMFEATUREANALYSIS_ITEM_TEXT_DIFFICULTY_AMSTAD' => 'http://www.tao.lu/Ontologies/ItemFeatureAnalysis.rdf#ItemTextDifficultyAmstad',
		'ITEMFEATUREANALYSIS_ITEM_TEXT_DIFFICULTY_FINAL_TEXT_DIFFICULTY' => 'http://www.tao.lu/Ontologies/ItemFeatureAnalysis.rdf#ItemTextDifficultyFinalTextDifficulty',
		
		// Called WebServices
		
		// Disco
		'ITEMFEATUREANALYSIS_DISCO_WEBSERVICE_URL' => 'http://eagle.list.lu:8080/DISCO_Webservice/metrics/getNewDiscoSimilarity',
		'ITEMFEATUREANALYSIS_DISCO_WEBSERVICE_TIMEOUT_IN_SECONDS' => 5,
		// Wordnet
		'ITEMFEATUREANALYSIS_WORDNET_WEBSERVICE_URL' => 'http://eagle.list.lu:8080/WordNet-Webservice/wordnet/query',
		'ITEMFEATUREANALYSIS_WORDNET_WEBSERVICE_TIMEOUT_IN_SECONDS' => 5,
		// TextDifficulty
		'ITEMFEATUREANALYSIS_TEXTDIFFICULTY_WEBSERVICE_URL' => 'http://eagle.list.lu:8080/Textdifficulty-Webservice/metrics/query',
		'ITEMFEATUREANALYSIS_TEXTDIFFICULTY_WEBSERVICE_TIMEOUT_IN_SECONDS' => 60,
		// Koda
		'ITEMFEATUREANALYSIS_KODA_WEBSERVICE_URL' => 'http://smartdocs.list.lu/kodaweb/rest/koda-1.0/annotate',
		'ITEMFEATUREANALYSIS_KODA_WEBSERVICE_ONTOLOGY_EN' => 'DBPEDIA_EN_EN',
		'ITEMFEATUREANALYSIS_KODA_WEBSERVICE_ONTOLOGY_DE' => 'DBPEDIA_EN_DE',
		'ITEMFEATUREANALYSIS_KODA_WEBSERVICE_ONTOLOGY_FR' => 'DBPEDIA_EN_FR',
		'ITEMFEATUREANALYSIS_KODA_WEBSERVICE_TIMEOUT_IN_SECONDS' => 60,
		// SemSim
		'ITEMFEATUREANALYSIS_SEMSIM_WEBSERVICE_URL' => 'http://eagle.list.lu:8080/SemSimWebService/semsim',
		'ITEMFEATUREANALYSIS_SEMSIM_WEBSERVICE_ONTOLOGY_EN' => 'DBPEDIA_EN',
		'ITEMFEATUREANALYSIS_SEMSIM_WEBSERVICE_TIMEOUT_IN_SECONDS' => 60,
		
		// Divers
		
		// Stopwords
		'ITEMFEATUREANALYSIS_STOPWORDS_EN_FILENAME' => DIR_STOPWORDS . 'englishST.txt',
		'ITEMFEATUREANALYSIS_STOPWORDS_DE_FILENAME' => DIR_STOPWORDS . 'germanST.txt',
		'ITEMFEATUREANALYSIS_STOPWORDS_FR_FILENAME' => DIR_STOPWORDS . 'frenchST.txt' 
];
?>
