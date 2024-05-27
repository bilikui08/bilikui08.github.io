<?php

namespace App\Entity;

use Doctrine\Mapping\Entity;
use Doctrine\Mapping\Column;
use Doctrine\Mapping\Table;
use Doctrine\Mapping\OneToMany;
use Doctrine\Mapping\ManyToMany;
use Doctrine\Mapping\JoinColumn;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use App\Repository\AportanteRepository;

/**
 * @Entity(repositoryClass=App\Repository\AportanteRepository::class)
 * @Table(name="aportante")
 */
class Aportante 
{
    /**
     * @Id
     * @Column(type="integer")
     * @GeneratedValue
     */
    private $id;

    /**
     * @Column(name="nombre", type="string", length=255, nullable=false)
     */
    private $nombre;

    /**
     * @Column(name="apellido", type="string", length=255, nullable=false)
     */
    private $apellido;
	
	/**
     * @Column(name="dni", type="integer", nullable=false)
     */
    private $dni;

    /**
     * @Column(name="email", type="string", length=255, nullable=false)
     */
    private $email;
	
	/**
     * @Column(name="numero_telefono", type="string", length=100, nullable=true)
     */
	private $numeroTelefono;

    /**
     * @Column(name="numero_sorteo", type="integer", nullable=true)
     */
    private $numeroSorteo;
	
	/**
     * @Column(name="fecha_creacion", type="datetime", nullable=false)
     */
	private $fechaCreacion;

    /**
     * @OneToMany(targetEntity="Pago", mappedBy="aportante")
     */
    private $pagos;
	
	/**
     * @ManyToMany(targetEntity="Sorteo", inversedBy="aportantes")
     */
	private $sorteos;
	
	public function __construct()
	{
		$this->fechaCreacion = new \DateTime();
		$this->pagos = new ArrayCollection();
	}

    public function __toString()
    {
        return $this->nombre . ' ' . $this->apellido;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setNombre(string $nombre): self
    {
        $this->nombre = $nombre;
        return $this;
    }

    public function getNombre(): string
    {
        return $this->nombre;
    }

    public function setApellido(string $apellido): self
    {
        $this->apellido = $apellido;
        return $this;
    }

    public function getApellido(): string
    {
        return $this->apellido;
    }
	
	public function setDni(int $dni): self
    {
        $this->dni = $dni;
        return $this;
    }

    public function getDni(): int
    {
        return $this->dni;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }

    public function getEmail(): string
    {
        return $this->email;
    }
	
	public function setNumeroTelefono(?string $numeroTelefono): self
    {
        $this->numeroTelefono = $numeroTelefono;
        return $this;
    }

    public function getNumeroTelefono(): ?string
    {
        return $this->numeroTelefono;
    }
	
	public function setNumeroSorteo(int $numeroSorteo): self
    {
        $this->numeroSorteo = $numeroSorteo;
        return $this;
    }

    public function getNumeroSorteo(): ?int
    {
        return $this->numeroSorteo;
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
	
	/**
     * @return Collection|Pago[]
     */
    public function getPagos(): Collection
    {
        return $this->pagos;
    }

    public function addPago(Pago $pago): self
    {
        if (!$this->pagos->contains($pago)) {
            $this->pagos[] = $pago;
            $pago->setAportante($this);
        }

        return $this;
    }

    public function removePago(Pago $pago): self
    {
        if ($this->pagos->removeElement($pago)) {
            // set the owning side to null (unless already changed)
            if ($pago->getAportante() === $this) {
                $pago->setAportante(null);
            }
        }

        return $this;
    }
	
	public function getSorteos() 
	{
		return $this->sorteos;
	}
}