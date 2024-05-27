<?php

namespace App\Entity;

use Doctrine\Mapping\Entity;
use Doctrine\Mapping\Column;
use Doctrine\Mapping\Table;
use Doctrine\Mapping\ManyToMany;
use Doctrine\Mapping\ManyToOne;
use Doctrine\Mapping\JoinColumn;
use Doctrine\Mapping\JoinTable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * @Entity
 * @Table(name="sorteo")
 */
class Sorteo
{
	/**
     * @Id
     * @Column(type="integer")
     * @GeneratedValue
     */
    private $id;
	
	 /**
     * @ManyToMany(targetEntity="Aportante")
	 * @JoinTable(name="sorteo_aportante", 
	 *   joinColumns={@JoinColumn(name="sorteo_id", referencedColumnName="id")},
	 *   inverseJoinColumns={@JoinColumn(name="aportante_id", referencedColumnName="id")}
	 * )
     */
	private $aportantes;
	
	/**
     * @Column(name="fecha", type="date", nullable=false)
     */
	private $fecha;
	
	/**
     * @Column(name="vigente", type="boolean", nullable=false)
     */
	private $vigente;
	
	/**
     * @return Collection|Aportante[]
     */
    public function getAportantes(): Collection
    {
        return $this->aportantes;
    }

    public function addAportante(Aportante $aportante): self
    {
        $this->aportantes[] = $aportante;
            
        return $this;
    }

    public function removeSorteo(Aportante $aportante): self
    {
        $this->aportantes->removeElement($aportante);
            
        return $this;
    }
	
	
	public function getId()
    {
        return $this->id;
    }
	
	public function setFecha(\DateTimeInterface $fecha): self
    {
        $this->fecha = $fecha;
        return $this;
    }

    public function getFecha(): \DateTimeInterface
    {
        return $this->fecha;
    }
	
	public function setVigente(bool $vigente): self
	{
		$this->vigente = $vigente;
		return $this;
	}
	
	public function getVigente(): bool
	{
		return $this->vigente;
	}
}