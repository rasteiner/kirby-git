<?php
namespace Rasteiner\KirbyGit;

require_once(__DIR__ . '/git.php');

use Kirby;

Kirby::plugin('rasteiner/git', [
  'routes' => [
    [
      'pattern' => 'api/rasteiner-git/commits',
      'method' => 'GET',
      'action' => Git::list_commits()
    ],
    [
      'pattern' => 'api/rasteiner-git/commits',
      'method' => 'POST',
      'action' => Git::create_commit()
    ],
    [
      'pattern' => 'api/rasteiner-git/commits/(:any)',
      'method' => 'GET',
      'action' => Git::show_commit()
    ],
    [
      'pattern' => 'api/rasteiner-git/rollback/(:any)',
      'method' => 'GET',
      'action' => Git::checkout_commit()
    ],
    [
      'pattern' => 'api/rasteiner-git/auth',
      'method' => 'GET',
      'action' => Git::has_auth()
    ]
  ]
]);