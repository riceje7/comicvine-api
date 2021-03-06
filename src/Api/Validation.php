<?php

namespace ComicVine\Api;

use ComicVine\Api\Filters\FilterCheck;
use ComicVine\Api\Filters\FilterValidation;

/**
 * Check validation of inputs for ControllerQuery.
 *
 * Class Validation
 *
 * @package grzgajda/comicvine-api
 * @author  Grzegorz Gajda <grz.gajda@outlook.com>
 */
class Validation
{
    use FilterCheck;
    use FilterValidation;

    /**
     * Mock for enabled filters.
     *
     * @var array
     */
    protected $enabledFilters = [];

    /**
     * Validation constructor.
     *
     * @param array $filters
     */
    public function __construct($filters = [])
    {
        if ($filters !== []) {
            $this->enabledFilters = $filters;
        }
    }

    /**
     * Check validation for $input
     *
     * @param string       $type
     * @param string|array $input
     *
     * @return boolean|null
     */
    public function validation($type = "", $input)
    {
        switch ($type) {
            case 'field_list':
                return $this->validFieldList($input);
            case 'limit':
                return $this->validNumber('limit', $input, 0, 100);
            case 'offset':
                return $this->validNumber('offset', $input, 0);
            case 'filter':
                return $this->validFilter($input);
            case 'sort':
                return $this->validSort($input);
            default:
                return false;
        }
    }

    /**
     * Validation for FIELD_LIST parameter.
     *
     * @param string|array $input
     *
     * @return bool
     */
    protected function validFieldList($input)
    {
        if (is_array($input) === false) {
            return false;
        }

        foreach ($input as $key => $value) {
            if ($this->isKeyAndValueAre($key, 'int', $value, 'string') === false) {
                return false;
            }
        }

        return true;
    }

    /**
     * Check if offset or limit is valid.
     *
     * @param string       $type  Type of valid (offset or limit)
     * @param string|array $input Value
     * @param integer      $min   Min range what value can be
     * @param integer      $max   Max range what value can be
     *
     * @return $this|bool
     */
    protected function validNumber($type, $input, $min, $max = "")
    {
        if ($this->isIntAndBetween($input, $min, $max) === false) {
            return false;
        }

        return $this->isEnabledFilter($type, $this->enabledFilters);
    }

    /**
     * Validation for FILTER parameter.
     *
     * @param string|array $input
     *
     * @return boolean|null
     */
    protected function validFilter($input)
    {
        if (is_array($input) === false) {
            return false;
        }

        foreach ($input as $key => $value) {
            if ($this->isKeyAndValueAre($key, 'string', $value, ['string', 'int', 'float']) === false) {
                return false;
            }
        }

        return $this->isEnabledFilter('filter', $this->enabledFilters);
    }

    /**
     * Validation for SORT parameter.
     *
     * @param string|array $input
     *
     * @return boolean|null
     */
    protected function validSort($input)
    {
        if (is_array($input) === false) {
            return false;
        }

        foreach ($input as $key => $value) {
            if ($this->isParamAValue($key, 'string', $value, ['asc', 'desc']) === false) {
                return false;
            }
        }

        return $this->isEnabledFilter('sort', $this->enabledFilters);
    }
}