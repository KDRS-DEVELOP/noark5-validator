<?php

/*
 * This is a pretty straight forward implementation of callback methods
 * for the SAX based parser created by the command xml_parser_create()
 *
 * At a higher level, there are three important functions startElement,
 * endElement and cdata. startElement is called every time a new element
 * is detected. We distinguish between the noark 5 complexTypes (arkiv, arkivdel)
 * etc and simpleTypes (title, systemID) etc. The complexTypes are listed first and
 * are pushed to a stack ($stack) to keep track of where we are in the XML-file.
 * endElement pops the stack and there is a check on type to ensure we are in sync
 * with the file.
 *
 * Only a few of the functions are documented in detail.
 *
 * The methods in this class are as follows. First we have the startElement, endElement and cdata
 * for parsing, followed by checkObjectClassTypeCorrect. Then all the handle methods for the various
 * simpletypes, followed by a list of pre and post process methods that can be overridden and finally
 * the getter and setters for the counts. If you want to build a test on top of this parser, you should
 * probably be overriding a post processor of a Noark 5 complex type and or one of the handle methods.
 *
 * NOTE: You will not be able to access all the simpleElements (systemId) in a noark 5 complexType (arkiv
 * arkivdel etc) until the post process event has occured. A preprocess method is invoked when the start tag
 * is seen, a postprocess when the closing tag in encountered.
 *
 * NOTE: Only values specified belonging to arkivstruktur.xml validated by arkivstruktur.xsd will be found
 *       here. Other values in the metadatacatlog are not included. e.g M580, brukerNavn
 *       The same applies to fields belonging to endringslogg.xml etc
 *
 * NTE: Check that all new ArrayCollection() aer actually initiaslsed;
 */
class ArkivstrukturParser
{

    /**
     *
     * @var array $stack: Stack that holds parsed Noark 5 objects (complexTypes, simpleTypes are object variables)
     */
    protected $stack;

    /**
     *
     * @var string $currentCdata: Stack that holds parsed Noark 5 objects (complexTypes, simpleTypes are object variables)
     */
    protected $currentCdata;

    /**
     *
     * @var int $numberOfFondsProcessed: The number of Fonds <arkiv> elements that are processed
     */
    protected $numberOfFondsProcessed = 0;

    /**
     *
     * @var int $numberOfFondsCreatorProcessed: The number of FondsCreator <arkivskaper> elements that are processed
     */
    protected $numberOfFondsCreatorProcessed = 0;

    /**
     *
     * @var int $numberOfSeriesProcessed: The number of Series, <arkivdel> elements that are processed
     */
    protected $numberOfSeriesProcessed = 0;

    /**
     *
     * @var int $numberOfClassificationSystemProcessed: The number of ClassificationSystem, <klassifikasjonssystem,> elements that are processed
     */
    protected $numberOfClassificationSystemProcessed = 0;

    /**
     *
     * @var int $numberOfClassProcessed: The number of Class, <klasse> elements that are processed
     */
    protected $numberOfClassProcessed = 0;

    /**
     *
     * @var int $numberOfFileProcessed: The number of File, <mappe> elements that are processed
     */
    protected $numberOfFileProcessed = 0;

    /**
     *
     * @var int $numberOfCaseFileProcessed: The number of CaseFile, <saksmappe> elements that are processed
     */
    protected $numberOfCaseFileProcessed = 0;

    /**
     *
     * @var int $numberOfMeetingFileProcessed: The number of MeetingFile, <moetemappe> elements that are processed
     */
    protected $numberOfMeetingFileProcessed = 0;

    /**
     *
     * @var int $numberOfRecordProcessed: The number of Record, <registrering> elements that are processed
     */
    protected $numberOfRecordProcessed = 0;

    /**
     *
     * @var int $numberOfBasicRecordProcessed: The number of BasicRecord, <basisregistrering> elements that are processed
     */
    protected $numberOfBasicRecordProcessed = 0;

    /**
     *
     * @var int $numberOfRegistryEntryProcessed: The number of RegistryEntry, <journalpost> elements that are processed
     */
    protected $numberOfRegistryEntryProcessed = 0;

    /**
     *
     * @var int $numberOfMeetingRecordProcessed: The number of MeetingRecord, <moeteregistrering> elements that are processed
     */
    protected $numberOfMeetingRecordProcessed = 0;

    /**
     *
     * @var int $numberOfDocumentDescriptionProcessed: The number of DocumentDescription, <dokumentbeskrivelse> elements that are processed
     */
    protected $numberOfDocumentDescriptionProcessed = 0;

    /**
     *
     * @var int $numberOfDocumentObjectProcessed: The number of DocumentObject, <documentobjekt> elements that are processed
     */
    protected $numberOfDocumentObjectProcessed = 0;

    /**
     *
     * @var int $numberOfSignOffProcessed: The number of SignOff, <avskrivning> elements that are processed
     */
    protected $numberOfSignOffProcessed = 0;

    /**
     *
     * @var int $numberOfCorrespondancePartProcessed: The number of CorrespondancePart, <korrespondansepart> elements that are processed
     */
    protected $numberOfCorrespondancePartProcessed = 0;

    /**
     *
     * @var int $numberOfClassificationProcessed: The number of Classification, <gradering> elements that are processed
     */
    protected $numberOfClassificationProcessed = 0;

    /**
     *
     * @var int $numberOfDeletionProcessed: The number of Deletion, <sletting> elements that are processed
     */
    protected $numberOfDeletionProcessed = 0;

    /**
     *
     * @var int $numberOfDisposalProcessed: The number of Disposal, <kassasjon> elements that are processed
     */
    protected $numberOfDisposalProcessed = 0;

