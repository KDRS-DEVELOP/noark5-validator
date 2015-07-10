<?php
namespace models\noark5\v31;

/**
 * @Entity @Table(name="comment")
 **/
class Comment
{
    /** @Id @Column(type="bigint", name="pk_comment_id", nullable=false) @GeneratedValue **/
    protected $id;

    /** M310 - merknadstekst (xs:string) */
    /** @Column(type="string", name="comment_text", nullable=true, length = 2000) **/
    protected $commentText;

    /** M084 - merknadstype (xs:string) */
    /** @Column(type="string", name="comment_type", nullable=true) **/
    protected $commentType;

    /** M611 - merknadsdato (xs:dateTime)*/
    /** @Column(type="datetime", name="comment_time", nullable=true) **/
    protected $commentTime;

    /** M612 - merknadRegistrertAv (xs:string) */
    /** @Column(type="string", name="comment_registered_by", nullable=true) **/
    protected $commentRegisteredBy;

    // Link to File
    /**
     * @ManyToOne
     * @JoinColumn(targetEntity="File", name = "comment_file_id", referencedColumnName = "pk_file_id")
     */
    protected $referenceFile;

    // Link to BasicRecord
    /**
     * @ManyToOne
     * @JoinColumn(targetEntity="BasicRecord", name = "comment_basic_record_id", referencedColumnName = "pk_record_id")
     */
    protected  $referenceBasicRecord;

    // Link to DocumentDescription
    /**
     * @ManyToOne
     * @JoinColumn(targetEntity="DocumentDescription", name = "comment_document_description_id", referencedColumnName = "pk_document_description_id")
     */
    protected $referenceDocumentDescription;

    public function __construct() {}

    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    public function getCommentText()
    {
        return $this->commentText;
    }

    public function setCommentText($commentText)
    {
        $this->commentText = $commentText;
        return $this;
    }

    public function getCommentType()
    {
        return $this->commentType;
    }

    public function setCommentType($commentType)
    {
        $this->commentType = $commentType;
        return $this;
    }

    public function getCommentTime()
    {
        return $this->commentTime;
    }

    public function setCommentTime($commentTime)
    {
        $this->commentTime = $commentTime;
        return $this;
    }

    public function getCommentRegisteredBy()
    {
        return $this->commentRegisteredBy;
    }

    public function setCommentRegisteredBy($commentRegisteredBy)
    {
        $this->commentRegisteredBy = $commentRegisteredBy;
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

    public function getReferenceBasicRecord()
    {
        return $this->referenceBasicRecord;
    }

    public function setReferenceBasicRecord($referenceBasicRecord)
    {
        $this->referenceBasicRecord = $referenceBasicRecord;
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