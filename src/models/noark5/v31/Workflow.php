<?php
namespace models\noark5\v31;

/**
 * @Entity @Table(name="workflow")
 **/
class Workflow
{
    /** @Id @Column(type="bigint", name="pk_workflow", nullable=false) @GeneratedValue **/
    protected $id;

    /** M660 flytTil (xs:string) */
    /** @Column(type="string", name="workflow_to", nullable=true) **/
    protected $workflowTo;

    /** M665 flytFra  (xs:string) */
    /** @Column(type="string", name="workflow_from", nullable=true) **/
    protected $workflowFrom;

    /** M661 - flytMottattDato (xs:dateTime) */
    /** @Column(type="dateTime", name="workflow_received_date", nullable=true) **/
    protected $workflowReceivedDate;

    /** M662 flytSendtDato (xs:dateTime) */
    /** @Column(type="dateTime", name="workflow_sent_date", nullable=true) **/
    protected $workflowSentDate;

    /** M663 flytStatus (xs:string) */
    /** @Column(type="string", name="workflow_status", nullable=true) **/
    protected $flytStatus;

    /** M664 flytMerknad (xs:string) */
    /** @Column(type="string", name="workflow_comment", nullable=true) **/
    protected $flytMerknad;

    // Link to RegistryEntry
    /** @ManyToOne(targetEntity="RegistryEntry", fetch="EXTRA_LAZY", cascade={"persist", "remove"})
     *   @JoinColumn(name="workflow_registry_entry_id",
     *        referencedColumnName="pk_record_id")
     **/
    protected $referenceRegistryEntry;

    public function __construct(){}

    public function getId()
    {
        return $this->id;
    }

    public function getWorkflowTo()
    {
        return $this->workflowTo;
    }

    public function setWorkflowTo($workflowTo)
    {
        $this->workflowTo = $workflowTo;
        return $this;
    }

    public function getWorkflowFrom()
    {
        return $this->workflowFrom;
    }

    public function setWorkflowFrom($workflowFrom)
    {
        $this->workflowFrom = $workflowFrom;
        return $this;
    }

    public function getWorkflowReceivedDate()
    {
        return $this->workflowReceivedDate;
    }

    public function setWorkflowReceivedDate($workflowReceivedDate)
    {
        $this->workflowReceivedDate = $workflowReceivedDate;
        return $this;
    }

    public function getWorkflowSentDate()
    {
        return $this->workflowSentDate;
    }

    public function setWorkflowSentDate($workflowSentDate)
    {
        $this->workflowSentDate = $workflowSentDate;
        return $this;
    }

    public function getFlytStatus()
    {
        return $this->flytStatus;
    }

    public function setFlytStatus($flytStatus)
    {
        $this->flytStatus = $flytStatus;
        return $this;
    }

    public function getFlytMerknad()
    {
        return $this->flytMerknad;
    }

    public function setFlytMerknad($flytMerknad)
    {
        $this->flytMerknad = $flytMerknad;
        return $this;
    }

    public function getReferenceRegistryEntry()
    {
        return $this->referenceRegistryEntry;
    }

    public function setReferenceRegistryEntry($referenceRegistryEntry)
    {
        $this->referenceRegistryEntry = $referenceRegistryEntry;
        return $this;
    }
}

?>