    /**
     *
     * @var int $numberOfDisposalUndertakenProcessed: The number of DisposalUndertaken, <utfoertKassasjon> elements that are processed
     */
    protected $numberOfDisposalUndertakenProcessed = 0;

    /**
     *
     * @var int $numberOfPrecedenceProcessed: The number of Precedence, <presedens> elements that are processed
     */
    protected $numberOfPrecedenceProcessed = 0;

    /**
     *
     * @var int $numberOfCrossReferenceProcessed: The number of CrossReference, <kryssreferanse> elements that are processed
     */
    protected $numberOfCrossReferenceProcessed = 0;

    /**
     *
     * @var int $numberOfElectronicSignatureProcessed: The number of ElectronicSignature, <elektroniskSignatur> elements that are processed
     */
    protected $numberOfElectronicSignatureProcessed = 0;

    /**
     *
     * @var int $numberOfScreeningProcessed: The number of Screening, <skjerming> elements that are processed
     */
    protected $numberOfScreeningProcessed = 0;

    /**
     *
     * @var int $numberOfCommentProcessed: The number of Comment, <merknad> elements that are processed
     */
    protected $numberOfCommentProcessed = 0;

    /**
     *
     * @var int $numberOfConversionProcessed: The number of Conversion, <konvertering> elements that are processed
     */
    protected $numberOfConversionProcessed = 0;

    /**
     *
     * @var int $numberOfCasePartyProcessed: The number of CaseParty, <sakspart> elements that are processed
     */
    protected $numberOfCasePartyProcessed = 0;

    /**
     *
     * @var int $numberOfWorkflowProcessed: The number of Workflow, <dokumentflyt> elements that are processed
     */
     protected $numberOfWorkflowProcessed = 0;

    /**
     *
     * @var boolean $graderingIsSimpleType:  arkivstruktur.xsd has <gradering> both as a complexType
                                             and as a simpleType. This makes handling both <gradering>
                                             a bit more difficult as I have to keep track of whether
                                             or not the current <gradering> is the simpleType.
     */
    protected $graderingIsSimpleType = false;
    /**
     *
     * @var $logger: The Log4Php logger object
     */
    protected $logger;

    function __construct()
    {
        $this->stack = array();
        $this->currentCdata = "";
        $this->onlyParse = $onlyParse;
        Logger::configure('../resources/logging/log4php.xml');
        $this->logger = Logger::getLogger(basename(__FILE__));
        $this->logger->trace('Constructing an instance of ' . get_class($this));
    }

    /**
     * startElement is called whenever the parser encounters a opening tag.
     * Here
     * we are only interested in handling the opening tag of a Noark 5 complexType.
     * This is done to create an instance of a Noark 5 object (no:arkivenhet, e.g arkiv)
     * that is added to the stack so that the subsequent simpleType (e.g systemId) elements
     * that are found can be set to the object (that is at the top of the queue).
     * This function provides a handle method (e.g. preProcessFonds) for subclasses to call
     * when processing a Noark 5 arkivstruktur.xml file.
     *
     * @param xml_parser $parser
     *            Link to parser, variable is not used
     *
     * @param string $tag
     *            The actual tag that has been encountered
     *
     * @param array $attributes
     *            An array of attributes contained within the element
     *
     */
    function startElement($parser, $tag, $attributes)
    {
        $this->logger->trace('Processing startElement ' . $tag);
        switch ($tag) {
            case 'arkiv':
                $this->stack[] = new Fonds();
                $this->preProcessFonds();
                break;
            case 'arkivskaper':
                $this->stack[] = new FondsCreator();
                $this->preProcessFondsCreator();
                break;
            case 'arkivdel':
                $this->stack[] = new Series();
                $this->preProcessSeries();
                break;
            case 'klassifikasjonssystem':
                $this->stack[] = new ClassificationSystem();
                $this->preProcessClassificationSystem();
                break;
            case 'klasse':
                $this->stack[] = new Klass();
                $this->preProcessClass();
                break;
            case 'mappe':
                $classType = 'File';
                if (count($attributes) > 0) {
                    if (isset($attributes['xsi:type']) == true) {
                        if (strcmp($attributes['xsi:type'], 'saksmappe') == 0) {
                            $this->stack[] = new CaseFile();
                            $classType = 'CaseFile';
                        } elseif (strcmp($attributes['xsi:type'], 'moetemappe') == 0) {
                            $this->stack[] = new MeetingFile();
                            $classType = 'MeetingFile';
                            $this->preProcessMeetingFile();
                        } else {

$this->logger->error(Constants::EXCEPTION_UNKNOWN_NOARK5_OBJECT . ' Cannot handle mappe xsi:type = ' . $attributes['xsi:type']);
                            throw new Exception(Constants::EXCEPTION_UNKNOWN_NOARK5_OBJECT . ' Cannot handle mappe xsi:type = ' . $attributes['xsi:type']);
                        }
                    }
                } else {
                    $this->stack[] = new File();
                }
                $this->preProcessFile($classType);
                break;
            case 'registrering':
                $classType = 'Record';
                if (count($attributes) > 0) {
                    if (isset($attributes['xsi:type']) == true) {
                        if (strcmp($attributes['xsi:type'], 'basisregistrering') == 0) {
                            $this->stack[] = new BasicRecord();
                            $classType = 'BasicRecord';
                        } elseif (strcmp($attributes['xsi:type'], 'journalpost') == 0) {
                            $this->stack[] = new RegistryEntry();
                            $classType = 'RegistryEntry';
                        } elseif (strcmp($attributes['xsi:type'], 'moeteregistrering') == 0) {
                            $this->stack[] = new MeetingRecord();
                            $classType = 'MeetingRecord';
                            $this->preProcessMeetingRecord();
                        } else {

$this->logger->error(Constants::EXCEPTION_UNKNOWN_NOARK5_OBJECT . ' Cannot handle registrering xsi:type = ' . $attributes['xsi:type']);
                            throw new Exception(Constants::EXCEPTION_UNKNOWN_NOARK5_OBJECT . ' Cannot handle registrering xsi:type = ' . $attributes['xsi:type']);
                        }
                    }
                } else {
                    $this->stack[] = new Record();
                }
                $this->preProcessRecord($classType);
                break;
            case 'korrespondansepart':
                $this->stack[] = new CorrespondencePart();
                $this->preProcessCorrespondencePart();
                break;
            case 'avskrivning':
                $this->stack[] = new SignOff();
                $this->preProcessSignOff();
                break;
            case 'dokumentflyt':
                $this->stack[] = new Workflow();
                $this->preProcessWorkflow();
                break;
            case 'presedens':
                $this->stack[] = new Precedence();
                $this->preProcessPrecedence();
                break;
            case 'elektroniskSignatur':
                $this->stack[] = new ElectronicSignature();
                $this->preProcessElectronicSignature();
                break;
            case 'dokumentbeskrivelse':
                $this->stack[] = new DocumentDescription();
                $this->preProcessDocumentDescription();
                break;
            case 'dokumentobjekt':
                $this->stack[] = new DocumentObject();
                $this->preProcessDocumentObject();
                break;
            case 'elektroniskSignatur':
                $this->stack[] = new ElectornicSignature();
                $this->preProcessElectornicSignature();
                break;
            case 'gradering':
                /**
                 * NOTE: arkivstruktur.xsd has <gradering> both as a complexType
                 * and as a simpleType. This makes handling the element a little
                 * more complex in an event-based parser. Here we check to see if
                 * the head of the stack has an object of type gradering and if it
                 * does then this <gradering> element is ignored.
                 * $this->graderingIsSimpleType is set to true so that we know
                 * the endElement will be processed accordingly.
                 */
                if (get_class(end($this->stack)) !== "gradering") {
                    $this->graderingIsSimpleType = true;
                    $this->stack[] = new Classified();
                    $this->preProcessClassfied();
                }
                break;
            case 'kassasjon':
                $this->stack[] = new Disposal();
                $this->preProcessDisposal();
                break;
            case 'konvertering':
                $this->stack[] = new Conversion();
                $this->preProcessConversion();
                break;
            case 'kryssreferanse':
                $this->stack[] = new CrossReference();
                $this->preProcessCrossReference();
                break;
            case 'merknad':
                $this->stack[] = new Comment();
                $this->preProcessComment();
                break;
            case 'presedens':
                $this->stack[] = new Precedence();
                $this->preProcessPrecedence();
                break;
            case 'skjerming':
                $this->stack[] = new Screening();
                $this->preProcessScreening();
                break;
            case 'sletting':
                $this->stack[] = new Deletion();
                $this->preProcessDeletion();
                break;
            case 'utfoertKassasjon':
                $this->stack[] = new DisposalUndertaken();
                $this->preProcessDisposalUndertaken();
                break;
            case 'sakspart':
                $this->stack[] = new CaseParty();
                $this->preProcessCaseParty();
                break;

        }
    }

