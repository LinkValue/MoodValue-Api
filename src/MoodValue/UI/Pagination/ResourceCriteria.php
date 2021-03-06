<?php

namespace MoodValue\UI\Pagination;

/**
 * Allows to separate and filter url parameters for resource filtering
 */
class ResourceCriteria
{
    const PAGE_PARAMETER_NAME = 'page';
    const LIMIT_PARAMETER_NAME = 'limit';
    const MAX_LIMIT_PARAMETER_NAME = 'max_limit';
    const SORT_PARAMETER_NAME = 'sort';

    /**
     * @var int
     */
    private $limit;

    /**
     * @var int
     */
    private $maxLimit = 1000;

    /**
     * @var int
     */
    private $start;

    /**
     * @var int
     */
    private $page;

    /**
     * @var array
     */
    private $fieldsCriteria;

    /**
     * @var array
     */
    private $sortCriteria;

    public function __construct(array $criteria)
    {
        $page = isset($criteria[self::PAGE_PARAMETER_NAME]) ? intval($criteria[self::PAGE_PARAMETER_NAME]) : 1;
        $perPage = isset($criteria[self::LIMIT_PARAMETER_NAME]) ? intval($criteria[self::LIMIT_PARAMETER_NAME]) : 10;
        $sort = $criteria[self::SORT_PARAMETER_NAME] ?? '';

        $this->fieldsCriteria = array_diff_key(
            $criteria,
            array(
                self::PAGE_PARAMETER_NAME  => null,
                self::LIMIT_PARAMETER_NAME => null,
                self::SORT_PARAMETER_NAME  => null
            )
        );

        $this->sortCriteria = $sort ? explode(',', $sort) : [];

        $this->page = max($page, 1);
        $this->limit = max(min($perPage, $this->maxLimit), 1);
        $this->start = ($this->page - 1) * $this->limit;
    }

    public function getFieldsCriteria() : array
    {
        return $this->fieldsCriteria;
    }

    public function getSortCriteria() : array
    {
        return $this->sortCriteria;
    }

    public function getSortFieldsWithDirections() : array
    {
        $sortFieldsWithDirections = array();

        foreach ($this->sortCriteria as $field) {
            if (empty($field)) {
                continue;
            }

            $desc = '-' === $field[0];
            $fieldName = $desc ? substr($field, 1) : $field;

            $sortFieldsWithDirections[] = array($fieldName, $desc ? 'DESC' : 'ASC');
        }

        return $sortFieldsWithDirections;
    }

    public function getLimit() : int
    {
        return $this->limit;
    }

    public function getStart() : int
    {
        return $this->start;
    }

    public function getPage() : int
    {
        return $this->page;
    }
}
