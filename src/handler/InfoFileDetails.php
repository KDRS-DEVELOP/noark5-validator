<?php
namespace handler;
require_once 'models/noark5/v31/infoXML/ChecksumInfo.php';
require_once 'models/noark5/v31/infoXML/ExtractionInfo.php';
require_once 'models/noark5/v31/infoXML/FondsCreatorInfo.php';
require_once 'models/noark5/v31/infoXML/System.php';

class InfoFileDetails
{
    protected $infoFileFilename;

    /**
     *
     * @var FondsCreatorInfo $fondsCreatorInfo:
     */
    protected $checksumInfo;

    /**
     *
     * @var FondsCreatorInfo $fondsCreatorInfo:
     */
    protected $fondsCreatorInfo;

    /**
     *
     * @var ExtractionInfo $extractionInfo:
     */
    protected $extractionInfo;

    /**
     *
     * @var System $system:
     */
    protected $system;

    /**
     *
     * @var ChecksumInfo $ChecksumInfo:
     */
    protected $checksumInfo;

    function __construct($infoFileFilename) {
        $this->infoFileFilename = $infoFileFilename;
        $this->fondsCreatorInfo = new FondsCreatorInfo();
        $this->checksumInfo = new ChecksumInfo();
        $this->system = new System();
        $checksumInfo = new ChecksumInfo();
    }

    public function getInfoFilename()
    {
        return $this->infoFilename;
    }

    public function setInfoFilename($infoFilename)
    {
        $this->infoFilename = $infoFilename;
        return $this;
    }

    public function getChecksumArkivuttrekk()
    {
        return $this->checksumArkivuttrekk;
    }

    public function getFondsCreatorInfo()
    {
        return $this->fondsCreatorInfo;
    }

    public function getExtraction()
    {
        return $this->extraction;
    }

    public function getSystem()
    {
        return $this->system;
    }

    public function getChecksumInfo()
    {
        return $this->checksumInfo;
    }

}

?>