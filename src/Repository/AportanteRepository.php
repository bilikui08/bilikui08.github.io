<?php

namespace App\Repository;

use App\Entity\Aportante;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\ORM\EntityRepository;

class AportanteRepository extends EntityRepository
{
	
	
	public function getNumeroSorteoUnivoco($numeroSorteo, $sorteo)
    {
		$rsm = new ResultSetMapping();
		
		$rsm->addScalarResult('id', 'id');
		$rsm->addScalarResult('numero_sorteo', 'numero_sorteo');
		
		$sql = " 
			select a.id, a.`numero_sorteo`
			from aportante a
			inner join sorteo_aportante pivot on a.id = pivot.`aportante_id`
			inner join sorteo s on pivot.`sorteo_id` = s.id
			where a.`numero_sorteo` = :numeroSorteo
			and s.id = :sorteo
		";
		
		$query = $this->_em->createNativeQuery($sql, $rsm);
		
		$query->setParameter('numeroSorteo', $numeroSorteo);
        $query->setParameter('sorteo', $sorteo->getId());
		
		return $query->getOneOrNullResult();
    }
}