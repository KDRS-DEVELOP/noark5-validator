<?php
require_once ('tests/xml/XMLTestValidation.php');
require_once ('tests/xml/XMLTestWellFormed.php');
require_once ('tests/file/DocumentDirectoryTest.php');
require_once ('tests/noark5/v31/CheckNumberObjectsArkivutrekk.php');
require_once ('handler/DokumenterFolderHandler.php');
require_once ('handler/DocumentListHandler.php');
require_once ('testProperties/XMLWellFormedTestProperty.php');
require_once ('testProperties/FileChecksumTestProperty.php');
require_once ('testProperties/DocumentDirectoryTestProperty.php');
require_once ('tests/Test.php');


// TODO :  Fix this !! $extractionInfo = $this->arkivUtrekk->getExtractionInfo();
//$arkivstrukturData = $this->arkivUtrekk->getArkivstruktur();
//$numberOfFileReportedInArkivUttrekk = $arkivstrukturData->getNumberMappe();

class StandardTest extends Test
{

    protected $directory;

    protected $standardExtractionContents;

    protected $arkivUttrekk;

    protected $arkivstrukturFilename;

    protected $testResultsHandler;

    protected $numberOfDocumentsProcessed;

    protected $numberOfFileProcessed = -1;

    protected $numberOfDocumentsInDirectory = -1;

    protected $numberOfRegistryEntryProcessed;

    protected $infoFileDetails;
    protected $arkivUttrekkDetails;

    protected $dokumenterDirectory;

    /**
     *
     * @var ArkivstrukturStatistics $statistics: counts of the various noark5 complextypes that have been processed
     */
    protected $statistics = null;

    public function __construct($testName, $directory, $testResultsHandler, $infoFilename, $noark5StructureFile,  $testProperty)
    {
        parent::__construct($testName, $testProperty);
        $this->directory = $directory;
        $this->testResultsHandler = $testResultsHandler;

        // A list of files we expect to see in the directory and a list of xml-files to validate
        if ($noark5StructureFile !== null) {
            $this->standardExtractionContents = simplexml_load_file($noark5StructureFile);
        } else {
            $this->standardExtractionContents = simplexml_load_file($runDirectory . DIRECTORY_SEPARATOR . Constants::LOCATION_OF_NOARK5_V31_STRUCTURE_FILE);
        }

        $this->processArkivUttrekk();
        $this->processInfoFile($infoFilename);

        $this->arkivstrukturFilename = $this->arkivUttrekk->getArkivstruktur()->getFilename();
    }

    public function processArkivUttrekk() {
        $arkivUttrekkHandler = new ArkivuttrekkHandler(join(DIRECTORY_SEPARATOR, array($directory, Constants::NAME_ARKIVUTTREKK_XML)));
        $arkivUttrekkHandler->processArkivuttrekk();
        $this->arkivUttrekkDetails = $arkivUttrekkHandler->getArkivUttrekkDetails();
        $arkivUttrekkHandler = null;
    }

    public function processInfoFile($infoFilename) {
        $infoFileHandler = new InfoFileHandler($infoFilename);
        $infoFileHandler->processInfofile();
        $this->infoFileDetails = $infoFileHandler->getInfoFileDetails();
        $infoFileHandler = null;
    }

    public function runTest()
    {
        $this->preTestProcessing();
        $this->testA0();
        $this->testA1();
        $this->test2();
        $line = readline("Press enter to continue: ");
        $this->test3();
        $line = readline("Press enter to continue: ");
        $this->test4();
        $line = readline("Press enter to continue: ");
        $this->test5();
        //$line = readline("Press enter to continue: ");
        $this->test6();
        $line = readline("Press enter to continue: ");
        $this->test9();
        $line = readline("Press enter to continue: ");
    }



