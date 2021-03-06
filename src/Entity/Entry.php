<?php

namespace App\Entity;

use App\Repository\EntryRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=EntryRepository::class)
 */
class Entry implements \JsonSerializable
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Client::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $client;

    /**
     * @ORM\ManyToOne(targetEntity=Status::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $status;

    /**
     * @ORM\ManyToOne(targetEntity=BankAccount::class)
     */
    private $bankAccount;

    /**
     * @ORM\ManyToOne(targetEntity=RecurringEntry::class)
     */
    private $recurringEntry;

    /**
     * @ORM\ManyToOne(targetEntity=Category::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $category;

    /**
     * @ORM\ManyToOne(targetEntity=Payment::class)
     */
    private $payment;

    /**
     * @ORM\ManyToOne(targetEntity=CreditCardBill::class)
     */
    private $creditCardBill;

    /**
     * @ORM\ManyToOne(targetEntity=SplitEntry::class)
     */
    private $splitEntry;

    /**
     * @ORM\ManyToOne(targetEntity=Client::class)
     */
    private $debtorClient;

    /**
     * @ORM\Column(type="datetime")
     */
    private $issuanceDate;

    /**
     * @ORM\Column(type="datetime")
     */
    private $dueDate;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $dateWithdrew;

    /**
     * @ORM\Column(type="decimal", precision=15, scale=2)
     */
    private $amount;

    /**
     * @ORM\Column(type="decimal", precision=15, scale=2, nullable=true)
     */
    private $debitedAmount;

    /**
     * @ORM\Column(type="smallint")
     */
    private $typeEntry;

    /**
     * @ORM\Column(type="boolean")
     */
    private $active;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getClient(): ?client
    {
        return $this->client;
    }

    public function setClient(?client $client): self
    {
        $this->client = $client;

        return $this;
    }

    public function getStatus(): ?status
    {
        return $this->status;
    }

    public function setStatus(?status $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getBankAccount(): ?bankAccount
    {
        return $this->bankAccount;
    }

    public function setBankAccount(?bankAccount $bankAccount): self
    {
        $this->bankAccount = $bankAccount;

        return $this;
    }

    public function getRecurringEntry(): ?recurringEntry
    {
        return $this->recurringEntry;
    }

    public function setRecurringEntry(?recurringEntry $recurringEntry): self
    {
        $this->recurringEntry = $recurringEntry;

        return $this;
    }

    public function getCategory(): ?category
    {
        return $this->category;
    }

    public function setCategory(?category $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function getPayment(): ?payment
    {
        return $this->payment;
    }

    public function setPayment(?payment $payment): self
    {
        $this->payment = $payment;

        return $this;
    }

    public function getCreditCardBill(): ?creditCardBill
    {
        return $this->creditCardBill;
    }

    public function setCreditCardBill(?creditCardBill $creditCardBill): self
    {
        $this->creditCardBill = $creditCardBill;

        return $this;
    }

    public function getSplitEntry(): ?splitEntry
    {
        return $this->splitEntry;
    }

    public function setSplitEntry(?splitEntry $splitEntry): self
    {
        $this->splitEntry = $splitEntry;

        return $this;
    }

    public function getDebtorClient(): ?client
    {
        return $this->debtorClient;
    }

    public function setDebtorClient(?client $debtorClient): self
    {
        $this->debtorClient = $debtorClient;

        return $this;
    }

    public function getIssuanceDate(): ?\DateTime
    {
        return $this->issuanceDate;
    }

    public function setIssuanceDate(\DateTime $issuanceDate): self
    {
        $this->issuanceDate = $issuanceDate;

        return $this;
    }

    public function getDueDate(): ?\DateTime
    {
        return $this->dueDate;
    }

    public function setDueDate(\DateTime $dueDate): self
    {
        $this->dueDate = $dueDate;

        return $this;
    }

    public function getDateWithdrew(): ?\DateTime
    {
        return $this->dateWithdrew;
    }

    public function setDateWithdrew(?\DateTime $dateWithdrew): self
    {
        $this->dateWithdrew = $dateWithdrew;

        return $this;
    }

    public function getAmount(): ?string
    {
        return $this->amount;
    }

    public function setAmount(string $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    public function getDebitedAmount(): ?string
    {
        return $this->debitedAmount;
    }

    public function setDebitedAmount(?string $debitedAmount): self
    {
        $this->debitedAmount = $debitedAmount;

        return $this;
    }

    public function getTypeEntry(): ?int
    {
        return $this->typeEntry;
    }

    public function setTypeEntry(int $typeEntry): self
    {
        $this->typeEntry = $typeEntry;

        return $this;
    }

    public function getActive(): ?bool
    {
        return $this->active;
    }

    public function setActive(bool $active): self
    {
        $this->active = $active;

        return $this;
    }

    public function jsonSerialize()
    {
        return [
            'id' => $this->getId(),
            'statusId' => $this->getStatus()->getId(),
            'statusName' => $this->getStatus()->getName(),
            'bankAccountId' => $this->getBankAccount()->getId(),
            'bankAccouuntName' => $this->getBankAccount()->getName(),
            'recurringEntryId' => $this->getRecurringEntry()?->getId(),
            'categoryId' => $this->getCategory()->getId(),
            'categoryName' => $this->getCategory()->getName(),
            'paymentId' => $this->getPayment()->getId(),
            'paymentName' => $this->getPayment()->getName(),
            'creditCardBillId' => $this->getCreditCardBill()?->getId(),
            'splitEntryId' => $this->getSplitEntry()?->getId(),
            'debtorClientId' => $this->getDebtorClient()?->getId(),
            'debtorClientName' => $this->getDebtorClient()?->getName(),
            'issuanceDate' => date_format($this->getIssuanceDate(),"Y-m-d"),
            'dueDate' => date_format($this->getDueDate(),"Y-m-d"),
            'dateWithdrew' => ($this->getDateWithdrew() == null) ? null : date_format($this->getDateWithdrew(),"Y-m-d"),
            'amount' => $this->getAmount(),
            'debitedAmount' => $this->getDebitedAmount(),
            'typeEntry' => $this->getTypeEntry()
        ];
    }
}
