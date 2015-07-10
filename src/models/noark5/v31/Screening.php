<?php


/** @Embeddable */
class Screening
{
    /** M500 - tilgangsrestriksjon n4 (JP.TGKODE) */
    /** @Column(name = "access_restriction", type = "string", nullable=true) **/
    protected $accessRestriction;

    /** M501 - skjermingshjemmel n4 (JP.UOFF) */
    /** @Column(name = "screening_authority", type = "string", nullable=true) **/
    protected $screeningAuthority;

    /** M502 - skjermingMetadata should be 1-M */
    /** @Column(name = "screening_metadata", type = "string", nullable=true) **/
    protected $screeningMetadata;

    /** M503 - skjermingDokument */
    /** @Column(name = "screening_document", type = "string", nullable=true) **/
    protected $screeningDocument;

    /** M505 - skjermingOpphoererDato n4(JP.AGDATO)*/
    /** @Column(name = "screening_expires", type = "datetime", nullable=true) **/
    protected $screeningExpiresDate;

    /** M504 - skjermingsvarighet */
    /** @Column(name = "screening_duration", type = "string", nullable=true) **/
    protected $screeningDuration;

    function __construct(){}

    public function getAccessRestriction()
    {
        return $this->accessRestriction;
    }

    public function setAccessRestriction($accessRestriction)
    {
        $this->accessRestriction = $accessRestriction;
        return $this;
    }

    public function getScreeningAuthority()
    {
        return $this->screeningAuthority;
    }

    public function setScreeningAuthority($screeningAuthority)
    {
        $this->screeningAuthority = $screeningAuthority;
        return $this;
    }

    public function getScreeningMetadata()
    {
        return $this->screeningMetadata;
    }

    public function setScreeningMetadata($screeningMetadata)
    {
        $this->screeningMetadata = $screeningMetadata;
        return $this;
    }

    public function getScreeningDocument()
    {
        return $this->screeningDocument;
    }

    public function setScreeningDocument($screeningDocument)
    {
        $this->screeningDocument = $screeningDocument;
        return $this;
    }

    public function getScreeningExpiresDate()
    {
        return $this->screeningExpiresDate;
    }

    public function setScreeningExpiresDate($screeningExpiresDate)
    {
        $this->screeningExpiresDate = $screeningExpiresDate;
        return $this;
    }

    public function getScreeningDuration()
    {
        return $this->screeningDuration;
    }

    public function setScreeningDuration($screeningDuration)
    {
        $this->screeningDuration = $screeningDuration;
        return $this;
    }


}

?>