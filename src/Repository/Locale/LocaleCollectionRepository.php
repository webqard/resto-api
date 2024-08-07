<?php

declare(strict_types=1);

namespace App\Repository\Locale;

use App\Entity\Locale;
use App\Exception\UnexpectedDirectionException;
use App\Exception\UnexpectedFieldException;
use App\Repository\OrderableDirection;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Repository for the get method of the Locale's collection.
 */
class LocaleCollectionRepository extends ServiceEntityRepository
{
    // Magic methods :

    /**
     * The constructor.
     * @param \Doctrine\Persistence\ManagerRegistry $registry the registry manager.
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Locale::class);
    }


    // Methods :

    /**
     * Finds locales by parameters.
     * @param string[] $criterias the criterias.
     * @param string[]|null $orderBy the orders.
     * @param ?int $limit the limit.
     * @param int $offset the offset.
     * @throws \UnexpectedValueException if the limit is negative.
     * @throws \UnexpectedValueException if the offset is negative.
     * @return \App\Entity\Locale[] the locales.
     */
    public function findByPartialString(
        array $criterias = [],
        ?array $orderBy = null,
        ?int $limit = null,
        ?int $offset = 0
    ): array {

        if ($limit < 0) {
            throw new \UnexpectedValueException('limitIsNegative');
        }

        if ($offset < 0) {
            throw new \UnexpectedValueException('offsetIsNegative');
        }

        $queryBuilder = $this->createQueryBuilder('locale');
        $this->addCriterias($queryBuilder, $criterias);

        if ($orderBy !== null) {
            $this->addOrders($queryBuilder, $orderBy);
        }

        $queryBuilder->setFirstResult($offset);
        $queryBuilder->setMaxResults($limit);

        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * Adds criterias for the query.
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder the query builder.
     * @param array $criterias the criterias.
     * @throws \App\Exception\UnexpectedFieldException if the field is not queryable.
     * @throws \UnexpectedValueException if the value to search for is not scalar.
     */
    private function addCriterias(QueryBuilder $queryBuilder, array $criterias): void
    {
        foreach ($criterias as $field => $value) {
            if (LocaleQueryableField::tryFrom($field) === null) {
                $availableFields = array_column(LocaleQueryableField::cases(), 'value');
                throw new UnexpectedFieldException('notQueryableField', $field, $availableFields);
            }

            if (\is_scalar($value) === false) {
                throw new \UnexpectedValueException('notAScalarValueInQuery');
            }

            $likeExpression = $queryBuilder->expr()->like(
                $queryBuilder->getRootAliases()[0] . '.' . $field,
                ':value'
            );

            $queryBuilder->where($likeExpression);
            $queryBuilder->setParameter('value', '%' . $value . '%');
        }
    }

    /**
     * Adds orders for the query.
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder the query builder.
     * @param string[] $orderBy the orders.
     * @param string[] $desc the descending orders.
     * @throws \App\Exception\UnexpectedFieldException if the field is not orderable.
     * @throws \App\Exception\UnexpectedDirectionException if the direction is invalid.
     */
    private function addOrders(
        QueryBuilder $queryBuilder,
        array $orderBy
    ): void {

        foreach ($orderBy as $field => $direction) {
            if (LocaleOrderableField::tryFrom($field) === null) {
                $availableFields = array_column(LocaleOrderableField::cases(), 'value');
                throw new UnexpectedFieldException('notOrderableField', $field, $availableFields);
            }

            if (OrderableDirection::tryFrom($direction) === null) {
                throw new UnexpectedDirectionException('notADirection');
            }

            $queryBuilder->addOrderBy(
                $queryBuilder->getRootAliases()[0] . '.' . $field,
                $direction
            );
        }
    }
}
