<?php


/**
 * @Entity @Table(name="deletion")
 **/
class Deletion
{
    /** @Id @Column(type="bigint", name="pk_deletion_id", nullable=false) @GeneratedValue **/
    protected $id;

    /** M089 - slettingstype (xs:string) */
    /** @Column(type="string", name = "deletion_type", nullable=true) **/
    protected $deletionType;

    /** M614 - slettetAv (xs:string) */
    /** @Column(type="string", name = "deletion_by", nullable=true) **/
    protected $deletionBy;

    /** M613 slettetDato (xs:dateTime) */
    /** @Column(type="datetime", name = "deletion_date", nullable=true) **/
    protected $deletionDate;

    // Links to Series
    /** @OneToMany(targetEntity="Series", mappedBy="referenceDeletion", fetch="EXTRA_LAZY") **/
    protected $referenceSeries;

    // Links to DocumentDescription
    /** @OneToMany(targetEntity="DocumentDescription", mappedBy="referenceDeletion", fetch="EXTRA_LAZY") **/
    protected $referenceDocumentDescription;

    public function __construct(){}

    public function getId()
    {
        return $this->id;
    }

    public function getDeletionType()
    {
        return $this->deletionType;
    }

    public function setDeletionType($deletionType)
    {
        $this->deletionType = $deletionType;
        return $this;
    }

    public function getDeletionBy()
    {
        return $this->deletionBy;
    }

    public function setDeletionBy($deletionBy)
    {
        $this->deletionBy = $deletionBy;
        return $this;
    }

    public function getDeletionDate()
    {
        return $this->deletionDate;
    }

    public function setDeletionDate($deletionDate)
    {
        $this->deletionDate = DateTime::createFromFormat(Constants::XSD_DATETIME_FORMAT, $deletionDate);
        return $this;
    }

    public function getReferenceSeries()
    {
        return $this->referenceSeries;
    }

    public function setReferenceSeries($referenceSeries)
    {
        $this->referenceSeries = $referenceSeries;
        return $this;
    }

    public function getReferenceDocumentDescription()
    {
        return $this->referenceDocumentDescription;
    }

    public function setReferenceDocumentDescription($referenceDocumentDescription)
    {
        $this->referenceDocumentDescription = $referenceDocumentDescription;
        return $this;
    }

    public function __toString() {
        return ' [' . $this->id. '],' .
        ' [' . $this->deletionType. '],' .
        ' [' . $this->deletionBy. '],' .
        ' [' . $this->deletionDate. '],' .
        ' [' . $this->referenceSeries. '],' .
        ' [' . $this->referenceDocumentDescription. '],';

    }
}

?>