    /**
     * endElement is called whenever the parser encounters a closing tag.
     * Here
     * we are interested in handling both the closing tag of a Noark 5 complexType as well
     * as that of simpleType.
     * A simple check that the head of the stack is in sync with the xml file is undertaken
     * as well as calling a postProcess() function for the complexTypes and a handle function
     * for the simpleTypes. The handle functions copy the value in currentCdata to the appropriate
     * variable in the Noark 5 object. $this->currentCdata gets its value from the cdata function
     *
     * These handle and postProcess functions are very useful when creating a subclass.
     *
     * Note: Only when you have processed the end tag, will you actually have a complete instance
     * of a Noark 5 object (no:arkivenhet)
     *
     * Note: It is important that this function resets currentCdata to an empty value, "". Otherwise
     * you will have problems with your element values. This is done in the last statement of
     * this function.
     *
     * @param xml_parser $parser
     *            Link to parser, not used
     *
     * @param string $tag
     *            The actual tag that has been encountered
     *
     */
    function endElement($parser, $tag)
    {
        $this->logger->trace('Processing endElement ' . $tag);
        switch ($tag) {
            case 'arkiv':
                $this->checkObjectClassTypeCorrect('Fonds');
                $this->postProcessFonds();
                $this->numberOfFondsProcessed++;
                array_pop($this->stack);
                break;
            case 'arkivdel':
                $this->checkObjectClassTypeCorrect('Series');
                $this->postProcessSeries();
                $this->numberOfSeriesProcessed++;
                array_pop($this->stack);
                break;
            case 'mappe':

                $classType = get_class(end($this->stack));

                if (strcasecmp($classType, 'CaseFile') == 0) {
                    $this->checkObjectClassTypeCorrect('CaseFile');
                    $this->numberOfFileProcessed++;
                } elseif (strcasecmp($classType, 'File') == 0) {
                    $this->checkObjectClassTypeCorrect('File');
                    $this->numberOfCaseFileProcessed++;
                } elseif (strcasecmp($classType, 'MeetingFile') == 0) {
                    $this->checkObjectClassTypeCorrect('MeetingFile');
                    $this->numberOfMeetingFileProcessed++;
                } else {
                    $this->logger->error('Unable to process a specific mappe type. Type identified as (' . $classType . ')');
                    throw new Exception('Unable to process a specific mappe type. Type identified as (' . $classType . ')');
                }
                $this->postProcessFile($classType);
                array_pop($this->stack);
                break;
            case 'registrering':
                $classType = get_class(end($this->stack));

                if (strcasecmp($classType, 'Record') == 0) {
                    $this->checkObjectClassTypeCorrect('Record');
                    $this->numberOfRecordProcessed++;
                } elseif (strcasecmp($classType, 'BasicRecord') == 0) {
                    $this->checkObjectClassTypeCorrect('BasicRecord');
                    $this->numberOfBasicRecordProcessed++;
                } elseif (strcasecmp($classType, 'RegistryEntry') == 0) {
                    $this->checkObjectClassTypeCorrect('RegistryEntry');
                    $this->numberOfRegistryEntryProcessed++;
                } elseif (strcasecmp($classType, 'MeetingRecord') == 0) {
                    $this->checkObjectClassTypeCorrect('MeetingRecord');
                    $this->numberOfMeetingRecordProcessed++;
                } else {
                    $this->logger->error('Unable to process a specific registrering type. Type identified as (' . $classType . ')');
                    throw new Exception('Unable to process a specific registrering type. Type identified as (' . $classType . ')');
                }
                $this->postProcessRecord($classType);
                array_pop($this->stack);
                break;
            case 'korrespondansepart':
                $this->checkObjectClassTypeCorrect('CorrespondencePart');
                $this->numberOfCorrespondencePartProcessed++;
                $this->postProcessCorrespondencePart();
                array_pop($this->stack);
                break;
            case 'avskrivning':
                $this->checkObjectClassTypeCorrect('SignOff');
                $this->postProcessSignOff();
                $this->numberOfSignOffProcessed++;
                array_pop($this->stack);
                break;
            case 'dokumentflyt':
                break;
            case 'presedens':
                $this->checkObjectClassTypeCorrect('Precedence');
                $this->postProcessPrecedence();
                $this->numberOfPrecedenceProcessed++;
                array_pop($this->stack);
                break;
            case 'elektroniskSignatur':
                break;
            case 'dokumentbeskrivelse':
                $this->checkObjectClassTypeCorrect('DocumentDescription');
                $this->postProcessDocumentDescription();
                $this->numberOfDocumentDescriptionProcessed++;
                array_pop($this->stack);
                break;
            case 'dokumentobjekt':
                $this->checkObjectClassTypeCorrect('DocumentObject');
                $this->numberOfDocumentObjectProcessed++;
                $this->postProcessDocumentObject();
                array_pop($this->stack);
                break;
            case 'arkivskaper':
                $this->checkObjectClassTypeCorrect('FondsCreator');
                $this->postProcessFondsCreator();
                $this->numberOfFondsCreatorProcessed++;
                array_pop($this->stack);
                break;
            case 'gradering':
                /**
                 * NOTE: arkivstruktur.xsd has <gradering> both as a complexType
                 * and as a simpleType. This makes handling the element a little
                 * more complex in an event-based parser.
                 *
                 * There are potential two ways of dealing with this. First, check
                 * to see if $this->currentCdata is empty or not. If it is, then
                 * this is most likely the complexType. However, I think
                 * <gradering></gradering> would be misinterpreted in this
                 * situation. So I have decided to set a boolean flag when we detect
                 * <gradering> as a simpleType, then we know whether this closing
                 * element is a simpleType or a complexType
                 */

                if ($this->graderingIsSimpleType === true) {
                    $this->handleClassification();
                    $this->graderingIsSimpleType = false;
                }
                else {

                    $this->checkObjectClassTypeCorrect('Gradering');
                    $this->postProcessClassfication();
                    $this->numberOfClassificationProcessed++;
                    array_pop($this->stack);
                }
                break;

            case 'klasse':
                $this->checkObjectClassTypeCorrect('Klass');
                $this->postProcessClass();
                $this->numberOfClassProcessed++;
                array_pop($this->stack);
                break;
            case 'klassifikasjonssystem':
                $this->checkObjectClassTypeCorrect('ClassificationSystem');
                $this->postProcessClassificationSystem();
                $this->numberOfClassificationSystemProcessed++;
                array_pop($this->stack);
                break;
            case 'kryssreferanse':
                $this->checkObjectClassTypeCorrect('CrossReference');
                $this->postProcessCrossReference();
                $this->numberOfCrossReferenceProcessed++;
                array_pop($this->stack);
                break;
            case 'sletting':
                $this->checkObjectClassTypeCorrect('Deletion');
                $this->postProcessDeletion();
                $this->numberOfDeletionProcessed++;
                array_pop($this->stack);
                break;
            case 'kassasjon':
                $this->checkObjectClassTypeCorrect('Disposal');
                $this->postProcessDisposal();
                $this->numberOfDisposalProcessed++;
                array_pop($this->stack);
                break;
            case 'utfoertKassasjon':
                $this->checkObjectClassTypeCorrect('DisposalUndertaken');
                $this->postProcessDisposal();
                $this->numberOfDisposalUndertakenProcessed++;
                array_pop($this->stack);
                break;
            case 'sakspart':
                $this->checkObjectClassTypeCorrect('DisposalUndertaken');
                $this->postProcessDisposal();
                $this->numberOfCasePartyProcessed++;
                array_pop($this->stack);
                break;
            case 'elektroniskSignatur':
                $this->checkObjectClassTypeCorrect('DisposalUndertaken');
                $this->postProcessDisposal();
                $this->numberOfElectronicSignatureProcessed++;
                array_pop($this->stack);
                break;
            case 'skjerming':
                $this->checkObjectClassTypeCorrect('Screening');
                $this->postProcessScreening();
                $this->numberOfScreeningProcessed++;
                array_pop($this->stack);
                break;
            case 'merknad':
                $this->checkObjectClassTypeCorrect('Comment');
                $this->postProcessComment();
                $this->numberOfCommentProcessed++;
                array_pop($this->stack);
                break;
           case 'konvertering':
                $this->checkObjectClassTypeCorrect('Conversion');
                $this->postProcessComment();
                $this->numberOfConversionProcessed++;
                array_pop($this->stack);
                break;
            case 'dokumentflyt':
                $this->checkObjectClassTypeCorrect('Workflow');
                $this->postProcessWorkflow();
                $this->numberOfWorkflowProcessed++;
                array_pop($this->stack);
                break;
            // The rest of the elements are elements that simpleTypes and
            // within one of the complexTypes above
            case 'administrativEnhet':
                $this->handleAdministrativeUnit();
                break;
            case 'antallVedlegg':
                $this->handleNumberOfAttachments();
                break;
            case 'arkivdelstatus':
                $this->handleSeriesStatus();
                break;
            case 'arkivskaperID':
                $this->handleFondsCreatorID();
                break;
            case 'arkivskaperNavn':
                $this->handleFondsCreatorName();
                break;
            case 'arkivstatus':
                $this->handleFondsStatus();
                break;
            case 'avskrevetAv':
                $this->handleSignOffBy();
                break;
            case 'avskrivningsdato':
                $this->handleSignOffDate();
                break;
            case 'avskrivningsmaate':
                $this->handleSignOffMethod();
                break;
            case 'avsluttetAv':
                $this->handleFinalisedBy();
                break;
            case 'avsluttetDato':
                $this->handleFinalisedDate();
                break;
            case 'arkivertAv':
                $this->handleArchivedBy();
                break;
            case 'arkivertDato':
                $this->handleArchivedDate();
                break;
            case 'arkivperiodeStartDato':
                $this->handleSeriesStartDate();
                break;
            case 'arkivperiodeSluttDato':
                $this->handleSeriesEndDate();
                break;
            case 'beskrivelse':
                $this->handleDescription();
                break;
            case 'bevaringstid':
                $this->handlePreservationTime();
                break;
            case 'brukerNavn':
                $this->handleUsername();
                break;
            case 'dokumentetsDato':
                $this->handleDocumentDate();
                break;
            case 'dokumentmedium':
                $this->handleDocumentMedium();
                break;
            case 'dokumentnummer':
                $this->handleDocumentNumber();
                break;
            case 'dokumentstatus':
                $this->handleDocumentStatus();
                break;
            case 'dokumenttype':
                $this->handleDocumentType();
                break;
            case 'elektroniskSignaturSikkerhetsnivaa':
                $this->handleElectronicSignatureSecurityLevel();
                break;
            case 'epostadresse':
                $this->handleEmailAddress();
                break;
            case 'filstoerrelse':
                $this->handleFileSize();
                break;
            case 'flytFra':
                $this->handleWorkflowFrom();
                break;
            case 'flytTil':
                $this->handleWorkflowTo();
                break;
            case 'flytMottattDato':
                $this->handleWorkflowReceivedDate();
                break;
            case 'flytSendtDato':
                $this->handleWorkflowSentDate();
                break;
            case 'flytStatus':
                $this->handleWorkflowStatus();
                break;
            case 'flytMerknad':
                $this->handleWorkflowComment();
                break;
            case 'forfallsdato':
                $this->handleDueDate();
                break;
            case 'forfatter':
                $this->handleAuthor();
                break;
            case 'format':
                $this->handleFormat();
                break;
            case 'formatDetaljer':
                $this->handleFormatDetails();
                break;
            case 'gradertAv':
                $this->handleClassificationBy();
                break;
            case 'graderingsdato':
                $this->handleClassificationDate();
                break;
            case 'journalaar':
                $this->handleRecordYear();
                break;
            case 'journaldato':
                $this->handleRecordDate();
                break;
            case 'journalenhet':
                $this->handleRecordsManagementUnit();
                break;
            case 'journalpostnummer':
                $this->handleRecordNumber();
                break;
            case 'journalposttype':
                $this->handleRecordType();
                break;
            case 'journalsekvensnummer':
                $this->handleRecordSequenceNumber();
                break;
            case 'journalstatus':
                $this->handleRecordStatus();
                break;
            case 'journalStartDato':
                $this->handleRecordStartDate();
                break;
            case 'kassasjonsdato':
                $this->handleDisposalDate();
                break;
            case 'kassasjonshjemmel':
                $this->handleDisposalAuthority();
                break;
            case 'kassasjonsvedtak':
                $this->handleDisposalDecision();
                break;
            case 'kassertAv':
                $this->handleDisposalUndertakenBy();
                break;
            case 'kassertDato':
                $this->handleDisposalUndertakenDate();
                break;
            case 'klasseID':
                $this->handleClassId();
                break;
            case 'klassifikasjonstype':
                $this->handleClassificationType();
                break;
            case 'kontaktperson':
                $this->handleContactPerson();
                break;
            case 'konverteringsverktoey':
                $this->handleConversionTool();
                break;
            case 'konverteringskommentar':
                $this->handleConversionDate();
                break;
            case 'konvertertAv':
                $this->handleConvertedBy();
                break;
            case 'konvertertDato':
                $this->handleConvertedDate();
                break;
            case 'konvertertFraFormat':
                $this->handleConvertedFromFormat();
                break;
            case 'konvertertTilFormat':
                $this->handleConvertedToFormat();
                break;
            case 'korrespondansepartNavn':
                $this->handleCorrespondencePartName();
                break;
            case 'korrespondanseparttype':
                $this->handleCorrespondencePartType();
                break;
            case 'land':
                $this->handleCountry();
                break;
            case 'mappeID':
                $this->handleFileId();
                break;
            case 'merknadRegistrertAv':
                $this->handleCommentRegisteredBy();
                break;
            case 'merknadsdato':
                $this->handleCommentDate();
                break;
            case 'merknadstype':
                $this->handleCommentType();
                break;
            case 'merknadstekst':
                $this->handleCommentText();
                break;
            case 'moetedato':
                $this->handleMeetingDate();
                break;
            case 'moetedeltakerFunksjon':
                $this->handleMeetingParticipantFunction();
                break;
            case 'moetedeltakerNavn':
                $this->handleMeetingParticipantName();
                break;
            case 'moetenummer':
                $this->handleMeetingNumber();
                break;
            case 'moeteregistreringsstatus':
                $this->handleMeetingRecordStatus();
                break;
            case 'moeteregistreringstype':
                $this->handleMeetingRecordType();
                break;
            case 'moetesakstype':
                $this->handleMeetingCaseType();
                break;
            case 'moetested':
                $this->handleMeetingPlace();
                break;
            case 'mottattDato':
                $this->handleReceivedDate();
                break;
            case 'nedgradertAv':
                $this->handleClassificationDowngradedBy();
                break;
            case 'nedgraderingsdato':
                $this->handleClassificationDowngradedDate();
                break;
            case 'noekkelord':
                $this->handleKeyword();
                break;
            case 'offentlighetsvurdertDato':
                $this->handleReviewFOIDate();
                break;
            case 'offentligTittel':
                $this->handleOfficialTitle();
                break;
            case 'oppbevaringssted':
                $this->handleStorageLocation();
                break;
            case 'opprettetAv':
                $this->handleCreatedBy();
                break;
            case 'opprettetDato':
                $this->handleCreatedDate();
                break;
            case 'postadresse':
                $this->handlePostalAddress();
                break;
            case 'postnummer':
                $this->handlePostalNumber();
                break;
            case 'poststed':
                $this->handlePostalTown();
                break;
            case 'presedensDato':
                $this->handlePrecedenceDate();
                break;
            case 'presedensstatus':
                $this->handlePrecedenceStatus();
                break;
            case 'presedensHjemmel':
                $this->handlePrecedenceAuthority();
                break;
            case 'presedensGodkjentDato':
                $this->handlePrecedenceApprovedDate();
                break;
            case 'presedensGodkjentAv':
                $this->handlePrecedenceApprovedBy();
                break;
            case 'referanseArkivdel':
                $this->handleReferenceSeries();
                break;
            case 'referanseArvtaker':
                $this->handleReferenceSuccessor();
                break;
            case 'referanseAvskrivesAvJournalpost':
                $this->handleReferenceSignedOffByRegistryEntry();
                break;
            case 'referanseDokumentfil':
                $this->handleReferenceDocumentFile();
                break;
            case 'referanseForloeper':
                $this->handleReferencePrecursor();
                break;
            case 'referanseForrigeMoete':
                $this->handleReferencePreviousMeeting();
                break;
            case 'referanseNesteMoete':
                $this->handleReferenceNextMeeting();
                break;
            case 'referanseSekundaerKlassifikasjon':
                $this->handleSecondaryClassification();
                break;
            case 'referanseTilMoeteregistrering':
                $this->handleReferenceToMeetingRecord();
                break;
            case 'referanseTilKlasse':
                $this->handleReferenceToClass();
                break;
            case 'referanseTilMappe':
                $this->handleReferenceToFile();
                break;
            case 'referanseTilRegistrering':
                $this->handleReferenceToRecord();
                break;
            case 'registreringsID':
                $this->handleRecordId();
                break;
            case 'rettskildefaktor':
                $this->handleSourceOfLaw();
                break;
            case 'saksaar':
                $this->handleCaseYear();
                break;
            case 'saksansvarlig':
                $this->handleCaseResponsible();
                break;
            case 'saksbehandler':
                $this->handleCaseHandler();
                break;
            case 'saksdato':
                $this->handleCaseDate();
                break;
            case 'sakspartID':
                $this->handleCasePartyId();
                break;
            case 'sakspartNavn':
                $this->handleCasePartyName();
                break;
            case 'sakspartRolle':
                $this->handleCasePartyRole();
                break;
            case 'sakssekvensnummer':
                $this->handleCaseSequenceNumber();
                break;
            case 'seleksjon':
                $this->handleSelection();
                break;
            case 'saksstatus':
                $this->handleCaseStatus();
                break;
            case 'sendtDato':
                $this->handleSentDate();
                break;
            case 'sjekksum':
                $this->handleChecksum();
                break;
            case 'sjekksumAlgoritme':
                $this->handleChecksumAlgorithm();
                break;
            case 'skjermingshjemmel':
                $this->handleScreeningAuthority();
                break;
            case 'skjermingDokument':
                $this->handleScreeningDocument();
                break;
            case 'skjermingMetadata':
                $this->handleScreeningMetadata();
                break;
            case 'skjermingOpphoererDato':
                $this->handleScreeningCeasesDate();
                break;
            case 'skjermingsvarighet':
                $this->handleScreeningDuration();
                break;
            case 'slettetDato':
                $this->handleDeletionDate();
                break;
            case 'slettingstype':
                $this->handleDeletionType();
                break;
            case 'systemID':
                $this->handleSystemId();
                break;
            case 'telefonnummer':
                $this->handleTelephoneNumber();
                break;
            case 'tilgangsrestriksjon':
                $this->handleAccessRestriction();
                break;
            case 'tilknyttetAv':
                $this->handleAssociatedBy();
                break;
            case 'tilknyttetDato':
                $this->handleAssociationDate();
                break;
            case 'tilknyttetRegistreringSom':
                $this->handleAssociatedWithRecordAs();
                break;
            case 'tittel':
                $this->handleTitle();
                break;
            case 'utlaantDato':
                $this->handleLoanedDate();
                break;
            case 'utlaantTil':
                $this->handleLoanedTo();
                break;
            case 'utvalg':
                $this->handleCommittee();
                break;
            case 'variantformat':
                $this->handleVariantFormat();
                break;
            case 'versjonsnummer':
                $this->handleVersionNumber();
                break;
            case 'verifisertAv':
                $this->handleVerifiedBy();
                break;
            case 'verifisertDato':
                $this->handleVerifiedDate();
                break;

            default:
                $this->logger->error('Unknown Noark 5 tag ' . $tag . '. This has not been handled. This is a serious error');
        }

        $this->currentCdata = "";
    }