    /*
     * Test A0
     *
     *  checks if all the files that we expect in the directory are present and readable
     *
     */
    public function testA0()
    {
        foreach ($this->standardExtractionContents->directoryContents->file as $file) {
            $testProperty = new TestProperty(Constants::TEST_FILE_EXISTS_AND_READABLE);
            $fileExistsTest = new FileExistsTest(Constants::TEST_FILE_EXISTS_AND_READABLE, $file->filename, $this->directory,  $testProperty);
            $fileExistsTest->runTest();

            if ($testProperty->getResult() == true) {
                $this->logger->info('There are no problems reading the file [' . $file->filename . ' ].');
            } else {
                $this->logger->error('There are problems reading the file [' . $file->filename . ' ]. See log file for details.');
                // This test is only reported in the reportfile, in the event of a failure
                $this->testResultsHandler->addResult($testProperty, Constants::TEST_TYPE_A0);
                $this->testProperty->setTestResult(false);
            }
        }
    }

    /*
     * Test A1
     *
     * Checks if all the xml files in the directory are well-formed and valid
     * The test checks whether the XML files are wellformed according to the XML 1.0 standard, and if
     * the files validates against their respective Noark 5 XSD schema.
     *
     */
    public function testA1()
    {
        $this->logger->trace('Starting test A1');
        $this->logger->info('Testing all XML/XSD files for well-formedness');

        foreach ($this->standardExtractionContents->directoryContents->file as $file) {
            $xmlValidationTestProperty = new XMLWellFormedTestProperty(Constants::TEST_XMLTEST_VALIDATION_WELLFORMED);
            $xmlTestWellFormed = new XMLTestWellFormed(Constants::TEST_XMLTEST_VALIDATION_WELLFORMED, $this->directory, $file->filename, $xmlValidationTestProperty);
            $xmlTestWellFormed->runTest();
            if ($xmlTestWellFormed->getResult() == false) {
                $this->testProperty->setTestResult(false);
            }
            $this->testResultsHandler->addResult($xmlValidationTestProperty, Constants::TEST_TYPE_A1);
            $xmlTestWellFormed = null;
        }

        $this->logger->info('Testing all XML files for validity');

        foreach ($this->standardExtractionContents->filesToValidate->file as $file) {
            $testProperty = new XMLValidationTestProperty(Constants::TEST_XMLTEST_VALIDATION_VALID);
            $xmlTestValidation = new XMLTestValidation(Constants::TEST_XMLTEST_VALIDATION_VALID, $this->directory, $file->filename, $file->validatedBy, $testProperty);
            $xmlTestValidation->runTest();
            if ($xmlTestValidation->getResult() == false) {
                $this->testProperty->setTestResult(false);
            }
            $this->testResultsHandler->addResult($testProperty, Constants::TEST_TYPE_A1);
            $xmlTestValidation = null;
        }
    }

    /*
     * Test A2
     * Calculate checksums of all documents listed under a DocumentObject and report if it is correct or not
     *
     */
    public function testA2()
    {
        $testProperty = new TestProperty(Constants::TEST_CHECKSUM_ALL_DOCUMENTS);
        try {
            $allDocumentChecksumTest = new Test(Constants::TEST_CHECKSUM_ALL_DOCUMENTS, $testProperty);

            // We don't call $allDocumentChecksumTest->runTest because the test is handled
            // in ArkivstrukturDocumentChecksumTest durnig parsing of the file
            $akivstrukturParser = new ArkivstrukturDocumentChecksumTest($this->directory);
            $this->parseFile($akivstrukturParser, $this->arkivstrukturFilename);

            if ($akivstrukturParser->getErrorsEncountered() == true) {
                $testProperty->addTestResultReportDescription('Det var funnet feil med sjeksumm av filer i dokument mappen. Antall feil funnet er ' .
                    $akivstrukturParser->getNumberErrorsEncountered() . ' logfilen inneholder en liste av filer som ble sjekket');
                $this->testProperty->setTestResult(false);
            }
            $this->testResultsHandler->addResult($allDocumentChecksumTest, Constants::TEST_TYPE_A2);
        }
        catch (Exception $e) {
            $this->logger->error("Error when attempting test " . $testProperty->getDescription() . ". The following exception occured " . $e);
        }
    }

