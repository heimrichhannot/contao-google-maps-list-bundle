# Contao Google Maps List Bundle

This bundle acts as a bridge between [heimrichhannot/contao-list-bundle](https://github.com/heimrichhannot/contao-list-bundle) and [heimrichhannot/contao-google-maps-bundle](https://github.com/heimrichhannot/contao-google-maps-bundle) in order render lists as a google map.

## Installation

Install via composer: `composer require heimrichhannot/contao-google-maps-list-bundle` and update your database.

## Configuration

### Render a list as a Google Map

1. Create a Google map as you would normally.
2. Create a list config as you would normally and activate the option `Render items as map`. Afterwards select the map you created before.
3. Switch the List class to `HeimrichHannot\GoogleMapsListBundle\Lists\DefaultList` Item class to `HeimrichHannot\GoogleMapsListBundle\Item\DefaultItem`
4. You can also add an ordinary textual list containing the items as links for triggering click events on the markers in the map (which are representing the same items as mentioned before).
    1. For this activate `Add map control list` in the list config.
    2. Switch the list template to `list_google_maps_default.html.twig` and the item template to `list_item_google_maps_default.html.twig`.
