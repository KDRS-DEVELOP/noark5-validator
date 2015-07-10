<?php
require_once ('handler/ArkivstrukturParser.php');

/*
 * This class uses ArkivstrukturParser and picks out every journalpost->recordType
 * and if the recordType is an incoming document, it adds it to a list of
 * $listOfRegistryEntryIncoming all incoming registryEntry.
 *
 * The parser continues and if when a signOff is encountered
 *
 */

class AllIncomingRegistryEntrySignedOff extends ArkivstrukturParser
{
    protected $listOfRegistryEntryIncoming = array();
    protected $listOfSignOffThatAreWrong = array();

    protected $processedRecord = null;

    public function __construct()
    {
        parent::__construct($directory, null, null, false);
    }

    public function handleRecordStatus() {
        parent::handleRecordStatus();
        $recordType = $this->currentRecord->getRecordType();

        if (isset($recordType) &&
                $recordType === Constants::REGISTRY_ENTRY_TYPE_INCOMING ) {
                    $listOfRegistryEntryIncoming[$this->currentRecord->getSystemId()] = $this->currentRecord;
        }
    }

    public function postProcessSignOff()
    {
        if ($this->checkObjectClassTypeCorrect("SignOff") == true) {
            $signOff = end($this->stack);

            // Signing off of the wrong type of record, this is an error! Log it and continue
            if ($this->currentRecord->getRecordType() !== Constants::REGISTRY_ENTRY_TYPE_INCOMING) {
                $this->logger->error('The following RegistryEntry has been signedOff, but it is not specified as Incoming (' .
                     Constants.REGISTRY_ENTRY_TYPE_INCOMING . '). The actual RegistryEntry is '. $this->currentRecord .
                    ' while the signOff is ' .  $signOff . ' This message is coming from ' . __METHOD__ );
            }

            if (isset($listOfRegistryEntryIncoming[$this->currentRecord->getSystemId()])) {
                unset($listOfRegistryEntryIncoming[$this->currentRecord->getSystemId()]);
            }
            else {
                $this->logger->error('Encountered a sigoff for the following RegistryEntry, but am not expecting this RegistryEntry. The actual RegistryEntry is '. $this->currentRecord .
                                        ' while the signOff is ' .  $signOff . ' This message is coming from ' . __METHOD__ );
            }

            $this->logger->trace('Post process SignOff. Method (' . __METHOD__ . ')' . $signOff);
        } else {
            throw new Exception(Constants::STACK_ERROR . __METHOD__ . ". Expected SignOff found " . get_class(end($this->stack)));
        }
    }

}

?>