    /*
     * Test A5
     * Tester om antall dokumenter som oppgis i arkivstruktur.xml validerer mot antall dokumenter
     * som oppgis i «antallDokumentfiler» i arkivuttrekk.xml – dvs om antall dokumenter hentet ut
     * i uttrekket stemmer overens med faktiske dokumenter i arkivdelen.';
     *
     */
    public function testA5()
    {
        if ($this->statistics === null) {
            $this->parseArkivstruktur();
        }

        $extractionInfo = $this->arkivUtrekk->getExtractionInfo();
        $numberOfDocumentsReportedInArkivUttrekk = $extractionInfo->getAntallDokumentfiler();

        $testProperty = new TestProperty(Constants::TEST_COUNT_DOCUMENTS_ARKIVUTTREKK);
        $documentDirectoryTest = new CheckNumberObjectsArkivutrekk(Constants::TEST_COUNT_DOCUMENTS_ARKIVUTTREKK,
                                                                    $numberOfDocumentsReportedInArkivUttrekk,
                                                                     $this->statistics->getNumberOfDocumentObjectProcessed(),
                                                                       Constants::NAME_ARKIVSTRUKTUR_XML,
                                                                        'dokument', $testProperty);
        $documentDirectoryTest->runTest();
        $this->testResultsHandler->addResult($testProperty, Constants::TEST_TYPE_A5);
        print $testProperty . PHP_EOL;
    }

    /*
     * Test A6
     *
     * Existence test of documents in document folder against list of DocumentObjects in arkivstruktur.xml
     *
     *  NOTE: If this test crashes the script, you should consider increasing the amount of memory available to the script
     *
     */
    public function testA6()
    {
        try {
            $this->documentListOverview();
            if ($this->numberOfDocumentsProcessed == - 1) {
                print 'Cannot run a test on documents unless test (Document test) has been run' . PHP_EOL;
                return;
            }

            print 'Testing for known problems in documents directory cross-referenced with arkivstruktur.xml' . PHP_EOL;

            $testProperty = new DocumentDirectoryTestProperty(Constants::TEST_DOCUMENT_DIRECTORY);
            $documentDirectoryTest = new DocumentDirectoryTest(Constants::TEST_DOCUMENT_DIRECTORY, $this->dokumenterDirectory,
                $this->documentListHandler, $testProperty);
            $documentDirectoryTest->runTest();
            $this->testResultsHandler->addResult($testProperty, Constants::TEST_TYPE_A7);
            print $testProperty . PHP_EOL;
        }
        catch (Exception $e) {
            $this->logger->error("Error when attempting test " . $testProperty->getDescription() . ". The following exception occured " . $e);
        }
    }


    /*
     * Test A7
     * Tester om antall elementer av type «mappe» stemmer overens med antall «mappe numerOfOccurrences»
     * i arkivuttrekk.xml – altså om antall mapper som blir med ut i uttrekket stemmer overens med faktisk
     * antall mapper i arkivdelen.
     *
     */
    public function testA7()
    {
        if ($this->statistics === null) {
            $this->parseArkivstruktur();
        }

        $extractionInfo = $this->arkivUtrekk->getExtractionInfo();
        $numberOfFileReportedInArkivUttrekk = $extractionInfo->getNumberOfFile();

        $testProperty = new TestProperty(Constants::TEST_COUNT_MAPPE_ARKIVUTTREKK);
        $documentDirectoryTest = new CheckNumberObjectsArkivutrekk(Constants::TEST_COUNT_MAPPE_ARKIVUTTREKK,
            $numberOfDocumentsReportedInArkivUttrekk,
            $this->statistics->getNumberOfFileProcessed(),
            Constants::NAME_ARKIVSTRUKTUR_XML,
            'mappe', $testProperty);
        $documentDirectoryTest->runTest();
        $this->testResultsHandler->addResult($testProperty, Constants::TEST_TYPE_A7);
        print $testProperty . PHP_EOL;
    }

