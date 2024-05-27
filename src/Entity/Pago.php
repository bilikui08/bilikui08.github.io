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
 * @Table(name="pago")
 */
class Pago
{
	/**
     * @Id
     * @Column(type="integer")
     * @GeneratedValue
     */
    private $id;
	
	 /**
     * @ManyToOne(targetEntity="Aportante", inversedBy="pagos")
	 * @JoinColumn(name="aportante_id")
     */
	private $aportante;
	
	/**
     * @ManyToOne(targetEntity="TipoPago")
	 * @JoinColumn(name="tipo_pago_id")
     */
	private $tipoPago;
	
	/**
     * @ManyToOne(targetEntity="EstadoPago")
	 * @JoinColumn(name="estado_pago_id")
     */
	private $estadoPago;
	
	/**
     * @Column(name="monto", type="decimal", precision=15, scale=2, nullable=false)
     */
	private $monto;
	
	/**
     * @Column(name="numero_orden_api_externa", type="string", length=100, nullable=true)
     */
	private $numeroOrdenApiExterna;
	
	/**
     * @Column(name="respuesta_api_externa", type="text", nullable=true)
     */
	private $respuestaApiExterna;
	
	/**
     * @Column(name="hash", type="string", length=64, nullable=false)
     */
	private $hash;
	
	/**
     * @Column(name="fecha_creacion", type="datetime", nullable=false)
     */
	private $fechaCreacion;
	
	public function __construct()
	{
		$this->fechaCreacion = new \DateTime();
	}
	
	public function getId()
    {
        return $this->id;
    }
	
	public function setAportante(Aportante $aportante): self
	{
		$this->aportante = $aportante;
		return $this;
	}
	
	public function getAportante(): Aportante
	{
		return $this->aportante;
	}
	
	public function setTipoPago(TipoPago $tipoPago): self
	{
		$this->tipoPago = $tipoPago;
		return $this;
	}
	
	public function getTipoPago(): TipoPago
	{
		return $this->tipoPago;
	}
	
	public function setEstadoPago(EstadoPago $estadoPago): self
	{
		$this->estadoPago = $estadoPago;
		return $this;
	}
	
	public function getEstadoPago(): EstadoPago
	{
		return $this->estadoPago;
	}
	
	public function setMonto($monto): self
	{
		$this->monto = $monto;
		return $this;
	}
	
	public function getMonto(): float
	{
		return $this->monto;
	}
	
	public function setNumeroOrdenApiExterna(string $numeroOrdenApiExterna): self
	{
		$this->numeroOrdenApiExterna = $numeroOrdenApiExterna;
		return $this;
	}
	
	public function getNumeroOrdenApiExterna(): ?string
	{
		return $this->numeroOrdenApiExterna;		
	}
	
	public function setRespuestaApiExterna(array $respuestaApiExterna): self
	{
		if (!empty($respuestaApiExterna)) {
			$this->respuestaApiExterna = json_encode($respuestaApiExterna);
		}
		
		return $this;
	}
	
	public function getRespuestaApiExterna(): ?array
	{
		if (!empty($this->respuestaApiExterna)) {
			return json_decode($this->respuestaApiExterna, true);
		}
		
		return [];
	}
	
	public function setHash(string $hash) : self
	{
		$this->hash = $hash;
		return $this;
	}
	
	public function getHash() : string
	{
		return $this->hash;
	}
	
	public function setFechaCreacion(\DateTimeInterface $fechaCreacion): self
    {
        $this->fechaCreacion = $fechaCreacion;
        return $this;
    }

    public function getFechaCreacion(): \DateTimeInterface
    {
        return $this->fechaCreacion;
    }
}