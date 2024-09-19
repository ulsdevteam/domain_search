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
      SearchApiEvents::QUERY_PRE_EXECUTE => 'executingQuery'
    ];
  }

  public function executingQuery(QueryPreExecuteEvent $event) {
    $query = $event->getQuery();
    if ($query->getIndex()->getField(DomainAccessManager::DOMAIN_ACCESS_FIELD)) {
      $domain_negotiator = \Drupal::service('domain.negotiator');
      $domain_id = $domain_negotiator->getActiveId();
      $conditions = $query->createAndAddConditionGroup();
      $conditions->addCondition(DomainAccessManager::DOMAIN_ACCESS_FIELD, $domain_id, 'IN');
    }
  }
}
