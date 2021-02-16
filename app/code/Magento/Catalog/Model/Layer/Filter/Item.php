<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\Catalog\Model\Layer\Filter;

use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\UrlInterface;
use Magento\Theme\Block\Html\Pager;

/**
 * Filter item model
 *
 * @api
 */
class Item extends DataObject
{
    /**
     * Url
     *
     * @var UrlInterface
     */
    private $_url;

    /**
     * Html pager block
     *
     * @var Pager
     */
    private $_htmlPagerBlock;

    /**
     * Construct
     *
     * @param UrlInterface $url
     * @param Pager $htmlPagerBlock
     * @param array $data
     */
    public function __construct(
        UrlInterface $url,
        Pager $htmlPagerBlock,
        array $data = []
    ) {
        $this->_url = $url;
        $this->_htmlPagerBlock = $htmlPagerBlock;
        parent::__construct($data);
    }

    /**
     * Get filter instance
     *
     * @return AbstractFilter
     * @throws LocalizedException
     */
    public function getFilter()
    {
        $filter = $this->getData('filter');
        if (!is_object($filter)) {
            throw new LocalizedException(
                __('The filter must be an object. Please set the correct filter.')
            );
        }
        return $filter;
    }

    /**
     * Get filter item url
     *
     * @return string
     */
    public function getUrl()
    {
        $query = [
            $this->getFilter()->getRequestVar() => $this->getValue(),
            // exclude current page from urls
            $this->_htmlPagerBlock->getPageVarName() => null,
        ];
        return $this->_url->getUrl('*/*/*', ['_current' => true, '_use_rewrite' => true, '_query' => $query]);
    }

    /**
     * Get url for remove item from filter
     *
     * @return string
     */
    public function getRemoveUrl()
    {
        $query = [$this->getFilter()->getRequestVar() => $this->getFilter()->getResetValue()];
        $params['_current'] = true;
        $params['_use_rewrite'] = true;
        $params['_query'] = $query;
        $params['_escape'] = true;
        return $this->_url->getUrl('*/*/*', $params);
    }

    /**
     * Get url for "clear" link
     *
     * @return false|string
     */
    public function getClearLinkUrl()
    {
        $clearLinkText = $this->getFilter()->getClearLinkText();
        if (!$clearLinkText) {
            return false;
        }

        $urlParams = [
            '_current' => true,
            '_use_rewrite' => true,
            '_query' => [$this->getFilter()->getRequestVar() => null],
            '_escape' => true,
        ];
        return $this->_url->getUrl('*/*/*', $urlParams);
    }

    /**
     * Get item filter name
     *
     * @return string
     */
    public function getName()
    {
        return $this->getFilter()->getName();
    }

    /**
     * Get item value as string
     *
     * @return string
     */
    public function getValueString()
    {
        $value = $this->getValue();
        if (is_array($value)) {
            return implode(',', $value);
        }
        return $value;
    }
}
