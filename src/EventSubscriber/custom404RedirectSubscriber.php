<?php

namespace Drupal\custom_404_redirect\EventSubscriber;

use Drupal\Core\Access\AccessManagerInterface;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\EventSubscriber\DefaultExceptionHtmlSubscriber;
use Drupal\Core\Routing\RedirectDestinationInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Routing\Matcher\UrlMatcherInterface;

/**
 * Exception subscriber for handling core custom HTML error pages.
 */
class Custom404RedirectSubscriber extends DefaultExceptionHtmlSubscriber {

  /**
   * The configuration factory.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * The access manager.
   *
   * @var \Drupal\Core\Access\AccessManagerInterface
   */
  protected $accessManager;

  /**
   * Constructs a new CustomPageExceptionHtmlSubscriber.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The configuration factory.
   * @param \Symfony\Component\HttpKernel\HttpKernelInterface $http_kernel
   *   The HTTP Kernel service.
   * @param \Psr\Log\LoggerInterface $logger
   *   The logger service.
   * @param \Drupal\Core\Routing\RedirectDestinationInterface $redirect_destination
   *   The redirect destination service.
   * @param \Symfony\Component\Routing\Matcher\UrlMatcherInterface $access_unaware_router
   *   A router implementation which does not check access.
   * @param \Drupal\Core\Access\AccessManagerInterface $access_manager
   *   The access manager.
   */
  public function __construct(ConfigFactoryInterface $config_factory, HttpKernelInterface $http_kernel, LoggerInterface $logger, RedirectDestinationInterface $redirect_destination, UrlMatcherInterface $access_unaware_router, AccessManagerInterface $access_manager) {
    parent::__construct($http_kernel, $logger, $redirect_destination, $access_unaware_router);
    $this->configFactory = $config_factory;
    $this->accessManager = $access_manager;
  }

  /**
   * {@inheritdoc}
   */
  protected static function getPriority() {
    return -50;
  }


  /**
   * {@inheritdoc}
   */
  public function on404(GetResponseForExceptionEvent $event) {
    $exclude_array = ['/test', '/testpage'];
    $request = \Drupal::request();
    $requestUri = $request->server->get('REQUEST_URI');
    if ( in_array($requestUri, $exclude_array) ) {
      $custom_path = '/node/1';
      $this->makeSubrequest($event, $custom_path, '200');
    }
  }

}