    public function cdata($parser, $cdata)
    {
        // If cdata is only whitespace just return
        if (! trim($cdata))
            return;
        $this->currentCdata .= $cdata;
    }

    /**
     * checkObjectClassTypeCorrect checks that the object at the head of the stack is an instance
     * of the correct type (class). It's s quick and dirty way to ensure that the xml file / stack
     * are being processed properly. It is possible that a subclass of this class repositions the
     * stack incorrectly. An exception is thrown if the stack isn't in sync as the code becomes
     * unpredicatable if this occurs.
     *
     * @param Noark5object $className
     * @return true if the object at the head of the stack is an instance of the class specified in $className
     */
    protected function checkObjectClassTypeCorrect($className)
    {
        if (strcmp($className, get_class(end($this->stack))) != 0) {
            $this->logger->error('Error processing arkivstruktur.xml. Unsafe to continue Expected (' . $className . ') found (' . get_class(end($this->stack)) . '). Unsafe processing.');
            throw new Exception('Error processing arkivstruktur.xml. Unsafe to continue Expected (' . $className . ') found (' . get_class(end($this->stack)) . '). Unsafe processing.');
        }
        return true;
    }

    /**
     * function handleAccessRestriction()
     * Can be used by : skjerming, gradering
     * n5mdk : M500 tilgangsrestriksjon
     */
    protected function handleAccessRestriction()
    {
        $object = end($this->stack);
        $object->setAccessRestriction($this->currentCdata);
    }

