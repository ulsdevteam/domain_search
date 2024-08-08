<?php

namespace Drupal\domain_search\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Drupal\domain_access\DomainAccessManager;
use Drupal\search_api\Event\SearchApiEvents;
use Drupal\search_api\Event\IndexingItemsEvent;
use Drupal\search_api\Event\QueryPreExecuteEvent;

class DomainSearchSubscriber implements EventSubscriberInterface {
  public static function getSubscribedEvents() {
    return [
      SearchApiEvents::INDEXING_ITEMS => 'indexingItems',
      SearchApiEvents::QUERY_PRE_EXECUTE => 'executingQuery'
    ];
  }

  public function indexingItems(IndexingItemsEvent $event) {
    // return;
    foreach ($event->getItems() as $item) {
      $original_object = $item->getOriginalObject(true);
      $entity = $original_object->getEntity();
      $domains = DomainAccessManager::getAccessValues($entity);
      $item->setField('domain_ids', $domains);
    }
  }

  public function executingQuery(QueryPreExecuteEvent $event) {
    // return;
    $domain_negotiator = \Drupal::service('domain.negotiator');
    $domain_id = $domain_negotiator->getActiveId();
    $query = $event->getQuery();
    $conditions = $query->createAndAddConditionGroup();
    $conditions->addCondition('domain_ids', $domain_id, 'IN');
  }
}
