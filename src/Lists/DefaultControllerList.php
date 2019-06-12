<?php

namespace HeimrichHannot\GoogleMapsListBundle\Lists;

use Contao\Config;
use Contao\Database;
use Contao\Date;
use Contao\FrontendTemplate;
use Contao\Model;
use Contao\StringUtil;
use Contao\System;
use HeimrichHannot\Blocks\BlockModuleModel;
use HeimrichHannot\FilterBundle\Config\FilterConfig;
use HeimrichHannot\FilterBundle\QueryBuilder\FilterQueryBuilder;
use HeimrichHannot\ListBundle\Backend\ListConfig;
use HeimrichHannot\ListBundle\Event\ListAfterParseItemsEvent;
use HeimrichHannot\ListBundle\Event\ListAfterRenderEvent;
use HeimrichHannot\ListBundle\Event\ListBeforeParseItemsEvent;
use HeimrichHannot\ListBundle\Event\ListBeforeRenderEvent;
use HeimrichHannot\ListBundle\Event\ListModifyQueryBuilderEvent;
use HeimrichHannot\ListBundle\Event\ListModifyQueryBuilderForCountEvent;
use HeimrichHannot\ListBundle\HeimrichHannotContaoListBundle;
use HeimrichHannot\ListBundle\Item\ItemInterface;
use HeimrichHannot\ListBundle\Manager\ListManagerInterface;
use HeimrichHannot\ListBundle\Model\ListConfigModel;
use HeimrichHannot\ListBundle\Pagination\RandomPagination;
use Symfony\Component\EventDispatcher\EventDispatcher;

class DefaultControllerList extends \HeimrichHannot\ListBundle\Lists\DefaultList
{
    /**
     * @var Model|null
     */
    protected $controlledMap;

    /**
     * @var string
     */
    protected $renderedControlledMap;

    public function parse(string $listTemplate = null, string $itemTemplate = null, array $data = []): ?string
    {
        $listConfig = $this->_manager->getListConfig();
        $mapManager = System::getContainer()->get('huh.google_maps.map_manager');

        if (!$listConfig->useListAsMapControl)
        {
            return parent::parse($listTemplate, $itemTemplate, $data);
        }

        // add map
        if (null === ($this->controlledMap = System::getContainer()->get('huh.utils.model')->findModelInstanceByPk('tl_google_map', $listConfig->controlledMap)))
        {
            return parent::parse($listTemplate, $itemTemplate, $data);
        }

        $this->renderedControlledMap = $mapManager->render($listConfig->controlledMap, $this->controlledMap->row());

        return parent::parse($listTemplate, $itemTemplate, $data);
    }

    /**
     * @return Model|null
     */
    public function getControlledMap()
    {
        return $this->controlledMap;
    }

    /**
     * @param Model $controlledMap
     */
    public function setControlledMap(Model $controlledMap): void
    {
        $this->controlledMap = $controlledMap;
    }

    /**
     * @return string
     */
    public function getRenderedControlledMap(): string
    {
        return $this->renderedControlledMap ?: '';
    }

    /**
     * @param string $renderedControlledMap
     */
    public function setRenderedControlledMap(string $renderedControlledMap): void
    {
        $this->renderedControlledMap = $renderedControlledMap;
    }
}