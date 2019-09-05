<?php

namespace HeimrichHannot\GoogleMapsListBundle\Lists;

use Contao\Environment;
use Contao\Model;
use Contao\System;
use HeimrichHannot\GoogleMapsBundle\EventListener\MapRendererListener;
use HeimrichHannot\GoogleMapsBundle\Model\OverlayModel;
use HeimrichHannot\ListBundle\Event\ListBeforeParseItemsEvent;
use HeimrichHannot\ListBundle\Manager\ListManagerInterface;
use Ivory\GoogleMap\Helper\Builder\ApiHelperBuilder;
use Ivory\GoogleMap\Helper\Builder\MapHelperBuilder;
use Ivory\GoogleMap\Map;
use Model\Collection;

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

    /**
     * @var string
     */
    protected $_addMapControlList;


    public function __construct(ListManagerInterface $_manager)
    {
        parent::__construct($_manager);

        System::getContainer()->get('event_dispatcher')->addListener(ListBeforeParseItemsEvent::NAME,
            function (ListBeforeParseItemsEvent $event) {
                $listConfig = $event->getListConfig();

                /** @var DefaultList $list */
                $list = $event->getList();

                if (!$listConfig->renderItemsAsMap || null === ($map = System::getContainer()->get('huh.utils.model')->findModelInstanceByPk('tl_google_map',
                        $listConfig->itemMap))) {
                    return;
                }

                $overlays = $this->transformItemsToOverlays($event->getItems());

                $list->setRenderedMap($this->renderMap($listConfig->itemMap, $map->row(), $overlays));
                $list->setAddMapControlList($listConfig->addMapControlList);

                $markerVariableMapping = System::getContainer()->get('huh.google_maps.overlay_manager')->getMarkerVariableMapping();

                $items = [];

                foreach ($event->getItems() as $item) {
                    $item['markerVariable'] = $markerVariableMapping[$item['id']];
                    $item['markerHref']     = Environment::get('uri') . '#' . $markerVariableMapping[$item['id']];

                    $items[] = $item;
                }

                $event->setItems($items);
            });
    }


    public function transformItemsToOverlays(array $items)
    {
        $models = array_map(function ($item) {
            $overlay = new OverlayModel();

            $overlay->setRow($item);

            return $overlay;
        }, $items);

        return new Collection($models, 'tl_google_map_overlay');
    }

    public function renderMap(int $mapId, array $config = [], Collection $overlays = null)
    {
        $mapManager = System::getContainer()->get('huh.google_maps.map_manager');

        if (null === ($mapConfig = System::getContainer()->get('huh.utils.model')->findModelInstanceByPk('tl_google_map',
                $mapId))) {
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

    /**
     * @return bool|null
     */
    public function getAddMapControlList()
    {
        return $this->_addMapControlList;
    }

    /**
     * @param bool $addMapControlList
     */
    public function setAddMapControlList(bool $addMapControlList): void
    {
        $this->_addMapControlList = $addMapControlList;
    }
}
