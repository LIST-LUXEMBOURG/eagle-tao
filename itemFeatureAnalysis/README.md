## Unit Test
- add following lines to "[tao_root]/phpunit.xml"

```xml
<testsuite name="itemFeatureAnalysis">
    <directory>itemFeatureAnalysis/test</directory>
</testsuite>
```

- run "phpunit --testsuite itemFeatureAnalysis"

## TAO integration
Add an action button to the Item extension and generate a TAO report

## Analysis features
- analyse one interaction per item
- interaction types supported:
    - choiceInteraction
    - inlineChoiceInteraction
    - matchInteraction
    
## Metrics

### JaroWinkler
- Average of distances between all combinations between distractors
- Standard deviation of all combinations between distractors

For distractors A, B & C, combinations are A-B, A-C, B-C.
Distractors are cleaned by replacing punctuation by a space, then replacing multiple spaces by only one.
Supported languages: any

### Disco
- Average of distances between all combinations of distractors words
- Standard deviation of all combinations of distractors words

For distractors A & B where A is "a1 a2" (2 words) and B is "b1 b2"(2 words), all combinations are a1-b1, a1-b2, a2-b1, a2-b2.
Distractors are cleaned by replacing punctuation by a space, then replacing multiple spaces by only one.
Supported languages: fr, en, de

### WordNet
- Average of distances between all combinations of distractors words
- Standard deviation of all combinations of distractors words

For distractors A & B where A is "a1 a2" (2 words) and B is "b1 b2"(2 words), all combinations are a1-b1, a1-b2, a2-b1, a2-b2.
Distractors are cleaned by replacing punctuation by a space, then replacing multiple spaces by only one.
Supported languages: en

### TextDifficulty
- Number of sentences
- Number of words
- Number of syllables
- Number of propositions
- Average sentence length
- Average word length
- Average of syllables per word
- Average  of propositions
- Type token ratio
- References
- Identifications
- Flesch Kincaid
- Dale
- Dictionary COCA
- Amstad
- Final Text difficulty

Metrics on:
- item body ("itemBody" QTI XML tag) + serialized distractors (space separated) for "**choiceInteraction**" interaction
- item body ("itemBody" QTI XML tag) with interaction replaced by correct response for "**inlineChoiceInteraction**" interaction
- item body ("itemBody" QTI XML tag) + serialized distractors (space separated) extracted from the second interaction "simpleMatchSet" (vertical distractors) for "**matchInteraction**" interaction

Supported languages: fr, en, de
Analysed text is the item body without incorrect distrators
German analyse can take more time

### SemSim
Uris of distrators are solved via Koda.
The complete item body is analysed by Koda.
A distractor is considered having one or more URI if, when it is cleaned (punctuation replaced by spaces and multiple spaces replaced by one space), it has an uri or if one or more words in a distractor has an uri (Stop-words excluded).

If Koda solves all distractors (except for "**matchInteraction**" interaction where only the second "simpleMatchSet" is used), SemSim is performed:
- Average of similarities between all combinations between distractors
- Standard deviation of similarities of all combinations between distractors

For distractors A, B & C, combinations are A-B, A-C, B-C.
Supported languages: en

## Divers
### Stopwords
Source of stopwords: http://members.unine.ch/jacques.savoy/clef/index.html (2016/03/02) BSD License with Copyright (c) 2005, Jacques Savoy.

