<?php
namespace models\noark5\v31;

/**
 * @Entity @Table(name="classified")
 **/
class Classified
{
    /** @Id @Column(type="bigint", name="pk_classified", nullable=false) @GeneratedValue **/
    protected $id;

    /** M506 - gradering (xs:string) */
    /** @Column(type="string", name="classifcation", nullable=true) **/
    protected $classifcation;

    /** M624 - graderingsdato (xs:dateTime) */
    /** @Column(type="dateTime", name="classifcation_date", nullable=true) **/
    protected $classifcationDate;

    /** M629 - gradertAv (xs:string) */
    /** @Column(type="string", name = "classifcation_by", nullable=true) **/
    protected $classifcationBy;

    /** M626 - nedgraderingsdato (xs:dateTime) */
    /** @Column(type="dateTime", name="classifcation_downgraded_date", nullable=true) **/
    protected $classifcationDowngradedDate;

    /** M627 - nedgradertAv (xs:string) */
    /** @Column(type="string", name = "classifcation_downgraded_by", nullable=true) **/
    protected $classifcationDowngradedBy;

    // Links to Series
    /** @OneToMany(targetEntity="Series", mappedBy="referenceClassification", fetch="EXTRA_LAZY") **/
    protected $referenceSeries;

    // Links to Klass
    /** @OneToMany(targetEntity="Klass", mappedBy="referenceClassification", fetch="EXTRA_LAZY") **/
    protected $referenceKlass;

    // Links to File
    /** @OneToMany(targetEntity="File", mappedBy="referenceClassification", fetch="EXTRA_LAZY") **/
    protected $referenceFile;

    // Links to Record
    /** @OneToMany(targetEntity="Record", mappedBy="referenceClassification", fetch="EXTRA_LAZY") **/
    protected $referenceRecord;

    // Links to DocumentDescription
    /** @OneToMany(targetEntity="DocumentDescription", mappedBy="referenceClassification", fetch="EXTRA_LAZY") **/

    protected $referenceDocumentDescription;

    public function getId()
    {
        return $this->id;
    }

    public function getClassifcation()
    {
        return $this->classifcation;
    }

    public function setClassifcation($classifcation)
    {
        $this->classifcation = $classifcation;
        return $this;
    }

    public function getClassifcationDate()
    {
        return $this->classifcationDate;
    }

    public function setClassifcationDate($classifcationDate)
    {
        $this->classifcationDate = $classifcationDate;
        return $this;
    }

    public function getClassifcationBy()
    {
        return $this->classifcationBy;
    }

    public function setClassifcationBy($classifcationBy)
    {
        $this->classifcationBy = $classifcationBy;
        return $this;
    }

    public function getClassifcationDowngradedDate()
    {
        return $this->classifcationDowngradedDate;
    }

    public function setClassifcationDowngradedDate($classifcationDowngradedDate)
    {
        $this->classifcationDowngradedDate = $classifcationDowngradedDate;
        return $this;
    }

    public function getClassifcationDowngradedBy()
    {
        return $this->classifcationDowngradedBy;
    }

    public function setClassifcationDowngradedBy($classifcationDowngradedBy)
    {
        $this->classifcationDowngradedBy = $classifcationDowngradedBy;
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

    public function getReferenceKlass()
    {
        return $this->referenceKlass;
    }

    public function setReferenceKlass($referenceKlass)
    {
        $this->referenceKlass = $referenceKlass;
        return $this;
    }

    public function getReferenceFile()
    {
        return $this->referenceFile;
    }

    public function setReferenceFile($referenceFile)
    {
        $this->referenceFile = $referenceFile;
        return $this;
    }

    public function getReferenceRecord()
    {
        return $this->referenceRecord;
    }

    public function setReferenceRecord($referenceRecord)
    {
        $this->referenceRecord = $referenceRecord;
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


}

?>