    /**
     * function handleAdministrativeUnit()
     * Can be used by : saksmappe, journalpost, moeteregistrering
     * n5mdk : M305 administrativEnhet
     */
    protected function handleAdministrativeUnit()
    {
        $object = end($this->stack);
        $object->setAdministrativeUnit($this->currentCdata);
    }

    /**
     * function handleArchivedBy()
     * Can be used by : Record
     * n5mdk : M605 arkivertAv
     */
    protected function handleArchivedBy()
    {
        $object = end($this->stack);
        $object->setArchivedBy($this->currentCdata);
    }

    /**
     * function handleArchivedDate()
     * Can be used by : registrering
     * n5mdk : M604 arkivertDato
     */
    protected function handleArchivedDate()
    {
        $object = end($this->stack);
        $object->setArchivedDate($this->currentCdata);
    }

    /**
     * function handleAssociatedBy()
     * Can be used by : dokumentbeskrivelse
     * n5mdk: M621 tilknyttetAv
     */
    protected function handleAssociatedBy()
    {
        $object = end($this->stack);
        $object->setAssociatedBy($this->currentCdata);
    }

    /**
     * function handleAssociatedWithRecordAs()
     * Can be used by : dokumentbeskrivelse
     * n5mdk : M217 tilknyttetRegistreringSom
     */
    protected function handleAssociatedWithRecordAs()
    {
        $object = end($this->stack);
        $object->setAssociatedWithRecordAs($this->currentCdata);
    }

