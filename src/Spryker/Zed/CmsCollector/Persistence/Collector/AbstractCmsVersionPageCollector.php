<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\CmsCollector\Persistence\Collector;

use Orm\Zed\Cms\Persistence\Map\SpyCmsPageTableMap;
use Orm\Zed\Cms\Persistence\Map\SpyCmsVersionTableMap;
use Orm\Zed\Cms\Persistence\SpyCmsVersionQuery;
use Orm\Zed\Touch\Persistence\Map\SpyTouchTableMap;
use Orm\Zed\Url\Persistence\Map\SpyUrlTableMap;
use Spryker\Zed\Collector\Persistence\Collector\AbstractPropelCollectorQuery;
use Spryker\Zed\PropelOrm\Business\Runtime\ActiveQuery\Criteria;

abstract class AbstractCmsVersionPageCollector extends AbstractPropelCollectorQuery
{
    /**
     * @var string
     */
    public const COL_URL = 'url';

    /**
     * @var string
     */
    public const COL_IS_ACTIVE = 'is_active';

    /**
     * @var string
     */
    public const COL_DATA = 'data';

    /**
     * @var string
     */
    public const COL_VALID_FROM = 'valid_from';

    /**
     * @var string
     */
    public const COL_VALID_TO = 'valid_to';

    /**
     * @var string
     */
    public const COL_IS_SEARCHABLE = 'is_searchable';

    /**
     * @return void
     */
    protected function prepareQuery()
    {
        $this->touchQuery->addJoin(
            SpyTouchTableMap::COL_ITEM_ID,
            SpyCmsPageTableMap::COL_ID_CMS_PAGE,
            Criteria::INNER_JOIN,
        )
        ->addJoin(
            SpyCmsPageTableMap::COL_ID_CMS_PAGE,
            SpyCmsVersionTableMap::COL_FK_CMS_PAGE,
            Criteria::INNER_JOIN,
        )
        ->addJoin(
            SpyCmsPageTableMap::COL_ID_CMS_PAGE,
            SpyUrlTableMap::COL_FK_RESOURCE_PAGE,
            Criteria::INNER_JOIN,
        )
        ->addAnd(
            SpyUrlTableMap::COL_FK_LOCALE,
            $this->getLocale()->getIdLocale(),
            Criteria::EQUAL,
        );

        $this->touchQuery->withColumn(SpyCmsVersionTableMap::COL_DATA, static::COL_DATA);
        $this->touchQuery->withColumn(SpyCmsPageTableMap::COL_IS_SEARCHABLE, static::COL_IS_SEARCHABLE);
        $this->touchQuery->withColumn(SpyUrlTableMap::COL_URL, static::COL_URL);
        $this->touchQuery->withColumn(SpyCmsPageTableMap::COL_IS_ACTIVE, static::COL_IS_ACTIVE);
        $this->touchQuery->withColumn(SpyCmsPageTableMap::COL_VALID_FROM, static::COL_VALID_FROM);
        $this->touchQuery->withColumn(SpyCmsPageTableMap::COL_VALID_TO, static::COL_VALID_TO);
        $this->touchQuery->withColumn(SpyCmsVersionTableMap::COL_VERSION);

        /** @var literal-string $where */
        $where = sprintf('%s = (%s)', SpyCmsVersionTableMap::COL_VERSION, $this->getMaxVersionSubQuery());
        $this->touchQuery->where($where);
    }

    /**
     * @return string
     */
    protected function getMaxVersionSubQuery()
    {
        $maxVersionQuery = SpyCmsVersionQuery::create()
            ->addSelfSelectColumns()
            ->clearSelectColumns()
            ->withColumn(sprintf('MAX(%s)', SpyCmsVersionTableMap::COL_VERSION))
            ->where(sprintf('%s = %s', SpyCmsVersionTableMap::COL_FK_CMS_PAGE, SpyCmsPageTableMap::COL_ID_CMS_PAGE));

        $queryParams = [];
        $queryString = $maxVersionQuery->createSelectSql($queryParams);

        return $queryString;
    }
}