    /*
     * Test A8
     * Tester om antall elementer av type «mappe» stemmer overens med antall «mappe numerOfOccurrences»
     * i arkivuttrekk.xml – altså om antall mapper som blir med ut i uttrekket stemmer overens med faktisk
     * antall mapper i arkivdelen.
     *
     */
    public function testA8()
    {
        if ($this->statistics === null) {
            $this->parseArkivstruktur();
        }

        $extractionInfo = $this->arkivUtrekk->getExtractionInfo();
        $numberOfRegistrationReportedInArkivUttrekk = $extractionInfo->getNumberOfRegistration();

        $testProperty = new TestProperty(Constants::TEST_COUNT_MAPPE_ARKIVUTTREKK);
        $documentDirectoryTest = new CheckNumberObjectsArkivutrekk(Constants::TEST_COUNT_MAPPE_ARKIVUTTREKK,
            $numberOfRegistrationReportedInArkivUttrekk,
            $this->statistics->getNumberOfFileProcessed(),
            Constants::NAME_ARKIVSTRUKTUR_XML,
            'registrering', $testProperty);
        $documentDirectoryTest->runTest();
        $this->testResultsHandler->addResult($testProperty, Constants::TEST_TYPE_A8);
        print $testProperty . PHP_EOL;
    }


    /*
     * testA9: Calculate and check the checksum value of arkivuttrekk.xml against the value specified in info.xml
     */
    public function testA9()
    {

        $checksumValue = $this->infoFileDetails->getChecksumInfo()->getChecksumValue();
        $checksumAlgorithm = $this->infoFileDetails->getChecksumInfo()->getChecksumAlgorithm();

        $testProperty = new FileChecksumTestProperty(Constants::TEST_CHECKSUM);
        $checksumTest = new ChecksumTest(Constants::TEST_CHECKSUM, Constants::NAME_ARKIVUTTREKK_XML, $this->directory, $checksumAlgorithm, $checksumValue, $testProperty);
        $checksumTest->runTest();
        if ($testProperty->getResult() == true) {
            $this->logger->info('The checksum for arkivuttrekk.xml, specified in info.xml has been checked and found to be correct');
        }
        else {
            $this->logger->error('The checksum for arkivuttrekk.xml, specified in info.xml has been checked and found to be incorrect!. See logfile for details');
        }
        $this->testResultsHandler->addResult($testProperty, Constants::TEST_TYPE_A9);

        print $testProperty . PHP_EOL;
        $checksumTest = null;
    }

    /*
     * test10 : Test if number of documents specified in arkivuttrekk.xml is correct with count of
     *        documents in the dokumenter folder
     *
     * Note: $this->preTestProcessing must be run first!
     */
    public function testA10()
    {

        $testProperty = new TestProperty(Constants::TEST_COUNT_DOCUMENTS);
        $extractionInfo = $this->arkivUtrekk->getExtractionInfo();

        if ($this->numberOfDocumentsInDirectory != -1 && ($this->numberOfDocumentsInDirectory == $extractionInfo->getAntallDokumentfiler())) {
            $testProperty->addTestResult(true);
            $testProperty->addTestResultDescription('Number of documents identified in ' . Constants::NAME_ARKIVUTTREKK_XML . 'is correct. Number identified is ' . $extractionInfo->getAntallDokumentfiler());
        }
        else {
            $this->testProperty->addTestResult(false);
            $this->testProperty->addTestResultDescription('Number of documents identified in ' . Constants::NAME_ARKIVUTTREKK_XML . 'is in correct. Number identified is '
                . $extractionInfo->getAntallDokumentfiler() . ' while number of documents found is ' .  $this->numberOfDocumentsInDirectory);
        }

        $this->testResultsHandler->addResult($testProperty, Constants::TEST_TYPE_A6);
        print $testProperty . PHP_EOL;
    }