    /**
     * function handleAssociationDate()
     * Can be used by : dokumentbeskrivelse
     * n5mdk: M620 tilknyttetDato
     */
    protected function handleAssociationDate()
    {
        $object = end($this->stack);
        $object->setAssociationDate($this->currentCdata);
    }

    /**
     * function handleAuthor()
     * Can be used by : dokumentbeskrivelse, basisregistrering
     * n5mdk : M024 forfatter
     */
    protected function handleAuthor()
    {
        $object = end($this->stack);
        $object->addAuthor($this->currentCdata);
    }

    /**
     * function handleCaseDate()
     * Can be used by : saksmappe
     * n5mdk : M100 saksdato
     */
    protected function handleCaseDate()
    {
        $object = end($this->stack);
        $object->setCaseDate($this->currentCdata);
    }

    /**
     * function handleCaseHandler()
     * Can be used by : journalpost, moeteregistrering
     * n5mdk : M307 saksbehandler
     */
    protected function handleCaseHandler()
    {
        $object = end($this->stack);
        $object->setCaseHandler($this->currentCdata);
    }

    /**
     * function handleCasePartyId()
     * Can be used by : sakspart
     * n5mdk : M010 sakspartID
     */
    protected function handleCasePartyId()
    {
        $object = end($this->stack);
        $object->setCasePartyId($this->currentCdata);
    }

