<?php

namespace App\Entity;

use Doctrine\Mapping\Entity;
use Doctrine\Mapping\Column;
use Doctrine\Mapping\Table;
use Doctrine\Mapping\ManyToOne;
use Doctrine\Mapping\JoinColumn;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * @Entity
 * @Table(name="tipo_pago")
 */
class TipoPago
{
	
	const MERCADO_PAGO = 1;
	const EFECTIVO = 2;
	const TRANSFERENCIA_BANCARIA = 3;
	
	/**
     * @Id
     * @Column(type="integer")
     * @GeneratedValue
     */
    private $id;
	
	 /**
     * @ORM\Column(type="string", length=255)
     */
    private $denominacion;

    public function __toString()
    {
        return $this->denominacion;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDenominacion(): ?string
    {
        return $this->denominacion;
    }

    public function setDenominacion(string $denominacion): self
    {
        $this->denominacion = $denominacion;

        return $this;
    }

}