    /*
     * Test C1
     *
     *
     */
    public function testC1()
    {
        $testProperty = new TestProperty(Constants::TEST_CHECKSUM_ALL_DOCUMENTS);
        try {
            $allDocumentChecksumTest = new Test(Constants::TEST_CHECKSUM_ALL_DOCUMENTS, $testProperty);

            // We don't call $allDocumentChecksumTest->runTest because the test is handled
            // in ArkivstrukturDocumentChecksumTest durnig parsing of the file
            $akivstrukturParser = new AllIncomingRegistryEntrySignedOff();
            $this->parseFile($akivstrukturParser, $this->arkivstrukturFilename);

            if ($akivstrukturParser->getErrorsEncountered() == true) {
                $testProperty->addTestResultReportDescription('Det var funnet feil med sjeksumm av filer i dokument mappen. Antall feil funnet er ' .
                    $akivstrukturParser->getNumberErrorsEncountered() . ' logfilen inneholder en liste av filer som ble sjekket');
                $this->testProperty->setTestResult(false);
            }
            $this->testResultsHandler->addResult($allDocumentChecksumTest, Constants::TEST_TYPE_A2);
        }
        catch (Exception $e) {
            $this->logger->error("Error when attempting test " . $testProperty->getDescription() . ". The following exception occured " . $e);
        }
    }




    protected function documentListOverview()
    {
        $this->documentListHandler = new DocumentListHandler($this->directory);

        $this->dokumenterDirectory = join(DIRECTORY_SEPARATOR, array(
            $this->directory,
            'dokumenter'
        ));
        $this->logger->info('Building up a list of files in dokumenter folder. Dir is (' . $this->dokumenterDirectory . ') ');

        $dokumenterFolderHandler = new DokumenterFolderHandler($this->dokumenterDirectory, Constants::FILE_PROCESSING_MAX_RECURSIVE_DEPTH, $this->documentListHandler);
        $dokumenterFolderHandler->process();
        $this->numberOfDocumentsInDirectory = $dokumenterFolderHandler->getNumberOfFiles();
        $numberOfUniqueDocumentsInDirectory = $dokumenterFolderHandler->getNumberOfUniqueFiles();

        $this->logger->info('Number of documents found ' . $this->numberOfDocumentsInDirectory);

        if ($numberOfUniqueDocumentsInDirectory != $this->numberOfDocumentsInDirectory) {
            $this->logger->info('Number of unique documents in document directory is ' . $numberOfUniqueDocumentsInDirectory);
            $this->logger->info('Number of documents in document directory is ' . $this->numberOfDocumentsInDirectory);
            $this->logger->info('Potential duplicate of documents that may cause trouble. Warning!');
        }
    }


    /**
     * This function is provided as a little cheat. If you need to run single test e.g A5, where
     * you need to parse Arkivstruktur to get a value, then you can just call this to parse it.
     *
     */
    protected function parseArkivstruktur() {
        parseFile(new AkivstrukturParser(), $this->arkivstrukturFilename);
    }
    /**
     *
     * @param unknown $parserElementController
     * @param unknown $filename
     */

    protected function parseFile($parserElementController, $filename) {
        $parser = xml_parser_create('UTF-8');

        xml_set_object($parser, $parserElementController);
        // XML_OPTION_CASE_FOLDING Do not fold element names to upper case
        xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, false);

        // set function names to handle start / end tags of elements and to parse content between tags
        xml_set_element_handler($parser, "startElement", "endElement");
        xml_set_character_data_handler($parser, "cdata");

        $xmlFile = fopen(join(DIRECTORY_SEPARATOR, array(
            $this->directory,
            $filename
        )), 'r');

        while ($data = fread($xmlFile, 4096)) {
            xml_parse($parser, $data, feof($xmlFile));
            flush();
        }
        fclose($xmlFile);
        xml_parser_free($parser);


        if ($statistics === null && get_parent_class($parserElementController) === 'ArkivstrukturParser' ) {
            $statistics = $parserElementController->getStatistics();
        }
    }

}

?>