    /**
     * function handleCasePartyName()
     * Can be used by : sakspart
     * n5mdk : M302 sakspartNavn
     */
    protected function handleCasePartyName()
    {
        $object = end($this->stack);
        $object->setCasePartyName($this->currentCdata);
    }

    /**
     * function handleCasePartyRole()
     * Can be used by : sakspart
     * n5mdk : M303 sakspartRolle
     */
    protected function handleCasePartyRole()
    {
        $object = end($this->stack);
        $object->setCasePartyRole($this->currentCdata);
    }

    /**
     * function handleCaseResponsible()
     * Can be used by : saksmappe
     * n5mdk : M306 saksansvarlig
     */
    protected function handleCaseResponsible()
    {
        $object = end($this->stack);
        $object->setCaseResponsible($this->currentCdata);
    }

    /**
     * function handleCaseSequenceNumber()
     * Can be used by : saksmappe
     * n5mdk : M012 sakssekvensnummer
     */
    protected function handleCaseSequenceNumber()
    {
        $object = end($this->stack);
        $object->setCaseSequenceNumber($this->currentCdata);
    }

    /**
     * function handleCaseStatus()
     * Can be used by : saksmappe
     * n5mdk : M052 saksstatus
     */
    protected function handleCaseStatus()
    {
        $object = end($this->stack);
        $object->setCaseStatus($this->currentCdata);
    }

