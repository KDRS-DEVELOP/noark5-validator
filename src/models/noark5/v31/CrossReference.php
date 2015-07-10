<?php
namespace models\noark5\v31;

/**
 * @Entity @Table(name="cross_reference")
 **/
class CrossReference
{
    /** @Id @Column(type="bigint", name="pk_cross_reference", nullable=false) @GeneratedValue **/
    protected $id;

    // Link to Class
    // M219 - referanseTilKlasse
    /** @ManyToOne(targetEntity="Klass", fetch="EXTRA_LAZY")
     *   @JoinColumn(name="cross_reference_class_id",
     *        referencedColumnName="pk_class_id")
     **/
    protected $referenceToClass;

    // Link to File
    // M210 - referanseTilMappe
    /** @ManyToOne(targetEntity="Klass", fetch="EXTRA_LAZY")
     *   @JoinColumn(name="cross_reference_class_id",
     *        referencedColumnName="pk_class_id")
     **/
    protected $referenceToFile;

    // Link to Record
    // M212 - referanseTilRegistrering
    /** @ManyToOne(targetEntity="Klass", fetch="EXTRA_LAZY")
     *   @JoinColumn(name="cross_reference_class_id",
     *        referencedColumnName="pk_class_id")
     **/
    protected $referenceToRecord;

    public function __construct(){}

    public function getId()
    {
        return $this->id;
    }

    public function getReferenceToClass()
    {
        return $this->referenceToClass;
    }

    public function setReferenceToClass($referenceToClass)
    {
        $this->referenceToClass = $referenceToClass;
        return $this;
    }

    public function getReferenceToFile()
    {
        return $this->referenceToFile;
    }

    public function setReferenceToFile($referenceToFile)
    {
        $this->referenceToFile = $referenceToFile;
        return $this;
    }

    public function getReferenceToRecord()
    {
        return $this->referenceToRecord;
    }

    public function setReferenceToRecord($referenceToRecord)
    {
        $this->referenceToRecord = $referenceToRecord;
        return $this;
    }

}

?>