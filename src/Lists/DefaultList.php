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
use HeimrichHannot\GoogleMapsBundle\EventListener\MapRendererListener;
use HeimrichHannot\GoogleMapsBundle\Model\OverlayModel;
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
use Ivory\GoogleMap\Helper\Builder\ApiHelperBuilder;
use Ivory\GoogleMap\Helper\Builder\MapHelperBuilder;
use Ivory\GoogleMap\Map;
use Model\Collection;
use Symfony\Component\EventDispatcher\EventDispatcher;

class DefaultList extends \HeimrichHannot\ListBundle\Lists\DefaultList
{
    /**
     * @var Model|null
     */
    protected $_map;

    /**
     * @var string
     */
    protected $_renderedMap;

    public function parse(string $listTemplate = null, string $itemTemplate = null, array $data = []): ?string
    {
        System::getContainer()->get('event_dispatcher')->addListener(ListBeforeRenderEvent::NAME, function(ListBeforeRenderEvent $event) {
            $listConfig = $event->getListConfig();
            $templateData = $event->getTemplateData();

            if (!$listConfig->renderItemsAsMap || null === ($map = System::getContainer()->get('huh.utils.model')->findModelInstanceByPk('tl_google_map', $listConfig->itemMap)))
            {
                return;
            }

            $overlays = $this->transformItemsToOverlays($event->getItems());

            $templateData['renderedMap'] = $this->renderMap($listConfig->itemMap, $map->row(), $overlays);
            $event->setTemplateData($templateData);
        });

        return parent::parse($listTemplate, $itemTemplate, $data);
    }

    public function transformItemsToOverlays(array $items)
    {
        $models = array_map(function($item) {
            $overlay = new OverlayModel();

            $overlay->setRow($item);

            return $overlay;
        }, $items);

        return new Collection($models, 'tl_google_map_overlay');
    }

    public function renderMap(int $mapId, array $config = [], Collection $overlays = null)
    {
        $mapManager = System::getContainer()->get('huh.google_maps.map_manager');

        if (null === ($mapConfig = System::getContainer()->get('huh.utils.model')->findModelInstanceByPk('tl_google_map', $mapId))) {
            return null;
        }

        $apiKey = $mapManager->computeApiKey($mapConfig);

        $templateData = $mapManager->prepareMap($mapId, $config, $overlays);

        if (null === $templateData) {
            return null;
        }

        /** @var Map $map */
        $map = $templateData['mapModel'];

        $mapHelper = MapHelperBuilder::create()->build();
        $apiHelper = ApiHelperBuilder::create()->setLanguage($mapManager->getLanguage($mapId))->setKey($apiKey)->build();

        $listener = new MapRendererListener($templateData['mapConfigModel'], $mapManager, $mapHelper);

        $mapHelper->getEventDispatcher()->addListener('map.stylesheet', [$listener, 'renderStylesheet']);

        $templateData['mapHtml']     = $mapHelper->renderHtml($map);
        $templateData['mapCss']      = $mapHelper->renderStylesheet($map);
        $templateData['mapJs']       = $mapHelper->renderJavascript($map);
        $templateData['mapGoogleJs'] = $apiHelper->render([$map]);

        $template = $templateData['mapConfig']['template'] ?: 'gmap_map_default';
        $template = System::getContainer()->get('huh.utils.template')->getTemplate($template);

        return System::getContainer()->get('twig')->render($template, $templateData);
    }

    /**
     * @return Model|null
     */
    public function getMap()
    {
        return $this->_map;
    }

    /**
     * @param Model $map
     */
    public function setMap(Model $map): void
    {
        $this->_map = $map;
    }

    /**
     * @return string
     */
    public function getRenderedMap(): string
    {
        return $this->_renderedMap ?: '';
    }

    /**
     * @param string $renderedMap
     */
    public function setRenderedMap(string $renderedMap): void
    {
        $this->_renderedMap = $renderedMap;
    }
}