    /**
     * function handleCaseYear()
     * Can be used by : saksmappe
     * n5mdk : M011 saksaar
     */
    protected function handleCaseYear()
    {
        $object = end($this->stack);
        $object->setCaseYear($this->currentCdata);
    }

    /**
     * function handleChecksum()
     * Can be used by : dokumentobjekt
     * n5mdk: M705 sjekksum
     */
    protected function handleChecksum()
    {
        $object = end($this->stack);
        $object->setChecksum($this->currentCdata);
    }

    /**
     * function handleChecksumAlgorithm()
     * Can be used by : dokumentobjekt
     *  n5mdk: M706 sjekksumAlgoritme
     */
    protected function handleChecksumAlgorithm()
    {
        $object = end($this->stack);
        $object->setChecksumAlgorithm($this->currentCdata);
    }

    /**
     * function handleClassId()
     * Can be used by : klasse
     * n5mdk: M002 klasseID
     */
    protected function handleClassId()
    {
        $object = end($this->stack);
        $object->setClassId($this->currentCdata);
    }

    /**
     * function handleClassification()
     * Can be used by : gradering
     * n5mdk: M506 gradering
     */
    protected function handleClassification()
    {
        $object = end($this->stack);
        $object->setClassification($this->currentCdata);
    }

    /**
     * function handleClassificationBy()
     * Can be used by : gradering
     * n5mdk: M625 gradertAv
     */
    protected function handleClassificationBy()
    {
        $object = end($this->stack);
        $object->setClassificationBy($this->currentCdata);
    }

    /**
     * function handleClassificationDate()
     * Can be used by : gradering
     * n5mdk: M624 graderingsdato
     */
    protected function handleClassificationDate()
    {
        $object = end($this->stack);
        $object->setClassificationDate($this->currentCdata);
    }

    /**
     * function handleClassificationDowngradedDate()
     * Can be used by : gradering
     * n5mdk: M626 nedgraderingsdato
     */
    protected function handleClassificationDowngradedDate()
    {
        $object = end($this->stack);
        $object->setClassificationDowngradedDate($this->currentCdata);
    }

    /**
     * function handleClassificationDowngradedBy()
     * Can be used by : gradering
     * n5mdk: M627 nedgradertAv
     */
    protected function handleClassificationDowngradedBy()
    {
        $object = end($this->stack);
        $object->setClassificationDowngradedBy($this->currentCdata);
    }

    /**
     * function handleClassificationType()
     * Can be used by : klassifikasjonssystem
     * n5mdk : M086 klassifikasjonstype
     */
    protected function handleClassificationType()
    {
        $object = end($this->stack);
        $object->setClassificationType($this->currentCdata);
    }

    /**
     * function handleCommentDate()
     * Can be used by : merknad
     * n5mdk : M611 merknadsdato
     */
    protected function handleCommentDate()
    {
        $object = end($this->stack);
        $object->setCommentText($this->currentCdata);
    }

    /**
     * function handleCommentRegisteredBy()
     * Can be used by : merknad
     * n5mdk : M612 merknadRegistrertAv
     */

    protected function handleCommentRegisteredBy()
    {
        $object = end($this->stack);
        $object->setCommentText($this->currentCdata);
    }

    /**
     * function handleCommentText()
     * Can be used by : merknad
     * n5mdk : M310 merknadstekst
     */
    protected function handleCommentText()
    {
        $object = end($this->stack);
        $object->setCommentText($this->currentCdata);
    }

    /**
     * function handleCommentType()
     * Can be used by : merknad
     * n5mdk : M084 merknadstype
     */
    protected function handleCommentType()
    {
        $object = end($this->stack);
        $object->setCommentType($this->currentCdata);
    }

    /**
     * function handleCommittee()
     * Can be used by : moetemappe
     * n5mdk : M370 utvalg
     */
    protected function handleCommittee() {
        $object = end($this->stack);
        $object->setCommittee($this->currentCdata);
    }

    /**
     * function handleContactPerson()
     * Can be used by : journalpost, sakspart
     * n5mdk : M412 kontaktperson
     */
    protected function handleContactPerson()
    {
        $object = end($this->stack);
        $object->setContactPerson($this->currentCdata);
    }

    /**
     * function handleConvertedBy()
     * Can be used by : konvertering
     * n5mdk : M616 konvertertAv
     */
    protected function handleConvertedBy()
    {
        $object = end($this->stack);
        $object->setConvertedBy($this->currentCdata);
    }

    /**
     * function handleConversionComment()
     * Can be used by : konvertering
     * n5mdk : M715 konverteringskommentar
     */
    protected function handleConversionComment()
    {
        $object = end($this->stack);
        $object->setConversionComment($this->currentCdata);
    }

    /**
     * function handleConvertedDate()
     * Can be used by : konvertering
     * n5mdk : M615 konvertertDato
     */
    protected function handleConvertedDate()
    {
        $object = end($this->stack);
        $object->setConvertedDate($this->currentCdata);
    }


    /**
     * function handleConvertedFromFormat()
     * Can be used by : konvertering
	 
?>
